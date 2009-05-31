<?php
require_once('lanceur_spip.php');

class Test_balise_foreach extends SpipTest{
	
	
	function testPresenceBaliseConfig(){
		$this->assertOkCode('[(#CONFIG|oui)ok]');
	}
	function testForeachConfigSimple(){
		$this->assertOkCode('[(#FOREACH{CONFIG}|oui)ok]');
		$this->assertOkCode("[(#FOREACH{CONFIG,''}|oui)ok]");
	}
	function testForeachConfigUlLi(){
		$this->assertOkCode('[(#FOREACH{CONFIG,foreach_test_ul_li}|oui)ok]');
	}
	function testForeachConfigAvecParams(){
		// pas array
		$this->assertOkCode("[(#FOREACH{CONFIG,'',version_installee}|non)ok]");
		// array
		$this->assertOkCode("[(#FOREACH{CONFIG,'',plugin}|oui)ok]");
	}	
}


?>
