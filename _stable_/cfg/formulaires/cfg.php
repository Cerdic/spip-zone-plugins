<?php

//
// Ce fichier charge dans l'environnement du formulaire
// les valeurs par defaut de celui-ci
//
// Pour CFG, il va donc lire le fond demande ainsi que les valeurs des champs presents dans ce fond
// pour les renvoyer dans l'environnement du formulaire, ces variables sont donc accessible par #ENV{nom}
//
function formulaires_cfg_charger_dist($cfg="", $cfg_id=""){

	if (!$cfg) return false;

	// ici, on a le nom du fond cfg... 
	// on recupere donc les parametres du formulaire.	
	include_spip('inc/cfg_formulaire');
	$config = &new cfg_formulaire($cfg, $cfg_id);

	$valeurs = array(
		'_cfg_fond' => 'fonds/cfg_'.$cfg,
		'_cfg_nom' => $cfg,
		'id' => $cfg_id,
		// passer aussi les arguments spécifiques a cfg
		'_cfg_' => $config->creer_hash_cfg('cfg') // passer action=cfg pour avoir un hash formulaire correct
	);

	// il faut passer les noms des champs (input et consoeurs) de CFG dans l'environnement
	// pour pouvoir faire #ENV{nom_du_champ}
	if (is_array($config->val)){
		foreach($config->val as $nom=>$val){
			$valeurs[$nom] = $val;	
		}
	}

	// return $valeurs; // retourner simplement les valeurs
	return array(true,$valeurs); // forcer l'etat editable du formulaire et retourner les valeurs

}


function formulaires_cfg_verifier_dist($cfg="", $cfg_id=""){
	
	include_spip('inc/cfg_formulaire');
	$config = &new cfg_formulaire($cfg, $cfg_id);
	
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


//
// Cette fonction enregistre les variables postees par le formulaire.
// Ces variables ayant etes verifies dans 'valider.php' au prealable,
// il y a simplement a les enregistrer.
//
function formulaires_cfg_traiter_dist($cfg="", $cfg_id=""){

	include_spip('inc/cfg_formulaire');
	$config = &new cfg_formulaire($cfg, $cfg_id);
	
	if ($config->verifier())
		$config->traiter();
		
	$message = join('<br />',$config->messages['message_ok']);	

	//return $message; // retourner simplement un message, le formulaire ne sera pas resoumis
	return array(true,$message); // forcer l'etat editable du formulaire et retourner le message
}



?>
