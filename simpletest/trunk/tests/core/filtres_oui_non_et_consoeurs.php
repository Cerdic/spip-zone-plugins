<?php
require_once('lanceur_spip.php');

	
class Test_filtres_oui_non_et_consoeurs extends SpipTest{
	
	function testOui(){
		$this->assertNotOkCode("[(#VAL|oui)ok]");
		$this->assertOkCode("[(#VAL{1}|oui)ok]");
		$this->assertOkCode("[(#VAL{' '}|oui)ok]");
	}
	function testYes(){
		$this->assertNotOkCode("[(#VAL|yes)ok]");
		$this->assertOkCode("[(#VAL{1}|yes)ok]");
		$this->assertOkCode("[(#VAL{' '}|yes)ok]");
	}
	
	function testNon(){
		$this->assertOkCode("[(#VAL|non)ok]");
		$this->assertNotOkCode("[(#VAL{1}|non)ok]");
		$this->assertNotOkCode("[(#VAL{' '}|non)ok]");
	}
	function testNot(){
		$this->assertOkCode("[(#VAL|not)ok]");
		$this->assertNotOkCode("[(#VAL{1}|not)ok]");
		$this->assertNotOkCode("[(#VAL{' '}|not)ok]");
	}
	
	function testEt(){
		$this->assertOkCode("[(#VAL{1}|et{#VAL{1}})ok]");
		$this->assertOkCode("[(#VAL{0}|et{#VAL{0}}|non)ok]");
		$this->assertOkCode("[(#VAL{1}|et{#VAL{0}}|non)ok]");
		$this->assertOkCode("[(#VAL{0}|et{#VAL{1}}|non)ok]");
	}
	function testAnd(){
		$this->assertOkCode("[(#VAL{1}|and{#VAL{1}})ok]");
		$this->assertOkCode("[(#VAL{0}|and{#VAL{0}}|non)ok]");
		$this->assertOkCode("[(#VAL{1}|and{#VAL{0}}|non)ok]");
		$this->assertOkCode("[(#VAL{0}|and{#VAL{1}}|non)ok]");
	}
	
	function testOu(){
		$this->assertOkCode("[(#VAL{1}|ou{#VAL{1}})ok]");
		$this->assertOkCode("[(#VAL{0}|ou{#VAL{0}}|non)ok]");
		$this->assertOkCode("[(#VAL{1}|ou{#VAL{0}})ok]");
		$this->assertOkCode("[(#VAL{0}|ou{#VAL{1}})ok]");
	}
	function testOr(){
		$this->assertOkCode("[(#VAL{1}|or{#VAL{1}})ok]");
		$this->assertOkCode("[(#VAL{0}|or{#VAL{0}}|non)ok]");
		$this->assertOkCode("[(#VAL{1}|or{#VAL{0}})ok]");
		$this->assertOkCode("[(#VAL{0}|or{#VAL{1}})ok]");
	}
	
	function testXou(){
		$this->assertOkCode("[(#VAL{1}|xou{#VAL{1}}|non)ok]");
		$this->assertOkCode("[(#VAL{0}|xou{#VAL{0}}|non)ok]");
		$this->assertOkCode("[(#VAL{1}|xou{#VAL{0}})ok]");
		$this->assertOkCode("[(#VAL{0}|xou{#VAL{1}})ok]");
	}
	function testXor(){
		$this->assertOkCode("[(#VAL{1}|xor{#VAL{1}}|non)ok]");
		$this->assertOkCode("[(#VAL{0}|xor{#VAL{0}}|non)ok]");
		$this->assertOkCode("[(#VAL{1}|xor{#VAL{0}})ok]");
		$this->assertOkCode("[(#VAL{0}|xor{#VAL{1}})ok]");
	}
}


?>
