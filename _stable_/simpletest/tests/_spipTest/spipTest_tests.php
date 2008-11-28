<?php
require_once('lanceur_spip.php');

class Test_SpipTest extends SpipTest{

	function testAssertOk(){
		$this->assertTrue($this->assertOK('ok'));
		$this->assertTrue($this->assertOK('OK'));
	}
	function testAssertNotOk(){
		$this->assertTrue($this->assertNotOK('nOK'));
		$this->assertTrue($this->assertNotOK('n'));
	}
	function testIsNa(){
		$this->assertTrue($this->isNa(' NA texte'));
		$this->assertTrue($this->isNa('na texte'));
		$this->assertfalse($this->isNa('texte NA'));
		$this->assertfalse($this->isNa('texte'));
	}

}


?>
