<?php
class safe {




	/**
	 * 通用安全检查静态函数
	 * @return void 
	 */
	public static function commonCheck(){
		//检查POST过来的验证码
		$v=self::checkIfVerifyCodeNeeded();
		if ($v&&isset($_POST['submit'])) {
			$r=self::checkVerifyCode();
			if (!$r) {
				if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){
					//in ajax
					die(json_encode(array("status"=>0,"info"=>"verifycode error","data"=>"验证码不对,请刷新页面重试")));
				}else{
					die("验证码不对");
				}
			}
		}
	}

	/**
	 * 检查该ip地址是否需要验证码
	 * @param  string $ip ip地址，默认为用户ip,也可以自己指定
	 * @return bool    
	 */
	public static function checkIfVerifyCodeNeeded($ip=''){
		if ($ip=='') {
			$ip=get_client_ip();
		}
		//1.同一 ip 一天 24 小时内连续注册 10 个帐号 
		$current=CacheModel::getInstance()->get("regipcount_".$ip);
		$current = $current?$current:0;
		if ($current>=10) {
			return true;
		}
		//2.同一 ip 一天 24 小时内连续输错密码 3 次
		



		return false;
	}

	/**
 	 * 检查验证码是否正确
 	 * @param  string $input 输入的验证码内容
 	 * @return bool        
 	 */
	public static function checkVerifyCode($input=''){
	    if ($input=='') {
	        $input=$_POST['verifycode'];
	    }
	    $answer=$_SESSION['_ss_verify'];
	    unset($_SESSION['_ss_verify']);
	    if ($input==$answer) {
	        return true;
	    }else{
	        return false;
	    }
	}

	/**
	 * 增加一个ip地址的注册计数器
	 * @param string $ip 
	 */
	public static function addARegisterCountOfIp($ip=''){
		if ($ip=='') {
			$ip=get_client_ip();
		}
		//计算有效期
		$expire=strtotime(date('Y-m-d',strtotime('+1 day')))-time();//距离今天24点还剩的秒数

		$current=CacheModel::getInstance()->get("regipcount_".$ip);
		$current = $current?$current:0;
		CacheModel::getInstance()->set("regipcount_".$ip,intval($current)+1,3600*24,$expire);
		return true;

	}













}

?>
