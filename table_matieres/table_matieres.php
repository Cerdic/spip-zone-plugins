<?php

if (!defined("_ECRIRE_INC_VERSION")) return;


// tester la presence de CFG
$tm = @unserialize($GLOBALS['meta']['table_matieres']);

define('_LG_ANCRE', isset($tm['lg']) ? $tm['lg'] : 35);
define('_SEP_ANCRE', isset($tm['sep']) ? $tm['sep'] : '-');
define('_MIN_ANCRE', isset($tm['min']) ? $tm['min'] : 3);
define('_RETOUR_TDM', '<a href="#tdm" class="tdm"><img src="' .
	find_in_path('images/tdm.png') . 
	'" /></a>');


function TableMatieres_Table($url = '', $titre = '', $cId = 0, $vider_table = false) {
	static $table = array();
	if($vider_table) return ($table = array());
	if($url == '') return $table;
	$url = array_key_exists($url, $table) ? $url.$cId : $url;
	$table[$url] = $titre;
	return $url;
}

function TableMatieres_SiNombreSuffisantIntertitres() {
	$table = TableMatieres_Table();
	if (count($table) < _MIN_ANCRE)
		return array();
	return $table;
	
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
	$titre = preg_replace(",\n[_\s]*,", " ", $titre);
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
		// 3e à true pour ne pas utiliser les fonctions d'echappement predefinis
		// et garder les textes tels quels (ex: <code><balise></code>)
		// sinon la transformation est effectuee 2 fois.
		$texte_ancre = echappe_html($texte, 'TDM', true); 
		$texte_ancre = preg_replace_callback("/{{{(.*)}}}/UmsS", 'TableMatieres_Callback', $texte_ancre);
		$nb_ancres = TableMatieres_Callback('', true);
		if ($nb_ancres >= _MIN_ANCRE) {
			$textes[$md5] = echappe_retour($texte_ancre, 'TDM');
		} else {
			$textes[$md5] = $texte;
		}
	}
	return $textes[$md5];
}

function TableMatieres_LienRetour($texte, $affiche_table = false) {
	$_RETOUR_TDM = preg_replace(',<img,i',
	'<img alt="'._T('tdm:retour_table_matiere').'" title="'._T('tdm:retour_table_matiere').'"',
	_RETOUR_TDM);

	// s'il y a moins d'ancres que ce que la config demande, on n'affiche rien
	if ($affiche_table AND !TableMatieres_SiNombreSuffisantIntertitres()) {
		return $texte;
	}

	// code HTML de la table des matieres
	$_table = recuperer_fond('modeles/table_matieres');

	# version en javascript (pas tres propre, a refaire avec un js externe)
	if (TDM_JAVASCRIPT AND $_table AND !test_espace_prive()
	AND !_AJAX # crayons
	) {
		$_table = inserer_attribut('<div class="encart"></div>',
			'rel', $_table)
			.'<script type="text/javascript"><!--
			$("div.encart").html($("div.encart").attr("rel")).attr("rel","");
			--></script>';
		$_RETOUR_TDM = '<script type="text/javascript"><!--
		document.write("'.str_replace('"', '\\"', $_RETOUR_TDM).'");
		--></script>';
	}

	return $affiche_table ?
		$_table :
		(((TableMatieres_BalisePresente() OR !strlen(trim($_table))) ? //calcul :)
			'' : $_table."\n\n").
		str_replace('@@RETOUR_TDM@@', $_RETOUR_TDM, $texte));
}

/**
 * Balise #TABLE_MATIERES
 * Affiche la table des matieres à l'endroit indique
 * A utiliser dans une boucle Articles
**/
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
		return balise_ENV($p, 'TableMatieres_SiNombreSuffisantIntertitres()');
	else
		return balise_ENV_dist($p, 'TableMatieres_SiNombreSuffisantIntertitres()');
	return $p;
}

?>
