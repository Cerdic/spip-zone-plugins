<?php

/**
 * Insertion dans le pipeline insert_head
 *
 * @param object $flux
 * @return
 */
function piwik_insert_head($flux){
	if(lire_config('piwik/mode_insertion') == 'pipeline'){
		$options['type'] = 'public';
		$flux .= piwik_head_js($options);
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
		if(is_array(lire_config('piwik/restreindre_statut_prive'))){
			$options['statuts_restreints'] = lire_config('piwik/restreindre_statut_prive');
		}
		if(is_array(lire_config('piwik/restreindre_auteurs_prive'))){
			$options['auteurs_restreints'] = lire_config('piwik/restreindre_auteurs_prive');
		}
		$options['type'] = 'prive';
		$flux .= piwik_head_js($options);
	}
	return $flux;
}

/**
 * La fonction de génération du code du tracker javascript
 *
 * @param object $options [optional]
 * @return
 */
function piwik_head_js($options=array()){
	$config = lire_config('piwik',array());
	$id_piwik = $config['idpiwik'];
	$url_piwik = $config['urlpiwik'];
	$afficher_js = true;

	$ret = '';

	if($url_piwik && $id_piwik){
		if($options['statut_restreint']){
			$statut = $GLOBALS['visiteur_session']['statut'];
			$id_auteur = $GLOBALS['visiteur_session']['id_auteur'];
			if(in_array($statut,$options['statuts_restreints'])){
				if(in_array($id_auteur,$options['auteurs_restreints'])){
					$afficher_js = false;
				}
			};
		}

		if($afficher_js){
			$ret .= '
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

	return $ret;
}
?>
