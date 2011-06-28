<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/iextras');
include_spip('inc/cextras_gerer');

function formulaires_editer_champ_extra_charger_dist($extra_id='new', $redirect=''){
	// nouveau ?
	$new = ($extra_id == 'new') ? ' ': '';
	
	// valeur par defaut (on utilise les valeurs d'un champ vide)
	$c = new ChampExtra;
	$valeurs = array_merge($c->toArray(), array(
		'extra_id' => $extra_id,
		'new' => $new,
		'redirect' => $redirect,
	));
	
	// valeur par defaut tout de meme sur sql et pour saisie
	if (!$valeurs['sql']) {
		$valeurs['sql'] = "text NOT NULL DEFAULT ''";
	}

	if (!$valeurs['saisie']) {
		$valeurs['saisie'] = "input";
	}
	
	if ($new) {
		// donnees des options de la saisie, pour #GENERER_SAISIES
		$s = saisies_charger_infos( $valeurs['saisie'] );
		$valeurs['_saisies_defs']    = $s['options'];

		// liste des champs (d'option) à interpreter et capturer pour CVT
		$valeurs = array_merge($valeurs, saisies_lister_valeurs_defaut( $valeurs['_saisies_defs'] ));
	}
	
	// si un extra est demande (pour edition)
	// remplir les valeurs avec infos de celui-ci
	if (!$new) {
		$extra = iextra_get_extra($extra_id);
		// si l'identifiant n'est pas trouve, c'est que le champ n'existe plus
		if (!$extra) return false;

		// donnees des options de la saisie, pour #GENERER_SAISIES
		$s = saisies_charger_infos( $extra->saisie );
		$valeurs['_saisies_defs']    = $s['options'];

		// liste des champs (d'option) à interpreter et capturer pour CVT
		$valeurs = array_merge($valeurs, saisies_lister_valeurs_defaut( $valeurs['_saisies_defs'] ));
				
		$valeurs = array_merge($valeurs, $extra->toArray());

		// chaque saisie_parametres devient un parametre a charger
		$valeurs = array_merge($valeurs, $valeurs['saisie_parametres']);
		unset ($valeurs['saisie_parametres']);
	}

	return $valeurs;
}


function formulaires_editer_champ_extra_verifier_dist($extra_id='new', $redirect=''){
	$erreurs = array();
	
	// nouveau ?
	$new = ($extra_id == 'new') ? ' ': '';	
	
	// recuperer les valeurs postees
	$extra = iextras_post_formulaire();
	
	// pas de champ vide
	foreach( array('champ', 'table', 'saisie', 'sql') as $c) {
		if (!$extra[$c]) {
			$erreurs[$c] = _T('iextras:veuillez_renseigner_ce_champ');
		}
	}
	
	// 'champ' correctement ecrit (pas de majuscule ni de tiret)
	if ($champ = trim($extra['champ'])) {
		if (!preg_match('/^[a-z0-9_]+$/',$champ)) {
			$erreurs['champ'] = _T('iextras:caracteres_interdits');
		}
	}
	
	// erreurs des options de saisies
	// donnees des options de la saisie
	$saisies_definitions = saisies_charger_infos( $valeurs['saisie'] );
	$erreurs = array_merge($erreurs, saisies_verifier( $saisies_definitions['options'] ));
	
	// si nouveau champ, ou modification du nom du champ
	// verifier qu'un champ homonyme 
	// n'existe pas deja sur la meme table
	$verifier = false;
	if (!$new) {
		$ancien = iextra_get_extra($extra_id);
		if (($ancien->champ != $champ) or ($ancien->table != $extra['table'])) {
			$verifier = true;
		}
	}
	if ($new or $verifier) {	
		$desc = sql_showtable($extra['table']);
		if (isset($desc['field'][$champ])) {
			$erreurs['champ'] = _T('iextras:champ_deja_existant');
		}
	}
	
	return $erreurs;
}


function formulaires_editer_champ_extra_traiter_dist($extra_id='new', $redirect=''){
	// nouveau ?
	$new = ($extra_id == 'new') ? ' ': '';
		
	// recuperer les valeurs postees (de base)
	$extra = iextras_post_formulaire();

	// recuperer les valeurs postees des options de la saisie
	$description_saisie = saisies_charger_infos( $extra['saisie'] );
	$champs = saisies_lister_champs( $description_saisie['options'] );
	$extra['saisie_parametres'] = array();
	foreach ($champs as $p) {
		$extra['saisie_parametres'][$p] = _request($p);
		unset($extra[$p]);
	}

	// recreer le tableau de stockage des extras
	$extras = iextras_get_extras();

	// ajout du champ ou modification du champ extra de meme id.
	$extra = new ChampExtra($extra);
	
	// creer le champ s'il est nouveau :
	if ($new) {
		$extras_old = $extras;
		$extras[] = $extra; // ajouter le champ cree
	} else {
		foreach($extras as $i=>$e) {
			if ($e->get_id() == $extra_id) {
				$extras[$i] = $extra;
				break;
			}
		}
	}
	
	// l'enregistrer les modifs
	iextras_set_extras($extras);
	
	$res = array(
		'editable' => true,
	);
		
	// creer le champ s'il est nouveau :
	if ($new) {
		extras_log("Creation d'un nouveau champ par auteur ".$GLOBALS['auteur_session']['id_auteur'], true);
		if (creer_champs_extras($extra)) {
			$res['message_ok'] = _T('iextras:champ_sauvegarde');
		} else {
			extras_log("! Aie ! Erreur de creation du champ", true);
			$res['message_erreur'] = _T('iextras:erreur_enregistrement_champ');
			// on remet l'ancienne declaration
			iextras_set_extras($extras_old);
		}
		extras_log($extra, true);
	} else {
		// modification
		$res['message_ok'] = _T('iextras:champ_sauvegarde');
	}
		

	if ($redirect and !isset($res['message_erreur'])) {
		$res['redirect'] = $redirect;
	}

	return $res;
}

// recuperer les valeurs postees par le formulaire
// des saisies (hors options fournies par le yaml de la saisie)
function iextras_post_formulaire() {
	$extra = array();
	foreach(array(
		'champ', 'table', 'saisie', 'sql',
		'traitements', 'rechercher'
	) as $c) {
		$extra[$c] = _request($c);
	}
	return $extra;	
}

?>
