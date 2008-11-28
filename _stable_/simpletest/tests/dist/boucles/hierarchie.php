<?php
require_once('lanceur_spip.php');

class Test_boucle_hierarchie extends SpipTest{
	
	function testBoucleHierarchie(){
		$this->assertOkCode("
			<BOUCLE_a(RUBRIQUES){tout}{0,50}>
			<BOUCLE_secteur(HIERARCHIE){0,1}>[(#ID_RUBRIQUE|=={#_a:ID_SECTEUR}|?{'',
				erreur secteur: attendu #_a:ID_SECTEUR ; resultat #ID_RUBRIQUE<br />})]
			</BOUCLE_secteur>
			<BOUCLE_parent(HIERARCHIE){n-1,1}>[(#ID_RUBRIQUE|=={#_a:ID_PARENT}|?{'',
				erreur parent: attendu #_a:ID_PARENT ; resultat #ID_RUBRIQUE<br />})]
			</BOUCLE_parent>
			</BOUCLE_a>
			OK		
		");
	}	
}

?>


