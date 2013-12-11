<?php
class ErrorController extends Base_Controller {
	
	 public function init() {//初始化函数
            //关闭自动渲染, 由我们手工返回响应
            // Yaf_Dispatcher::getInstance()->disableView();

   }
 /**
  * 此时可通过$request->getException()获取到发生的异常
  */
    public function errorAction() {
  		$exception = $this->getRequest()->getException();
  		try {
    		throw $exception;
  		} catch (Yaf_Exception_LoadFailed $e) {
    	//加载失败
        $this->assign("template","loadfail");
        // $this->assign("failinfo",$e->getMessage());
  		} catch (Yaf_Exception $e) {
   			 
   			 $err_msg = $e->getMessage();
         if ($err_msg=='NOT FOUND') {
           $this->assign("template","notfound");
         }elseif ($err_msg=="NOT AVAILABLE 0") {
           $this->assign("template","banbyadmin");
         }elseif ($err_msg=="NOT AVAILABLE 1") {
           $this->assign("template","waitforreview");
         }elseif ($err_msg=='WRONG PASSWORD') {
           $this->assign("template","wrongpasswd");//password eroor
         }elseif ($err_msg=='PASSWORD REQUIRED') {
           $this->assign("template","requirepwd");//password requierd
         }elseif ($err_msg=='VISIT IS LIMITED') {
           $this->assign("template","limitvisit");//visit limit
         }elseif ($err_msg=='COUNT KEY NOT INPUT') {
           $this->assign("template","nokey");
         }elseif ($err_msg=='ACCOUNT CREDIT UNAVAILABLE') {
           $this->assign("template","nocredit");
         }elseif ($err_msg=='ACCOUNT BANED') {
           $this->assign("template","baned");
         }elseif ($err_msg=='ACCOUNT NOT ACTIVATED') {
           $this->assign("template","notactive");
         }elseif ($err_msg=='ORDER ALREADY TRANSACTED') {
           $this->assign("template","ordertransed");
         }

         else{
          $this->assign("msg",$err_msg);
          $this->assign("template","unknown");
         }
  		}
    }
    
    
    
    
}