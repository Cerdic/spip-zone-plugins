<?php
function profilpublic_insert_head($flux)
{
	$flux .= '<link type="text/css" rel="stylesheet" href="'.url_absolue(find_in_path('css/profilpublic.css')).'" />
				<script type="text/javascript" src="'.url_absolue(find_in_path('lib/jquery.cookie.js')).'"></script>
				<script type="text/javascript" src="'.url_absolue(find_in_path('lib/igoogle-like.js')).'"></script>
				<script type="text/javascript" src="'.url_absolue(find_in_path('lib/fade-plugin.js')).'"></script>'	
			;

return $flux;
}

function profilpublic_jqueryui_forcer($scripts){
    $scripts[] = "jquery.ui.accordion";
	$scripts[] = "jquery.ui.sortable";
	$scripts[] = "jquery.ui.tabs";
	$scripts[] = "jquery.effects.core";
	$scripts[] = "jquery.effects.drop";
	$scripts[] = "jquery.effects.explode";
	$scripts[] = "jquery.effects.bounce";	
    return $scripts;
}
?>
