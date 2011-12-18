<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function formulaires_charger_plugin_charger_dist(){
	return array(
		'phrase' => _request('phrase'),
		'categorie' => _request('categorie'),
		'etat' => _request('etat'),
		'depot' => _request('depot'),
		'doublon' => _request('doublon'),
		'exclusion' => _request('exclusion'),
		'ids_paquet' => _request('ids_paquet'),
		'_todo' => _request('_todo'));
}

function formulaires_charger_plugin_verifier_dist(){

	$erreurs = array();
	$a_installer = array();

	if (_request('annuler_actions')) {
		// Requete : Annulation des actions d'installation en cours
		// -- On vide la liste d'actions en cours
		set_request('_todo', '');
	
	} elseif (_request('valider_actions')) {
		
	
	} elseif (_request('rechercher')) {
		// annuler les selections si nouvelle recherche
		set_request('ids_paquet', array());
	} else {
		// Requete : Installation d'un ou de plusieurs plugins
		// -- On construit le tableau des ids de paquets conformement a l'interface du decideur
		if (_request('installer')) {
			// L'utilisateur a demander une installation multiple de paquets
			// -- on verifie la liste des id_paquets uniquement
			if ($id_paquets = _request('ids_paquet')) {
				foreach ($id_paquets as $_id_paquet)
					$a_installer[$_id_paquet] = 'geton';
			}
		}
		else {
			// L'utilisateur a demande l'installation d'un paquet en cliquant sur le bouton en regard
			// du resume du plugin -> installer_paquet
			if ($install = _request('installer_paquet'))
				if ($id_paquet = key($install))
					$a_installer[$id_paquet] = 'geton';
		}

		if (!$a_installer)
			$erreurs = _T('svp:message_nok_aucun_plugin_selectionne');
		else {
			// On fait appel au decideur pour determiner la liste exacte des commandes apres
			// verification des dependances
			include_spip('inc/svp_decider');
			$decideur = new Decideur;
			$decideur->log = true;
			$decideur->verifier_dependances($a_installer);

			if (!$decideur->ok) {
				$erreurs['decideur_erreurs'] = array();
				foreach ($decideur->err as $id=>$errs) {
					foreach($errs as $err) {
						$erreurs['decideur_erreurs'][] = $err;
					}
				}
			}
			else {
				$erreurs['decideur_propositions'] 	= $decideur->presenter_actions('changes');
				$erreurs['decideur_demandes'] 		= $decideur->presenter_actions('ask');
				$erreurs['decideur_actions'] 		= $decideur->presenter_actions('todo');

				// On construit la liste des actions pour la passer au formulaire en hidden
				$todo = array();
				foreach ($decideur->todo as $_todo) {
					$todo[$_todo['i']] = $_todo['todo'];
				}
				set_request('_todo', serialize($todo));
			}
		}
	}
	
	return $erreurs;
}

function formulaires_charger_plugin_traiter_dist(){

	$retour = array();

	if (_request('rechercher') OR _request('annuler_actions')) {

	}
	elseif (_request('valider_actions')) {
		#refuser_traiter_formulaire_ajax();
		// Ajout de la liste des actions à l'actionneur
		// c'est lui qui va effectuer rellement les actions
		// lors de l'appel de action/actionner 
		$actions = unserialize(_request('_todo'));
		include_spip('inc/svp_actionner');
		$actionneur = new Actionneur();
		$actionneur->log = true;
		$actionneur->ajouter_actions($actions);
		$actionneur->verrouiller();
		$actionneur->sauver_actions();

		$retour['redirect'] = generer_url_action('actionner', 'redirect='. generer_url_ecrire('admin_plugin'));
		set_request('_todo', '');
		$retour['message_ok'] = _T("svp:action_patienter");
	}
	else {
		// On a demande une installation, "installer" ou "installer_paquet" la fonction verifier a appele
		// le decideur afin de definir la liste des commandes a actionner en fonction des dependances
		set_request('traitement_fait',1);
	}
	$retour['editable'] = true;

	return $retour;
}


?>
