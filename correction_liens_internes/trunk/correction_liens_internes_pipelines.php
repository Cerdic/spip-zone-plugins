<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

# Constante surchargeable à placer dans config/mes_options.php en cas de multidomaines
# Liste de domaines supplémentaires considérés comme locaux :
# define('CORRECTION_LIENS_INTERNES_AUTRES_DOMAINES', 'http://domaine2.tld/, http://domaine3.tld');

/**
 * Corrige les liens internes juste avant l'édition d'un texte
 * @param array $flux l'entrée du pipeline
 * @return array $flux le flux modifié
**/
function correction_liens_internes_pre_edition($flux){
	if ($flux['args']['action'] == 'modifier') {
		foreach ($flux['data'] as $champ => $valeur) {
			if ($champ != 'virtuel') {
				$flux['data'][$champ] = correction_liens_internes_correction($valeur);
			} else {
				$flux['data'][$champ] = correction_liens_internes_correction($valeur, false);
			}
		}
	}
	return $flux;
}

/**
 * A partir d'une url privée, donne les infos sur l'objet auquel on renvoie
 * @param string $mauvaise_url l'url privée
 * @param array $composants_url les composants de l'url, parsée par php
 * @return array ($objet, $id_objet, $ancre)
 **/
function correction_liens_internes_correction_url_prive($mauvaise_url,$composants_url){
	// Pour le cas où on a copié-collé une URL depuis espace public.
	if (array_key_exists('fragment',$composants_url)){
		$ancre = $composants_url["fragment"];
	} else {
		$ancre = '';
	}
	$args =array();
	parse_str($composants_url["query"],$args);
	$exec = str_replace("_edit","",$args["exec"]); #prendre en compte les _edit
	if (array_key_exists("id_".$exec,$args)){
		$objet=$exec;
		$id_objet = $args["id_".$objet];
	}
	return array($objet,$id_objet,$ancre);
}

/**
 * A partir d'une url publique, donne les infos sur l'objet auquel on renvoie
 * @param string $mauvaise_url l'url publique
 * @param array $composants_url les composants de l'url, parsée par php
 * @return array ($objet, $id_objet, $ancre)
 **/
function correction_liens_internes_correction_url_public($mauvaise_url, $composants_url) {
	// Pour le cas où on a copié-collé une URL depuis espace public.
	$ancre = isset($composants_url['fragment']) ? '#' . $composants_url['fragment'] : '';

	list($fond, $contexte) = urls_decoder_url(str_replace($ancre, '', $mauvaise_url));

	if(
			($objet = isset($contexte['type']) ? $contexte['type'] : $fond) &&
			($id_objet = $contexte[id_table_objet($objet)])
			);
	else {
		// on tente de reconnaitre les formats simples...
		parse_str($composants_url["query"], $composants_url);
		if (($objet = $composants_url[_SPIP_PAGE]) && ($id_objet = $composants_url[id_table_objet($objet)]));
		else {
			list($composants_url, $objet) = nettoyer_url_page(str_replace(url_de_base(), '', $mauvaise_url), $composants_url);
			$id_objet = $composants_url[id_table_objet($objet)];
		}
	}
	return array($objet,$id_objet,$ancre);
}

/**
 * Parse un texte, à la recherche des liens erronnées, et les corriges
 * @param string $texte
 * @param bool $raccourci_spip=true si on doit tester la présence du raccourci [->], false si on remplace directement les urls même hors raccourcis SPIP
 * @return string le texte modifié
 **/
