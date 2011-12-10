<?php
if (!defined("_ECRIRE_INC_VERSION")) return;
function tablesorter_insert_head_css($flux){
	static $done = false;
	if (!$done) {
		$done = true;
		$flux .= '<link rel="stylesheet" href="'.url_absolue(find_in_path('styles/tablesorter.css')).'" type="text/css" />';
	}

	return $flux;
}


function tablesorter_insert_head($flux){
	// Insertion des librairies js
	$flux .='<script src="'.url_absolue(find_in_path('scripts/jquery.tablesorter.js')).'" type="text/javascript"></script>';
	// Init de tablesorter
	$flux .='
	<script type="text/javascript">/* <![CDATA[ */
	(function($){
		$(function(){
			var tablesorter_init = function(){$("table.spip").not(".ss_tablesort").tablesorter();}
			tablesorter_init();
			onAjaxLoad(tablesorter_init);
		});
	})(jQuery);
	/* ]]> */</script>';
	
	$flux .= tablesorter_insert_head_css(''); // compat pour les vieux spip
	return $flux;
}

function tablesorter_header_prive($flux){
	// Insertion des librairies js
	$flux .='<script src="'.url_absolue(find_in_path('scripts/jquery.tablesorter.js')).'" type="text/javascript"></script>';
	// Inclusion des styles du plugin
	$flux .='<link rel="stylesheet" href="'.url_absolue(find_in_path('styles/tablesorter.css')).'" type="text/css" />';
	// Init de tablesorter
	$flux .='
	<script type="text/javascript">/* <![CDATA[ */
	(function($){
		$(function(){
			$("table.spip").tablesorter();
		});
	})(jQuery);
	/* ]]> */</script>';
	return $flux;
}
?>