<?php
require_once('lanceur_spip.php');

class Test_doublons_generiques extends SpipTest{
	
	function testDoublonsAuteurs(){
		$n = $this->recuperer_code("<BOUCLE_a(AUTEURS)>#COMPTEUR_BOUCLE</BOUCLE_a>");
		if ($n<=1) {
			throw new SpipTestException("Il faut au moins 2 auteurs ayant publie sur le site !");
		}
		$this->assertOkCode("
			<BOUCLE_a(AUTEURS){doublons}></BOUCLE_a>
			<BOUCLE_b(AUTEURS){doublons}{0,1}>Erreur doublons Auteurs</BOUCLE_b>OK<//B_b>");
	}
	function testDoublonsNommesAUTEURS(){
		$this->assertOkCode("
			<BOUCLE_a(AUTEURS){doublons polisson}></BOUCLE_a>
			<BOUCLE_b(AUTEURS){doublons polisson}{0,1}>Erreur doublons Auteurs</BOUCLE_b>OK<//B_b>");
		$this->assertOkCode("
			<BOUCLE_a(AUTEURS){1/2}{doublons kakis}></BOUCLE_a>
			<BOUCLE_b(AUTEURS){2/2}{doublons kokos}></BOUCLE_b>
			<BOUCLE_c(AUTEURS){doublons kakis}{0,1}>ok</BOUCLE_c>Erreur doubles doublons Auteurs<//B_c>");
		$this->assertOkCode("
			<BOUCLE_a(AUTEURS){1/2}{doublons kakis}></BOUCLE_a>
			<BOUCLE_b(AUTEURS){2/2}{doublons kokos}></BOUCLE_b>
			<BOUCLE_c(AUTEURS){doublons kakis}{0,1}> </BOUCLE_c>Erreur doubles doublons Auteurs<//B_c>			
			<BOUCLE_d(AUTEURS){doublons kokos}{0,1}> </BOUCLE_d>Erreur doubles doublons Auteurs<//B_d>
			<BOUCLE_e(AUTEURS){doublons kakis}{doublons kokos}{0,1}>Erreur doubles doublons Auteurs</BOUCLE_e>OK<//B_e>");
	}
}

?>
