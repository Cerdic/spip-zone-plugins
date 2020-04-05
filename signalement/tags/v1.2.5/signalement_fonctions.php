<?php
/**
 * Plugin Signalement
 *
 * Auteurs :
 * kent1 (http://www.kent1.info - kent1@arscenic.info)
 *
 * © 2012 - Distribue sous licence GNU/GPL
 *
 * Fonctions de Signalement
 *
 **/

if (!defined("_ECRIRE_INC_VERSION")) return;

function lister_signalements($objet='',$id_objet=''){
	$signalements = array(
		'actes_danger' => _T('signalement:motif_option_actes_danger'),
		'enfants' => _T('signalement:motif_option_enfants'),
		'haine' => _T('signalement:motif_option_haine'),
		'droits_auteurs' => _T('signalement:motif_option_droits_auteurs'),
		'sexe' => _T('signalement:motif_option_sexe'),
		'spam' => _T('signalement:motif_option_spam'),
		'violence' => _T('signalement:motif_option_violence'),
		'autre' => _T('signalement:motif_option_autre')
	);
	$signalements = pipeline('signalement_liste',
		array(
			'args' => array(
				'objet'=>$objet,
				'id_objet'=>$id_objet
			),
			'data' => $signalements
		)
	);
	return $signalements;
}
?>