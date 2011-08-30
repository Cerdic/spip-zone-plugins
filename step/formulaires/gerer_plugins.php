<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/step');
include_spip('inc/step_presentation');

function formulaires_gerer_plugins_charger_dist(){

	// mettre a jour les plugins locaux si necessaire...
	if (!_request('traitement_fait')
	or  _request('update_local')) {
		// pas le meme nombre : on met a jour
		include_spip('inc/step');
		if (!_request('update_local')) {
			include_spip('inc/plugin');
			actualise_plugins_actifs();
			$nb = sql_countsel('spip_plugins', array('actif=' . sql_quote('oui')));
			$nb2 = count(step_liste_plugin_actifs());
			$do = $nb != $nb2;
		} else {
			$do = true;
		}
		if ($do) {
			spip_log('Mise a jour auto de la liste des plugins locaux !','step');
			step_actualiser_plugins_locaux();
		}
	}
	$_todo = _request('_todo') ? _request('_todo') : serialize(array());

	return array(
		'recent' => 1,
		'recherche' => '',
		'rechercher' => '', // pour annuler $recent : toute recherche affiche tous les plugins, non pas que les recents
		'etat' => '',
		'present' => '',
		'actif' => '',
		'nom' => '',
		'categorie' => '',
		'tags' => '',
		'superieur' => 'non',
		'obsolete' => 'non',
		'id_zone' => '',
		'id_plugin_info' => '',
		'_todo' => $_todo,
	);
}

function formulaires_gerer_plugins_verifier_dist(){
	set_request('traitement_fait', 1);
	$erreurs = array();

	$add = _request('add');
	$_todo = _request('_todo');
	$_todo = @unserialize($_todo);
	if (!$_todo) $_todo = array();


	if ($add) {
		$verif = false;

		list($action, $id_plugin) = explode('/',$add);
		if ($action and $id_plugin) {
			if (!isset($_todo[$id_plugin]) or $_todo[$id_plugin] != $action) {
				$_todo[$id_plugin] = $action;
				$verif = true;
			}
		}
	} elseif (_request('upgrade')) {
		// selection de toutes les maj
		include_spip('inc/step');
		if ($ids = step_selectionner_maj()) {
			foreach ($ids as $id) {
				if (!isset($_todo[$id])) {
					$_todo[$id] = 'up';
				} elseif ($_todo[$id] == 'on') {
					$_todo[$id] = 'upon';
				}
				// sinon : souci... la demande ne peut pas se realiser.
			}
		}

	}

	// on demande a executer les actions demandees
	if ($verif) {

		include_spip('inc/step_decideur');
		$decideur = new Decideur;
		$decideur->log = true;
		$decideur->verifier_dependances($_todo);

		if (!$decideur->ok) {
			$erreurs['message_erreur'] = "c'est pas ok !";
			$erreurs['decideur_erreurs'] = array();
			foreach ($decideur->err as $id=>$errs) {
				foreach($errs as $err) {
					$erreurs['decideur_erreurs'][] = $err;
				}
			}
		}

		$erreurs['decideur_propositions'] 	= $decideur->presenter_actions('changes');
		$erreurs['decideur_demandes'] 		= $decideur->presenter_actions('ask');
		$erreurs['decideur_actions'] 		= $decideur->presenter_actions('todo');

		// c'est pas vraiment des erreurs... a suivre...
		$_todo = array();
		foreach ($decideur->todo as $info) {
			$_todo[$info['i']] = $info['todo'];
		}

	}

	if (_request('update_local')) set_request('update_local', 1);
	set_request('_todo', serialize($_todo));

	return $erreurs;
}


function formulaires_gerer_plugins_traiter_dist(){
	set_request('traitement_fait',1);
	// si on arrive la, on peut faire les actions demandees.
	return array(
		'editable'=>true,
		'message_ok'=>''
	);
}
?>
