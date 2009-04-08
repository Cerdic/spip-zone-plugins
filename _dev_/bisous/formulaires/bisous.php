<?php
/*
 * Plugin SPIP Bisous pour Spip 2.0
 * Licence GPL
 */

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('base/abstract_sql');

function formulaires_bisous_charger_dist($id_auteur){
    
	if (
		($GLOBALS['visiteur_session']['id_auteur'] > 0
		and !sql_getfetsel(
			'id_bisou',
			'spip_bisous',
			array(
				array('=', 'id_donneur', intval($GLOBALS['visiteur_session']['id_auteur'])),
				array('=', 'id_receveur', intval($id_auteur))
			)
         
		)
	   or lire_config('bisous/multiple'))
	   
	   and (lire_config('bisous/self') or $GLOBALS['visiteur_session']['id_auteur']!=$id_auteur )  // il faudrait voir à pas se donner à soi même un bisous
	)
         
        return array();

	else
		return false;

}

function formulaires_bisous_verifier_dist($id_auteur){

	return array();

}

function formulaires_bisous_traiter_dist($id_auteur){

	// On teste si ya pas déjà un bisou
	if (!lire_config('bisous/multiple')){
           $bisou = sql_getfetsel(
            'id_bisou',
            'spip_bisous',
            array(
                array('=', 'id_donneur', intval($GLOBALS['visiteur_session']['id_auteur'])),
                array('=', 'id_receveur', intval($id_auteur))
            )
        );
	}
	// On ajoute un bisou si c'est pas déjà fait
	if (!$bisou)
		sql_insertq(
			'spip_bisous',
			array(
				'id_donneur' => intval($GLOBALS['visiteur_session']['id_auteur']),
				'id_receveur' => intval($id_auteur),
				'date' => date('Y-m-d H:i:s')
			)
		);
	
	// Relance la page
	return array('redirect'=>self());

}

?>
