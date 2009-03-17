<?php


	function facteur_autoriser() {}
	
	
	function autoriser_facteur_dist($faire, $type, $id, $qui, $opt) {
		switch ($faire) {
			case 'onglet':
			case 'configurer':
			case 'editer':
				return ($qui['statut'] == '0minirezo');
				break;
			default:
				return false;
				break;
		}
	}


?>