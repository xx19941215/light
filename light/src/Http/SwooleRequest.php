<?php
namespace Light\Http;

use Light\I18n\Locale\LocaleManager;
use Light\Routing\Route;

class SwooleRequest
{
    protected $route;
    protected $siteManager;
    protected $localeManager;
    protected $path;
    protected $request;

    public function __construct($request)
    {
       $this->request = $request;
    }

    public function setLocaleManager(LocaleManager $localeManager)
    {
        $this->localeManager = $localeManager;
    }

    public function setSiteManager(SiteManager $siteManager)
    {
        $this->siteManager = $siteManager;
    }

    public function getLocaleManager(): LocaleManager
    {
        return $this->localeManager;
    }

    public function setRoute(Route $route)
    {
        $this->route = $route;
        return $this->route;
    }

    public function getLocaleKey()
    {
       return $this->guessLocaleKey();
    }

    public function guessLocaleKey()
    {
        foreach ($this->swooleRequest->header['accept-language'] as $lang) {
            $localeKey = str_replace('_', '-', strtolower($lang));
            if ($this->localeManager->isAvailableLocaleKey($localeKey)) {
                return $localeKey;
            }
        }
        return $this->localeManager->getDefaultLocaleKey();
    }

    public function getRoute() : Route
    {
        return $this->route;
    }

    public function getSite()
    {
        $host = $this->getHost();
        return $this->siteManager->getSite($host);
    }

    public function getPath()
    {
        if ($this->path) {
           return $this->path;
        }

        if ($path = $this->attributes->get('path')) {
            return $path;
        }

        return $this->getPathInfo();
    }

    public function setPath($path)
    {
        $this->path = $path;
    }

    public function getSiteManager() : SiteManager
    {
       return $this->siteManager;
    }

    public function getLocaleMode()
    {
        return $this->localeManager->getMode();
    }

    public function getApp()
    {

    }
}