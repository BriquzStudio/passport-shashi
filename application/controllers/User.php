<?php
class UserController extends Base_Controller {
	
   /**
    * 初始化函数
    * @return void
    * @uses  检查登陆情况，权限等
    */
   public function init() {//初始化函数
        if ($this->getRequest()->isXmlHttpRequest()) {
            //如果是Ajax请求, 关闭自动渲染, 由我们手工返回Json响应
            Yaf_Dispatcher::getInstance()->disableView();
        }
        //验证码安全检查
        safe::commonCheck();
        if (safe::checkIfVerifyCodeNeeded()) {
          $this->assign("verify",1);
        }else{
          $this->assign("verify",0);
        }
   }
   
   /**
    * 默认action
    * @return void
    */
   public function indexAction() {//默认Action
      Yaf_Dispatcher::getInstance()->disableView();
      header("http://passport.shashi-inc.com/user/login");
   		 
   }

   /**
    * 登陆
    * @return void
    */
   public function loginAction(){

   }

   /**
    * 注册
    * @return void
    */
   public function registerAction(){
    //检查输入，检查ip，检查验证码
    //自动判断输入内容类型
    //同一个ip短时间内注册过多显示验证码
    //登陆次数过多显示验证码
    if (isset($_POST['submit'])) {
      if (empty($_POST['name'])||empty($_POST['password'])) {
        $this->ajax(0,"some field empty","您有字段没填");
      }
      $type=typeOfUsername($_POST['name']);
      if ($type=='username'&&!preg_match("/^[a-zA-Z0-9\x{4e00}-\x{9fa5}]+$/u",$_POST['name'])) {
        $this->ajax(0,"illegal username","用户名只能包含字母,数字,汉字哦");
      }
      $data[$type]=$_POST['name'];
      $data['salt']=rand_string(12);
      $data['password']=md5($_POST['password'].$data['salt']);
      $data['status']=1;
      $data['register_time']=time();
      $data['register_ip']=get_client_ip();
      $result=UserModel::getInstance()->register($data);
      $last_query=UserModel::getInstance()->last_query();
      if ($result) {
        safe::addARegisterCountOfIp();
        $this->ajax(1,$last_query,"注册成功");
      }else{
        $this->ajax(0, $last_query,"注册失败,用户名已被他人占用,请刷新页面重试");
      }

    }

   }

   public function testAction(){
    // session_destroy();
    // var_dump($_SESSION);
    // $a=UserModel::getInstance()->getInfo();
    // var_dump($a);
    // echo typeOfUsername("344511@qq");
    $key="regipcount_".get_client_ip();
    echo "cache-key:".$key;
    echo "<br>";
    echo "value:".CacheModel::getInstance()->get($key);
    if (isset($_GET['del'])) {
      CacheModel::getInstance()->del($key);
      echo "<br>";
      echo "count deleted,please refresh";
    }
    // echo strtotime(date('Y-m-d',strtotime('+1 day')))-time();
    die();
   }
   
   
}
?>