<?php
//公共通用函数库

/**
 * 检查用户名的类型
 * @param  string $name 用户名，可以是手机号，用户名，邮箱
 * @return string  username,email,mobile
 */
function typeOfUsername($name){
    if (email_check($name)) {
        return "email";
    }
    if (CheckMobileNum($name)) {
        return "mobile";
    }
    return "username";
}

/**
 * 检查手机号是否合法
 * @param string $number 输入的手机号
 * @return bool 
 */
function CheckMobileNum($number){
    return preg_match('/^1[3458][0-9]{9}$/', $number);
}



function get_extension($file){
    return pathinfo($file, PATHINFO_EXTENSION);
}


function deldir($dir) {
  //先删除目录下的文件：
  $dh=opendir($dir);
  while ($file=readdir($dh)) {
    if($file!="." && $file!="..") {
      $fullpath=$dir."/".$file;
      if(!is_dir($fullpath)) {
          unlink($fullpath);
      } else {
          deldir($fullpath);
      }
    }
  }
  closedir($dh);
  //删除当前文件夹：
  if(rmdir($dir)) {
    return true;
  } else {
    return false;
  }
}


function recurse_copy($src,$dst) {  // 原目录，复制到的目录

    $dir = opendir($src);
    !file_exists($dst)&&mkdir($dst);
    while(false !== ( $file = readdir($dir)) ) {
        if (( $file != '.' ) && ( $file != '..' )) {
            if ( is_dir($src . '/' . $file) ) {
                recurse_copy($src . '/' . $file,$dst . '/' . $file);
            }
            else {
                copy($src . '/' . $file,$dst . '/' . $file);
            }
        }
    }
    closedir($dir);
}
function tmall2mob($url){
    if (empty($url)) {
        return false;
    }
    $a= parse_url($url);
    // print_r($a);
    // return false;
    if ($a['host']=="detail.tmall.com"&&$a['path']=='/item.htm') {
        // $query=$a['query'];
        parse_str($a['query'],$query);
        // print_r($query);
        $id=$query['id'];
        $new_url="http://a.m.tmall.com/i".$id.".htm";
        return $new_url;
    }elseif($a['host']=="item.taobao.com"&&$a['path']=='/item.htm'){
        parse_str($a['query'],$query);
        // print_r($query);
        $id=$query['id'];
        $new_url="http://a.m.taobao.com/i".$id.".htm";
        return $new_url;
    }else{
        return $url;
    }
}

function is_mobile(){
    $ua=parse_user_agent();
    $device=$ua['platform'];
    $mobile_device=array("Android",'iPod',"iPhone","BlackBerry","Windows Phone");
    if (in_array($device, $mobile_device)) {
        return true;
    }else{
        return false;
    }
}

function sms_generate_sig($params, $secret) {
    ksort($params);
    $sig = '';
    foreach($params as $key=>$value) {
        $sig .= "$key=$value";
    }
    $sig .= $secret;
    return md5($sig);
}
function sendSms($phone,$sms){
    // return json_encode(array("status"=>1,"info"=>"send success","data"=>"短信发送成功"));
    $apikey='shashi-inc';
    $secret='UGFUW8hweit4inoshuasg9h30h4HIO9u4t3hgicj';
    $params=array("apikey"=>$apikey,"phonenum"=>$phone,"sms"=>$sms);
    $sign=sms_generate_sig($params, $secret);
    $params['sign']=$sign;
    $ch=curl_init();
    curl_setopt_array(
    $ch,
    array(
      CURLOPT_URL=>'http://2.mosait.sinaapp.com/api.php',
      CURLOPT_RETURNTRANSFER=>true,
      CURLOPT_POST=>true,
      CURLOPT_POSTFIELDS=>http_build_query($params)
    )
  );
  $content=curl_exec($ch);
  if(curl_errno($ch)){
    return json_encode(array("status"=>0,"info"=>curl_error($ch),"data"=>"短信发送失败，请过段时间再试"));
  }else{
    curl_close($ch);
    return $content; 
  }
}

function GetDomain($url){
    if (empty($url)) {
        return 'unknown';
    }
    $a= parse_url($url);
    return $a['host'];
}

function GetIPLocation($ip){
    $info = chunzhen_getIPLocation($ip);
    $data['Country']=iconv("gb2312", "utf-8", $info['Country']);
    $data['Area']=iconv("gb2312", "utf-8", $info['Area']);
    return $data;
}

