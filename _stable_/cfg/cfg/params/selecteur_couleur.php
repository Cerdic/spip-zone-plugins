<?php

/*
 * Plugin CFG pour SPIP
 * (c) toggg, marcimat, dF 2008, distribue sous licence GNU/GPL
 * Documentation et contact: http://www.spip-contrib.net/
 * 
 * Patch de compatibilité avec classe cfg_couleur, OBSOLETE (utilisez la classe palette) 
 */


function cfg_charger_param_selecteur_couleur($valeur, &$cfg){
	// si provient d'un CVT, on met inline, sinon dans head
	$ou = ($cfg->depuis_cvt) ? 'inline':'head';
	// si le plugin Palette est installé, on patche
	if (is_dir(find_in_path(_DIR_PLUGIN_PALETTE))) {
		$cfg->param[$ou] .= "
<style>
.colorpicker {position: relative;}
</style>
<script type='text/javascript'>
//<![CDATA[
jQuery(document).ready(function() {
	jQuery('input.cfg_couleur').each(function() {
		jQuery(this).addClass('palette');
		jQuery(this).removeClass('cfg_couleur');
	});
	init_palette(); // relancer la palette pour prendre en compte les changements precedents (et pour les cas ajax)
});
//]]>
</script>
";
	}
}
?>
