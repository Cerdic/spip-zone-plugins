<?php
/*	*********************************************************************
	*
	* Copyright (c) 2008
	* Xavier Burot
	* fichier : genea_date.php
	*
	* Ce programme est un logiciel libre distribue sous licence GNU/GPL.
	*
	*********************************************************************
*/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('base/genea_base');

global
	$cal_rev,
	$cal_heb;

// Mois revolutionnaires francais
	$cal_rev = array('VEND','BRUM','FRIM','NIVO','PLUV','VENT','GERM','FLOR','PRAI','MESS','THER','FRUC','COMP');
// Mois hebraiques
	$cal_heb = array('TSH','CSH','KSL','TVT','SHV','ADR','ADS','NSN IYR','SVN','TMZ', 'AAV','ELL');

	$date_enr = array(
		'debut' => '0000-00-00 00:00:00',
		'fin' => '0000-00-00 00:00:00',
		'calendrier' => '',
		'precision' => '');

// -- Ecriture de la date dans la base de donnees -------------------------
function genea_ecrire_date($date_evt){
	return serialize($date_evt);
}

// -- Lecture de la date dans la base de donnees --------------------------
function genea_lire_date(){
	return unserialize($date_evt);
}


//
// GESTION DES DATES ET DES DIFFERENTS CALENDRIERS
//

// -- Affichage de la date si le visiteur est authoriser a la voir --------
function genea_affiche_date($date){
}


// -- Recupere et verifie le droit a l'affichage des dates ----------------
function genea_date_evt($id_individu, $type_evt, $type_liens='ap', $filtres=NULL){

	$date_evt = '';  // Initialisation de la variable de retour

	// lecture des droits d'acces a la donnee en fonction du statut
	include_spip('inc/genea_autoriser');
	$auteur_ok = autoriser('voirfiche', 'genea', $id_individus);

	if (($auteur_ok) AND ($id_individu) AND ($type_evt)) {
		$q="SELECT evt.date_evt FROM spip_genea_evt AS evt, spip_genea_participes AS liens  WHERE (liens.id_individu=$id_individu) AND (liens.type_liens='$type_liens') AND (liens.id_genea_evt=evt.id_genea_evt) AND (evt.type_evt='$type_evt')";
		$res=spip_query($q);
		if ($row=spip_fetch_array($res)) {
			$date_ret = normaliser_date($row['date_evt']);
		}

		// Faire verification des droits de visualisation des dates
		// si elles sont inferieures a 100 ans. Ne pas afficher, sauf
		// pour les redacteurs et les administrateurs autorises.
		$maintenant = getdate();
		$centans = intval($maintenant["year"])-100;
		if ($centans>=date("Y", strtotime($date_ret)) OR $auteur_ok) $date_evt = convert_date($date_ret, 'DE');
	}
	return vider_date($date_evt);
}

// -- Affiche la date de deces  -----------------------------------------
function balise_DATE_DECES($p){
	$p->code = "genea_date_evt(".champ_sql('id_individu',$p).", 'deat')";
	$p->interdire_scripts = true;
	return $p;
}

// -- Affiche la date de naissance --------------------------------------
function balise_DATE_NAISSANCE($p){
	$p->code = "genea_date_evt(".champ_sql('id_individu',$p).", 'birt')";
	$p->interdire_scripts = true;
	return $p;
}

// -- Affiche la date d'un evenement ------------------------------------
function balise_DATE_EVT($p){
	$p->code = "genea_date_evt(".champ_sql('id_individu',$p).", '".champ_sql('type_evt',$p)."')";
	$p->interdire_scripts = true;
	return $p;
}

//
// API de conversion des dates afin de permettre la prise en compte de
// calendrier non gregorien et de les traduire.
//

// surcharge possible de convert_date(), sinon convert_date_dist()
if (!function_exists('convert_date')) {
	function convert_date() {
		$args = func_get_args();
		return call_user_func_array('convert_date_dist', $args);
	}
}

function convert_date_dist($date_evt, $faire='DE', $type='gregorian', $filtres = NULL) {

	if ($type='') $type = 'gregorian';

	// Chercher une fonction d'autorisation explicite
	if (
	// 1. Sous la forme "convert_date_type_faire"
		(
		$type
		AND $f = 'convert_date_'.$type.'_'.$faire
		AND (function_exists($f) OR function_exists($f.='_dist'))
		)
	// 2. Sinon autorisation generique
		OR (
			$f = 'convert_date_defaut'
			AND (function_exists($f) OR function_exists($f.='_dist'))
			)
		)
		$a = $f($date_evt, $faire, $type, $filtres);

	return $a;
}

// si pas de convertion possible alors donner date telque.
function convert_date_defaut_dist($date_evt, $faire, $type, $filtres){
	return $date_evt;
}
?>