function qrcode($chl,$widhtHeight ='200',$EC_level='L',$margin='0') 
{ 
    $chl = urlencode($chl); 
    return 'http://chart.apis.google.com/chart?chs='.$widhtHeight.'x'.$widhtHeight.'&cht=qr&chld='.$EC_level.'|'.$margin.'&chl='.$chl;
} 
function url_check($url){
    if (preg_match('/^(http|https|ftp):\/\/([A-Z0-9][A-Z0-9_-]*(?:\.[A-Z0-9][A-Z0-9_-]*)+):?(\d+)?\/?/i', $url)) {
        return true;
    }else{
        return false;
    }
    
}

function checkshorturl($backfix){
     if (preg_match('#^([a-zA-Z0-9]+)$#', $backfix)) {
        return true;
    }else{
        return false;
    }   
}

function urlsafe_base64_encode($str){
    $find = array("+","/");
    $replace = array("-", "_");
    return str_replace($find, $replace, base64_encode($str));
}
function urlsafe_base64_decode($str){
    $find = array("-","_");
    $replace = array("+", "/");
    return str_replace($find, $replace, base64_decode($str));
}
function authcode($string,$op){
    if ($op=="ENCODE") {
        $real=realauthcode($string,"ENCODE");
        return urlsafe_base64_encode($real);
    }elseif ($op=="DECODE") {
        $string=urlsafe_base64_decode($string);
        $reald=realauthcode($string,"DECODE");
        return $reald;
    }
}
/**
 * @param string $string: 输入的需要加密（或解密）的明文（或密文）
 * @param string $operation: 'DECODE'或其它，其中默认表示解密，输入其它表示加密
 * @param string $key: 加解密密钥
 * @param int $expiry: 有效期
 */
function realauthcode($string, $operation = 'DECODE', $key = '', $expiry = 0) {
    global $authkey;
    $ckey_length = 4;
    $key = md5($key != '' ? $key : "fahoiabgoahgiagiahighaioh678");
    $keya = md5(substr($key, 0, 16));
    $keyb = md5(substr($key, 16, 16));
    $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';

    $cryptkey = $keya.md5($keya.$keyc);
    $key_length = strlen($cryptkey);

    $string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
    $string_length = strlen($string);

    $result = '';
    $box = range(0, 255);

    $rndkey = array();
    for($i = 0; $i <= 255; $i++) {
        $rndkey[$i] = ord($cryptkey[$i % $key_length]);
    }

    for($j = $i = 0; $i < 256; $i++) {
        $j = ($j + $box[$i] + $rndkey[$i]) % 256;
        $tmp = $box[$i];
        $box[$i] = $box[$j];
        $box[$j] = $tmp;
    }

    for($a = $j = $i = 0; $i < $string_length; $i++) {
        $a = ($a + 1) % 256;
        $j = ($j + $box[$a]) % 256;
        $tmp = $box[$a];
        $box[$a] = $box[$j];
        $box[$j] = $tmp;
        $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
    }

    if($operation == 'DECODE') {
        if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
            return substr($result, 26);
        } else {
            return '';
        }
    } else {
        return $keyc.str_replace('=', '', base64_encode($result));
    }

}

