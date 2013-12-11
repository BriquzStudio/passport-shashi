<?php

/**
 * 所有在Bootstrap类中, 以_init开头的方法, 都会被Yaf调用,
 * 这些方法, 都接受一个参数:Yaf_Dispatcher $dispatcher
 * 调用的次序, 和申明的次序相同
 */
class Bootstrap extends Yaf_Bootstrap_Abstract{

        public function _initConfig() {
                $config = Yaf_Application::app()->getConfig();
                Yaf_Registry::set("config", $config);
        }

        public function _initTimeZone(){
                        date_default_timezone_set('Asia/Shanghai');
        }
        
        public function _initUserController(){
                        Yaf_Loader::import("Base/Controller.php");
                        Yaf_Loader::import("Base/Fun.php");
                                        // Yaf_Loader::import("Qiniu/Cloud.php");
                                        
        }
        
        public function _initSession(){
        	//header('P3P: CP=CAO PSA OUR');
            if(!isset($_GET['ssid'])){
                Yaf_Session::getInstance()->start();
            }
        				
        }
        
        // public function _initPlugin(Yaf_Dispatcher $dispatcher) {
        //     $user = new UserPlugin();
        //     $dispatcher->registerPlugin($user);
        // }

        public function _initRoute(Yaf_Dispatcher $dispatcher) {
                $router = Yaf_Dispatcher::getInstance()->getRouter();
                /**
                 * 添加配置中的路由
                 */
                // $router->addConfig(Yaf_Registry::get("config")->routes);
        }

        
        public function _initCharset(){
        	
        				header("Content-Type:text/html; charset=utf-8");			
        }
        




}
    