function correction_liens_internes_correction($texte, $raccourci_spip = true){
	// pas de liens, on s'en va...
	if (!is_string($texte) || (strpos($texte, '->') === false && $raccourci_spip )) {
		return $texte;
	}


	// traiter d'autre domaines ?
	if ($domaines = correction_liens_internes_autres_domaines()) {
		$domaines_origine = $domaines;
		$domaines = array_unique(array_merge(array(url_de_base()), $domaines));
		array_walk($domaines, create_function('&$v', '$v = preg_quote($v, "#");'));
		$url_site = '(?:' . join('|',$domaines) . ')';
	} else {
		$domaines_origine = array(url_de_base());
		$url_site = preg_quote(url_de_base());
	}
	// http ou https, même combat
	$url_site = str_replace('https\://', 'https?\://', $url_site);
	$url_site = str_replace('http\://', 'https?\://', $url_site);

	// on repère les mauvaises URLs
	$match = array();
	$objet = '';
	$id_objet = 0;
	if ($raccourci_spip) {
		preg_match_all("#\[.*->\s?($url_site.*)\]#U", $texte, $match, PREG_SET_ORDER);
	} else {
		preg_match_all("#($url_site.*(\s)?)#", $texte, $match, PREG_SET_ORDER);
	}
	include_spip("inc/urls");

	foreach($match as $lien) {
		$mauvais_raccourci = $lien[0];
		$mauvaise_url = $lien[1];
		$bonne_url = correction_liens_internes_trouver_bonne_url($mauvaise_url, $domaines_origine);
		if ($bonne_url) {
			$bon_raccourci = str_replace($mauvaise_url, $bonne_url, $mauvais_raccourci);
		}
		$texte = str_replace($mauvais_raccourci, $bon_raccourci, $texte);
		spip_log(self() . (_request('self')?' / '._request('self'):'')  //pour crayons notamment...
			. " : $mauvais_raccourci => $bon_raccourci", 'liens_internes.' . _LOG_AVERTISSEMENT);
	}
	return $texte;
}

/**
 * Trouver la bonne url a partir de la mauvaise
 * @param string $mauvaise_url la mauvaise url
 * @param array $domaines_origine les domaines
 * @return string la bonne url
**/
function correction_liens_internes_trouver_bonne_url($mauvaise_url, $domaines_origine) {
	// alias historiques
	static $racc = array('article' => '', 'auteur' => 'aut', 'rubrique' => 'rub', 'breve' => 'br');
	$bonne_url = '';
	$mauvaise_url_reelle = str_replace($domaines_origine, url_de_base(), $mauvaise_url);
	$composants_url = parse_url($mauvaise_url_reelle);
	// Url copiée depuis le privé ou depuis le public?
	if (strrpos($composants_url['path'],_DIR_RESTREINT_ABS)!=false){
		list ($objet, $id_objet,$ancre) = correction_liens_internes_correction_url_prive($mauvaise_url,$composants_url);
	}
	else{
		list ($objet, $id_objet,$ancre) = correction_liens_internes_correction_url_public($mauvaise_url_reelle,$composants_url);
	}
	if (!$objet && !$id_objet && strpos($mauvaise_url_reelle, str_replace(_DIR_RACINE, '', _DIR_IMG)) != false) {
		$url_doc = str_replace(array(url_de_base(), str_replace(_DIR_RACINE, '', _DIR_IMG)), '', $mauvaise_url_reelle);
		$id_objet = sql_getfetsel('id_document', 'spip_documents', 'fichier='.sql_quote($url_doc));
		$objet = 'document';
	}
	if($objet && $id_objet){
		if(isset($racc[$objet])){
			$objet = $racc[$objet];
		}
		// Exception historique : sites, cf https://core.spip.net/issues/4283
		if ($objet === 'site') {
			if (!defined('_CORRECTION_LIENS_INTERNES_LIEN_SITES'))
				define('_CORRECTION_LIENS_INTERNES_LIEN_SITES', 'site');
			$objet = _CORRECTION_LIENS_INTERNES_LIEN_SITES;
		}
		$bonne_url  = $objet . $id_objet . $ancre;
	}
	return $bonne_url;
}
/**
 * Retourne les domaines qu'on peut gérer, en plus du domaine de la requette http courante
 * @return array
**/
function correction_liens_internes_autres_domaines() {
	// si la constante est définie, prendre en compte les domaines déclarés
	$autres_domaines = defined('CORRECTION_LIENS_INTERNES_AUTRES_DOMAINES')
	? preg_split('#([\s,|])+#i', CORRECTION_LIENS_INTERNES_AUTRES_DOMAINES) : array();

	// si le plugin multidomaine est actif, prendre en compte tous les domaines déclarés
	if (test_plugin_actif('multidomaines')) {
		$config_multi = lire_config('multidomaines');
		foreach($config_multi as $key => $value) {
			if(preg_match('#editer_url#', $key) && $value) {
				$autres_domaines[] = $value;
			}
		}
	}
	// mettre en forme les domaines
	foreach($autres_domaines as $i => $v) {
		// ajouter un slash final si nécessaire
		if(substr($v, -1) != '/') {
			$autres_domaines[$i] = $v . '/';
		}
		// ajouter http:// par défaut si pas de scheme
		$infos = parse_url($v);
		if(!$infos['scheme']) {
			$autres_domaines[$i] = 'http://'  .$v;
		}
	}
	return $autres_domaines;
}