function parse_user_agent( $u_agent = null ) { 
    if(is_null($u_agent) && isset($_SERVER['HTTP_USER_AGENT'])) $u_agent = $_SERVER['HTTP_USER_AGENT'];

    $data = array(
        'platform' => null,
        'browser'  => null,
        'version'  => null,
    );
    
    if(!$u_agent) return $data;
    
    if( preg_match('/\((.*?)\)/im', $u_agent, $regs) ) {

        preg_match_all('/(?P<platform>Android|CrOS|iPod|iPhone|iPad|Linux|Macintosh|Windows(\ Phone\ OS)?|Silk|linux-gnu|BlackBerry|Nintendo\ (WiiU?|3DS)|Xbox)
            (?:\ [^;]*)?
            (?:;|$)/imx', $regs[1], $result, PREG_PATTERN_ORDER);

        $priority = array('Android', 'Xbox');
        $result['platform'] = array_unique($result['platform']);
        if( count($result['platform']) > 1 ) {
            if( $keys = array_intersect($priority, $result['platform']) ) {
                $data['platform'] = reset($keys);
            }else{
                $data['platform'] = $result['platform'][0];
            }
        }elseif(isset($result['platform'][0])){
            $data['platform'] = $result['platform'][0];
        }
    }

    if( $data['platform'] == 'linux-gnu' ) { $data['platform'] = 'Linux'; }
    if( $data['platform'] == 'CrOS' ) { $data['platform'] = 'Chrome OS'; }

    preg_match_all('%(?P<browser>Camino|Kindle(\ Fire\ Build)?|Firefox|Safari|MSIE|AppleWebKit|Chrome|IEMobile|Opera|Silk|Lynx|Version|Wget|curl|NintendoBrowser|PLAYSTATION\ \d+)
            (?:;?)
            (?:(?:[/ ])(?P<version>[0-9A-Z.]+)|/(?:[A-Z]*))%x', 
    $u_agent, $result, PREG_PATTERN_ORDER);

    $key = 0;

    $data['browser'] = $result['browser'][0];
    $data['version'] = $result['version'][0];

    if( ($key = array_search( 'Kindle Fire Build', $result['browser'] )) !== false || ($key = array_search( 'Silk', $result['browser'] )) !== false ) {
        $data['browser']  = $result['browser'][$key] == 'Silk' ? 'Silk' : 'Kindle';
        $data['platform'] = 'Kindle Fire';
        if( !($data['version'] = $result['version'][$key]) || !is_numeric($data['version'][0]) ) {
            $data['version'] = $result['version'][array_search( 'Version', $result['browser'] )];
        }
    }elseif( ($key = array_search( 'NintendoBrowser', $result['browser'] )) !== false || $data['platform'] == 'Nintendo 3DS' ) {
        $data['browser']  = 'NintendoBrowser';
        $data['version']  = $result['version'][$key];
    }elseif( ($key = array_search( 'Kindle', $result['browser'] )) !== false ) {
        $data['browser']  = $result['browser'][$key];
        $data['platform'] = 'Kindle';
        $data['version']  = $result['version'][$key];
    }elseif( $result['browser'][0] == 'AppleWebKit' ) {
        if( ( $data['platform'] == 'Android' && !($key = 0) ) || $key = array_search( 'Chrome', $result['browser'] ) ) {
            $data['browser'] = 'Chrome';
            if( ($vkey = array_search( 'Version', $result['browser'] )) !== false ) { $key = $vkey; }
        }elseif( $data['platform'] == 'BlackBerry' ) {
            $data['browser'] = 'BlackBerry Browser';
            if( ($vkey = array_search( 'Version', $result['browser'] )) !== false ) { $key = $vkey; }
        }elseif( $key = array_search( 'Safari', $result['browser'] ) ) {
            $data['browser'] = 'Safari';
            if( ($vkey = array_search( 'Version', $result['browser'] )) !== false ) { $key = $vkey; }
        }
        
        $data['version'] = $result['version'][$key];
    }elseif( ($key = array_search( 'Opera', $result['browser'] )) !== false ) {
        $data['browser'] = $result['browser'][$key];
        $data['version'] = $result['version'][$key];
        if( ($key = array_search( 'Version', $result['browser'] )) !== false ) { $data['version'] = $result['version'][$key]; }
    }elseif( $result['browser'][0] == 'MSIE' ){
        if( $key = array_search( 'IEMobile', $result['browser'] ) ) {
            $data['browser'] = 'IEMobile';
        }else{
            $data['browser'] = 'MSIE';
            $key = 0;
        }
        $data['version'] = $result['version'][$key];
    }elseif( $key = array_search( 'PLAYSTATION 3', $result['browser'] ) !== false ) {
        $data['platform'] = 'PLAYSTATION 3';
        $data['browser']  = 'NetFront';
    }

    return $data;

}
//十进制转到其他制
function dec2any( $num, $base=62, $index=false ) {
    if (! $base ) {
        $base = strlen( $index );
    } else if (! $index ) {
        $index = substr( "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ" ,0 ,$base );
    }
    $out = "";
    for ( $t = floor( log10( $num ) / log10( $base ) ); $t >= 0; $t-- ) {
        $a = floor( $num / pow( $base, $t ) );
        $out = $out . substr( $index, $a, 1 );
        $num = $num - ( $a * pow( $base, $t ) );
    }
    return $out;
}
//从任意进制转换到10进制
function any2dec( $num, $base=62, $index=false ) {
    if (! $base ) {
        $base = strlen( $index );
    } else if (! $index ) {
        $index = substr( "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ", 0, $base );
    }
    $out = 0;
    $len = strlen( $num ) - 1;
    for ( $t = 0; $t <= $len; $t++ ) {
        $out = $out + strpos( $index, substr( $num, $t, 1 ) ) * pow( $base, $len - $t );
    }
    return $out;
}
/** 
 * 生成唯一的id
 *
 * @author     zhiping.yin@me.com
 * @param      string an optional prefix
 * @return     string the formatted uuid
 */
 function uuid($prefix = '')
 {
     $chars = md5(uniqid(mt_rand(), true));
     $uuid = substr($chars,8,8) . '-';
     $uuid .= substr($chars,24,8) . '-';
     $uuid .= substr($chars,0,8) . '-';
     $uuid .= substr($chars,16,8);

     return $prefix . strtoupper($uuid);
 } 

/*
+------------------------------------------------------
*显示人性化的时间
+------------------------------------------------------
* @param time  int   unix timestamp
* @return string  eg. 2011-06-12 3:22:50 PM
* date("Y-m-d h:i:s A",$time);
+------------------------------------------------------
*/

function rxhtime($time){
    $now=time();//获取当前时间
    $x=$now-$time;//当前时间与传入值的时间差
    $r='未知火星时间';
    if($x>=0&&$x<=86400){
        //时间差小于1天(24小时)
        if($x<=60)$r=$x.'秒前';
        elseif($x>60&&$x<=3600)$r=floor($x/60).'分'.($x%60).'秒前';
        else $r=floor($x/3600).'小时'.floor(($x%3600)/60).'分'.(($x%3600)%60).'秒前';
        
    }elseif($x>86400&&$x<=172800){
        //大于24小时小于48小时
        $ts=$now%86400;//今天现在的秒数
        if(($x-86400)<=$ts)$r='昨天'.date("h:i:s A",$time);
        else $r='前天'.date("h:i:s A",$time);
    }elseif($x>172800){
        //时间差大于48个小时
        $r=date("Y-m-d h:i:s A",$time);
    }elseif($x<0&&$x>=-86400){
        if($x>=-60)$r=(-$x).'秒后';
        elseif($x<-60&&$x>=-3600)$r=ceil((-$x)/60).'分'.((-$x)%60).'秒后';
        else $r=ceil((-$x)/3600).'小时'.ceil(((-$x)%3600)/60).'分'.(((-$x)%3600)%60).'秒后';
    }elseif($x<-86400&&$x>=-172800){
        $ts=$now%86400;//今天现在的秒数
        if(($x+86400)>=-$ts)$r='后天'.date("h:i:s A",$time);
        else $r='明天'.date("h:i:s A",$time);
    }else {
        $r=date("Y-m-d h:i:s A",$time);
    }
    
    return $r;
    
}

/**
 +------------------------------------------------------------------------------
 * Think扩展函数库 需要手动加载后调用或者放入项目函数库
 +------------------------------------------------------------------------------
 * @category   Think
 * @package  Common
 * @author   liu21st <liu21st@gmail.com>
 * @version  $Id: extend.php 2436 2011-12-18 05:08:21Z liu21st $
 +------------------------------------------------------------------------------
 */

// 获取客户端IP地址
function get_client_ip() {
    static $ip = NULL;
    if ($ip !== NULL) return $ip;
    if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        $pos =  array_search('unknown',$arr);
        if(false !== $pos) unset($arr[$pos]);
        $ip   =  trim($arr[0]);
    }elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    }elseif (isset($_SERVER['REMOTE_ADDR'])) {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    // IP地址合法验证
    $ip = (false !== ip2long($ip)) ? $ip : '0.0.0.0';
    return $ip;
}

