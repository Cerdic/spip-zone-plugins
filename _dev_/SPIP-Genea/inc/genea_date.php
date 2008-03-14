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
}

// -- Lecture de la date dans la base de donnees --------------------------
function genea_lire_date(){
	return '';
}


//
// GESTION DES DATES ET DES DIFFERENTS CALENDRIERS
//

// -- Affichage de la date si le visiteur est authoriser a la voir --------
function genea_affiche_date($date){
}


// -- Recupere et verifie le droit a l'affichage des dates ----------------
function genea_date_evt($id_individu, $type_evt, $fin=false, $second=false){
	global $table_prefix;

	// lecture des droits d'acces a la donnee en fonction du statut
	include_spip('inc/genea_autoriser');
	$auteur_ok = autoriser('voirfiche', 'genea', $id_individus);

	if (!$auteur_ok) return '';

	$date_evt = '';
	$date_ret = '';

	if ($id_individu) {
		$q="SELECT ";
		if ($fin==false) {
			if (($second==false)) $q.="date_debut";
			else $q.="date_debut2";
		}else{
			if (($second==false)) $q.="date_fin";
			else $q.="date_fin2";
		}
		$q.=" FROM ".$table_prefix."_genea_evt WHERE (id_individu=$id_individu) AND (type_evt='$type_evt')";
		$res=spip_query($q);
		if ($row=spip_fetch_array($res)) {
			if ($fin==false) {
				if (($second==false)) $date_evt=$row['date_debut'];
				else $date_evt=$row['date_debut2'];
			}else{
				if (($second==false)) $date_evt=$row['date_fin'];
				else $date_evt=$row['date_fin2'];
			}
		}
	}

	// Faire verification des droits de visualisation des dates
	// si elles sont inferieures a 100 ans. Ne pas afficher, sauf
	// pour les redacteurs et les administrateurs autorises.
	$maintenant = getdate();
	$centans = intval($maintenant["year"])-100;
	if ($centans>=date("Y", strtotime(normaliser_date($date_evt))) OR $auteur_ok) $date_ret = $date_evt;

	return vider_date(normaliser_date($date_ret));
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

function convert_date_dist($faire, $type='', $id=0, $qui = NULL, $opt = NULL) {
	static $restreint = array();

	// Qui ? auteur_session ?
	if ($qui === NULL)
		$qui = $GLOBALS['auteur_session']; // "" si pas connecte
	elseif (is_int($qui)) {
		$s = spip_query("SELECT * FROM spip_auteurs WHERE id_auteur=".$qui);
		$qui = spip_fetch_array($s);
	}

	// Admins restreints, les verifier ici (pas generique mais...)
	// Par convention $restreint est un array des rubriques autorisees
	// (y compris leurs sous-rubriques), ou 0 si admin complet
	if (is_array($qui)
	AND $qui['statut'] == '0minirezo'
	AND !isset($qui['restreint'])) {
		if (!isset($restreint[$qui['id_auteur']])) {
			include_spip('inc/auth'); # pour auth_rubrique
			$restreint[$qui['id_auteur']] = auth_rubrique($qui['id_auteur'], $qui['statut']);
		}
		$qui['restreint'] = $restreint[$qui['id_auteur']];
	}

	// Chercher une fonction d'autorisation explicite
	if (
	// 1. Sous la forme "convert_date_type_faire"
		(
		$type
		AND $f = 'convert_date_'.$type.'_'.$faire
		AND (function_exists($f) OR function_exists($f.='_dist'))
		)

	// 2. Sous la forme "convert_date_type"
	// ne pas tester si $type est vide
	OR (
		$type
		AND $f = 'convert_date_'.$type
		AND (function_exists($f) OR function_exists($f.='_dist'))
	)

	// 3. Sous la forme "convert_date_faire"
	OR (
		$f = 'convert_date_'.$faire
		AND (function_exists($f) OR function_exists($f.='_dist'))
	)

	// 4. Sinon autorisation generique
	OR (
		$f = 'convert_date_defaut'
		AND (function_exists($f) OR function_exists($f.='_dist'))
	)

	)
		$a = $f($faire,$type,intval($id),$qui,$opt);

	return $a;
}

// si pas de convertion possible alors donner date telque.
function convert_date_defaut_dist($date_evt){
	return $date_evt;
}

?>