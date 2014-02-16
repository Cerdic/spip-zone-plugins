<?php
require_once('lanceur_spip.php');

	
class Test_balise_val extends SpipTest{
	
	function testBaliseVal(){
		$this->assertEqualCode('','#VAL');
		$this->assertEqualCode('','#VAL{}');
		$this->assertEqualCode('',"#VAL{''}");
		$this->assertEqualCode('ok','#VAL{ok}');
		$this->assertEqualCode('1','#VAL{1}');
	}

}

class Test_balise_eval extends SpipTest{
	
	function testBaliseEval(){
		$this->assertEqualCode('','#EVAL{\'\'}');
		$this->assertEqualCode('ok','#EVAL{"ok"}');
	 	$this->assertEqualCode('1','#EVAL{1}');
		$this->assertEqualCode(_DIR_CACHE,'#EVAL{_DIR_CACHE}');
		$this->assertEqualCode(20,'#EVAL{3*5+5}');
	}
}
/* pas au point
class Test_balise_self extends SpipTest{
	function testBaliseSelf(){
		$self = $this->recuperer_code('#EVAL{$_SERVER[\'PHP_SELF\']}');
		//$self = $this->recuperer_code($this->php('echo $_SERVER[\'PHP_SELF\'];'));
		$this->assertOkCode("[(#SELF|=={'$self'}|oui)ok]");
	}
}
* */
?>
