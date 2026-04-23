<?php

return [
    'plans' => [
        'basic' => [
            'name' => 'Basic',
            'price_id' => env('STRIPE_BASIC_PRICE'),
            'price' => 9.99,
            'interval' => 'monthly',
            'features' => [
                'Up to 10 projects',
                'Basic support',
                '5GB storage',
            ],
        ],
        'medium' => [
            'name' => 'Medium',
            'price_id' => env('STRIPE_MEDIUM_PRICE'),
            'price' => 19.99,
            'interval' => 'monthly',
            'features' => [
                'Up to 50 projects',
                'Priority support',
                '50GB storage',
                'Advanced analytics',
            ],
        ],
        'premium' => [
            'name' => 'Premium',
            'price_id' => env('STRIPE_PREMIUM_PRICE'),
            'price' => 49.99,
            'interval' => 'monthly',
            'features' => [
                'Unlimited projects',
                '24/7 priority support',
                '500GB storage',
                'Advanced analytics',
                'Custom integrations',
                'Team collaboration',
            ],
        ],
    ],
];