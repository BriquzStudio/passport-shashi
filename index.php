<?php
define("APPLICATION_PATH",  dirname(__FILE__));
define("PUB_TPL",dirname(__FILE__)."/application/views");
$app  = new Yaf_Application(APPLICATION_PATH . "/conf/application.ini");
$app->bootstrap() //call bootstrap methods defined in Bootstrap.php
    ->run();
    
    ?>