<?php
/*
 * Plugin Porte Plume pour SPIP 2
 * Licence GPL
 * Auteur Matthieu Marcillaud
 */

function porte_plume_insert_head($flux){
	
	$js = chemin('javascript/jquery.markitup_pour_spip.js');
	$js_previsu = chemin('javascript/jquery.previsu_spip.js');
	$js_settings = generer_url_public('porte_plume.js');
	$css = chemin('css/barre_outils.css');
	$css_icones = generer_url_public('barre_outils_icones.css');

	$flux .= "<link rel='stylesheet' type='text/css' media='all' href='$css' />\n"
		. "<link rel='stylesheet' type='text/css' media='all' href='$css_icones' />\n"
		. "<script type='text/javascript' src='$js'></script>\n"
		. "<script type='text/javascript' src='$js_previsu'></script>\n"
		. "<script type='text/javascript' src='$js_settings'></script>\n";
		
	$lang = $GLOBALS['spip_lang'];
	$preview = url_absolue(generer_url_public('preview'));
	$tEditer = _T('barre_outils:editer');
	$tVoir = _T('barre_outils:voir');
	$flux .= <<<EOF
		<script type="text/javascript">
<!--
jQuery(document).ready(function()	{
	// ajoute les barres d'outils markitup
	function barrebouilles(){
		// si c'est un appel de previsu markitup, faut pas relancer
		// on attrappe donc uniquement les textarea qui n'ont pas deja la classe markItUpEditor
		jQuery('.formulaire_forum textarea[name=texte]:not(.markItUpEditor)').markItUp(barre_outils_forum,{lang:'$lang'});
		jQuery('textarea.textarea_forum:not(.markItUpEditor)').markItUp(barre_outils_forum,{lang:'$lang'});
		jQuery('.formulaire_spip textarea[name=texte]:not(.markItUpEditor)')
			.markItUp(barre_outils_edition,{lang:'$lang'})
			.previsu_spip({
				previewParserPath:'$preview',
				textEditer:'$tEditer',
				textVoir:'$tVoir'});
	}
	barrebouilles();
	onAjaxLoad(barrebouilles);

});
-->		
		</script>
EOF;

	return $flux;
}


?>
