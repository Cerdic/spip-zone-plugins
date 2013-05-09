<?php
/**
 * Plugin Notation
 * par JEM (jean-marc.viglino@ign.fr) / b_b / Matthieu Marcillaud
 *
 * Copyright (c) 2008
 * Logiciel libre distribue sous licence GNU/GPL.
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/notation'); // pour fonction |note_ponderee

function cfg_config_notation_ponderation_verifier(&$cfg){
    $err = array();
    if ($cfg->val['ponderation'] < 1) {
        $cfg->val['ponderation'] = 1;
    }
   return $cfg->ajouter_erreurs($err);
}

// apres le traitement du formulaire par CFG
function cfg_config_notation_ponderation_post_traiter(&$cfg){
	// Recalculer les notes
	$ponderation = $cfg->val['ponderation'];
	
	// Mettre a jour les moyennes
	// cf critere {notation}
	$select = array(
		'notations.objet',
		'notations.id_objet',
		'COUNT(notations.note) AS nombre_votes',
		'ROUND(AVG(notations.note),2) AS moyenne',
		'ROUND(AVG(notations.note)*(1-EXP(-5*COUNT(notations.note)/'.$ponderation.')),2) AS moyenne_ponderee'
	);
	$res = sql_select($select,"spip_notations AS notations","","objet, id_objet");
	while($n = sql_fetch($res)){
		sql_updateq("spip_notations_objets", array(
			"note" => $n['moyenne'],
			"note_ponderee" => $n['moyenne_ponderee'],
			"nombre_votes" => $n['nombre_votes']),
			array(
				"objet=" . sql_quote($n['objet']),
				"id_objet=" . sql_quote($n['id_objet'])
			));		
	}

}
?>
