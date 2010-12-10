<?php
#---------------------------------------------------#
#  Plugin  : Jeux                                   #
#  Auteur  : Patrice Vanneufville, 2006             #
#  Gestion des scores : Maieul Rouquette, 2007      #
#  Contact : patrice¡.!vanneufville¡@!laposte¡.!net #
#  Licence : GPL                                    #
#--------------------------------------------------------------------------#
#  Documentation : http://www.spip-contrib.net/Des-jeux-dans-vos-articles  #
#--------------------------------------------------------------------------#

include_spip('base/jeux_tables');

function boucle_JEUX($id_boucle, &$boucles) {
	$boucle = &$boucles[$id_boucle];
	$id_table = $boucle->id_table;
	$mstatut = $id_table .'.statut';

	return calculer_boucle($id_boucle, $boucles);
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
	if(strpos($texte, _JEUX_DEBUT)===false) return $texte;
	return preg_replace(','.preg_quote(_JEUX_DEBUT).'.*?'.preg_quote(_JEUX_FIN).',UimsS', '', $texte);
}

// aide le Couteau Suisse a calculer la balise #INTRODUCTION
$GLOBALS['cs_introduire'][] = 'pas_de_balise_jeux';

// ajoute l'id_jeu du jeu a sa config interne et traite le jeu grace a propre()
// ce filtre doit agir sur #CONTENU*
function traite_contenu_jeu($texte, $id_jeu) {
	return propre(str_replace(_JEUX_FIN, "[config]id_jeu=$id_jeu"._JEUX_FIN, $texte));
}

// renvoie le titre public du jeu que l'on peut trouver grace au separateur [titre]
function titre_jeu($texte) {
	include_spip('jeux_utils');
	return jeux_trouver_titre_public($texte);
}

// renvoie le type du jeu
function type_jeu($texte) {
	include_spip('jeux_utils');
	return jeux_trouver_nom($texte);
}

/* Quelques balises "raccourcis" */

// extraction du titre public, equivalent a : #CONTENU*|titre_jeu
function balise_TITRE_PUBLIC_dist($p) {
	$texte = champ_sql('contenu', $p);
	$p->code = "titre_jeu($texte)";
	return $p;
}

// interpretation du jeu, equivalent a : #CONTENU*|traite_contenu_jeu{#ID_JEU}
function balise_CONTENU_PROPRE_dist($p) {
	$id = champ_sql('id_jeu', $p);
	$texte = champ_sql('contenu', $p);
	$p->code = "traite_contenu_jeu($texte, $id)";
	return $p;
}

// traduction longue du type de resultat
function balise_TYPE_RESULTAT_LONG_dist($p) {
	$type = champ_sql('type_resultat', $p);
	$p->code = "_T('jeux:resultat2_'.$type)";
	return $p;
}

// traduction courte du type de resultat
function balise_TYPE_RESULTAT_COURT_dist($p) {
	$type = champ_sql('type_resultat', $p);
	$p->code = "_T('jeux:resultat_'.$type)";
	return $p;
}

include_spip('public/interfaces');
global $table_des_traitements;
// TITRE_PUBLIC est un TITRE :
if (!isset($table_des_traitements['TITRE_PUBLIC']))
	$table_des_traitements['TITRE_PUBLIC'] = $table_des_traitements['TITRE'];

?>