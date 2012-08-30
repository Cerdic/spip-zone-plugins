<?php

if (!defined("_ECRIRE_INC_VERSION")) return;	#securite

// Pour par exemple limiter l'action de ancres_douces au div #contenu ou aux divs de classe .ancres_douces,
// inserez dans votre config/mes_options.php les lignes :
// define ('ANCRES_DOUCES_CONTEXTE','#contenu'); 
// ou 
// define ('ANCRES_DOUCES_CONTEXTE','.ancres_douces'); 

function ancresdouces_insert_head($flux){

if (!defined('CONTEXTE_ANCRES_DOUCES'))
	$appel_ancres_douces='jQuery';
else $appel_ancres_douces='$(\''.CONTEXTE_ANCRES_DOUCES.'\')';

// Inclusion des scripts jquery
$flux .= '<script src="'.url_absolue(find_in_path("js/jquery.scrollto.js")).'" type="text/javascript"></script>';
$flux .= '<script src="'.url_absolue(find_in_path("js/jquery.localscroll.js")).'" type="text/javascript"></script>';

// Code d'init
$flux .= '<script type="text/javascript">/* <![CDATA[ */
function ancre_douce_init() {if(typeof jQuery.localScroll=="function")'.$appel_ancres_douces.'.localScroll({hash:true,onAfter:function( anchor, settings ){
			jQuery(anchor).focus();
		}});}
if(window.jQuery)jQuery(document).ready(function() {
	ancre_douce_init();
	onAjaxLoad(ancre_douce_init);
});
/* ]]> */</script>';

return $flux;
}
?>