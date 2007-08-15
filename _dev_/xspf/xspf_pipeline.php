<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

// ajoute les css et js necessaires dans les pages adequates
function xspf_header_prive($texte) {
	global $auteur_session, $spip_display, $spip_lang;
	if (_request('cfg') == 'xspf'){
		$tooltipcss = find_in_path('javascript/jquery.tooltip.css');
		$farbcss = url_absolue_css(direction_css(find_in_path('css/farbtastic.css')));
		
		$bgiframejs = find_in_path('javascript/jquery.bgiframe.js');
		$dimensionjs = find_in_path('javascript/jquery.dimensions.pack.js');
		$tooltipjs = find_in_path('javascript/jquery.tooltip.pack.js');
		$farbjs = find_in_path('javascript/farbtastic.js');
				
		$texte.= "
		
			<link rel='stylesheet' type='text/css' href='$tooltipcss' />\n
			<link rel='stylesheet' type='text/css' href='$farbcss' />\n
			<script type='text/javascript' src='$bgiframejs'></script>\n
			<script type='text/javascript' src='$dimensionjs'></script>\n
			<script type='text/javascript' src='$tooltipjs'></script>\n
			<script type='text/javascript' src='$farbjs'></script>" . "\n";

		$texte.= "
			<script type='text/javascript'>
				$(document).ready(function() {
					$('a').Tooltip({
						track: true,
						delay: 0,
						showURL: false,
						showBody: ' - '
					});
				});
	
			$(document).ready(function() {
				$('.picker').css('opacity', 0.25);
				var selected;
				$('.colorwell')
					.each(function () { 
						var pick = $(this).parent().children('.picker');
						$.farbtastic(pick).linkTo(this);$(this).css('opacity', 0.75);})
				
					.focus(function() {
						if (selected) {
							$(selected).css('opacity', 0.75).removeClass('colorwell-selected');
						}
						var pick = $(this).parent().children('.picker');
						$.farbtastic(pick).linkTo(this);
						pick.css('opacity', 1);
						$(selected = this).css('opacity', 1).addClass('colorwell-selected');
					})
					.blur(function() {
						var pick = $(this).parent().children('.picker');
						pick.css('opacity', 0.25);
						$(selected = this).css('opacity', 0.75).removeClass('colorwell-selected');
					});
				$('#container-1').tabs();
			});
			</script>\n";
	}
	return $texte;
}
?>

