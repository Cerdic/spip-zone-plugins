<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/step_decideur');
include_spip('inc/step_actionneur');
include_spip('inc/texte');

function formulaires_step_actions_charger_dist($actions){
	if (!$actions) {
		return false;
	}

	if (!_request('gogogo') and !_request('end')) {
		$actionneur = new Actionneur();
		$actionneur->log = true;
		$actionneur->ajouter_actions($actions);
		$affiche = $actionneur->presenter_actions();
	} else {
		$affiche = _request('affiche');
	}


	return array(
		'gogogo' => 0,
		'_affiche' => $affiche,
		'_todo' => $actions
	);
}

function formulaires_step_actions_verifier_dist($actions){
	if (_request('annuler')) {
		set_request('gogogo', 0);

	} else {
		$actionneur = new Actionneur();
		$actionneur->log = true;
		$actionneur->get_actions();

		if (_request('applicateur')) {
			// on sauve les actions a realiser...
			$actionneur->ajouter_actions($actions);
			$actionneur->sauver_actions();
			set_request('gogogo', 1);   // indiquer que le formulaire travaille...
		}

		// on effectue une action
		$actionneur->one_action();
		if ($actionneur->end) {
			$act = reset($actionneur->end);
			$infos = array(
						'plugin' => typo($act['n']),
						'version' => $act[v]
					);
			$erreurs['actionneur_action'] = _T("step:message_action_end_$act[todo]",$infos);
		} else {
			// une fois que tout est realise, on met a jour la liste des plugins locaux !
			include_spip('inc/step');
			step_actualiser_plugins_locaux();
			$erreurs['actionneur_action'] = _T('step:messages_actions_realisees');
			set_request('gogogo', 0);
			set_request('end', 1);
		}

		$erreurs['actionneur_actions'] = $actionneur->presenter_actions();
	}

	return $erreurs;
}


function formulaires_step_actions_traiter_dist($actions){

	return array(
		'editable' => true,
		'message_ok' => "C'est fini"
	);
}

?>
