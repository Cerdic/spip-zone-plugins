<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/iextras');

function formulaires_editer_champ_extra_charger_dist($id_extra='new', $redirect=''){
	// valeur par defaut
	$valeurs = array(
		'champ' => '',
		'table' => '',
		'type' => '',
		'label' => '',
		'sql' => "text NOT NULL DEFAULT ''",
		'id_extra' => $id_extra,
		'new' => intval($id_extra)?'':' ',
		'redirect' => $redirect,
	);
	
	// si un extra est demande (pour edition)
	// remplir les valeurs avec infos de celui-ci
	if (intval($id_extra)) {
		$extras = iextras_get_extras();
		// $id_extra = 1, mais l'entree reelle est 0 dans le tableau
		if ($extra = $extras[--$id_extra]) {
			$valeurs = array_merge($valeurs, $extra->toArray());
		}
	}
	return $valeurs;
}


function formulaires_editer_champ_extra_verifier_dist($id_extra='new', $redirect=''){
	$erreurs = array();
	
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
	if (!intval($id_extra) 
	// $id_extra = 1, mais l'entree reelle est 0 dans le tableau
	OR ($extras[--$id_extra]->champ !== $champ)) { 
		foreach ($extras as $i=>$e) {
			if (($i !== $id_extra)
			and ($e->champ == $champ) 
			and ($e->table == $extra['table'])) {
				$erreurs['champ'] = _T('iextras:champ_deja_existant');	
			}
		}
	}

	return $erreurs;
}


function formulaires_editer_champ_extra_traiter_dist($id_extra='new', $redirect=''){
	
	// recuperer les valeurs postees
	$extra = iextras_post_formulaire();

	// recreer le tableau de stockage des extras
	$extras = iextras_get_extras();
	$new = false;
	if (intval($id_extra)) {
		// $id_extra = 1, mais l'entree reelle est 0 dans le tableau
		$extras[--$id_extra] = new ChampExtra($extra);
	} else {
		$extras[] = new ChampExtra($extra);
		$new = true;
	}
	// l'enregistrer
	iextras_set_extras($extras);
	
	// creer le champ s'il est nouveau :
	if ($new) {
		include_spip('base/create');
		$table = table_objet_sql($extra['table']);
		extras_log("Creation d'un nouveau champ par auteur n°".$GLOBALS['auteur_session']['id_auteur'],true);
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
	foreach(array('champ', 'table', 'type', 'label', 'sql') as $c) {
		$extra[$c] = _request($c);
	}
	return $extra;	
}

?>
