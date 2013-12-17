<?php
if (isset($_GET['pass'])) {
	if ($_GET['pass']=="phpinfo") {
		phpinfo();
	// echo confirm_HyperMobile_compiled("xx");
	}elseif($_GET['pass']=='hm'){
		header("Content-type:text/html;charset=utf-8");
		$hm=new HyperMobile;
		$a=$hm->loadjsfromstring("A string 123456789 avnmkjghh");
		// $b=$hm->getErrorMsg();
		$hm->convert();
		// var_dump($b);
		// var_dump($hm);
		echo $hm->getObjc();
	}
}

?>