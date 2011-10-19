<?php
function fader_insert_head($flux)
{
	$flux .= '<link type="text/css" rel="stylesheet" href="'.url_absolue(find_in_path('css/fader.css')).'" />
			<script type="text/javascript" src="'.url_absolue(find_in_path('lib/fader-plugin.js')).'"></script>'	
			;

return $flux;
}


