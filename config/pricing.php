<?php

declare(strict_types=1);

/**
 * Pricing Configuration
 * Omniwallet CMS
 */

return [
    'activity_multiplier' => 1.3, // Ventas × 1.3 = Actividades

    'plans' => [
        'starter' => [
            'name' => 'Starter',
            'price' => 39,
            'description' => 'Para pequeños negocios',
        ],
        'plus' => [
            'name' => 'Plus',
            'price' => 149,
            'highlighted' => true,
            'description' => 'Para negocios en crecimiento',
        ],
        'advanced' => [
            'name' => 'Advanced',
            'price' => 249,
            'description' => 'Para negocios consolidados',
        ],
    ],

    // Tramos de precio por actividad (acumulativos)
    // Las primeras 100 actividades son gratis (Freemium del plan Advanced)
    // A partir de 100, se cobra el plan + variable por actividades
    'activity_tiers' => [
        ['max' => 100, 'price' => 0.00, 'label' => 'Hasta 100'],
        ['max' => 250, 'price' => 0.00, 'label' => '101 a 250'],
        ['max' => 1000, 'price' => 0.10, 'label' => '251 a 1.000'],
        ['max' => 5000, 'price' => 0.08, 'label' => '1.001 a 5.000'],
        ['max' => 10000, 'price' => 0.06, 'label' => '5.001 a 10.000'],
        ['max' => 50000, 'price' => 0.04, 'label' => '10.001 a 50.000'],
        ['max' => PHP_INT_MAX, 'price' => 0.02, 'label' => 'Más de 50.000'],
    ],

    'freemium_limit' => 100,      // Menos de 100 actividades = Freemium gratis
    'max_calculator' => 150000,   // Máximo calculable, más de esto = contactar
];
