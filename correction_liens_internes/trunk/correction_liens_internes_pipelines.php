<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

# Constante surchargeable à placer dans config/mes_options.php en cas de multidomaines
# Liste de domaines supplémentaires considérés comme locaux :
# define('CORRECTION_LIENS_INTERNES_AUTRES_DOMAINES', 'http://domaine2.tld/, http://domaine3.tld');

function correction_liens_internes_pre_edition($flux){
	if ($flux['args']['action'] == "modifier") {
		foreach ($flux['data'] as $champ => $valeur)
			$flux['data'][$champ] = correction_liens_internes_correction($valeur);
	}
	return $flux;
}

function correction_liens_internes_correction($texte){
	// pas de liens, on s'en va...
	if(strpos($texte, '->') === false) return $texte;

	// alias historiques
	static $racc = array('article' => '', 'auteur' => 'aut', 'rubrique' => 'rub', 'breve' => 'br');

	// traiter d'autre domaines ?
	if($domaines = correction_liens_internes_autres_domaines()) {
		$domaines = array_unique(array_merge(array(url_de_base()), $domaines));
		// array_walk($domaines, function(&$v) { $v = preg_quote($v); });
		array_walk($domaines, create_function('&$v', '$v = preg_quote($v, "#");'));
		$url_site = '(?:' . join('|',$domaines) . ')';
	} else {
		$url_site = preg_quote(url_de_base());
	}

    // on repère les mauvaises URLs
    $match = array(); $objet = ''; $id_objet = 0; 
    preg_match_all("#\[.*->($url_site.*)\]#U", $texte, $match, PREG_SET_ORDER); 
    include_spip("inc/urls");
    foreach($match as $lien){
        $mauvais_raccourci = $lien[0];
        $mauvaise_url = $lien[1];
        $composants_url =  parse_url($mauvaise_url);
		$ancre = isset($composants_url['fragment']) ? '#' . $composants_url['fragment'] : '';
        list($fond, $contexte) = urls_decoder_url($mauvaise_url);
        if(($objet = isset($contexte['type']) ? $contexte['type'] : $fond) && ($id_objet = $contexte[id_table_objet($objet)]));
		else {
			// on tente de reconnaitre les formats simples...
            parse_str($composants_url["query"], $composants_url);
			if (($objet = $composants_url[_SPIP_PAGE]) && ($id_objet = $composants_url[id_table_objet($objet)]));
			else {
				list($composants_url, $objet) = nettoyer_url_page(str_replace(url_de_base(), '', $mauvaise_url), $composants_url);
				$id_objet = $composants_url[id_table_objet($objet)];
			}
		}
        if($objet && $id_objet){
			if(isset($racc[$objet])) $objet = $racc[$objet];
            $bonne_url  = $objet . $id_objet . $ancre;
            $bon_raccourci = str_replace($mauvaise_url, $bonne_url, $mauvais_raccourci);
            $texte = str_replace($mauvais_raccourci, $bon_raccourci, $texte);
			spip_log(self() . (_request('self')?' / '._request('self'):'') // pour crayons notamment...
				. " : $mauvais_raccourci => $bon_raccourci", 'liens_internes.' . _LOG_AVERTISSEMENT);
		}
	}
	return $texte;
}

function correction_liens_internes_autres_domaines() {
	// si la constante est définie, prendre en compte les domaines déclarés
	$autres_domaines = defined('CORRECTION_LIENS_INTERNES_AUTRES_DOMAINES') 
		? preg_split('#([\s,|])+#i', CORRECTION_LIENS_INTERNES_AUTRES_DOMAINES) : array();
	// si le plugin multidomaine est actif, prendre en compte tous les domaines déclarés
	if(test_plugin_actif('multidomaines')) {
		$config_multi = lire_config('multidomaines');
		foreach($config_multi as $key => $value) {
			if(preg_match('#editer_url#', $key) && $value) {
				$autres_domaines[] = $value;
			}
		}
	}
	// mettre en forme les domaines
	foreach($autres_domaines as $i=>$v) {
		// ajouter un slash final si nécessaire
		if(substr($v, -1) != '/') $autres_domaines[$i] = $v . '/';
		// ajouter http:// par défaut si pas de scheme
		$infos = parse_url($v);
		if(!$infos['scheme']) $autres_domaines[$i] = 'http://'  .$v;
	}
	return $autres_domaines;
}

?>