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

	$args[1]["fond"] = $args[0];

	if (_request('var_no_ajax')
	OR _request('var_mode') == 'inclure')
		return call_user_func_array('recuperer_fond', $args);

	$cle = md5(serialize($args));
	$ajax = entites_html(encoder_contexte_ajax($args[1]));
	

	$alt = entites_html(sinon($args[1]['ajaxloadalt'],$args[1]['fond']));
	$message = $args[1]['ajaxload'];
	$searching = sinon($args[1]['ajaxsearching'],
		"<img src='".find_in_path('images/searching.gif')."' alt='$alt' />");

	// Le lien mène à l'action de pose du cookie no_js
	$url = "spip.php?action=ia_nojs&amp;retour=".urlencode(parametre_url(self(),'no_js','oui'));

	if (isset($args[2]['ajax']) AND $args[2]['ajax']=='1') 
		$class_ajax = " ajaxbloc env-$ajax";

	$ret =
		"<div class='includeajax$class_ajax'><a href=\"$url\" rel=\"$ajax\">$searching</a></div>";
		
	return $ret;
}

function remettre_fond_ajax($matches) {
	$url = $matches[2];
	$c = $matches[3];
	$c = decoder_contexte_ajax($c);

	$page = recuperer_fond($c["fond"],$c,array('trim'=>false));
	
	return $page;
}

function INCLUREAJAXLOAD_affichage_final($page) {

	// Si le visiteur est un robot de moteur de recherche,
	// reconstituer les pages completes
	if(_IS_BOT || $_COOKIE["no_js"] == "no_js" ) {
		include_spip("inc/filtres");
		include_spip("public/assembler");
		$page = preg_replace_callback(",(<div class='includeajax[^\']*'><a href=\"(.*)\" rel=\"(.*)\">.*</a></div>),msU", "remettre_fond_ajax", $page);
	}

	return $page;
}

function INCLUREAJAXLOAD_insert_head($flux) {
	$flux .= "\n<script src=\"".find_in_path('javascript/inclure-ajaxload.js')."\" type=\"text/javascript\"></script>";

$flux = '<?php if ($_COOKIE["no_js"] != "no_js" && !_IS_BOT) { ?>
<script type="text/javascript"><!--
document.write("<\/script><script>/*");
//--></script>
<meta http-equiv="refresh" content="0; url=spip.php?action=ia_nojs&amp;retour=<?php echo urlencode(parametre_url(self(),\'no_js\',\'oui\'));?>" />
<meta http-equiv="pragma" content="no-cache" />
<meta http-equiv="cache-control" content="no-cache" />
<meta http-equiv="expires" content="-1" />
<script type="text/javascript">/* */</script>
<?php } else { ?>
<script type="text/javascript"><!--
	document.cookie = "no_js=; expires=Thu, 01-Jan-70 00:00:01 GMT;";
--></script>
<?php } ?>'.$flux;

	return $flux;
}



?>
