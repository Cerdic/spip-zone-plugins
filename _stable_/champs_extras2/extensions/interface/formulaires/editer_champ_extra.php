<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/iextras');
include_spip('inc/cextras_gerer');

function formulaires_editer_champ_extra_charger_dist($id_extra='new', $redirect=''){
	// nouveau ?
	$new = ($id_extra == 'new') ? ' ': '';
	
	// valeur par defaut
	$valeurs = array(
		'champ' => '',
		'table' => '',
		'type' => '',
		'label' => '',
		'precisions' => '',
		'enum' => '',
		'sql' => "text NOT NULL DEFAULT ''",
		'id_extra' => $id_extra,
		'new' => $new,
		'redirect' => $redirect,
	);
	
	// si un extra est demande (pour edition)
	// remplir les valeurs avec infos de celui-ci
	if (!$new) {
		$extras = iextras_get_extras();
		foreach($extras as $extra) {
			if ($extra->get_id() == $id_extra) {
				$valeurs = array_merge($valeurs, $extra->toArray());
				break;
			}
		}
	}
	return $valeurs;
}


function formulaires_editer_champ_extra_verifier_dist($id_extra='new', $redirect=''){
	$erreurs = array();
	
	// nouveau ?
	$new = ($id_extra == 'new') ? ' ': '';	
	
	// recuperer les valeurs postees
	$extra = iextras_post_formulaire();
	
	// pas de champ vide
	foreach(array('champ', 'table', 'type', 'label', 'sql') as $c) {
		if (!$extra[$c]) {
			$erreurs[$c] = _T('iextras:veuillez_renseigner_ce_champ');
		}
	}
	
	// 'champ' correctement ecrit
	if ($champ = trim($extra['champ'])) {
		if (!preg_match('/^[a-zA-Z0-9_-]+$/',$champ)) {
			$erreurs['champ'] = _T('iextras:caracteres_interdits');
		}
	}
	
	// si nouveau champ, ou modification du nom du champ
	// verifier qu'un champ homonyme 
	// n'existe pas deja sur la meme table
	$extras = iextras_get_extras();
	foreach ($extras as $e) {
		if ($new OR ($e->get_id() !== $id_extra)) {
			if (($e->champ == $champ) and ($e->table == $extra['table'])) {
				$erreurs['champ'] = _T('iextras:champ_deja_existant');
				break;
			}
		}
	}


	return $erreurs;
}


function formulaires_editer_champ_extra_traiter_dist($id_extra='new', $redirect=''){
	// nouveau ?
	$new = ($id_extra == 'new') ? ' ': '';
		
	// recuperer les valeurs postees
	$extra = iextras_post_formulaire();

	// recreer le tableau de stockage des extras
	$extras = iextras_get_extras();

	// ajout du champ ou modification du champ extra de meme id.
	if ($new) {
		$extras[] = new ChampExtra($extra);
	} else {
		foreach($extras as $i=>$e) {
			if ($e->get_id() == $id_extra) {
				$extras[$i] = new ChampExtra($extra);
				break;
			}
		}		
	}

	// l'enregistrer
	iextras_set_extras($extras);
	
	// creer le champ s'il est nouveau :
	if ($new) {
		// recharger les tables principales
		include_spip('base/serial');
		global $tables_principales;
		base_serial($tables_principales);
		
		include_spip('base/create');
		$table = table_objet_sql($extra['table']);
		extras_log("Creation d'un nouveau champ par auteur ".$GLOBALS['auteur_session']['id_auteur'],true);
		extras_log($extra, true);
		maj_tables($table);
	}
	
	$res = array(
		'editable' => true,
		'message_ok' => _T('iextras:champ_sauvegarde'),
	);
	if ($redirect) $res['redirect'] = $redirect;

	return $res;
}

// recuperer les valeurs postees par le formulaire
function iextras_post_formulaire() {
	$extra = array();
	foreach(array('champ', 'table', 'type', 'label', 'sql', 'precisions', 'enum') as $c) {
		$extra[$c] = _request($c);
	}
	return $extra;	
}

?>
