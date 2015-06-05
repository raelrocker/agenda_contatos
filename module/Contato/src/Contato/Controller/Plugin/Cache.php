<?php
 
namespace Contato\Controller\Plugin;
 
use Zend\Mvc\Controller\Plugin\AbstractPlugin;
 
use Zend\Cache\Storage\Adapter\Filesystem as CacheFilesystem;
 
class Cache extends AbstractPlugin
{
 
    public $cache;
 
    public function __construct(CacheFilesystem $cache)
    {
        if (!is_null($cache)) {
            $this->cache = $cache;
        }
    }
 
    public function __invoke()
    {
        return $this->cache;
    }
 
}