<?php
/*
 * Plugin Polyhierarchie
 * (c) 2009-2010 Cedric Morin
 * Distribue sous licence GPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Afficher le chemin, avec liens indirects
 * 
 * @param array $flux
 * @return array
 */
function polyhier_affiche_hierarchie($flux){
	$objet = $flux['args']['objet'];
	if (in_array($objet,array('article','rubrique'))){
		$id_objet = $flux['args']['id_objet'];
		include_spip('inc/polyhier');
		$parents = polyhier_get_parents($id_objet,$objet,$serveur='');
		$out = array();
		foreach($parents as $p)
			$out[] = "[->rubrique$p]";
		if (count($out)){
			$out = implode(', ',$out);
			$out = (count($out) > 1) ? _T('polyhier:label_autres_parents')." ".$out : _T('polyhier:label_autre_parent')." ".$out;
			$out = PtoBR(propre($out));
			$flux['data'] .= "<div id='chemins_transverses'>$out</div>";
		}

	}
	return $flux;
}

/**
 * Afficher les enfants indirects d'une rubrique
 *
 * @param array $flux
 * @return array
 */
function polyhier_affiche_enfants($flux) {
	if ($id_rubrique = $flux['args']['id_rubrique']) {
		include_spip('inc/autoriser');
		$flux['data'] .= recuperer_fond("prive/contenu/rubrique-enfants-indirects",$_GET, array('ajax'=>true));
	}
	return $flux;
}

/**
 * Pipeline pour charger les parents transverses dans le formulaire
 * d'edition article et rubrique
 * 
 * @param array $flux
 * @return array 
 */
function polyhier_formulaire_charger($flux){
	$form = $flux['args']['form'];
	if (
		(array_key_exists('_polyhier', $flux['data']) AND $objet = $flux['data']['_polyhier'] AND in_array($objet,array('article','rubrique')))
		OR ($objet = substr($form,7) AND in_array($form,array('editer_article','editer_rubrique')))
		){
		// On ne fait rien si l'id_parent principal est incoherent (exemple : compat pages uniques)
		if ($flux['data']['id_parent'] < 0) return $flux;

		$id_table_objet = id_table_objet($objet);

		// on met en tete l'id_parent principal
		// pour unifier la saisie
		$flux['data']['parents'] = array("rubrique|".$flux['data']['id_parent']);
		if ($id_objet = intval($flux['data'][$id_table_objet])){
			include_spip('inc/polyhier');
			$parents = polyhier_get_parents($id_objet,$objet,$serveur='');
			foreach($parents as $p)
				$flux['data']['parents'][] = "rubrique|$p";
		}
		$flux['data']['_hidden'] .= "<input type='hidden' name='_polyhier' value='$objet' />";
	}
	return $flux;
}


/**
 * Pipeline pour verifier les parents transverses dans le formulaire
 * d'edition article et rubrique
 * 
 * @param array $flux
 * @return array 
 */
function polyhier_formulaire_verifier($flux){
	$form = $flux['args']['form'];
	if ($objet = _request('_polyhier')
		AND in_array($objet,array('article','rubrique'))){
		// On ne fait rien si l'id_parent principal est incoherent (exemple : compat pages uniques)
		if (_request('id_parent') < 0) return $flux;

		$id_table_objet = id_table_objet($objet);

		// on verifie qu'au moins un parent est present si c'est un article
		if (!count(_request('parents')) AND $objet=='article'){
			$flux['data']['parents'] = _T('polyhier:parent_obligatoire');
			set_request('parents',array()); // eviter de revenir au choix initial
		}
		// sinon, c'est ok, on rebascule le premier parent[] dans id_parent
		// ou on est a la racine..
		else {
			$id_parent = _request('parents');
			$id_parent = explode('|',is_array($id_parent)?reset($id_parent):"rubrique|0");
			set_request('id_parent',intval(end($id_parent)));
			// Puisque c'est ok, on enlève l'éventuelle erreur que SPIP aurait mis sur "id_parent"
			unset($flux['data']['id_parent']);
		}

	}
	return $flux;
}

/**
 * Inserer le selecteur de rubriques trasnverses dans les formulaires d'edition
 * article et rubrique
 *
 * @param string $flux
 * @return string
 */
function polyhier_editer_contenu_objet($flux){
	$args = $flux['args'];
	$type = $args['type'];
	if (in_array($type,array('rubrique','article'))){
		// On ne fait rien si l'id_parent principal est incoherent (exemple : compat pages uniques)
		if ($args['contexte']['id_parent'] < 0) return $flux;

		$saisie = recuperer_fond("formulaires/inc-selecteur-parents",$args['contexte']);
		if (strpos($flux['data'],'<!--polyhier-->')!==FALSE)
			$flux['data'] = preg_replace(',(.*)(<!--polyhier-->),ims',"\\1$saisie\\2",$flux['data'],1);
		elseif (preg_match(",<li [^>]*class=[\"']editer_(descriptif|virtuel|chapo|liens_sites|texte),Uims",$flux['data'],$regs)){
			$flux['data'] = preg_replace(",(<li [^>]*class=[\"']editer_".$regs[1]."),Uims",$saisie."\\1",$flux['data'],1);
		}
		elseif (strpos($flux['data'],'<!--extra-->')!==FALSE)
			$flux['data'] = preg_replace(',(.*)(<!--extra-->),ims',"\\1$saisie\\2",$flux['data'],1);
		else
			$flux['data'] = preg_replace(',(.*)(</fieldset>),ims',"\\1\\\$saisie",$flux['data'],1);
	}
	return $flux;
}

