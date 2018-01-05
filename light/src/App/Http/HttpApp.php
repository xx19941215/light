<?php
namespace Light\App\Http;

use Light\App\App;
use Light\Http\HttpHandler;
use Light\Http\Request;
use Light\Http\SiteManager;
use Light\Http\UrlManager;
use Light\Meta\Meta;
use Symfony\Component\HttpFoundation\Session\Session;

class HttpApp extends App
{
    protected $type = 'http';
    protected $httpHandler;
    protected $urlManager;
    protected $meta;
    
    public function getRouter()
    {
        return $this->httpHandler->getRouter();
    }

    public function getMeta()
    {
       if ($this->meta) {
           return $this->meta;
       }

       $this->meta = new Meta(
           $this->getDmg()->connect('meta'),
           $this->getCmg()->connect('meta')
       );

       return $this->meta;
    }

    public function getUrlManager(Request $request)
    {
        return new UrlManager($this->getRouter(), $request);
    }

    public function handle(Request $request)
    {
        $this->initSession();
        $request->setSession(new Session());

        $request->setSiteManager(new SiteManager($this->config->get('site')));
        $this->registerDebug($request);

        $this->httpHandler = new HttpHandler($this);
        return $this->httpHandler->handle($request);
    }

    protected function registerDebug(Request $request)
    {
        if ($this->isDebug) {
            if ('api' !== $request->getSite()) {
                $whoops = new \Whoops\Run;
                $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
                $whoops->register();
            }
        }
    }

    protected function initSession()
    {
        $opts = $this->config->get('session');
        $validOptions = array_flip(array(
            'cache_limiter', 'cookie_domain', 'cookie_httponly',
            'cookie_lifetime', 'cookie_path', 'cookie_secure',
            'entropy_file', 'entropy_length', 'gc_divisor',
            'gc_maxlifetime', 'gc_probability', 'hash_bits_per_character',
            'hash_function', 'name', 'referer_check',
            'serialize_handler', 'use_cookies',
            'use_only_cookies', 'use_trans_sid', 'upload_progress.enabled',
            'upload_progress.cleanup', 'upload_progress.prefix', 'upload_progress.name',
            'upload_progress.freq', 'upload_progress.min-freq', 'url_rewriter.tags',
            'save_handler', 'save_path'
        ));
        foreach ($opts as $key => $value) {
            if (isset($validOptions[$key])) {
                ini_set('session.' . $key, $value);
            }
        }
    }
}