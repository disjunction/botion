<?php
namespace Botion\Arms;

class Reporter
{
    /**
     * @var Zend_Config
     */
    public $config;
    
    protected $_f;
    
    protected $_lastMessage = '';
    
    /**
     * @PdInject config
     * @param Zend_Config $config
     */
    public function __construct($config)
    {
        $this->config = $config;
        $this->_f = fopen($this->config->reporter->file, 'w');
    }
    
    public static function _()
    {
        static $o;
        !$o and $o = \Pd_Make('\Botion\Arms\Reporter');
        return $o;
    }
    
    protected function _format($something)
    {
        if ($this->config->reporter->format == 'festival') {
            return '(SayText "' . $something . '")' . "\n";
        } else {
            return $something . "\n";
        }
    }
    
    public function say($something)
    {
        if ($something == $this->_lastMessage) {
            $this->_lastMessage = $something;
            $something = 'and again...';
        } else {
            $this->_lastMessage = $something;
        }
        fputs($this->_f, $this->_format($something));
        echo $something, "\n";
    }
}