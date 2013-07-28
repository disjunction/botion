<?php
namespace Botion\Arms;

class DriverFactory
{
    /**
    * @var \Zend_Config
    * @PdInject config
    */
    public $config;
    
    protected $_memcache;
    
    /**
     * @return Memcache
     */
    protected function _getMemcache()
    {
        if ($this->_memcache) return $this->_memcache;
    
        $config = $this->config->driver->state->memcache;
        $this->_memcache = new \Memcache();
        $this->_memcache->connect($config->host, $config->port);
        return $this->_memcache;
    }
    
    protected function _getStateData()
    {
        if ($this->config->driver->state->memcache) {
            $cache = $this->_getMemcache($this->config->driver->state->memcache);
            return $cache->get('webdriver');
        } else {
            if (file_exists($file = $this->config->driver->state->file)) {
                return file_get_contents($file);
            }
        }
    }
    
    /**
     * @return Driver
     */
    protected function _makeAndStore()
    {
        $object = \Pd_Make::name('\Botion\Arms\Driver');
        $serialized = serialize($object);
        if ($this->_memcache) {
            $this->_memcache->set('webdriver', $serialized);
        } else {
            file_put_contents($this->config->driver->state->file, $serialized);
        }
        return $object;
    }
    
    /**
     * webdriver persistence - reusing of the same window
     * @return Driver
     */
    public function getPersistent()
    {
        $objectStr = $this->_getStateData();
    
        if (!$objectStr) {
            $object = $this->_makeAndStore();
        } else {
            $object = unserialize($objectStr);
            \Pd_Make_Setter::inject($object);
    
            // make sure the browser is running
            try {
                $object->getCurrentWindowPosition();
            } catch (\Exception $e) {
                $object = $this->_makeAndStore();
            } catch (\SeleniumClient\Http\SeleniumUnknownErrorException $e) {
                $object = $this->_makeAndStore();
            }
        }
    
        return $object;
    }
}