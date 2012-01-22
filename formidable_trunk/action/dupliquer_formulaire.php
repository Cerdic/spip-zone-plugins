<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Dupliquer un formulaire
 * @param unknown_type $arg
 * @return unknown_type
 */
function action_dupliquer_formulaire_dist($arg=null) {
	if (is_null($arg)){
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}
	$id_formulaire = intval($arg);

	// Si le formulaire existe bien
	if ($id_formulaire > 0 and $formulaire = sql_fetsel('*', 'spip_formulaires', 'id_formulaire = '.$id_formulaire)){
		include_spip('action/editer_formulaire');
		// On enlève les champs inutiles
		unset($formulaire['id_formulaire']);
		// On modifie un peu le titre
		$formulaire['titre'] = $formulaire['titre'].' '._T('formidable:formulaires_dupliquer_copie');
		// On s'assure que l'identifiant n'existe pas déjà
		$formulaire['identifiant'] = $formulaire['identifiant'].'_'.time();
		// On insère un nouveau formulaire
		$id_formulaire = insert_formulaire();
		// Si ça a marché on modifie les champs de base
		if ($id_formulaire > 0 and !($erreur = formulaire_set($id_formulaire, $formulaire))){
			// Et ensuite les saisies et les traitements
			$ok = sql_updateq(
				'spip_formulaires',
				array(
					'saisies' => $formulaire['saisies'],
					'traitements' => $formulaire['traitements']
				),
				'id_formulaire = '.$id_formulaire
			);
			// Et on redirige vers la vue
			$redirect = parametre_url(generer_url_ecrire('formulaires_voir'), 'id_formulaire', $id_formulaire, '&');
		}
		// Sinon on reste sur la page qui liste tout
		else{
			$redirect = generer_url_ecrire('formulaires_tous');
		}
	}

	// Si on a précisé une direction on va plutôt là
	if (_request('redirect')) {
		$redirect = parametre_url(urldecode(_request('redirect')),
			'id_formulaire', $id_formulaire, '&') . $erreur;
	}
	
	// On redirige
	include_spip('inc/headers');
	redirige_par_entete($redirect);
}

?>
