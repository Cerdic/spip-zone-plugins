<?php
require_once('lanceur_spip.php');

class Test_balise_config extends SpipTest{
	
	// initialisation
	function Test_balise_config() {
		$this->SpipTest();
		$this->options_recuperer_code(array(
			'fonctions'=>"
						function test_meta(\$raz = 0) {
							static \$meta = array();
							if (!\$meta) \$meta = \$GLOBALS['meta'];
							\$assoc = array('one' => 'element 1', 'two' => 'element 2');
							\$GLOBALS['meta'] = array(
								'zero' => 0,
								'zeroc' => '0',
								'chaine' => 'une chaine',
								'assoc' => \$assoc,
								'serie' => serialize(\$assoc)
							);
							if (\$raz) \$GLOBALS['meta'] = \$meta;
						}",
			'avant_code'=>'[(#VAL|test_meta)]',
			'apres_code'=>'[(#VAL{1}|test_meta)]',
		));
	}
	
	// avant chaque appel de fonction test
	function setUp() {

    }
    // apres chaque appel de fonction test
    function tearDown() {
        
    }
	
	function testConfigNomAbsent(){
		$this->assertOkCode('[(#CONFIG{pasla}|non)ok]');
	}
	function testConfigNomAbsentAvecDefaut(){
		$this->assertOkCode('[(#CONFIG{pasla,defaut}|=={defaut}|oui)ok]');
	}
	function testConfigChaine(){
		$this->assertOkCode('[(#CONFIG{chaine}|=={une chaine}|oui)ok]');
		$this->assertOkCode('[(#CONFIG{chaine,defaut}|=={une chaine}|oui)ok]');
	}
	function testConfigValeurZero(){
		$this->assertOkCode('[(#CONFIG{zero}|=={0}|oui)ok]');
		$this->assertOkCode('[(#CONFIG{zero,defaut}|=={0}|oui)ok]');
	}
	function testConfigChaineZero(){
		$this->assertOkCode("[(#CONFIG{zeroc}|=={'0'}|oui)ok]");
		$this->assertOkCode("[(#CONFIG{zeroc,defaut}|=={'0'}|oui)ok]");
	}
	function testArrayAssoc(){
		$this->assertOkCode("[(#CONFIG{assoc,'',''}|=={#ARRAY{one,element 1,two,element 2}}|oui)ok]");
		$this->assertOkCode("[(#CONFIG{assoc,defaut,''}|=={#ARRAY{one,element 1,two,element 2}}|oui)ok]");
	}	
	function testArraySerialize(){
		$this->assertOkCode('[(#CONFIG{serie}|=={a:2:{s:3:"one";s:9:"element 1";s:3:"two";s:9:"element 2";}}oui)ok]');
		$this->assertOkCode('[(#CONFIG{serie,defaut}|=={a:2:{s:3:"one";s:9:"element 1";s:3:"two";s:9:"element 2";}}oui)ok]');
	}	
	
}


?>
