<?php
namespace Light\Http;

use Light\I18n\Locale\LocaleManager;
use Light\Routing\Route;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

class Request extends SymfonyRequest
{
    protected $route;
    protected $siteManager;
    protected $localeManager;
    protected $path;

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
        if ($localeKey = $this->attributes->get('localeKey')) {
            return $localeKey;
        }

        return $this->guessLocaleKey();
    }

    public function guessLocaleKey()
    {
        foreach ($this->getLanguages() as $lang) {
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

        $port = $this->getPort();

        if ($port != 80) {
            $host = $host . ':' . $port;
        }

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
