<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * @return array
 */
function action_editer_seo_dist(){
	include_spip('inc/autoriser');
	$err = "";

	// si id_article n'est pas un nombre, c'est une creation 
	// mais on verifie qu'on a toutes les donnees qu'il faut.
	if (!$id_objet = _request('id_objet') OR !$objet = _request('objet')){
		$err = "Pas d'objet ou id_objet";
	} else {
		if (!autoriser('modifier', $objet, $id_objet)){
			$err = "Error auth :(";
		} else {
			$meta_tags = array('title', 'description', 'author', 'keywords', 'copyright', 'robots');
			sql_delete("spip_seo", "id_objet = " . intval($id_objet) . " AND objet =" . sql_quote($objet));
			foreach ($meta_tags as $tag){
				if (($value = _request($tag)) && (strlen($value)>0)){
					sql_insertq('spip_seo', array('id_objet' => $id_objet, 'objet' => $objet, 'meta_name' => $tag, 'meta_content' => $value));
				}
			}
		}
	}

	if ($err)
		spip_log("echec editeur seo: $err", _LOG_ERREUR);

	return array(array($objet, $id_objet), $err);
}

?>
