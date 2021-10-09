<?php
require 'vendor/autoload.php';

use \WebPConvertCloudService\WebPConvertCloudService;

$options = [
    // Set dir for storing converted images temporarily
    // Make sure to create that dir, with permissions for web server to write.
    // You can __DIR__ to get same dir as this script, or "dirname(__DIR__)" to get parent dir of the script,
    'destination-dir' => dirname(__DIR__) . '/conversions',

    // Set acccess restrictions
    'access' => [
        'whitelist' => [
            [
                'ip' => '*',
                'api-key' => 'gogogo2021',
                'require-api-key-to-be-crypted-in-transfer' => false
            ]
        ]
    ],

    // Optionally set webp-convert options
    'webp-convert' => [
        'converters' => ['cwebp', 'gd', 'imagick'],
        'converter-options' => [
            'cwebp' => [
                'try-common-system-paths' => true,
                'try-supplied-binary-for-os' => true,
                'use-nice' => true
            ]
        ]
    ]
];

$wpc = new WebPConvertCloudService();
$wpc->handleRequest($options);
?>
