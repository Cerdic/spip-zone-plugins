<?php

/*
 * Plugin CFG pour SPIP
 * (c) toggg 2007, distribue sous licence GNU/GPL
 * Documentation et contact: http://www.spip-contrib.net/
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

// Ajoute le bouton d'amin aux webmestres
function cfg_ajouter_boutons($flux) {
	// si on est admin
	if (autoriser('configurer','cfg')) {
	  // on voit le bouton dans la barre "configuration"
		$flux['configuration']->sousmenu['cfg']= new Bouton(
		"../"._DIR_PLUGIN_CFG."cfg-22.png",  // icone
		_T('cfg:CFG'));
	}
	return $flux;
}


/*
 * Gerer l'option <!-- head= xxx --> des fonds CFG
 * uniquement dans le prive
 */
function cfg_header_prive($flux){
	
	if (!_request('cfg') || (!_request('exec') == 'cfg')) {
		return $flux;
	}

	// Ajout des css de cfg (uniquement balise arbo pour l'instant) dans le header prive
	$flux .= '<link rel="stylesheet" href="'._DIR_PLUGIN_CFG.'css/cfg.css" type="text/css" media="all" />';

	include_spip('inc/filtres');
	include_spip('inc/cfg_formulaire');
	$config = &new cfg_formulaire(
				sinon(_request('cfg'), ''),
				sinon(_request('cfg_id'),''));
	
	if ($config->param['head']) 
		$flux .= "\n".$config->param['head'];
	
	return $flux;
}




// teste si $form n'est pas un formulaire CVT deja existant
// (et non un formulaire CFG nomme $form en CVT)
// #FORMULAIRE_TOTO <> #FORMULAIRE_CFG{toto}
function est_cvt($form){
	$f = 'formulaires_' . $form;
	return (function_exists($f . '_stat')
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
	if (!est_cvt($form)){

		// ici, on a le nom du fond cfg... 
		// on recupere donc les parametres du formulaire.	
		include_spip('inc/cfg_formulaire');
		#$config = &new cfg_formulaire($cfg, $cfg_id);
		$cfg_id = isset($flux['args']['args'][0]) ? $flux['args']['args'][0] : '';
		$config = &new cfg_formulaire($form, $cfg_id);

		$valeurs = array(
			'_cfg_fond' => 'formulaires/'.$form,
			'_cfg_nom' => $form,
			'id' => $cfg_id,
			// passer aussi les arguments spécifiques a cfg
			'_cfg_' => $config->creer_hash_cfg() // passer action=cfg pour avoir un hash formulaire correct
		);

		// il faut passer les noms des champs (input et consoeurs) de CFG dans l'environnement
		// pour pouvoir faire #ENV{nom_du_champ}
		if (is_array($config->val)){
			foreach($config->val as $nom=>$val){
				$valeurs[$nom] = $val;	
			}
		}

		if (!$config->autoriser()) {
			$valeurs['editable'] = false;
		} else {
			$valeurs['editable'] = true;
		}
		
		$valeurs['_pipeline'] = array('editer_contenu_formulaire_cfg',
			'args'=>array(
				'nom'=>$form,
				'contexte'=>$valeurs,
				'ajouter'=>$config->param['inline'])
			);
		$flux['data'] = $valeurs;
		// return $valeurs; // retourner simplement les valeurs
		#return array(true,$valeurs); // forcer l'etat editable du formulaire et retourner les valeurs

	}
	return $flux;
}

function cfg_formulaire_verifier($flux){

	$form = $flux['args']['form'];
	if (!est_cvt($form)){
		include_spip('inc/cfg_formulaire');
		#$config = &new cfg_formulaire($cfg, $cfg_id);
		$cfg_id = isset($flux['args']['args'][0]) ? $flux['args']['args'][0] : '';
		$config = &new cfg_formulaire($form, $cfg_id);
		
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

		$flux['data'] = $err;
		
		// si c'est vide, modifier sera appele, sinon le formulaire sera resoumis
		// a ce moment la, on transmet $config pour eviter de le recreer 
		// juste ensuite (et de refaire les analyse et la validation)
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
	if (!est_cvt($form)){
		$config = cfg_instancier();

		$config->traiter();	
		$message = join('<br />',$config->messages['message_ok']);	

		$flux['data'] = array(true,$message); // forcer l'etat editable du formulaire et retourner le message
	}
	return $flux;
}

// pipeline sur l'affichage du contenu 
// pour supprimer les parametres CFG du formulaire
function cfg_editer_contenu_formulaire_cfg($flux){
	$flux['data'] = preg_replace('/(<!-- ([a-z0-9_]\w+)(\*)?=)(.*?)-->/sim', '', $flux['data']);
	$flux['data'] .= $flux['args']['ajouter'];
	return $flux;
}

?>
