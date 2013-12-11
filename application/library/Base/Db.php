<?php
class Base_Db {

    /**
     * 单例模式实例化对象
     *
     * @var object
     */
    private static $instance;

    /**
     * 数据库连接ID
     *
     * @var object
     */
    public $db_link;
    
    public static $db_id;//连接的数据库id号

    /**
     * 事务处理开启状态
     *
     * @var boolean
     */
    public $Transactions;


    /**
     * 构造函数
     *
     * 用于初始化运行环境,或对基本变量进行赋值
     * @access private
     * @param array $params 数据库连接参数,如主机名,数据库用户名,密码等
     * @return boolean
     */
    private function __construct($params = array()) {

        //分析数据库连接信息
        if (!$params['dsn']) {
            return false;
        }
        $this->db_id=$params['id'];

        //连接数据库
        $this->db_link = @new PDO($params['dsn'], $params['username'], $params['password'], array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));

        if (!$this->db_link) {
           trigger_error($params['driver'] . ' Server connect fail! <br/>Error Message:' . $this->error() . '<br/>Error Code:' . $this->errno(), E_USER_ERROR);
        }

        return true;
    }

    /**
     * 执行SQL语句
     *
     * SQL语句执行函数
     * @access public
     * @param string $sql SQL语句内容
     * @return mixed
     */
    public function query($sql) {

        //参数分析
        if (!$sql) {
            return false;
        }

        $result = $this->db_link->query($sql);
        

        return $result;
    }
    /**
     * 执行SQL语句
     *
     * SQL语句执行函数
     * @access public
     * @param string $sql SQL语句内容
     * @return int
     */
    public function exec($sql) {

        //参数分析
        if (!$sql) {
            return false;
        }

        $result = $this->db_link->exec($sql);
        

        return $result;
    }
    /**
     * 获取数据库错误描述信息
     *
     * @access public
     * @return string
     */
    public function error() {

        $info = $this->db_link->errorInfo();

        return $info[2];
    }

    /**
     * 获取数据库错误信息代码
     *
     * @access public
     * @return int
     */
    public function errno() {

        return $this->db_link->errorCode();
    }

    /**
     * 通过一个SQL语句获取一行信息(字段型)
     *
     * @access public
     * @param string $sql SQL语句内容
     * @return mixed
     */
    public function fetchRow($sql) {

        //参数分析
        if (!$sql) {
            return false;
        }

        $result = $this->query($sql);

        if (!$result) {
            return false;
        }

        $myrow     = $result->fetch(PDO::FETCH_ASSOC);
        $result = null;

        return $myrow;
    }

    /**
     * 通过一个SQL语句获取全部信息(字段型)
     *
     * @access public
     * @param string $sql SQL语句
     * @return array
     */
    public function getArray($sql) {

        //参数分析
        if (!$sql) {
            return false;
        }

        $result = $this->query($sql);

        if (!$result) {
            return false;
        }

        $myrow     = $result->fetchAll(PDO::FETCH_ASSOC);
        $result = null;

        return $myrow;
    }

    /**
     * 获取insert_id
     *
     * @access public
     * @return int
     */
    public function insertId() {

        return $this->db_link->lastInsertId();
    }

    /**
     * 开启事务处理
     *
     * @access public
     * @return boolean
     */
    public function startTrans() {

        if ($this->Transactions == false) {
            $this->db_link->beginTransaction();
            $this->Transactions = true;
        }

        return true;
    }

    /**
     * 提交事务处理
     *
     * @access public
     * @return boolean
     */
    public function commit() {

        if ($this->Transactions == true) {
            if ($this->db_link->commit()) {
                $this->Transactions = false;
            }
        }

        return true;
    }

    /**
     * 事务回滚
     *
     * @access public
     * @return boolean
     */
    public function rollback() {

        if ($this->Transactions == true) {
            if ($this->db_link->rollBack()) {
                $this->Transactions = false;
            }
        }
    }

    /**
     * 转义字符
     *
     * @access public
     * @param string $string 待转义的字符串
     * @return string
     */
    public function escapeString($string) {

        //参数分析
        if (!$string) {
            return  false;
        }

        return $this->db_link->quote($string);
    }

    /**
     * 析构函数
     *
     * @access public
     * @return void
     */
    public function __destruct() {

        if ($this->db_link == true) {
            $this->db_link = null;
        }
    }

    /**
     * 单例模式
     *
     * @access public
     * @param array $params 数据库连接参数,如数据库服务器名,用户名,密码等
     * @return object
     */
    public static function getInstance($params) {

        if (!self::$instance||self::$db_id!=$params['id']) {
            self::$instance = new self($params);
        }

        return self::$instance;
    }
}