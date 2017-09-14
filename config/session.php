<?php
$this->set('session', [
    'cookie_domain' => $this->get('baseHost'),
    'cookie_path' => '/',
    'cookie_lifetime' => 86400000,
    'gc_maxlifetime' => 86400000,
    'name' => 'light_session',
    'save_handler' => 'file',
    'save_path' => $this->baseDir . '/storage/framework/sessions'
]);