<?php
/**
 * Pipelines du plugin Tabelesorter
 * 
 * @plugin     Tablesorter
 * @licence    GNU/GPL v3
 * @package    SPIP\Tablesorter\Pipelines
 */
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Insertion dans le pipeline insert_head_css (SPIP)
 * 
 * Ajout de la feuille de style dans le head
 * 
 * @pipeline insert_head_css
 * @param string $flux Le contenu de la balise #INSERT_HEAD_CSS
 * @return string $flux Le contenu complété de la balise
 */
function tablesorter_insert_head_css($flux){
	static $done = false;
	if (!$done) {
		$done = true;
		$flux .= '<link rel="stylesheet" href="'.direction_css(find_in_path('css/tablesorter.css')).'" type="text/css" />';
	}

	return $flux;
}

/**
 * Insertion dans le pipeline insert_head (SPIP)
 * 
 * Ajout de la librairie js tablesorter et du script d'init 
 * dans le head de l'espace public
 * Ajout également, pour les anciennes versions de SPIP de la CSS
 * dans le head
 * 
 * @pipeline insert_head
 * @param string $flux Le contenu de la balise #INSERT_HEAD
 * @return string $flux Le contenu complété de la balise
 */
function tablesorter_insert_head($flux){
	$flux .='<script src="'.find_in_path('javascript/jquery.tablesorter.js').'" type="text/javascript"></script>';
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

/**
 * Insertion dans le pipeline header_prive (SPIP)
 * 
 * Ajout de la librairie js tablesorter, du script d'init et de la CSS
 * dans le head de l'espace privé
 * 
 * @pipeline header_prive
 * @param string $flux Le contenu du head de l'espace privé
 * @return string $flux Le contenu complété du head
 */
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