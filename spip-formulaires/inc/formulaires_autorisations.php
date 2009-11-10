<?php


	/**
	 * SPIP-Formulaires
	 *
	 * Copyright (c) 2006-2009
	 * Agence Artégo http://www.artego.fr
	 *  
	 * Ce programme est un logiciel libre distribue sous licence GNU/GPLv3.
	 * Pour plus de details voir http://www.gnu.org/licenses/gpl-3.0.html
	 *  
	 **/


	include_spip('formulaires_fonctions');


	function formulaires_autoriser() {}
	
	
	function autoriser_formulaires_dist($faire, $type, $id, $qui, $opt) {
		switch ($faire) {
			case 'bouton':
			case 'onglet':
			case 'voir':
			case 'editer':
			case 'modifier':
			case 'joindredocument':
				return ($qui['statut'] == '0minirezo');
				break;
			default:
				return false;
				break;
		}
	}
	
	function autoriser_formulaires_tous_bouton_dist($faire, $type, $id, $qui, $opt) {
		return autoriser_formulaires_dist($faire, $type, $id, $qui, $opt);
	}


?>