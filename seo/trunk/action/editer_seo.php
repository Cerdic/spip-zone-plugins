<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * @param $objet
 * @param $id_objet
 * @param string $prefixe
 * @return string
 */
function action_editer_seo_dist($objet, $id_objet, $prefixe=""){
	include_spip('inc/autoriser');
	$err = "";

	// si id_article n'est pas un nombre, c'est une creation 
	// mais on verifie qu'on a toutes les donnees qu'il faut.
	if ($objet
		AND $id_objet
	  AND autoriser('modifier', $objet, $id_objet)){

		$meta_tags = array('title', 'description', 'author', 'keywords', 'copyright', 'robots');

		$existing = array();
		$rows = sql_allfetsel("*","spip_seo", "id_objet=" . intval($id_objet) . " AND objet=" . sql_quote($objet));
		foreach ($rows as $row){
			$existing[$row['meta_name']] = $row['meta_content'];
		}

		$dels = array();
		$inss = array();
		foreach ($meta_tags as $tag){
			$value = _request($prefixe.$tag);
			if (is_null($value) OR !strlen($value)){
				if (isset($existing[$tag]))
					$dels[] = $tag;
			}
			else {
				if (!isset($existing[$tag])){
					$inss[] = array('objet'=>$objet,'id_objet'=>$id_objet,'meta_name'=>$tag,'meta_content'=>$value);
				}
				elseif($value!==$existing[$tag]){
					sql_updateq("spip_seo",array('meta_content'=>$value),"id_objet=" . intval($id_objet) . " AND objet=" . sql_quote($objet)." AND meta_name=".sql_quote($tag));
				}
			}
			if (($value = _request($tag)) && (strlen($value)>0)){
				sql_insertq('spip_seo', array('id_objet' => $id_objet, 'objet' => $objet, 'meta_name' => $tag, 'meta_content' => $value));
			}
		}
		if (count($dels))
			sql_delete("spip_seo","id_objet=" . intval($id_objet) . " AND objet=" . sql_quote($objet). " AND ".sql_in("meta_name",$dels));
		if (count($inss))
			sql_insertq_multi("spip_seo",$inss);

		return "";
	}

	return "Erreur : acces interdit";
}

?>
