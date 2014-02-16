<?php
require_once('lanceur_spip.php');
include_spip('inc/autoriser');

class Test_balise_autoriser extends SpipTest{
	
	function testAutoriserOkNiet(){
		$this->assertFalse(autoriser('niet'));
		$this->assertTrue(autoriser('ok'));
	}
	
	function testAutoriserSqueletteOkNiet(){
		$this->assertOkCode('[(#AUTORISER{ok})ok]');
		$this->assertOkCode('[(#AUTORISER{niet}|sinon{ok})]');
	}


	function testAutoriserNouvelleFonction(){
		function autoriser_chiparder($faire, $type, $id, $qui, $opt){
			return true;
		}
		$this->assertTrue(autoriser('chiparder'));
		
		function autoriser_chiparder_velo($faire, $type, $id, $qui, $opt){
			return true;
		}
		$this->assertTrue(autoriser('chiparder','velo'));
		$this->assertTrue(autoriser('velo','chiparder'));
		
		function autoriser_chiparder_carottes_dist($faire, $type, $id, $qui, $opt){
			return false;
		}
		function autoriser_chiparder_carottes($faire, $type, $id, $qui, $opt){
			return true;
		}		
		$this->assertTrue(autoriser('chiparder','carottes'));
		$this->assertTrue(autoriser('carottes','chiparder'));			
	}	
	
	
	function testAutoriserVerifAuteur(){
		function autoriser_moimeme($faire, $type, $id, $qui, $opt){
			if (!$id)
				return ($qui['id_auteur'] == $GLOBALS['visiteur_session']['id_auteur']);
			else
				return ($id == $qui['id_auteur']);
		}
		$this->assertTrue(autoriser('moimeme'));
		$this->assertTrue(autoriser('moimeme','',$GLOBALS['visiteur_session']['id_auteur']));
		$this->assertFalse(autoriser('moimeme','',$GLOBALS['visiteur_session']['id_auteur']+1));
	}
}


?>
