<?php

namespace Davidsonts\Correios\Carriers;

use Webkul\Shipping\Carriers\AbstractShipping;
use Webkul\Checkout\Models\CartShippingRate;
use Webkul\Checkout\Repositories\CartRepository;
use Webkul\Checkout\Facades\Cart; // Import the Cart facade

use Exception;
 
use function core;

class Correios extends AbstractShipping
{
    /**
     * Shipping method code
     *
     * @var string
     */
    protected $code  = 'correios';
    
    private static $methods = array(
        'sedex'            => '04014', // Sedex à vista
        //'sedex_a_cobrar'   => '40045', // Sedex a Cobrar
       // 'sedex_10'         => '40215', // Sedex 10
       // 'sedex_hoje'       => '40290', // Sedex Hoje
        'pac'              => '04510', // PAC Varejo
        'pac_contrato'     => '04669', // PAC Contrato Agência
        'sedex_contrato'   => '04162', // Sedex Contrato Agência
       // 'esedex'           => '81019', // e-SEDEX Prioritário
    );

    /**
     * Returns rate for shipping method
     *
     * @return CartShippingRate|false
     */
    public function calculate()
    {
        if (! $this->isAvailable()) {
            return false;
        }
        $codes = $this->getMethodCodes();  
        $originCep = core()->getConfigData('sales.shipping.origin.zipcode');
         
        $cart = Cart::getCart(); // Obtém o carrinho atual [[2]]

        $shippingAddress = $cart->shipping_address; // Acessa o endereço de entrega

        if ($shippingAddress) {
            $cepDestinatario = $shippingAddress->postcode; // Campo 'postcode' armazena o CEP
        } else {
            $cepDestinatario = null; // Trata caso sem endereço definido
        }
        // Gerando um novo token - Generating a new token
        $correios = new \Correios\Correios(
            username:  core()->getConfigData('sales.carriers.correios.cod_company'),
            password:  core()->getConfigData('sales.carriers.correios.password'),
            postcard:  core()->getConfigData('sales.carriers.correios.postcard'),
            isTestMode: core()->getConfigData('sales.carriers.correios.sandbox'),
        );

        $token           = $correios->authentication()->getToken();
        // $tokenExpiration = $correios->authentication()->getTokenExpiration();
        // $responseBody    = $correios->authentication()->getResponseBody();
        // $responseCode    = $correios->authentication()->getResponseCode();
        // $errors          = $correios->authentication()->getErrors();

        // Pega o número do e da diretoria com base na responsta da autenticação - Gets the board number based on the authentication response
        $contractNumber = $correios->authentication()->getContract();
        $drNumber = $correios->authentication()->getDr();

        // Usando um token gerado anteriormente - Using a token generated earlie
        $correios = new \Correios\Correios(
            username:  core()->getConfigData('sales.carriers.correios.cod_company'),
            password:  core()->getConfigData('sales.carriers.correios.password'),
            postcard:  core()->getConfigData('sales.carriers.correios.sandbox'),
            isTestMode: core()->getConfigData('sales.carriers.correios.sandbox'),
            token: $token  
        );

        $product = ['weight' => $this->getCartWeight()];

        if ($objectType = (int) core()->getConfigData('sales.carriers.correios.package_type') != 0) {
            $product['objectType'] = $objectType;

            if ($objectType == 2) {
                $product['length'] = core()->getConfigData('sales.carriers.correios.package_length') ?? 16;
                $product['height'] = core()->getConfigData('sales.carriers.correios.package_height') ?? 11;
                $product['width'] = core()->getConfigData('sales.carriers.correios.package_width') ?? 11;
            }

            if ($objectType == 3 && core()->getConfigData('sales.carriers.correios.roll_diameter') != 0) {
                // Se o tipo de objeto for 3 (rolo) e o diâmetro do rolo for diferente de 0
                $product['diameter'] = core()->getConfigData('sales.carriers.correios.roll_diameter');
            }
        }
        
        $response = $correios->price()->get(
            serviceCodes: $codes,
            products: [$product],
            originCep: $originCep,
            destinyCep: $cepDestinatario
        );

        // 1. Obter os prazos de entrega
        $prazoResponse = $correios->date()->get(
            serviceCodes: $codes,
            originCep: $originCep,
            destinyCep: $cepDestinatario
        );

        $prazoData = array_values(get_object_vars($prazoResponse['data']));
 
        // Acesse os dados diretamente
        $prazos = [];
        if (isset($prazoResponse['data']) && is_array($prazoResponse['data'])) {
            foreach ($prazoResponse['data'] as $item) {
                $prazos[$item['coProduto']] = $item['prazoEntrega'];
            }
        }  else {
            foreach ($prazoData as $item) {
                $prazos[$item->coProduto] = $item->prazoEntrega;
            }
        }

        // 2. Inverte o mapeamento [nome => código] para [código => nome]
        $flipped = array_flip(self::$methods); // array_flip() do PHP :contentReference[oaicite:1]{index=1}
       
        // 3. Atualizar a descrição do método de envio
        $rates = [];
        foreach ($response['data'] as $rate) {
            $object = new CartShippingRate();
            $object->carrier = 'correios';
            $object->carrier_title = $this->getConfigData('title');
            $object->method = 'correios_' . $rate->coProduto;
            $object->method_title = $this->getConfigData('title');
            // Captura o código do serviço
            $code = $rate->coProduto;

            // Busca o nome lógico no mapa invertido
            if (isset($flipped[$code])) {
                // Formata o nome: 'pac_contrato' → 'Pac Contrato'
                $nomeMetodo = str_replace('Contrato', '', ucwords(str_replace('_', ' ', $flipped[$code])));

                // Monta a descrição com o prazo
                $object->method_description = sprintf(
                    core()->getConfigData('sales.carriers.correios.method_template'),
                    $nomeMetodo,
                    $prazos[$code] + core()->getConfigData('sales.carriers.correios.extra_time') ?? 0
                );
            } else {
                // Fallback para descrição genérica
                $object->method_description = $this->getConfigData('description');
            }

            $pcFinal = str_replace(',', '.', $rate->pcFinal) + core()->getConfigData('sales.carriers.correios.tax_handling');
            $object->price = (float) $pcFinal;
            $object->base_price = (float) $pcFinal;

            $rates[] = $object;
        }

        return $rates;
    }

    public function getCartWeight()
    {
        $cart = Cart::getCart(); // Use the facade to get the current cart
        $totalWeight = 0;
    
        foreach ($cart->items as $item) {
            $totalWeight += $item->product->weight * $item->quantity;
        }
    
        return $totalWeight;
    }

    public function getMethodCodes(): array
    {
        $methods = core()->getConfigData('sales.carriers.correios.methods');
        $methods = explode(',', $methods);
        $methodCodes = [];

        foreach ($methods as $method) {
            if (isset(self::$methods[$method])) {
                $methodCodes[] = self::$methods[$method];
            }
        }

        return $methodCodes;
    }
 
}