/**
 +----------------------------------------------------------
 * 字符串截取，支持中文和其他编码
 +----------------------------------------------------------
 * @static
 * @access public
 +----------------------------------------------------------
 * @param string $str 需要转换的字符串
 * @param string $start 开始位置
 * @param string $length 截取长度
 * @param string $charset 编码格式
 * @param string $suffix 截断显示字符
 +----------------------------------------------------------
 * @return string
 +----------------------------------------------------------
 */
function msubstr($str, $start, $length, $charset="utf-8", $suffix=true)
{
    if(function_exists("mb_substr")){
        $slice = mb_substr($str, $start, $length, $charset);
    }elseif(function_exists('iconv_substr')) {
        $slice = iconv_substr($str,$start,$length,$charset);
        if(false === $slice) {
            $slice = '';
        }
    }else{
        $re['utf-8']   = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
        $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
        $re['gbk']    = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
        $re['big5']   = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
        preg_match_all($re[$charset], $str, $match);
        $slice = join("",array_slice($match[0], $start, $length));
    }
    return $suffix ? $slice.'...' : $slice;
}

/**
 +----------------------------------------------------------
 * 产生随机字串，可用来自动生成密码 默认长度6位 字母和数字混合
 +----------------------------------------------------------
 * @param string $len 长度
 * @param string $type 字串类型
 * 0 字母 1 数字 其它 混合
 * @param string $addChars 额外字符
 +----------------------------------------------------------
 * @return string
 +----------------------------------------------------------
 */
