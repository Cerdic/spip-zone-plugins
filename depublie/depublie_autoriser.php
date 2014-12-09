<?php
/**
 * Déclarations des autorisations
 *
 * @author     Anne-lise Martenot (http://elastick.net)
 * @plugin     Depublie
 * @copyright  2014
 * @licence    GNU/GPL
 * @package    SPIP\Depublie\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

function depublie_autoriser(){}

/**
 * Autorisation d'afficher le champ de dépublication sur un article
 */
function autoriser_depublierafficher($faire,$type,$id,$qui,$opt){
	if(!function_exists('lire_config')){
		include_spip('inc/config');
	}
	if(lire_config('depublie/rubrique_depublie') != '' || lire_config('depublie/secteur_depublie') !=''){
		$table = table_objet_sql($type);
	// pour l'instant, depublie ne s'applique qu'aux articles
	// on évite un select id_rubrique, id_secteur sur les objets qui n'en ont pas
	if ($table != "spip_articles")
		return false;

    $id_table_objet = id_table_objet($type);
		$infos_rubrique = sql_fetsel('id_rubrique,id_secteur',$table,"$id_table_objet = ".intval($id));
		if(in_array($infos_rubrique['id_rubrique'],explode(',',lire_config('depublie/rubrique_depublie'))) || in_array($infos_rubrique['id_secteur'],explode(',',lire_config('depublie/secteur_depublie'))))
			return autoriser('modifier',$type,$id,$qui,$opt) OR autoriser('dater',$type,$id,$qui,$opt);
	}
	else{
		return autoriser('modifier',$type,$id,$qui,$opt) OR autoriser('dater',$type,$id,$qui,$opt);
	}
	return false;
}

?>
