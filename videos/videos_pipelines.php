<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function videos_insert_head($flux){
	include_spip('inc/config');
	$variables = generer_url_public('videos_variables.js');
	$css = find_in_path('theme/css/videos.css');
	$flux .="
<!-- Variables de configuration pour le plugin Vidéo(s) -->
<script type='text/javascript'>var CONFIG_WMODE = '".lire_config('videos/wmode','opaque')."';</script>\n".
'<link rel="stylesheet" href="'.direction_css($css).'" type="text/css" media="all" />'.
"<!-- // Vidéo(s) -->"."\n";
	return $flux;
}

function videos_jquery_plugins($scripts){
	$scripts[] = "lib/html5media-1.1.4/api/html5media.min.js";
	return $scripts;
}

function videos_affiche_gauche($flux) {

	include_spip('inc/autoriser');
		
	// Si c'est un article en édition ou un article dans le privé, on propose le formulaire, si l'article n'existe pas encore, on ne fait rien
	if(($flux["args"]["exec"] == 'articles_edit' || $flux["args"]["exec"] == 'articles' || $flux["args"]["exec"] == 'article') && $flux["args"]["id_article"] != ''){
		$type_objet = 'article';
		$id_type_objet = 'id_article';
		$id_objet   = $flux["args"]["id_article"];
	}
	// Si c'est une rubrique, on ne fait rien
	elseif($flux["args"]["exec"] == 'naviguer' && $flux["args"]["id_rubrique"] != ''){
		$type_objet = 'rubrique';
		$id_type_objet = 'id_rubrique';
		$id_objet   = $flux["args"]["id_rubrique"];
	}
	// Sinon, et bien on ne fait rien non plus
	else{
		return $flux;
	}

	$fond = 'prive/contenu/videos_affiche_boite';
	$flux["data"] .= recuperer_fond($fond,array(
			'id_objet' => $id_objet,
			'objet' => $type_objet
	));

	return $flux;
}

?>