function rand_string($len=6,$type='',$addChars='') {
    $str ='';
    switch($type) {
        case 0:
            $chars='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'.$addChars;
            break;
        case 1:
            $chars= str_repeat('0123456789',3);
            break;
        case 2:
            $chars='ABCDEFGHIJKLMNOPQRSTUVWXYZ'.$addChars;
            break;
        case 3:
            $chars='abcdefghijklmnopqrstuvwxyz'.$addChars;
            break;
        case 4:
            $chars = "们以我到他会作时要动国产的一是工就年阶义发成部民可出能方进在了不和有大这主中人上为来分生对于学下级地个用同行面说种过命度革而多子后自社加小机也经力线本电高量长党得实家定深法表着水理化争现所二起政三好十战无农使性前等反体合斗路图把结第里正新开论之物从当两些还天资事队批点育重其思与间内去因件日利相由压员气业代全组数果期导平各基或月毛然如应形想制心样干都向变关问比展那它最及外没看治提五解系林者米群头意只明四道马认次文通但条较克又公孔领军流入接席位情运器并飞原油放立题质指建区验活众很教决特此常石强极土少已根共直团统式转别造切九你取西持总料连任志观调七么山程百报更见必真保热委手改管处己将修支识病象几先老光专什六型具示复安带每东增则完风回南广劳轮科北打积车计给节做务被整联步类集号列温装即毫知轴研单色坚据速防史拉世设达尔场织历花受求传口断况采精金界品判参层止边清至万确究书术状厂须离再目海交权且儿青才证低越际八试规斯近注办布门铁需走议县兵固除般引齿千胜细影济白格效置推空配刀叶率述今选养德话查差半敌始片施响收华觉备名红续均药标记难存测士身紧液派准斤角降维板许破述技消底床田势端感往神便贺村构照容非搞亚磨族火段算适讲按值美态黄易彪服早班麦削信排台声该击素张密害侯草何树肥继右属市严径螺检左页抗苏显苦英快称坏移约巴材省黑武培著河帝仅针怎植京助升王眼她抓含苗副杂普谈围食射源例致酸旧却充足短划剂宣环落首尺波承粉践府鱼随考刻靠够满夫失包住促枝局菌杆周护岩师举曲春元超负砂封换太模贫减阳扬江析亩木言球朝医校古呢稻宋听唯输滑站另卫字鼓刚写刘微略范供阿块某功套友限项余倒卷创律雨让骨远帮初皮播优占死毒圈伟季训控激找叫云互跟裂粮粒母练塞钢顶策双留误础吸阻故寸盾晚丝女散焊功株亲院冷彻弹错散商视艺灭版烈零室轻血倍缺厘泵察绝富城冲喷壤简否柱李望盘磁雄似困巩益洲脱投送奴侧润盖挥距触星松送获兴独官混纪依未突架宽冬章湿偏纹吃执阀矿寨责熟稳夺硬价努翻奇甲预职评读背协损棉侵灰虽矛厚罗泥辟告卵箱掌氧恩爱停曾溶营终纲孟钱待尽俄缩沙退陈讨奋械载胞幼哪剥迫旋征槽倒握担仍呀鲜吧卡粗介钻逐弱脚怕盐末阴丰雾冠丙街莱贝辐肠付吉渗瑞惊顿挤秒悬姆烂森糖圣凹陶词迟蚕亿矩康遵牧遭幅园腔订香肉弟屋敏恢忘编印蜂急拿扩伤飞露核缘游振操央伍域甚迅辉异序免纸夜乡久隶缸夹念兰映沟乙吗儒杀汽磷艰晶插埃燃欢铁补咱芽永瓦倾阵碳演威附牙芽永瓦斜灌欧献顺猪洋腐请透司危括脉宜笑若尾束壮暴企菜穗楚汉愈绿拖牛份染既秋遍锻玉夏疗尖殖井费州访吹荣铜沿替滚客召旱悟刺脑措贯藏敢令隙炉壳硫煤迎铸粘探临薄旬善福纵择礼愿伏残雷延烟句纯渐耕跑泽慢栽鲁赤繁境潮横掉锥希池败船假亮谓托伙哲怀割摆贡呈劲财仪沉炼麻罪祖息车穿货销齐鼠抽画饲龙库守筑房歌寒喜哥洗蚀废纳腹乎录镜妇恶脂庄擦险赞钟摇典柄辩竹谷卖乱虚桥奥伯赶垂途额壁网截野遗静谋弄挂课镇妄盛耐援扎虑键归符庆聚绕摩忙舞遇索顾胶羊湖钉仁音迹碎伸灯避泛亡答勇频皇柳哈揭甘诺概宪浓岛袭谁洪谢炮浇斑讯懂灵蛋闭孩释乳巨徒私银伊景坦累匀霉杜乐勒隔弯绩招绍胡呼痛峰零柴簧午跳居尚丁秦稍追梁折耗碱殊岗挖氏刃剧堆赫荷胸衡勤膜篇登驻案刊秧缓凸役剪川雪链渔啦脸户洛孢勃盟买杨宗焦赛旗滤硅炭股坐蒸凝竟陷枪黎救冒暗洞犯筒您宋弧爆谬涂味津臂障褐陆啊健尊豆拔莫抵桑坡缝警挑污冰柬嘴啥饭塑寄赵喊垫丹渡耳刨虎笔稀昆浪萨茶滴浅拥穴覆伦娘吨浸袖珠雌妈紫戏塔锤震岁貌洁剖牢锋疑霸闪埔猛诉刷狠忽灾闹乔唐漏闻沈熔氯荒茎男凡抢像浆旁玻亦忠唱蒙予纷捕锁尤乘乌智淡允叛畜俘摸锈扫毕璃宝芯爷鉴秘净蒋钙肩腾枯抛轨堂拌爸循诱祝励肯酒绳穷塘燥泡袋朗喂铝软渠颗惯贸粪综墙趋彼届墨碍启逆卸航衣孙龄岭骗休借".$addChars;
            break;
        default :
            // 默认去掉了容易混淆的字符oOLl和数字01，要添加请使用addChars参数
            $chars='ABCDEFGHIJKMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789'.$addChars;
            break;
    }
    if($len>10 ) {//位数过长重复字符串一定次数
        $chars= $type==1? str_repeat($chars,$len) : str_repeat($chars,5);
    }
    if($type!=4) {
        $chars   =   str_shuffle($chars);
        $str     =   substr($chars,0,$len);
    }else{
        // 中文随机字
        for($i=0;$i<$len;$i++){
          $str.= msubstr($chars, floor(mt_rand(0,mb_strlen($chars,'utf-8')-1)),1);
        }
    }
    return $str;
}

