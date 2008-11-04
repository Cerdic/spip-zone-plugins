<?php

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
	$pond = $cfg->val['ponderation'];
	sql_update("spip_notations_objets",
		array("note_ponderee"=>"ROUND(note*(1-EXP(-5*nombre_votes/$pond))*100)/100"));
}
?>
