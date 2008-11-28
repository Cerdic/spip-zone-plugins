<?php
require_once('lanceur_spip.php');

class Test_filtre_introduction extends SpipTest{
	var $func;
	
	// initialisation
	function Test_filtre_introduction() {
		$this->SpipTest();
		include_spip('inc/filtres');
		include_spip('public/composer');
		$this->func = chercher_filtre('introduction');

	}
	
	function testPresenceFiltre(){
		if (!$this->func) {
			throw new SpipTestException('Il faut le fichu filtre "introduction" !!');
		}		
	}
	
	// la description seule ressort avec propre() sans passer par couper()
	// or couper() enleve les balises <p> et consoeur, il faut en tenir compte dans la coupe
	// du texte, meme si le texte est plus petit
	function testDescriptifRetourneSiPresent(){
		if (!$f = $this->func) return;
		$this->assertEqual(propre('description petite'), $f('description petite','description plus longue',100,''));
	}
	// couper en plus...
	function testTexteNonCoupeSiPetit(){
		if (!$f = $this->func) return;
		$this->assertEqual(couper(propre('description plus longue'),100), $f('','description plus longue',100,''));
	}
	function testTexteCoupe(){
		if (!$f = $this->func) return;
		$this->assertEqual(couper(propre('description plus longue'),10), $f('','description plus longue',10,''));
		$this->assertNotEqual(couper(propre('description plus longue'),20), $f('','description plus longue',10,''));
	}
	function testTexteAvecBaliseIntro(){
		if (!$f = $this->func) return;
		$this->assertEqual(couper(propre('plus'),100), $f('','description <intro>plus</intro> longue',100,''));
	}
}


class Test_balise_introduction extends SpipTest{
	
	function testArticleDeRedirectionNeDoitPasAvoirDIntro(){
		$code = "
			[(#REM) un article de redirection n'a pas d'introduction]
			<BOUCLE_b(ARTICLES){chapo=='^='}{descriptif=''}{0,1}>
			[(#INTRODUCTION|?{erreur sur l'article de redirection #ID_ARTICLE,ok})]
			</BOUCLE_b>
			NA necessite un article de redirection sans descriptif
			<//B_b>		
		";
		$this->assertOkCode($code);
	}
	function testCoupeIntroduction(){
		# include_spip('public/composer');
		@define('_INTRODUCTION_SUITE', '&nbsp;(...)');
		$suite = _INTRODUCTION_SUITE;
		$code = "
			[(#REM) une introduction normale doit finir par _INTRODUCTION_SUITE]
			<BOUCLE_a(ARTICLES){chapo=='.{100}'}{texte>''}{descriptif=''}{chapo!=='^='}{0,1}>
			[(#INTRODUCTION)]
			</BOUCLE_a>
			NA necessite un article avec un texte long et pas de descriptif
			<//B_a>
		";
		if (!$this->exceptionSiNa($res = $this->recuperer_code($code))) {
			$this->assertPattern("/".preg_quote($suite)."$/", $res);
		}
	}
}

?>
