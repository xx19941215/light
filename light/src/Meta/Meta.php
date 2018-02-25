<?php
namespace Light\Meta;

use Light\Database\Connection\Mysql as MysqlConnection;

class Meta
{
    protected $cnn;
    protected $cache;

    protected $table = 'meta';
    protected $defaultLocaleKey = 'zh-cn';

    public function __construct(MysqlConnection $cnn, \Redis $redis)
    {
        $this->cnn = $cnn;
        $this->cache = $redis;
    }

    public function setDefaultLocaleKey($localeKey)
    {
        $this->defaultLocaleKey = $localeKey;
    }

    public function get($str, $vars = [], $localeKey = '')
    {
        if (!$localeKey) {
            $localeKey = $this->defaultLocaleKey;
        }

        if (!$vars) {
            return $this->lget($str, $localeKey);
        }

        if (is_string($vars)) {
            $vars = [$vars];
        }

        $index = 1;
        $args = [];
        $args[0] = '';

        foreach ($vars as $val) {
            $str .= "-%$index" . '$s';
            $args[$index] = $val;
            $index++;
        }

        $args[0] = $this->lget($str, $localeKey);
        return sprintf(...$args);
    }

    protected function lget($metaKey, $localeKey)
    {
        if (!$metaKey) {
            //todo
            throw new \Exception("cannot get empty metaKey");
        }

        if (!$localeKey) {
            //todo
            throw new \Exception("localeKey cannot be empty");
        }

        if ($metaValue = $this->cache->hGet($localeKey, $metaKey)) {
            return $metaValue;
        }

        if ($metaValue = $this->findFromDb($localeKey, $metaKey)) {
            $this->cache->hGet($localeKey, $metaValue);
            return $metaValue;
        }

        $metaValue = '#' . $metaKey;
        $this->set($localeKey, $metaKey, $metaValue);
        return $metaValue;
    }

    public function set($localeKey, $metaKey, $metaValue)
    {
        if (!$metaKey) {
            // todo
            throw new \Exception("cannot empty metaKey");
        }

        if (!$metaValue) {
            // todo
            throw new \Exception("cannot empty metaValue");
        }

        if (!$localeKey) {
            // todo
            throw new \Exception("cannot empty localeKey");
        }

        $this->cache->hSet($localeKey, $metaKey, $metaValue);
        $this->saveToDb($localeKey, $metaKey, $metaValue);
    }

    protected function findFromDb($localeKey, $metaKey)
    {
        $obj = $this->cnn->select('value')
            ->from($this->table)
            ->where('localeKey', '=', $localeKey)
            ->andWhere('key', '=', $metaKey)
            ->fetchObjOne();

        if (!$obj) {
            return null;
        }

        return $obj->value;
    }

    protected function saveToDb($localeKey, $metaKey, $metaValue)
    {
        if ($this->findFromDb($localeKey, $metaKey)) {
            $this->cnn->update($this->table)
                ->where('localeKey', '=', $localeKey)
                ->andWhere('key', '=', $metaKey)
                ->set('value', $metaValue)
                ->execute();
            return;
        }

        $this->cnn->insert($this->table)
                ->value('metaId', $this->cnn->xid())
                ->value('localeKey', $localeKey)
                ->value('key', $metaKey)
                ->value('value', $metaValue)
                ->execute();
    }
}
