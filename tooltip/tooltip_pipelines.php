<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function tooltip_insert_head_css($flux) {
	$flux .= '<link rel="stylesheet" type="text/css" href="'.direction_css(find_in_path('css/tooltip.css')).'" media="all" />'."\n";
	return $flux;
}

function tooltip_insert_head($flux) {
	$config = @unserialize($GLOBALS['meta']['tooltip']);
	if (!is_array($config))
		$config = array();
	if(isset($config['selecteur']) && strlen($config['selecteur']) > 0){
		$flux .=
			'<script type="text/javascript">/* <![CDATA[ */
				var tooltip_init=function(){$("'.$config['selecteur'].'").tooltip();}
				$(document).ready(function(){
					tooltip_init();
				});
				onAjaxLoad(tooltip_init);
			/* ]]> */</script>
			';
	}
	return $flux;
}

function tooltip_jquery_plugins($plugins){
	$plugins[] = 'lib/bgiframe.js';
	$plugins[] = 'lib/delegate.js';
	$plugins[] = 'lib/dimensions.js';
	$plugins[] = 'demo/chili-1.7.pack.js';
	$plugins[] = 'js/tooltip.js';
	
	return $plugins;
}
?>