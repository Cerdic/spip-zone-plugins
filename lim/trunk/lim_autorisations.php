<?php
/**
 * Définit les autorisations du plugin Lim
 *
 * @plugin     Lim
 * @copyright  2015
 * @author     Pierre Miquel
 * @licence    GNU/GPL
 * @package    SPIP\Lim\Autorisations
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Fonction d'appel pour le pipeline
 * @pipeline autoriser */
function lim_autoriser(){
	return $qui['statut'] == '0minirezo';
}


/**************************************************************/
/************* DESACTIVER DES LOGOS PAR CONTENUS *************/

/* Exceptions historiques */
function autoriser_auteur_iconifier($faire,$type,$id,$qui,$opt){
	if (in_array(table_objet_sql($type),explode(',',lire_config('lim_logos'))))
		return false;
	else return (($id == $qui['id_auteur']) OR
			(($qui['statut'] == '0minirezo') AND !$qui['restreint']));
}

function autoriser_mot_iconifier($faire,$type,$id,$qui,$opt){
	if (in_array(table_objet_sql($type),explode(',',lire_config('lim_logos'))))
		return false;
	return (($qui['statut'] == '0minirezo') AND !$qui['restreint']);
}

function autoriser_groupemots_iconifier($faire,$type,$id,$qui,$opt){
	if (in_array(table_objet_sql($type),explode(',',lire_config('lim_logos'))))
		return false;
	else return (($qui['statut'] == '0minirezo') AND !$qui['restreint']);
}

function autoriser_rubrique_iconifier($faire,$type,$id,$qui,$opt){
	if (in_array(table_objet_sql($type),explode(',',lire_config('lim_logos'))))
		return false;
	return autoriser('publierdans', 'rubrique', $id, $qui, $opt);
}


// iconifier ou non les objets
function autoriser_iconifier($faire, $type, $id, $qui, $opt) {
	if (in_array(table_objet_sql($type),explode(',',lire_config('lim_logos'))))
		return false;
	// par defaut, on a le droit d'iconifier si on a le droit de modifier
	else return autoriser('modifier', $type, $id, $qui, $opt);
}

/**********************************************************/
/************* RESTRICTION DANS LES RUBRIQUES *************/


if (!function_exists('autoriser_rubrique_creerarticledans')) {
	function autoriser_rubrique_creerarticledans($faire, $type, $id, $qui, $opt) {
		$quelles_rubriques = lire_config('lim_rubriques/article');
		is_null($quelles_rubriques) ? $lim_rub = true : $lim_rub = !in_array($id,$quelles_rubriques);
		
		return
			$id
			AND $lim_rub
			AND autoriser('voir','rubrique',$id)
			AND autoriser('creer', 'article');
	}
}

if (!function_exists('autoriser_rubrique_creerbrevedans')) {
	function autoriser_rubrique_creerbrevedans($faire, $type, $id, $qui, $opt) {
		$r = sql_fetsel("id_parent", "spip_rubriques", "id_rubrique=".intval($id));
		$quelles_rubriques = lire_config('lim_rubriques/breve');
		is_null($quelles_rubriques) ? $lim_rub = true : $lim_rub = !in_array($id,$quelles_rubriques);

		return
			$id
			AND $lim_rub
			AND ($r['id_parent']==0)
			AND ($GLOBALS['meta']["activer_breves"]!="non")
			AND autoriser('voir','rubrique',$id);
	}
}

if (!function_exists('autoriser_rubrique_creersitedans')) {
	function autoriser_rubrique_creersitedans($faire, $type, $id, $qui, $opt) {
		$quelles_rubriques = lire_config('lim_rubriques/site');
		is_null($quelles_rubriques) ? $lim_rub = true : $lim_rub = !in_array($id,$quelles_rubriques);
		return
			$id
			AND $lim_rub
			AND autoriser('voir','rubrique',$id)
			AND $GLOBALS['meta']['activer_sites'] != 'non'
			AND (
				$qui['statut']=='0minirezo'
				OR ($GLOBALS['meta']["proposer_sites"] >=
				    ($qui['statut']=='1comite' ? 1 : 2)));
	}
}

?>