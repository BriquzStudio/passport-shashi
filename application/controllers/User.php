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
    * 管理员登陆
    * @return void 
    */
   public function adminloginAction(){
      $_SESSION['url']=$_GET['returnurl']?$_GET['returnurl']:"";
      $qrcodeurl=qrcode(json_encode(array("shashi-client-qr"=>session_id())));    
      $this->assign("url",$qrcodeurl);
   }


   /**
    * 管理员跳转
    * @return void 
    */
   public function adminjumpAction(){
      Yaf_Dispatcher::getInstance()->disableView();
      if (isset($_SESSION['is_login'])) {

      }else{

        header('Refresh: 3; url=http://passport.shashi-inc.com/user/adminlogin');
        echo "没有登陆，3秒后跳转至登陆页面";
        // header("Location:http://passport.shashi-inc.com/user/adminlogin");
        die();
      }
      if(!empty($_SESSION['url'])) {
        $url=urldecode($_SESSION['url']);
        $time=time();
        $sign=md5($url.$time.$_SESSION['username']."gwibig8");
        $jumpurl=$url."?sign=".$sign."&username=".urlencode($_SESSION['username'])."&time=".$time;
        session_destroy();
        header('Location:'.$jumpurl);
      }else{
        //没有指定跳转到哪个App  
        session_destroy();   
        echo "没有指定登陆到哪，请返回网站重新发起登陆请求";
        //   
      }

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


  public function generateToken(){
     $hour=intval(date("H"));
     $minute = intval(date("i"));
     $day=intval(date("j"));
     $month=intval(date("n"));
     $year=intval(date("Y"));
     $tokenkey1=3;//需要小于4 *(hour+4)
     $tokenkey2=17;//需要小于等于39 +minute
     $tokenkey3=74;//third
     $tokenkey4=7;
    
    
     $gtoken1=($minute+$tokenkey2)%8;//数据范围 0-7
     $gtoken2=(($hour+4)*$tokenkey1)%($gtoken1+2);//数据范围  0-8
     $gtoken3=($gtoken1+$gtoken2+1)%9+1;//数据范围  2-9

     $altoken1=$tokenkey3-($gtoken1*$tokenkey1*3);//数据范围   11-74
     $altoken2=($gtoken2+$tokenkey2+$minute)-$tokenkey4;//10-99
     $altoken3=($hour+$minute)+$tokenkey4+3;//10-94
    
     $token=$altoken1*10000+$altoken2*100+$altoken3+($day*10)+($month*17)+($year*5);
     return $token;
  }


  public function trustlogintokengenerate(){

      $secret= "FYK8Gh3gF6dY";//12位定常密钥
      $current= date("YmdHi");//年月日时分e.g 201311231929
      $second=date("s");
      if (intval($second)>=30) {
        $time=$current."2";
        $prev=$current."1";
      }else{
        $time=$current."1";
        $prev=(intval($current)-1)."2";
      }
      $md5=md5($time.$secret);
      $md52=md5($prev.$secret);
      $nummd5=preg_replace("/[a-z]/i","",$md5);
      $nummd52=preg_replace("/[a-z]/i","",$md52);
      if (strlen($nummd5)<6) {
        $result="000000";
      }
      else{
        $temp=substr($nummd5, 0,6);
        if (intval($temp)*7<100000) {
          $result= "999999";
        }else{
          $result=substr(intval($temp)*7,0,6);
        }
        
      }
      if (strlen($nummd52)<6) {
        $result="000000";
      }
      else{
        $temp=substr($nummd52, 0,6);
        if (intval($temp*7<100000)) {
          $result2 = "999999";
        }else{
          $result2=substr((intval($temp))*7,0,6);
        }
        
      }
      $result=sprintf("%s",$result);
      $prev_result=sprintf("%s",$result2);
      return array($result,$prev_result);
  }

  public function trustloginAction(){
    Yaf_Dispatcher::getInstance()->disableView();
    header("Content-type: application/json");
    //需要的参数：username,token
    $tokens=$this->trustlogintokengenerate();
    // var_dump($tokens);
    if (!isset($_GET['username'])||!isset($_GET['token'])||!isset($_GET['ssid'])) {
      $this->ajax(0,"没有传入必要参数","lack of necessary parameter");
    }
    if (in_array($_GET['token'], $tokens)) {
      //尚未设置验证用户
      session_id($_GET['ssid']);
      session_start();
      //
      $_SESSION['is_login']=1;
      $_SESSION['username']=$_GET['username'];

      $this->ajax(1,"登陆成功","login success");

      
    }else{
      $this->ajax(0,"动态口令不对","token is wrong");
    }


  }


  public function isloginAction(){
      Yaf_Dispatcher::getInstance()->disableView();
      if (isset($_SESSION['is_login'])) {
        $this->ajax(1,"login","已经登陆");
      }else{
        $this->ajax(0,"not login","尚未登陆");
      }
  }

   public function testAction(){
    // session_destroy();
    var_dump($_SESSION);
    // $a=UserModel::getInstance()->getInfo();
    // var_dump($a);
    // echo typeOfUsername("344511@qq");
    // $key="regipcount_".get_client_ip();
    // echo "cache-key:".$key;
    // echo "<br>";
    // echo "value:".CacheModel::getInstance()->get($key);
    // if (isset($_GET['del'])) {
    //   CacheModel::getInstance()->del($key);
    //   echo "<br>";
    //   echo "count deleted,please refresh";
    // }
    // echo strtotime(date('Y-m-d',strtotime('+1 day')))-time();
    die();
   }
   
   
}
?>