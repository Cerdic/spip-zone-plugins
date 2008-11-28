<?php
require_once('lanceur_spip.php');

class Test_balise_mime_type extends SpipTest{
	
	function testMimeTypeDocumentJpg(){
		$code = "
			<BOUCLE_d(DOCUMENTS){extension IN jpg}{0,1}>
				[(#MIME_TYPE|match{^image/jpeg$}|?{OK, erreur mime_type : #MIME_TYPE})]
			</BOUCLE_d>
			NA Ce test ne fonctionne que s'il y a au moins un document jpg dans le site !
			<//B_d>
		";
		$this->assertOkCode($code);
	}
}




?>
