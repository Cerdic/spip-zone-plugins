<?php

function formulaires_cfg_valider_dist($cfg="", $cfg_id=""){
	$erreurs = array();
	
	// si c'est une suppression, pas besoin de validation
	// if (_request('_cfg_delete')) return;
	
	$cfg_formulaire = cfg_charger_classe('cfg_formulaire','inc');
	$config = &new $cfg_formulaire($cfg, $cfg_id);
	
	$err = array();
	if (!$config->verifier() && $e = $config->messages){
		if (isset($e['message_refus'])) {
			$err['message_erreur'] = $e['message_refus'];
		} else {
			if (count($e['erreurs']))  $err = $e['erreurs'];
			if (count($e['message_erreur']))  $err['message_erreur'] = join('<br />',$e['message_erreur']);
			if (count($e['message_ok']))  $err['message_ok'] = join('<br />',$e['message_ok']);
		}		
	}

	// si c'est vide, modifier sera appele, sinon le formulaire sera resoumis
	return $err;
}
?>
