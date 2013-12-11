<?php

class UserModel {
	
	public static $instance;
	
	private $db_link;

	public $err_msg;

	private $_fields=array(
			"uid"=>'',
			"username"=>"",
			"email"=>"",
			"mobile"=>"",
			"status"=>1,
			"password"=>'',
			"salt"=>'',
			"register_time"=>0,
			"register_ip"=>""
		);



    
    /**
     * 渲染数据表的数据
     * @access public
     * @param  array $data 用户输入的数组数据
     * @return bool       true
     */
    private function _dataRender($data){
    	foreach ($data as $key => $value) {
    		$this->_fields[$key]=$value;
    	}
    	return true;
    }

    /**
     * 注册用户
     * @access public
     * @param  array $data 传入的注册数据数组
     * @return bool       注册成功的布尔值
     */
    public function register($data){
    	$this->_dataRender($data);
    	if($this->db_link->insert("user",$this->_fields)){
    		return true;
    	}else{
    		$err=$this->db_link->error();
    		$this->err_msg=$err[2];
    		return false;
    	}
    }
	
	/**
	 * 返回最后一条sql
	 * @access public
	 * @return string 
	 */
	public function last_query(){
		return $this->db_link->last_query();
	}

	/**
	 * 构造函数
	 * @access public
	 * @param array $params 
	 */
	private function __construct($params = array()) {

        $this->db_link= $this->linkDb();

        return true;
    }

	/**
	 * 连接数据库
	 *
	 * @access public
	 * @return boolean
	 */
	public function linkDb() {
		//获取数据库连接信息
		$params = Yaf_Application::app()->getConfig()->application->medoo->toArray();
		$db = new medoo($params);
		return $db;
		// return Base_Db::getInstance($params);
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