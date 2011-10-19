<?php
function igooglelike_insert_head($flux)
{
	$flux .= '<link type="text/css" rel="stylesheet" href="'.url_absolue(find_in_path('css/fader.css')).'" />
			<script type="text/javascript" src="'.url_absolue(find_in_path('lib/fade-plugin.js')).'"></script>'	
			;

return $flux;
}


