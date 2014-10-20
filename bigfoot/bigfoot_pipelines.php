<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Insertion de la css du plugin dans les pages publiques
 *
 * @param $flux
 * @return mixed
 */
function bigfoot_insert_head_css($flux){
	$flux .="\n".'<link rel="stylesheet" href="'. find_in_path('css/bigfoot-number.css') .'" />';
	return $flux;
}


/**
 * Insertion du script du plugin dans les pages publiques
 *
 * @param $flux
 * @return mixed
 */
function bigfoot_insert_head($flux){
	$flux .="\n".'<script type="text/javascript" src="'. find_in_path('javascript/bigfoot.js') .'"></script>';
	$flux .= <<<EOH
<script type="text/javascript">/* <![CDATA[ */
(function($) {
	var bigfoot_init = function() {
		jQuery.bigfoot({
			anchorPattern: /[nb\d](fn|footnote|note)/gi,
			anchorParentTagname: "span",
			footnoteParentClass: "spip_note_ref",
			footnoteTagname: "div"
		});
	};
	$(function(){
		bigfoot_init();
		onAjaxLoad(bigfoot_init);
	});
})(jQuery);
/* ]]> */</script>

EOH;
	return $flux;
}

/**
 * Insertion du script du plugin dans les pages de l'espace privé
 * @param $flux
 * @return mixed
 */
function bigfoot_header_prive($flux){
	$flux .= bigfoot_insert_head_css('');
	$flux .= bigfoot_insert_head('');
	return $flux;
}

?>
