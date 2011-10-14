<?php
if (!defined("_ECRIRE_INC_VERSION")) return;
function videos_insert_head($flux){
	$variables = generer_url_public('videos_variables.js');
	$css = find_in_path('theme/css/videos.css');
	$flux .="
<!-- Variables de configuration pour le plugin Vidéo(s) -->
<script type='text/javascript' src='$variables'></script>\n".
'<link rel="stylesheet" href="'.direction_css($css).'" type="text/css" media="all" />'.
"<!-- // Vidéo(s) -->"."\n";
	return $flux;
}
