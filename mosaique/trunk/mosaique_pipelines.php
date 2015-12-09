<?php


function mosaique_header_prive($flux) {
	return $flux .= '<link rel="stylesheet" type="text/css" href="'.find_in_path('css/mosaique_prive.css').'" media="all" />';
}

function mosaique_insert_head($flux){
	$flux .= '<link rel="stylesheet" type="text/css" href="'.find_in_path('css/mosaique.css').'" media="all" />'."\n";
	return $flux;
}

function mosaique_jqueryui_plugins($plugins){
	$plugins[] = "jquery.ui.sortable";
	return $plugins;
}

/**
 * (Presque) copier-coller honteux depuis le plugin-dist media...
 */
function mosaique_affiche_gauche($flux){
	if ($en_cours = trouver_objet_exec($flux['args']['exec'])
		AND $en_cours['edition']!==false // page edition uniquement
		AND $type = $en_cours['type']
		AND $id_table_objet = $en_cours['id_table_objet']
		// id non defini sur les formulaires de nouveaux objets
		AND (isset($flux['args'][$id_table_objet]) and $id = intval($flux['args'][$id_table_objet])
			// et justement dans ce cas, on met un identifiant negatif
			OR $id = 0-$GLOBALS['visiteur_session']['id_auteur'])
		AND autoriser('joindredocument',$type,$id)
		AND $type == 'article') // Articles seulement pour l'instant
	{
		$flux['data'] = recuperer_fond('prive/objets/editer/mosaique_colonne', array('objet'=>$type,'id_objet'=>$id)) . $flux['data'];
	}

	return $flux;
}