/**
 * Appliquer les changements de polyhierarchie avant edition d'une rubrique ou
 * d'un article
 * On passe avant car si aucun autre champ n'est modifie, post_edition n'est pas appele
 *
 * @param array $flux
 * @return array
 */
function polyhier_pre_edition($flux){
	$objet = $flux['args']['type'];
	if (_request('_polyhier')
		AND in_array($objet,array('article','rubrique'))
		AND $flux['args']['action'] !== 'controler'){
		$id_objet = $flux['args']['id_objet'];
		$serveur = $flux['args']['serveur'];
		$id_parents = _request('parents');
		$id_parent = _request('id_parent');
		if (!$id_parents)
			$id_parents = array();

		$ids = array();
		foreach($id_parents as $sel){
			$sel = explode("|",$sel);
			if (reset($sel)=='rubrique')
				$ids[] = intval(end($sel));
		}

		// on enleve le parent principal qui est gere par SPIP
		$id_parents = array_diff($ids,array($id_parent));

		include_spip('inc/polyhier');
		$changed = polyhier_set_parents($id_objet,$objet,$id_parents,$serveur);
		if (count($changed['add']) OR count($changed['remove'])){
			$statut = sql_getfetsel("statut", table_objet_sql($objet), id_table_objet($objet)."=".intval($id_objet));
			// si l'objet est publie, repercuter le statut sur les rubriques quittes ou ajoutees
			if ($statut=='publie')
				polyhier_calculer_rubriques_if ($id_parents, $changed,$statut);
		}
	}

	return $flux;
}


/**
 * Appliquer les changements de statut sur les rubriques polyhierarchique
 *
 * @param array $flux
 * @return array
 */
function polyhier_post_edition($flux){
	$objet = objet_type($flux['args']['table']);

	if (in_array($objet,array('article','rubrique'))
		AND $flux['args']['action']=='instituer'
		AND $statut_ancien = $flux['args']['statut_ancien']
		AND isset($flux['data']['statut'])
		AND $statut = $flux['data']['statut']){

		$id_objet = $flux['args']['id_objet'];
		$serveur = $flux['args']['serveur'];
		include_spip('inc/polyhier');
		$id_parents = polyhier_get_parents($id_objet,$objet,$serveur);
		$postdate = (isset($flux['data']['date']) AND strtotime($flux['data']['date'])>time());

		polyhier_calculer_rubriques_if ($id_parents, array('statut'=>$statut,'add'=>array(),'remove'=>array()),$statut_ancien,$postdate);
	}

	return $flux;
}

/**
 * Compter les enfants indirects d'une rubrique
 *
 * @param array $flux
 * @return array
 */
function polyhier_objet_compte_enfants($flux) {

	if ($flux['args']['objet']=='rubrique'){
		$statut = (isset($flux['args']['statut'])?" AND A.statut=".sql_quote($flux['args']['statut']):"");
		$postdates = ($GLOBALS['meta']["post_dates"] == "non") ?
			" AND A.date <= ".sql_quote(date('Y-m-d H:i:s')) : '';

		$flux['data']['articles_indirects']+= sql_countsel(
						"spip_rubriques_liens as RL join spip_articles as A ON (RL.objet='article' AND RL.id_objet=A.id_article)",
						'RL.id_parent='.$flux['args']['id_objet'].$statut.$postdates);

		$statut = (isset($flux['args']['statut'])?" AND R.statut=".sql_quote($flux['args']['statut']):"");
		$flux['data']['rubriques_indirectes']+= sql_countsel(
						"spip_rubriques_liens as RL join spip_rubriques as R ON (RL.objet='rubrique' AND RL.id_objet=R.id_rubrique)",
						'RL.id_parent='.$flux['args']['id_objet'].$statut);
	}
	return $flux;
}


function polyhier_calculer_rubriques($flux) {

	// d'abord les articles indirects
	$r = sql_select("rub.id_rubrique AS id, max(fille.date) AS date_h", 
					"spip_rubriques AS rub 
						JOIN spip_rubriques_liens as RL ON rub.id_rubrique = RL.id_parent
						JOIN spip_articles as fille ON (RL.objet='article' AND RL.id_objet=fille.id_article)",
					"fille.statut='publie' AND rub.date_tmp <= fille.date",
					"rub.id_rubrique");
	while ($row = sql_fetch($r)) {
		sql_updateq("spip_rubriques", array("statut_tmp" => 'publie', "date_tmp" => $row['date_h']), "id_rubrique=".$row['id']);
	}

	// puis les rubriques qui ont une rubrique fille indirecte plus recente
	// on tourne tant que les donnees remontent vers la racine.
	do {
		$continuer = false;
		$r = sql_select("rub.id_rubrique AS id, max(fille.date_tmp) AS date_h",
						"spip_rubriques AS rub
							JOIN spip_rubriques_liens as RL ON rub.id_rubrique = RL.id_parent
							JOIN spip_rubriques as fille ON (RL.objet='rubrique' AND RL.id_objet=fille.id_rubrique)",
						"fille.statut_tmp='publie' AND (rub.date_tmp < fille.date_tmp OR rub.statut_tmp<>'publie')",
						"rub.id_rubrique");
		while ($row = sql_fetch($r)) {
		  sql_updateq('spip_rubriques', array('statut_tmp'=>'publie', 'date_tmp'=>$row['date_h']),"id_rubrique=".$row['id']);
			$continuer = true;
		}
	} while ($continuer);

	return $flux;
}

/* pour que le pipeline ne rale pas ! */
function polyhier_autoriser(){}

?>
