<?php


	/**
	 * SPIP-Sondages
	 *
	 * Copyright (c) 2006-2009
	 * Agence Artégo http://www.artego.fr
	 *  
	 * Ce programme est un logiciel libre distribue sous licence GNU/GPLv3.
	 * Pour plus de details voir http://www.gnu.org/licenses/gpl-3.0.html
	 *  
	 **/


	include_spip('sondages_fonctions');


	function formulaires_sondage_charger_dist($id_sondage) {
		$id = intval($_COOKIE['sondage_'.$id_sondage]);
		$valeurs = array(
						'deja_vote' => ($id == $id_sondage) ? ' ' : '',
						'id_sondage' => $id_sondage
						);
		return $valeurs;
	}


	function formulaires_sondage_verifier_dist($id_sondage) {
		$id			= intval($_COOKIE['sondage_'.$id_sondage]);
		$id_choix	= _request('choix_'.$id_sondage);

		$erreurs = array();

		if ($id == $id_sondage) $erreurs['deja_vote'] = _T('sondages:vous_avez_deja_vote');
		if (empty($id_choix)) $erreurs['faites_un_choix'] = _T('sondages:faites_un_choix');

		return $erreurs;
	}


	function formulaires_sondage_traiter_dist($id_sondage) {
		include_spip('base/abstract_sql');
		include_spip('inc/cookie');
		$id_choix = _request('choix_'.$id_sondage);
		sql_insertq('spip_avis', array('id_sondage' => $id_sondage, 'id_choix' => intval($id_choix)));
		spip_setcookie('sondage_'.$id_sondage, $id_sondage, time() + 60 * 60 * 24 * 365); // un an
		return array(
					'message_ok' => _T('sondages:merci_pour_votre_avis'),
					'afficher_resultats' => ' ',
					'id_sondage' => $id_sondage
					);
	}


?>
