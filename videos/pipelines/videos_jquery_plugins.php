<?php
// <script src="'._DIR_PLUGIN_videos.'theme/js/videos.js.html'.'" type="text/javascript"></script>
/*
	TODO Tester si insertion est activée dans CONFIG, pas la peine d'insérer des scripts sur les pages qui contiennent pas de vidéos
*/
function videos_jquery_plugins($scripts){
	$scripts[] = "javascript/html5media/flowplayer.js";
	$scripts[] = "javascript/html5media/html5media.js";
    return $scripts;
}
