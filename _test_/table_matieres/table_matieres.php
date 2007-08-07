<?php

function TableMatieres_Table($url = '', $titre = '') {
	static $table = array();
	if($url == '') return $table;
	$table[$url] = $titre;
	return '';
}

function TableMatieres_Callback($matches) {
	static $cId = 0;
	$cId++;
	$titre = typo($matches[1]);
	$url = translitteration($titre);
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
	TableMatieres_Table($url, $titre);
	return '{{{ ['.$url.'<-] '.$matches[1].' @@RETOUR_TDM@@ }}}';
}

function TableMatieres_AjouterAncres($texte) {
	static $premier_passage = false;
	static $texte_ancre;
	if($premier_passage == false) {
		$premier_passage = true;
		$texte = echappe_html($texte, 'TDM', true, ',<(code|cadre|math)'
			. '(\s[^>]*)?'
			. '>(.*)</\1>,UimsS');
		$texte = preg_replace_callback("/{{{(.*)}}}/UmsS", 'TableMatieres_Callback', $texte);
		$texte_ancre = echappe_retour($texte, 'TDM');
	}
	return $texte_ancre;
}

function TableMatieres_LienRetour($texte, $affiche_table = false) {
	$_RETOUR_TDM = preg_replace(',<img,i',
	'<img alt="'._T('tdm:retour_table_matiere').'" title="'._T('tdm:retour_table_matiere').'"',
	_RETOUR_TDM);
	return $affiche_table ?
		recuperer_fond('modeles/table_matieres') :
		preg_replace('/@@RETOUR_TDM@@/S', $_RETOUR_TDM, $texte);
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