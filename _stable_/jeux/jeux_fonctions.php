<?php
#---------------------------------------------------#
#  Plugin  : Jeux                                   #
#  Auteur  : Patrice Vanneufville, 2006             #
#  Gestion des scores : Maieul Rouquette, 2007      #
#  Contact : patrice�.!vanneufville�@!laposte�.!net #
#  Licence : GPL                                    #
#--------------------------------------------------------------------------#
#  Documentation : http://www.spip-contrib.net/Des-jeux-dans-vos-articles  #
#--------------------------------------------------------------------------#

include_spip('base/jeux_tables');

function boucle_JEUX($id_boucle, &$boucles) {
	$boucle = &$boucles[$id_boucle];
	$id_table = $boucle->id_table;
	$mstatut = $id_table .'.statut';
	// non requis sous 1.93
	if(!defined('_SPIP19300')) $boucle->from[] =  "spip_jeux AS $id_table";
//	if (!($boucle->modificateur['criteres']['statut']))
//		{$boucle->where[] = array("'='", "'$mstatut'", "'\\'publie\\''");}
	return calculer_boucle($id_boucle, $boucles);
}

// non requis sous 1.93
if(!defined('_SPIP19300')) {
	function boucle_JEUX_RESULTATS($id_boucle, &$boucles) {
		$boucle = &$boucles[$id_boucle];
		$id_table = $boucle->id_table;
		$boucle->from[] =  "spip_jeux_resultats AS $id_table";
		return calculer_boucle($id_boucle, $boucles);
	}
	if(!function_exists('balise_AUTORISER_dist')) {
		function balise_AUTORISER_dist($p) {
			$_code = array();
			$p->descr['session'] = true; // faire un cache par session
			$n=1; while ($_v = interprete_argument_balise($n++,$p)) $_code[] = $_v;
			$p->code = '(include_spip("inc/autoriser")&&autoriser(' . join(', ',$_code).')?" ":"")';
			$p->interdire_scripts = false;
			return $p;
		}
	}
}

// filtre retournant un lien cliquable si $nb!=0, sinon un simple tiret
function jeux_lien_jeu($nb='0', $exec='', $id_jeu=0) {
	$lien = generer_url_ecrire($exec,'id_jeu='.$id_jeu);
	return $nb=='0'?'-':"<a href='$lien'>$nb</a>";
}
// filtre qui evite d'afficher le resultat obtenu par certains plugins
// grace aux espions : <div title='PLUGIN-DEBUT'></div> et <div title='PLUGIN-FIN'></div>
// ou : <div title='PLUGIN-DEBUT-#xxxx'></div> et <div title='PLUGIN-FIN-#xxxx'></div>
//	 ou xxxx est le numero d'identification du plugin.
if (!function_exists("pas_de_plugin")) {	
 function pas_de_plugin($texte){
 	$texte = preg_replace(",<div[^<]+['\"]JEUX-HEAD-#[0-9]+[^>]+></div>,", '', $texte);
	return preg_replace(",<div[^<]+['\"]PLUGIN-DEBUT(-#[0-9]*)?.*<[^<]+['\"]PLUGIN-FIN\\1?[^>]+></div>,UmsS", '', $texte);
 }
}

// filtre qui retire le code source des jeux du texte original
function pas_de_balise_jeux($texte) {
	return preg_replace(",<jeux>.*?</jeux>,UimsS", '', $texte);
}

// aide le Couteau Suisse a calculer la balise #INTRODUCTION
$GLOBALS['cs_introduire'][] = 'pas_de_balise_jeux';

// ajoute un identifiant dans le formulaire, correspondant au jeu
// ce filtre doit agir sur #CONTENU
function ajoute_id_jeu($texte, $id_jeu) {
	$texte = str_replace('</form>', "<input type='hidden' name='id_jeu' value='".$id_jeu."' />\n</form>", $texte);
	return $texte;
}

// renvoie le titre du jeu que l'on peut trouver grace au separateur [titre]
function titre_jeu($texte) {
	include_spip('jeux_utils');
	return jeux_trouver_titre_public($texte);
}

function balise_TITRE_PUBLIC_dist($p) {
	$texte = champ_sql('contenu', $p);
	$p->code = "titre_jeu($texte)";
	return $p;
}
/*
function balise_CONTENU_dist($p) {
	$id = champ_sql('id_jeu', $p);
	$texte = champ_sql('contenu', $p);
	$p->code = "ajoute_id_jeu($texte, $id)";
	return $p;
}
*/

include_spip('public/interfaces');
global $table_des_traitements;
// TITRE_PUBLIC est un TITRE :
if (!isset($table_des_traitements['TITRE_PUBLIC']))
	$table_des_traitements['TITRE_PUBLIC'] = $table_des_traitements['TITRE'];

?>