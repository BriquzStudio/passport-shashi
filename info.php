<?php
if (isset($_GET['pass'])) {
	if ($_GET['pass']=="phpinfo") {
		phpinfo();
	// echo confirm_HyperMobile_compiled("xx");
	}elseif($_GET['pass']=='hm'){
		$hm=new HyperMobile;
		$a=$hm->loadjsfromstring("adbaaa");
		var_dump($a);
		var_dump($hm);
	}
}

?>