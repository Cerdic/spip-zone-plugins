<?php
function icone_visiter_header_prive($flux) {
global $spip_lang_left,$spip_lang_right;
	$chercher_logo = charger_fonction('chercher_logo', 'inc');
	if ($r = $chercher_logo(0, 'id_syndic', 'on'))  {
		list($fid, $dir, $nom, $format) = $r;
		include_spip('inc/filtres_images');
		$r = image_reduire("<img src='$fid' alt='' style='margin:0;' />", 46, 46);
	} else return $flux;
	return $flux. <<<JAVASCRIPT
<script type="text/javascript"><!--
// des que le DOM est pret...
if (window.jQuery) jQuery(document).ready(function(){
	jQuery("span.icon_fond:last").hide()
		.after("<span style='height:48px; margin:0;'>$r</span>");
});
//--></script>
JAVASCRIPT;
}
?>