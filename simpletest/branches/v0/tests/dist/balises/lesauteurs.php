<?php
require_once('lanceur_spip.php');

class Test_balise_lesauteurs extends SpipTest{
	
	function testLesAuteursRenvoieQqc(){
		$code = "
			<BOUCLE_a(ARTICLES){id_auteur>0}{0,1}>
				[(#LESAUTEURS|?{OK,'#LESAUTEURS a echoue'})]
			</BOUCLE_a>
			NA Ce test ne fonctionne que s'il existe un article ayant un auteur !
			<//B_a>
		";
		$this->assertOkCode($code);
	}
}




?>
