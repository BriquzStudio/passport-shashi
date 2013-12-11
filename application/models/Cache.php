<?php

class CacheModel {
	
	public static $instance;
	
	public $mc_link;

	 public function __construct($params = array()) {

        $this->mc_link= $this->linkMc();

        return true;
    }

    public function get($key){
    	return $this->mc_link->get($key);
    }
	

	public function set($key,$value,$expire=3600){
		return $this->mc_link->add($key,$value,false,$expire)?true:$this->mc_link->set($key,$value,false,$expire);
	}

	public function del($key){
		return $this->mc_link->delete($key,0);
	}
	
	
	/**
	 * 连接数据库
	 *
	 * @access public
	 * @return boolean
	 */
	public function linkMc() {
		//获取数据库连接信息
		$params = Yaf_Application::app()->getConfig()->application->cache->toArray();
		$memcache = new Memcache;
		$memcache->connect($params['server'],$params['port']);
		return $memcache;
	}
	
		 /**
     * 单例模式
     *
     * @access public
     * @param array $params 数据库连接参数,如数据库服务器名,用户名,密码等
     * @return object
     */
    public static function getInstance() {

        if (!self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }
}
?>