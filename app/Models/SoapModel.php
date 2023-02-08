<?php

namespace App\Models;

use Exception;
use Illuminate\Support\Facades\Cache;
use SoapClient;

class SoapModel
{    
    /**
     * @var mixed
     */
    protected $soapLink;

    /**
     * @var array
     */
    protected $data;

    /**
     * Default is className::methodName
     * @var string
     */
    protected $cacheName;

    /**
     * Default is an hour
     * @var int
     */
    protected $cacheSeconds = 1 * 60 * 60;

    public function __construct($soapUrl = null, $soapOptions = [], $cacheName=null)
    {        
        if (!$soapUrl) {
            throw new Exception('Не указана ссылка на soap-ресур!', 500);
        }        
        $this->soapLink = new SoapClient($soapUrl, $soapOptions);
        $this->cacheName = $cacheName;
        if (!$this->cacheName) {
            $this->cacheName = static::class;
        }
    }

    /**
     * @param string $name
     * @param array $arguments
     * @return SoapModel
     */
    public function __call($name, $arguments)
    {       
        $this->data = Cache::remember($this->cacheName . '::' . $name, $this->cacheSeconds, 
            fn() => $this->soapLink->__call($name, $arguments)
        );     
        // $this->data = $this->soapLink->__call($name, $arguments);   
        return $this;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return array
     */
    public function getDataAsArray()
    {
        return json_decode(json_encode($this->data), true);
    }

    /**
     * Сортровка по значению подмассива
     * @param array $data массив
     * @param string $keyName ключ в подмассиве, 
     * по значению которого будет выполнена сортировка
     * @return array
     */
    protected function arraySortByValue(&$data, $keyName)
    {
        if (!is_array($data)) {
            return $data;
        }
        usort($data, function($a, $b) use ($keyName) {                               
            if ($a[$keyName] == $b[$keyName]) {
                return 0;
            }
            return ($a[$keyName] < $b[$keyName]) ? -1 : 1;
        });
        return $data;
    }

}