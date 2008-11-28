<?php
require_once('lanceur_spip.php');

class Test_balise_expose extends SpipTest{
	
	function testExposerRubrique(){
		$id_rubrique = sql_getfetsel('id_rubrique','spip_rubriques',array('id_parent='.sql_quote(0),'statut='.sql_quote('publie')));
		
		if (!$id_rubrique) {
			$this->exceptionSiNa("NA Vous devez avoir au moins 2 rubriques racines publiees pour tester #EXPOSE...");
		}
		else {
			$this->assertOkCode("
					<BOUCLE_racine(RUBRIQUES){racine}>
					[(#EXPOSE{ON,''}|oui)ok]
					</BOUCLE_racine>
					",
					array('id_rubrique'=>$id_rubrique)
			);
			$this->assertOkCode("
					<BOUCLE_racine(RUBRIQUES){racine}{id_rubrique!=#ENV{id_rubrique}}{0,1}>
					[(#EXPOSE{ON,''}|non)ok]
					</BOUCLE_racine>
					",
					array('id_rubrique'=>$id_rubrique)
			);
		}	
	}
	
}


?>
