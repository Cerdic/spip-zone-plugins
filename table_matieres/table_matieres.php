<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function TableMatieres_Table($url = '', $titre = '', $cId = 0, $vider_table = false) {
	static $table = array();
	if($vider_table) return ($table = array());
	if($url == '') return $table;
	$url = array_key_exists($url, $table) ? $url.$cId : $url;
	$table[$url] = $titre;
	return $url;
}

function TableMatieres_ViderTable() {
	return TableMatieres_Table('', '', 0, true);
}

function TableMatieres_BalisePresente($test = false) {
	static $flag = false;
	if($test) $flag = $test;
	return $flag;
}

function TableMatieres_Callback($matches, $retour_cId = false) {
	static $cId = 0;
	if($retour_cId) return $cId;
	$cId++;
	$titre = supprimer_tags(typo($matches[1]));
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
			$url2 = $url._SEP_ANCRE.$mot;
			if (strlen($url2) > _LG_ANCRE) {
				break;
			}
			$url = $url2;
		}
		$url = substr($url, 1);
		if (strlen($url) < 2) $url = "ancre$cId";
	}
	$url = TableMatieres_Table($url, $titre, $cId);
	return '{{{ ['.$url.'<-] '.$matches[1].' @@RETOUR_TDM@@ }}}';
}

function TableMatieres_AjouterAncres($texte) {
	static $textes = array();
	$md5 = md5($texte);
	if(!isset($textes[$md5])) {
		$texte_ancre = echappe_html($texte, 'TDM');
		$texte_ancre = preg_replace_callback("/{{{(.*)}}}/UmsS", 'TableMatieres_Callback', $texte_ancre);
		$nb_ancres = TableMatieres_Callback('', true);
		$texte_ancre = echappe_retour($texte_ancre, 'TDM');
		$textes[$md5] = $texte_ancre;
	}
	return $textes[$md5];
}

function TableMatieres_LienRetour($texte, $affiche_table = false) {
	$_RETOUR_TDM = preg_replace(',<img,i',
	'<img alt="'._T('tdm:retour_table_matiere').'" title="'._T('tdm:retour_table_matiere').'"',
	_RETOUR_TDM);
	$_table = recuperer_fond('modeles/table_matieres');
	return $affiche_table ?
		$_table :
		(((TableMatieres_BalisePresente() OR !strlen(trim($_table))) ? //calcul :)
			'' :
			'<div class="encart">'.$_table."</div>\n\n") .
		str_replace('@@RETOUR_TDM@@', $_RETOUR_TDM, $texte));
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
		if(TableMatieres_BalisePresente(true)) { //REcalcul :(
			$_texte = champ_sql('texte', $p);
			$p->code = "$_texte";
		}
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