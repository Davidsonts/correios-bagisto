<?php

return [
    [
        'key'    => 'sales.carriers.correios',
        'name'   => 'Correios',
        'sort'   => 1,
        'info'   => 'Correios',
        'fields' => [
            [
                'name' => 'title',
                'title' => 'Título',
                'type' => 'text',
                'validation' => 'required',
                'channel_based' => false,
                'locale_based' => true
            ], [
                'name' => 'description',
                'title' => 'Descrição',
                'type' => 'textarea',
                'channel_based' => false,
                'locale_based' => true
            ], [
                'name' => 'tax_handling',
                'title' => 'Taxa de Manuseio',
                'type' => 'text',
                'channel_based' => false,
                'locale_based' => true,
                'validation' => 'required',
            ], [
                'name' => 'methods',
                'title' => 'Métodos',
                'type' => 'multiselect',
                'options' => [
                    [
                        'title' => 'Sedex',
                        'value' => 'sedex'
                    ], 
                    // [
                    //     'title' => 'Sedex a Cobrar',
                    //     'value' => 'sedex_a_cobrar'
                    // ], [
                    //     'title' => 'Sedex 10',
                    //     'value' => 'sedex_10'
                    // ], [
                    //     'title' => 'Sedex Hoje',
                    //     'value' => 'sedex_hoje'
                    // ], 
                    [
                        'title' => 'PAC',
                        'value' => 'pac'
                    ], [
                        'title' => 'PAC Contrato',
                        'value' => 'pac_contrato'
                    ], [
                        'title' => 'Sedex Contrato',
                        'value' => 'sedex_contrato'
                    ]
                ],
                'info' => 'Método Obrigatório',
                'validation' => 'required'
            ], [
                'name' => 'extra_time',
                'title' => 'Dias Extras ao Prazo de Entrega',
                'type' => 'text',
                'channel_based' => false,
                'locale_based' => true,
                'validation' => 'required',
            ], [
                'name' => 'method_template',
                'title' => 'Template do Retorno',
                'type' => 'text',
                'channel_based' => false,
                'locale_based' => true,
                'validation' => 'required',
                'info' => 'A variável %d será substituído pelo retorno dos Correios + os Dias Extras ao Prazo de Entrega definido, a variável %s será substituído pelo nome do método.',
            ],
            [  
                'name' => 'package_type',
                'title' => 'Formato do Pacote',
                'type' => 'select',
                'options' => [
                    [
                        'title' => 'Caixa',
                        'value' => 2
                    ], [
                        'title' => 'Rolo',
                        'value' => 3
                    ], [
                        'title' => 'Envelope',
                        'value' => 1
                    ], [
                        'title' => 'Default',
                        'value' => 0
                    ]
                ],
                'channel_based' => false,
                'locale_based' => true,
                'validation' => 'required'
            ],
            [
                'name' => 'package_length',
                'title' => 'Comprimento do Pacote',
                'type' => 'text',
                'channel_based' => false,
                'locale_based' => true,
                'info' => 'Válido apenas para Formato Caixa',
                'validation' => 'required',
            ],
            [
                'name' => 'package_height',
                'title' => 'Altura do Pacote',
                'type' => 'text',
                'channel_based' => false,
                'locale_based' => true,
                'validation' => 'required',
                'info' => 'Válido apenas para Formato Caixa'
            ],
            [
                'name' => 'package_width',
                'title' => 'Largura do Pacote',
                'type' => 'text',
                'channel_based' => false,
                'locale_based' => true,
                'info' => 'Válido apenas para Formato Caixa',
                'validation' => 'required',
            ],
            [
                'name' => 'roll_diameter',
                'title' => 'Diâmetro do Rôlo',
                'type' => 'text',
                'channel_based' => false,
                'locale_based' => true,
                'info' => 'Válido apenas para Formato Rolo, Manter 0 se não aplicável.',
                'validation' => 'required',
            ],
            [
                'name' => 'cod_company',
                'title' => 'Código Da Empresa',
                'type' => 'text',
                'channel_based' => false,
                'locale_based' => true,
                'info' => 'Código da Empresa junto aos Correios'
            ],
            [
                'name' => 'password',
                'title' => 'Senha',
                'type' => 'text',
                'channel_based' => false,
                'locale_based' => true,
                'info' => 'Senha da Empresa junto aos Correios'
            ],
            [
                'name' => 'postcard',
                'title' => 'Cartão Postal',
                'type' => 'text',
                'channel_based' => false,
                'locale_based' => true,
                'info' => 'Cartão Postal da Empresa junto aos Correios'
            ], [
                'name' => 'sandbox',
                'title' => 'Modo de Teste',
                'type' => 'boolean',
                'validation' => 'required'
            ],
            [
                'name' => 'active',
                'title' => 'Status',
                'type' => 'boolean',
                'validation' => 'required'
            ], 
            // [
            //     'name' => 'default_method',
            //     'title' => 'Método Padrão',
            //     'type' => 'select',
            //     'options' => [
            //         [
            //             'title' => 'Sedex',
            //             'value' => 'sedex'
            //         ], [
            //             'title' => 'PAC',
            //             'value' => 'pac'
            //         ]
            //     ],
            //     'info' => 'Selecione um método para mostrar esse método caso o serviço dos Correios estiver fora do ar.',
            //     'validation' => 'required'
            // ], 
            // [
            //     'name' => 'default_price',
            //     'title' => 'Preço Padrão',
            //     'type' => 'text',
            //     'channel_based' => false,
            //     'locale_based' => true,
            //     'validation' => 'required',
            // ], [
            //     'name' => 'default_estimate',
            //     'title' => 'Prazo padrão de entrega (em dias)',
            //     'type' => 'text',
            //     'channel_based' => false,
            //     'locale_based' => true,
            //     'validation' => 'required',
            // ],
        ]
    ]
];