/**
 +----------------------------------------------------------
 * 字节格式化 把字节数格式为 B K M G T 描述的大小
 +----------------------------------------------------------
 * @return string
 +----------------------------------------------------------
 */
function byte_format($size, $dec=2)
{
    $a = array("B", "KB", "MB", "GB", "TB", "PB");
    $pos = 0;
    while ($size >= 1024) {
         $size /= 1024;
           $pos++;
    }
    return round($size,$dec)." ".$a[$pos];
}


/**
 +----------------------------------------------------------
 * 检查字符串是否是UTF8编码
 +----------------------------------------------------------
 * @param string $string 字符串
 +----------------------------------------------------------
 * @return Boolean
 +----------------------------------------------------------
 */
function is_utf8($string)
{
    return preg_match('%^(?:
         [\x09\x0A\x0D\x20-\x7E]            # ASCII
       | [\xC2-\xDF][\x80-\xBF]             # non-overlong 2-byte
       |  \xE0[\xA0-\xBF][\x80-\xBF]        # excluding overlongs
       | [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}  # straight 3-byte
       |  \xED[\x80-\x9F][\x80-\xBF]        # excluding surrogates
       |  \xF0[\x90-\xBF][\x80-\xBF]{2}     # planes 1-3
       | [\xF1-\xF3][\x80-\xBF]{3}          # planes 4-15
       |  \xF4[\x80-\x8F][\x80-\xBF]{2}     # plane 16
   )*$%xs', $string);
}

function email_check($email){
    return filter_var($email,FILTER_VALIDATE_EMAIL);
}

function curlget($url){
    $curl = curl_init();  
    curl_setopt($curl, CURLOPT_URL, $url);  
    curl_setopt($curl, CURLOPT_HEADER, false);  
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 0);
    $values = curl_exec($curl);  
    curl_close($curl);  
    return $values;  
} 



?>