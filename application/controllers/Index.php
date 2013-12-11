<?php
class IndexController extends Base_Controller {
	
	 public function init() {//初始化函数

   }
	
   public function indexAction() {//默认Action
      Yaf_Dispatcher::getInstance()->disableView();
      header("http://passport.shashi-inc.com/user/login");
   		 
   }

   
   
}
?>