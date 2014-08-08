<?php

class Extra_Cache
{

    /**
     *
     * @var Zend_Cache
     */
    protected $cache;
    static $fileCache = null;

    public static function getInstance()
    {
        if (self::$fileCache === null) {
            self::$fileCache = new self();
        }
        return self::$fileCache;
    }

    /**
     *
     * @return Zend_Cache
     */
    public function getCache()
    {
        return $this->cache;
    }

    protected function __construct()
    {
        if (Zend_Registry::isRegistered('cache')) {
            $this->cache = Zend_Registry::get('cache');
        }
    }

    public static function load($keyName)
    {
        return self::getInstance()->getCache()->load($keyName);
    }

    public static function save($data, $keyName, $tags = array(), $timeLife = false)
    {
        self::getInstance()->getCache()->save($data, $keyName, $tags, $timeLife);
    }

    public static function clearAll()
    {
        self::getInstance()->getCache()->clean(Zend_Cache::CLEANING_MODE_ALL);
    }

    public static function clear($keyName)
    {
        self::getInstance()->getCache()->remove($keyName);
    }

}