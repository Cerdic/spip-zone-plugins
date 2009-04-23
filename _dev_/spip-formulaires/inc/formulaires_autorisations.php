<?php


	include_spip('formulaires_fonctions');


	function formulaires_autoriser() {}
	
	
	function autoriser_formulaires_dist($faire, $type, $id, $qui, $opt) {
		switch ($faire) {
			case 'bouton':
			case 'onglet':
			case 'voir':
			case 'editer':
			case 'joindre':
				return ($qui['statut'] == '0minirezo');
				break;
			default:
				return false;
				break;
		}
	}


?>