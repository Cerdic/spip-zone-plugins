<?php
if (!defined("_ECRIRE_INC_VERSION")) return;
function videos_insert_head($flux){
	$flux .="<!-- HTML5 Média(s) -->\n".
"<link href='http://vjs.zencdn.net/c/video-js.css' rel='stylesheet' type='text/css' media='all' />\n".
"<script src='http://vjs.zencdn.net/c/video.js'></script>\n".
"<!-- // HTML5 Média(s) -->"."\n";
	return $flux;
}
