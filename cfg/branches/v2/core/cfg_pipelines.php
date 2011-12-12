<?php

/*
 * Plugin CFG pour SPIP
 * (c) toggg, marcimat 2009, distribue sous licence GNU/GPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;



// teste si $form n'est pas un formulaire CVT deja existant
// (et non un formulaire CFG nomme $form en CVT)
// #FORMULAIRE_TOTO <> #FORMULAIRE_CFG{toto}
function est_cvt($form){
	$f = 'formulaires_' . $form;
	return (function_exists($f . '_stat')
		OR function_exists($f . '_dyn')
		OR function_exists($f . '_charger_dist')
		OR function_exists($f . '_charger')
		OR function_exists($f . '_verifier_dist')
		OR function_exists($f . '_verifier')
		OR function_exists($f . '_traiter_dist')
		OR function_exists($f . '_traiter')	
		);
}

# Formulaires CFG CVT
function cfg_formulaire_charger($flux){
	// s'il n'y a pas de fonction charger, on utilise le parseur de CFG
	$form = $flux['args']['form'];
	if (!est_cvt($form) AND !count($flux['data'])){

		// ici, on a le nom du fond cfg... 
		// on recupere donc les parametres du formulaire.	
		include_spip('inc/cfg_formulaire');

		$cfg_id = isset($flux['args']['args'][0]) ? $flux['args']['args'][0] : '';
		$config = new cfg_formulaire($form, $cfg_id);

		$contexte = array(
			'_cfg_fond' => 'formulaires/'.$form,
			'_cfg_nom' => $form,
			'id' => $cfg_id,
			'_param' => $config->param,
			// passer aussi les arguments spécifiques a cfg
			'_cfg_' => $config->creer_hash_cfg(), // passer action=cfg pour avoir un hash formulaire correct
			'_hidden' => "<input type='hidden' name='_cfg_is_cfg' value='oui' />"
		);

		// il faut passer les noms des champs (input et consoeurs) de CFG dans l'environnement
		// pour pouvoir faire #ENV{nom_du_champ}
		if (is_array($config->val)){
			$contexte = array_merge($contexte, $config->val);
		}

		if (!$config->autoriser()) {
			$contexte['editable'] = false;
		} else {
			$contexte['editable'] = true;
		}
		
		$contexte['_pipeline'] = array('editer_contenu_formulaire_cfg',
			'args'=>array(
				'nom'=>$form,
				'contexte'=>$contexte)
			);
		$flux['data'] = $contexte;

	}
	return $flux;
}

function cfg_formulaire_verifier($flux){

	$form = $flux['args']['form'];
	if (_request('_cfg_is_cfg') AND !est_cvt($form)){
		include_spip('inc/cfg_formulaire');

		$cfg_id = isset($flux['args']['args'][0]) ? $flux['args']['args'][0] : '';
		$config = new cfg_formulaire($form, $cfg_id);
		
		$err = array();

		if (!$config->verifier() && $e = $config->messages){
			if (isset($e['message_refus'])) {
				$err['message_erreur'] = $e['message_refus'];
			} else {
				if (count($e['erreurs']))  $err = $e['erreurs'];
				if (count($e['message_erreur']))  $err['message_erreur'] = join('<br />', $e['message_erreur']);
				if (count($e['message_ok']))  $err['message_ok'] = join('<br />', $e['message_ok']);
			}		
		}

		$flux['data'] = $err;
		
		// si c'est vide, modifier sera appele, sinon le formulaire sera resoumis
		// a ce moment la, on transmet $config pour eviter de le recreer 
		// juste ensuite (et de refaire les analyses et la validation)
		if (!$err) cfg_instancier($config);
	}
	return $flux;
}

// sauve ou redonne une instance de la classe cfg.
// sert a transmettre $config entre verifier() et traiter()
// car $flux le perd en cours de route si on lui donne...
function cfg_instancier($config=false){
	static $cfg=false; 
	if (!$config) return $cfg;
	return $cfg = $config;
}

// traitement du formulaire
function cfg_formulaire_traiter($flux){
	$form = $flux['args']['form'];
	if (_request('_cfg_is_cfg') AND !est_cvt($form)){
		$config = cfg_instancier();

		$config->traiter(); 
		$message = join('<br />',$config->messages['message_ok']); 
		$redirect = $config->messages['redirect']; 
		$flux['data'] = array( // forcer l'etat editable du formulaire et retourner le message 
			'editable'=>true,
			'message_ok' => $message,
			'redirect' => $redirect
		);
	}
	return $flux;
}

// pipeline sur l'affichage du contenu 
// pour supprimer les parametres CFG du formulaire
function cfg_editer_contenu_formulaire_cfg($flux){
	$flux['data'] = preg_replace('/(<!-- ([a-z0-9_]\w+)(\*)?=)(.*?)-->/sim', '', $flux['data']);
	return $flux;
}

?>
