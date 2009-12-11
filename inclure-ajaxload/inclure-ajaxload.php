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

	if (_request('var_no_ajax'))
		return call_user_func_array('recuperer_fond', $args);

	$cle = md5(serialize($args));
	$ajax = entites_html(encoder_contexte_ajax($args[1]));
	$alt = entites_html(sinon($args[1]['ajaxloadalt'],$args[1]['fond']));
	$message = $args[1]['ajaxload'];
	$searching = sinon($args[1]['ajaxsearching'],
		"<img src='".find_in_path('images/searching.gif')."' alt='$alt' />");

	$url = parametre_url(self(), 'var_no_ajax', 1);


	return
	"<div class='includeajax'>
	<a href='$url' rel=\"$ajax\">$searching</a>
	$message
	</div>
";
}

function INCLUREAJAXLOAD_affichage_final($page) {
	if ($GLOBALS['html']
	AND strpos($page, "class='includeajax'")
	AND $a = strpos($page, "</head>")) {
		$script = "
<script type='text/javascript'>
	$(function() {
		$('.includeajax').each(function() {
			var me = $(this);
			var env = $('a', this).attr('rel');
			if (env) {
				$('a', this).attr('href','#');
				$.post(
					window.location.href,
					{ var_ajax: 'recuperer', var_ajax_env: env },
					function(c) { me.html(c); }
				);
			}
		});
	});
</script>
";
		$page = substr_replace($page, $script, $a, 0);
	}
	return $page;
}

?>
