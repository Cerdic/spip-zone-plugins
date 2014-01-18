<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Insertion du lien infographies_data sur infographies
 *
 * @param array $flux
 * @return array
 */
function infographies_affiche_milieu($flux){
	// si on est sur une page ou il faut inserer les mots cles...
	if ($en_cours = trouver_objet_exec($flux['args']['exec'])
		AND $en_cours['edition']!==true // page visu
		AND $type = $en_cours['type']
		AND $id_table_objet = $en_cours['id_table_objet']
		AND ($id = intval($flux['args'][$id_table_objet]))
		AND $type == 'infographie'){
		$texte = recuperer_fond(
				'prive/objets/editer/liens',
				array(
					'table_source'=>'infographies_datas',
					'objet'=>$type,
					'id_objet'=>$id,
				)
		);
		if ($p=strpos($flux['data'],"<!--affiche_milieu-->"))
			$flux['data'] = substr_replace($flux['data'],$texte,$p,0);
		else
			$flux['data'] .= $texte;
	}
	return $flux;
}


/**
 * Pipeline afficher_complement_objet
 * afficher le portfolio et ajout de document sur les fiches objet
 * sur lesquelles les medias ont ete activees
 * Pour les articles, on ajoute toujours !
 * 
 * @param  $flux
 * @return
 */
function infographies_afficher_complement_objet($flux){
	if ($type = $flux['args']['type']
		AND $id = intval($flux['args']['id'])
		AND $type == 'infographies_data'){
		$flux['data'] .= recuperer_fond('prive/objets/contenu/infographies_donnees',array_merge($_GET,array('id_infographies_data'=>$id)),array('ajax'=>true));
	}
	return $flux;
}

/**
 * Pipeline header_prive
 * 
 * On réduit les embed dans les tables
 * @param  $flux
 * @return
 */
function infographies_header_prive($flux){
	$flux .= "<script type='text/javascript'>"
		."var oembed_resize = function(){\n"
			."jQuery('table iframe').each(function(){\n"
				."var width = jQuery(this).width();\n"
				."$(this).width('100%');\n"
				."var ratio = jQuery(this).parents('td').width()/width;\n"
				."$(this).height(jQuery(this).height()*ratio);\n"
			."});"
		."}\n"
		."onAjaxLoad(oembed_resize);\n"
		."jQuery(document).ready(function(){\n"
			."oembed_resize();\n"
		."});</script>";
	return $flux;
}
?>