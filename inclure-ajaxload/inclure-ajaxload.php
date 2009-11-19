<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

// Evolution de #INCLURE pour inclusions ajaxload
// #INCLURE{fond=xxx,....ajax,ajaxload} le fait
function balise_INCLURE($p) {
	$f = balise_INCLURE_dist($p);

	if (false !== strpos($f->code, "'ajaxload'"))
		$f->code = preg_replace('/recuperer_fond/', 'recuperer_fond_ajax',
		$f->code, 1);

	return $f;
}

// cree un appel ahah vers ce recuperer_fond
function recuperer_fond_ajax() {
	$args = func_get_args();
	$cle = md5(serialize($args));
	$ajax = urlencode(encoder_contexte_ajax($args[1]));
	$url = "?var_ajax=recuperer\x26var_ajax_env=$ajax";
	$alt = entites_html(sinon($args[1]['ajaxloadalt'],$args[1]['fond']));
	$message = $args[1]['ajaxload'];

	return 
	"<div class='includeajax'>
	<a href='$url'><img src='prive/images/searching.gif' alt='$alt' /></a>
	$message
	</div>
	<script type='text/javascript'>
	</script>
	";
}

?>
