<?php

/**
 * Insertion dans le pipeline insert_head
 * 
 * @param object $flux
 * @return 
 */
function piwik_insert_head($flux){
	$id_piwik=lire_config('piwik/idpiwik');
	$url_piwik=lire_config('piwik/urlpiwik');
	
	if($url_piwik && $id_piwik){
		if(lire_config('piwik/restreindre_statut')){
			$statut = $GLOBALS['visiteur_session']['statut'];
			if(in_array($statut,lire_config('piwik/restreindre_statut')));
		}
		$flux .= '
		<script type="text/javascript">
		var pkBaseURL = (("https:" == document.location.protocol) ? "https://'.$url_piwik.'/" : "http://'.$url_piwik.'/");
			document.write(unescape("%3Cscript src=\'" + pkBaseURL + "piwik.js\' type=\'text/javascript\'%3E%3C/script%3E"));
		</script>
		<script type="text/javascript">
		try {
		var piwikTracker = Piwik.getTracker(pkBaseURL + "piwik.php", '.$id_piwik.');
		piwikTracker.trackPageView();
		piwikTracker.enableLinkTracking();
		} catch( err ) {}
		</script>';
	}		
	return $flux;
}

/**
 * Insertion dans le pipeline header_prive
 * 
 * @param object $flux
 * @return 
 */
function piwik_header_prive($flux){
	if(lire_config('piwik/piwik_prive')){
		$id_piwik=lire_config('piwik/idpiwik');
		$url_piwik=lire_config('piwik/urlpiwik');
		
		if($url_piwik && $id_piwik){
			$flux .= '
			<script type="text/javascript">
			var pkBaseURL = (("https:" == document.location.protocol) ? "https://'.$url_piwik.'/" : "http://'.$url_piwik.'/");
				document.write(unescape("%3Cscript src=\'" + pkBaseURL + "piwik.js\' type=\'text/javascript\'%3E%3C/script%3E"));
			</script>
			<script type="text/javascript">
			try {
			var piwikTracker = Piwik.getTracker(pkBaseURL + "piwik.php", '.$id_piwik.');
			piwikTracker.trackPageView();
			piwikTracker.enableLinkTracking();
			} catch( err ) {}
			</script>';
		}
	}
	return $flux;
}
?>
