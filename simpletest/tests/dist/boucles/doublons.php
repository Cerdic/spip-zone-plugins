<?php
require_once('lanceur_spip.php');

class Test_doublons_divers extends SpipTest{
	
	function testDoublonsDivers(){
		$this->assertOkCode("
			<BOUCLE_a(ARTICLES){doublons}></BOUCLE_a>
			<BOUCLE_b(ARTICLES){doublons}{0,1}>erreur doublons articles</BOUCLE_b>

			<BOUCLE_a1(BREVES){doublons}></BOUCLE_a1>
			<BOUCLE_b1(BREVES){doublons}{0,1}>erreur doublons breves</BOUCLE_b1>

			<BOUCLE_a2(RUBRIQUES){doublons}></BOUCLE_a2>
			<BOUCLE_b2(RUBRIQUES){doublons}{0,1}>erreur doublons rubriques</BOUCLE_b2>

			<BOUCLE_a3(DOCUMENTS){doublons}{id_document<100}></BOUCLE_a3>
			<BOUCLE_b3(DOCUMENTS){doublons}{id_document<100}{0,1}>erreur doublons documents</BOUCLE_b3>

			<BOUCLE_a4(FORUMS){doublons}{id_forum<100}></BOUCLE_a4>
			<BOUCLE_b4(FORUMS){doublons}{id_forum<100}{0,1}>erreur doublons forums</BOUCLE_b4>

				OK

			<//B_b4>
			<//B_b3>
			<//B_b2>
			<//B_b1>
			<//B_b>
			");
	}
}

?>
