<?php

define("_LG_ANCRE", 35);
define("_RETOUR_TDM", '<a href="#tdm"><img src="' .
	find_in_path('images/tdm.png') . 
	'" alt="' .
	_T('tdm:retour_table_matiere') .
	'" title="' .
	_T('tdm:retour_table_matiere') .
	'" /></a>');

function TableMatieres_Table($url = '', $titre = '') {
	static $table = array();
	if($url == '') return $table;
	$table[$url] = $titre;
	return '';
}

function TableMatieres_Callback($matches) {
	static $cId = 0;
	$cId++;
	$url = translitteration(corriger_caracteres(
		supprimer_tags(supprimer_numero(extraire_multi(trim($matches[1]))))
	));
	$url = @preg_replace(',[[:punct:][:space:]]+,u', ' ', $url);

	// S'il reste des caracteres non latins, utiliser l'id a la place
	if (preg_match(",[^a-zA-Z0-9 ],", $url)) {
		$url = "ancre$cId";
	}
	else {
		$mots = explode(' ', $url);
		$url = '';
		foreach ($mots as $mot) {
			if (!$mot) continue;
			$url2 = $url.'-'.$mot;
			if (strlen($url2) > _LG_ANCRE) {
				break;
			}
			$url = $url2;
		}
		$url = substr($url, 1);
		if (strlen($url) < 2) $url = "ancre$cId";
	}
	TableMatieres_Table($url, $matches[1]);
	return '{{{ ['.$url.'<-] '.$matches[1].' @@RETOUR_TDM@@ }}}';
}

function TableMatieres_AjouterAncres($texte) {
	return preg_replace_callback("/{{{(.*)}}}/UmsS", 'TableMatieres_Callback', $texte);
}

function TableMatieres_LienRetour($texte, $affiche_table = false) {
	return $affiche_table ?
		recuperer_fond('modeles/table_matieres') :
		preg_replace('/@@RETOUR_TDM@@/S', _RETOUR_TDM, $texte);
}

function balise_TABLE_MATIERES_dist($p) {
	$b = $p->nom_boucle ? $p->nom_boucle : $p->descr['id_mere'];
	if ($b === '') {
		erreur_squelette(
			_T('zbug_champ_hors_boucle',
				array('champ' => '#TABLE_MATIERES')
			), $p->id_boucle);
		$p->code = "''";
	} elseif($p->type_requete != 'articles') {
		erreur_squelette(_T('tdm:zbug_champ_tdm_hors_boucle_articles'), $p->id_boucle);
		$p->code = "''";
	} else {
		$_texte = champ_sql('texte', $p);
		$p->code = "$_texte";
	}
	return $p;
}

function balise_TDM_dist($p) {
	if(function_exists('balise_ENV'))
		return balise_ENV($p, 'TableMatieres_Table()');
	else
		return balise_ENV_dist($p, 'TableMatieres_Table()');
	return $p;
}

?>