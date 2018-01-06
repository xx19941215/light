<?php
//
// scheme:[//[user:password@]host[:port]][/]path[?query][#fragment]
//

$this->set('i18n', [
    'db' => 'i18n',
    'cache' => 'i18n',
    'locale' => [
        'mode' => 'default',
        'available' => [
            'zh-cn' => ['key' => 'zh-cn', 'title' => '简体中文'],
            'en-us' => ['key' => 'en-us', 'title' => 'English'],
            'de-de' => ['key' => 'de-de', 'title' => 'Deutschland - Deutsch'],
            'fr-fr' => ['key' => 'fr-fr', 'title' => 'France - Français'],
            'zh-tw' => ['key' => 'zh-tw', 'title' => '繁體中文 - 台湾'],
            'zh-hk' => ['key' => 'zh-hk', 'title' => '繁體中文 - 香港'],
        ],
        'enabled' => [
            'zh-cn', 'en-us'
        ],
        'default' => 'zh-cn',
    ]
]);
