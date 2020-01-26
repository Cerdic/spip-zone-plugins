<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Insertion de la css du plugin dans les pages publiques
 *
 * @param $flux
 * @return mixed
 */
function bigfoot_insert_head_css($flux){
	$flux .="\n".'<link rel="stylesheet" href="'. find_in_path('css/littlefoot.css') .'" />';
	return $flux;
}


/**
 * Insertion du script du plugin dans les pages publiques
 *
 * @param $flux
 * @return mixed
 */
function bigfoot_insert_head($flux){
	$flux .="\n".'<script type="text/javascript" src="'. find_in_path('javascript/littlefoot.js') .'"></script>';
	$voir_note = _T('bigfoot:voir_note');
	$flux .= <<<EOH
<script type="text/javascript">/* <![CDATA[ */
(function($) {
	var bigfoot_init = function() {
		if (jQuery('div.notes').is(':hidden')) {
			return true; // pas a faire ou deja fait.
		}
		littlefoot.default({
			anchorPattern: /(nb\d+(-\d+)?(footnote|appendix))/gi,
			anchorParentSelector: "span",
			footnoteSelector: "div",
			buttonTemplate: '<span class="littlefoot-footnote__container"><button aria-controls="fncontent:<%= id %>" aria-expanded="false" aria-label="Footnote <%= number %>" class="littlefoot-footnote__button littlefoot-footnote__button__number" data-footnote-button-id="<%= id %>" data-footnote-number="<%= number %>" id="<%= reference %>" rel="footnote" title="$voir_note <%= number %>"><svg viewbox="0 0 31 6" preserveAspectRatio="xMidYMid"><circle r="3" cx="3" cy="3" fill="white"></circle><circle r="3" cx="15" cy="3" fill="white"></circle><circle r="3" cx="27" cy="3" fill="white"></circle></svg></button></span>'
		});
		jQuery('div.notes').hide();
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
