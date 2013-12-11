<?php 

class Api_Exception extends Exception
 {
 public function response()
  {
    $errmsg=$this->getMessage();
    $errcode=$this->getCode();
    $re=array(
        "status"=>0,
        "info"=>"ERROR CODE:".$errcode,
        "data"=>$errmsg
        );
    return json_encode($re);
  }
 }

?>