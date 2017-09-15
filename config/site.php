<?php
$baseHost = $this->get('baseHost');

$this->set('site', [
    'www' => [
        'host' => 'www.' . $baseHost,
    ]
]);
