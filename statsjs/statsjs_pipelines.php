<?php

function statsjs_affichage_final($page) {

	if (!$GLOBALS['html']) return $page;

	// Identification de l'element
	foreach($GLOBALS['contexte'] as $k => &$v) {
		if (preg_match(',^id_(\w+)$,S', $k, $r)
		AND ($id = intval($v))>0
		) {
			$identifier = $r[1].$id;
		}
	}

	if (isset($identifier)) {
		$page = preg_replace(',</head>,i',
			"\n".'<meta name="SPIP.identifier" content="'.$identifier.'" />'."\n".'\0',
			$page, 1);
	}

	if (!strpos($page, '<!-- MOUCHARD STATS SPIP -->'))
		$page = preg_replace(',</body>,i', statsjs_mouchard()."\n".'\0', $page, 1);


	return $page;
}


// Remplacer la balise spip_cron, qui ne sert plus a rien si on active les stats
if (!function_exists('balise_SPIP_CRON')) {
	function balise_SPIP_CRON ($p) {
		$p->code = _q("");
		$p->interdire_scripts = false;
		return $p;
	}
}


function statsjs_mouchard() {
	$urljs = generer_url_public('stats.js');
	$urlhit = generer_url_action('stats');
	$mouchard = "
<!-- MOUCHARD STATS SPIP -->
<script type='text/javascript'>
$(function(){setTimeout(function(){
  $.ajax({url: '${urljs}', dataType: 'script', cache: true,
    success: function() {
      var obj = $('meta[name=SPIP.identifier]').attr('content')||'';
      try {
        var piwikTracker = Piwik.getTracker('${urlhit}\\x26obj='+obj);
        piwikTracker.setDocumentTitle(document.title);
        piwikTracker.trackPageView();
      } catch( err ) {}
    }
  });
}, 100);});
</script>
<noscript><p><img src='${urlhit}' style='border:0' alt='' /></p></noscript>
<!-- / MOUCHARD -->\n";

	return $mouchard;
}