<?php
require_once('lanceur_spip.php');

class Test_SpipTestRecupererCode extends SpipTest{
	
	function testRecupererFond(){
		$this->assertEqual('Hello World', recuperer_fond('tests/core/inc/inclus_hello_world'));
	}
	function testRecupererCode(){
		$this->assertEqualCode('Hello World','Hello World');
	}
	function testRecupererCodeAvecFonctionEtApreCode(){
		$this->options_recuperer_code(array(
			'fonctions'=>"
						function so_smile(){
							return ' So Smile';
						}",
			'apres_code'=>'[(#VAL|so_smile)]',
		));
		$this->assertEqualCode('Hello World So Smile', 'Hello World');
	}
	function testRecupererCodeAvecFonctionUtiliseeAilleurs(){
		$this->assertEqualCode('Hello Kitty So Smile','Hello Kitty');
	}
	function testRecupererCodeAvecFonctionVide(){
		// pas de trace de l'ajout precedent (' So Smile' en plus)
		$this->options_recuperer_code();
		$this->assertEqualCode('Hello Kitty','Hello Kitty');
		
		// pas de fichier de fonctions
		$this->assertOkCode("[(#SQUELETTE|replace{'.html','_fonctions.php'}|find_in_path|non)ok]");
		
		// fichier de fonction
		$this->options_recuperer_code(array(
			'fonctions'=>"
						function so_smile(){
							return ' So Smile';
						}",
		));
		$this->assertNotOkCode("[(#SQUELETTE|replace{'.html','_fonctions.php'}|find_in_path|non)ok]");
		
		// pas de fichier de fonctions
		$this->options_recuperer_code();
		$this->assertOkCode("[(#SQUELETTE|replace{'.html','_fonctions.php'}|find_in_path|non)ok]");		
	}
	
	function testRecupererCodeAvantApres(){
		$this->options_recuperer_code(array(
			'avant_code'=>'Nice ',
			'apres_code'=>' So Beautiful',
		));
		$this->assertEqualCode('Nice Hello World So Beautiful', 'Hello World');		
	}
}

class Test_SpipTestRecupererInfosCode extends SpipTest{
	
	function testPresenceInfosFond(){
		$infos = $this->recuperer_infos_code('#SELF');
		$this->assertTrue(is_array($infos));
		$this->assertTrue(isset($infos['squelette']));
		$this->assertTrue(isset($infos['fond']));
	}
	function testPresenceInfosErreurCompilationFiltreAbsent(){
		$infos = $this->recuperer_infos_code('#CACHE{0}[(#SELF|la_gastro_nest_pas_la)]');
		$this->assertTrue(is_array($infos['erreurs']));
		$this->assertTrue(count($infos['erreurs']));
	}
	function testPresenceInfosErreurCompilationAbsentsDansNouvelleDemandeCorrecte(){
		$infos = $this->recuperer_infos_code('#CACHE{0}Aucun Probleme ici');
		$this->assertFalse(count($infos['erreurs']));
	}	
}
?>
