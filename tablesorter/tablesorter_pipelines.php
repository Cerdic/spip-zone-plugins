<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function tablesorter_insert_head_css($flux){
	static $done = false;
	if (!$done) {
		$done = true;
		$flux .= '<link rel="stylesheet" href="'.direction_css(find_in_path('css/tablesorter.css')).'" type="text/css" />';
	}

	return $flux;
}


function tablesorter_insert_head($flux){
	// Insertion des librairies js
	$flux .='<script src="'.find_in_path('javascript/jquery.tablesorter.js').'" type="text/javascript"></script>';
	// Init de tablesorter
	$flux .='
	<script type="text/javascript">/* <![CDATA[ */
	(function($){
		$(function(){
			var tablesorter_init = function(){
				$("table.spip").not(".ss_tablesort").each(function(){
					var options = {};
					if($(this).find("th.ts_disabled").size() >= 1){
						options.headers = {};
						$(this).find("th").each(function(index,value){
							if($(this).is(".ts_disabled"))
								options.headers[index] = {sorter : false}; 
						});
					}
					$(this).tablesorter(options);
				});
			}
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
	$flux .='<script src="'.find_in_path('javascript/jquery.tablesorter.js').'" type="text/javascript"></script>';
	// Inclusion des styles du plugin
	$flux .='<link rel="stylesheet" href="'.direction_css(find_in_path('css/tablesorter.css')).'" type="text/css" />';
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