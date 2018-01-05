<?php
$baseHost = $this->get('baseHost');

$this->set('site', [
    'www' => [
        'host' => 'www.' . $baseHost,
    ],
    'static' => [
        'host' => 'static.' . $baseHost,
        'dir' => $this->get('baseDir') . '/public',
    ],
]);
