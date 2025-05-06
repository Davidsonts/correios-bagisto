<?php

return [
    'correios' => [
        'code' => 'correios',
        'title' => 'Correios',
        'description' => 'Correios',
        'active' => true,
        'default_rate' => '10',
        'type'         => 'per_unit',
        'class' => 'Webkul\Correios\Carriers\Correios',
        'methods' => 'sedex,pac',
        'tax_handling' => 0,
        'extra_time' => 1,
        'method_template' => '%s - Entrega em até %d dia úteis(s)',
        'package_type' => '0',
        'package_length' => 16,
        'package_height' => 11,
        'package_width' => 11,
        // 'default_method' => 'pac',
        'default_price' => 20,
        'default_estimate' => 10,
    ],
];