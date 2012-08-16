<?php
/**
 * Fichier des autorisations spécifique au plugin
 *
 * @plugin FACD pour SPIP
 * @author b_b
 * @author kent1 (http://www.kent1.info - kent1@arscenic.info)
 * @license GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Déclarer l'utilisation du pipeline
 * Cela évite de recalculer les pipeline tout le temps
 */
function facd_autoriser(){}

/**
 * Fonction d'autorisation de relance de conversion en erreur
 * Seules les personnes suivantes peuvent relancer l'encodage :
 * -* Les personnes qui ont mis en ligne le document (id_auteur dans spip_facd_conversions)
 * -* Les personnes autorisées à configurer le site
 * 
 * @param string $faire
 * @param string $type
 * @param int $id
 * @param array $qui
 * @param array $opt
 */
function autoriser_relancerconversion_facd_dist($faire, $type, $id, $qui, $opt){
	$id_auteur = sql_getfetsel('id_auteur','spip_facd_conversions','id_facd_conversion='.intval($id));
	return ($qui['id_auteur'] == $id_auteur) OR autoriser('configurer','','',$qui,$opt);
}
?>