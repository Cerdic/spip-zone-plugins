<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function videos_insert_head($flux){
	include_spip('inc/config');
	$css = find_in_path('theme/css/videos.css');
	$flux .="
<!-- Variables de configuration pour le plugin Vidéo(s) -->
<script type='text/javascript'>var CONFIG_WMODE = '".lire_config('videos/wmode','opaque')."';</script>\n".
'<link rel="stylesheet" href="'.direction_css($css).'" type="text/css" media="all" />'.
"<!-- // Vidéo(s) -->"."\n";
	return $flux;
}

function videos_jquery_plugins($scripts){
	$scripts[] = "lib/html5media-1.1.5/api/html5media.min.js";
	return $scripts;
}

function videos_affiche_gauche($flux) {

	include_spip('inc/autoriser');
		
	if (in_array($flux['args']['exec'],$GLOBALS['medias_exec_colonne_document'])
		AND $table = preg_replace(",_edit$,","",$flux['args']['exec'])
		AND $type = objet_type($table)
		AND $id_table_objet = id_table_objet($type)
		AND ($id = intval($flux['args'][$id_table_objet]) OR $id = 0-$GLOBALS['visiteur_session']['id_auteur'])
	  AND (autoriser('joindredocument',$type,$id))){
		$flux['data'] .= recuperer_fond('prive/contenu/videos_affiche_boite',array('objet'=>$type,'id_objet'=>$id));
	}
	
	return $flux;
}

?>
