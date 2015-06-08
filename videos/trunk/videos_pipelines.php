<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function videos_ieconfig_metas($table){
	$table['videos']['titre'] = _T('paquet-videos:videos_nom');
    $table['videos']['icone'] = 'prive/themes/spip/images/videos-16.png';
    $table['videos']['metas_serialize'] = 'videos';
	return $table;
}

function videos_insert_head($flux){
	include_spip('inc/config');
	$flux .="
<!-- Variables de configuration pour le plugin Vidéo(s) -->
<script type='text/javascript'>var CONFIG_WMODE = '".lire_config('videos/wmode','opaque')."';</script>\n".
"<!-- // Vidéo(s) -->"."\n";
	return $flux;
}

function videos_insert_head_css($flux){
	include_spip('inc/config');
	$css = find_in_path('theme/css/videos.css');
	$flux .="
<!-- CSS pour le plugin Vidéo(s) -->".
'<link rel="stylesheet" href="'.direction_css($css).'" type="text/css" media="all" />'.
"<!-- // Vidéo(s) -->"."\n";
	return $flux;
}

function videos_jquery_plugins($scripts){
	$scripts[] = "lib/html5media-1.1.5/api/html5media.min.js";
	return $scripts;
}

function videos_formulaire_fond($flux) {

    if ($flux['args']['form'] == 'joindre_document') {
		$videos = recuperer_fond(
            'prive/contenu/videos_affiche_boite',
            array(
                'objet' => $flux['args']['contexte']['objet'],
                'id_objet' => $flux['args']['contexte']['id_objet']
            )
        );
		// Injecter videos au dessus du formulaire joindre_document.
        $flux['data'] = $flux['data'].$videos;
	}

	return $flux;
}
?>
