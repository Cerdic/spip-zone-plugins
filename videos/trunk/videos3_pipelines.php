<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function videos_insert_head($flux){
	include_spip('inc/config');
	$flux .= videos_insert_head_css($flux); // au cas ou il n'est pas implemente 
	$flux .="
<!-- Variables de configuration pour le plugin Vidéo(s) -->
<script type='text/javascript'>var CONFIG_WMODE = '".lire_config('videos/wmode','opaque')."';</script>\n".
"<!-- // Vidéo(s) -->"."\n";
	return $flux;
}

function videos_insert_head_css($flux){
	static $done = false;
	if (!$done) { 
		$done = true; 
		include_spip('inc/config');
		$css = find_in_path('theme/css/videos.css');
		$flux .="
<!-- CSS pour le plugin Vidéo(s) -->".
'<link rel="stylesheet" href="'.direction_css($css).'" type="text/css" media="all" />'.
"<!-- // Vidéo(s) -->"."\n";
	}
	return $flux;
}

function videos_jquery_plugins($scripts){
	$scripts[] = "lib/html5media-1.1.5/api/html5media.min.js";
	return $scripts;
}

function videos_affiche_gauche($flux) {

	include_spip('inc/autoriser');

	if ($en_cours = trouver_objet_exec($flux['args']['exec'])
		AND $en_cours['edition']!==false // page edition uniquement
		AND $type = $en_cours['type']
		AND $id_table_objet = $en_cours['id_table_objet']
		// id non defini sur les formulaires de nouveaux objets
		AND (isset($flux['args'][$id_table_objet]) and $id = intval($flux['args'][$id_table_objet])
			// et justement dans ce cas, on met un identifiant negatif
		    OR $id = 0-$GLOBALS['visiteur_session']['id_auteur'])
	  AND autoriser('joindredocument',$type,$id)){
		$flux['data'] .= recuperer_fond('prive/contenu/videos_affiche_boite',array('objet'=>$type,'id_objet'=>$id));
	}

	return $flux;
}

?>
