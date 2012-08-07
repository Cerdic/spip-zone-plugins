<?php 

// inc/fmp3_pipeline_header_prive.php

// $LastChangedRevision$
// $LastChangedBy$
// $LastChangedDate$

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/fmp3_api_globales');
include_spip('inc/fmp3_api_prive');

function fmp3_header_prive ($flux) {

	$exec = _request('exec');

	if(in_array($exec, array(
							 'fmp3_configure',
							 
							 // page d'edition identite site
							 'configuration', // SPIP <= 2
							 'configurer_identite', // SPIP 3
							 
							 // page d'edition rubrique
							 'naviguer', // SPIP <= 2
							 'rubrique', // SPIP 3
							 
							 // page d'edition article
							 'articles', // SPIP <= 2
							 'article' // SPIP 3
							 )
				)
	   )
	{
		
		$flux .= ''
			. "\n\n<!-- " . _FMP3_PREFIX . " -->\n"
			. "<link rel='stylesheet' type='text/css' href='".url_absolue(find_in_path('css/fmp3_prive.css'))."' />\n"
			. "<!--[if IE]>\n"
			. "<link rel='stylesheet' type='text/css' href='".url_absolue(find_in_path('css/fmp3_prive_ie.css'))."' />\n"
			. "<![endif]-->\n"
			;
		
		if($exec == "fmp3_configure") {
			
		}
		else {
			
			fmp3_log ( 'header exec: '.$exec );
			
			$preferences_default = unserialize(_FMP3_PREFERENCES_DEFAULT);
			$preferences_meta = fmp3_get_all_preferences();
			
			foreach($preferences_default as $key => $value)
			{
				if(!isset($preferences_meta[$key])) 
				{
					$preferences_meta[$key] = $value;
				}
			}
			
			$action_arg = "";
	
			$importer_scripts = FALSE;
						
			$f = charger_fonction('fmp3_prive', 'inc');
			
			switch($exec)
			{
				case 'fmp3_configure':
					$importer_scripts = TRUE;
					break;
				case 'configuration':
				case 'configurer_identite':
					$importer_scripts = TRUE;
					$action_arg = 'site,0';
					break;
				case 'naviguer': // rubriques ?
				case 'rubrique':
					if($id_rubrique = _request('id_rubrique'))
					{
						$importer_scripts = TRUE;
						$action_arg = 'rub,' . $id_rubrique;
					}
					break;
				case 'articles':
					if(
					   !fmp3_spip_version_3() &&
					   ($id_article = _request('id_article'))
					  )
					{
						$importer_scripts = TRUE;
						$action_arg = 'art,' . $id_article;
					}
					break;
				case 'article':
					if($id_article = _request('id_article'))
					{
						$importer_scripts = TRUE;
						$action_arg = 'art,' . $id_article;
					}
					break;
			}
			
			if ( $importer_scripts )
			{
				if ( fmp3_spip_version_3 () )
				{
					$flux .= "\n<script type='text/javascript'>
					//<![CDATA[
						var fmp3_spip_version_3 = 1;
						//]]>
					</script>\n";	
				}
				$flux .= "<script src='".url_absolue(find_in_path('javascript/jquery.fmp3.js'))."' type='text/javascript'></script>\n";
				$flux .= "<script src='".url_absolue(find_in_path('javascript/fmp3_prive.js'))."' type='text/javascript'></script>\n";
			}
			
			/**
			 * l'url pour ajax est placé en var. Sera appelé par js + tard
			 */
			if ( !empty( $action_arg ) ) 
			{
				$action = "fmp3_boite_son";
				$url = generer_action_auteur($action, $action_arg);
				// $.ajax n'aime pas &amp;
				$url = preg_replace("/&amp;/", "&", $url);
		
				$flux .= "\n<script type='text/javascript'>\n//<![CDATA[\n var fmp3_boite_son_url = \"" . $url . "\";\n//]]>\n</script>\n";					
			}
			
			if ($exec == "fmp3_configure")
			{
				$default = "";
				
				foreach($preferences_default as $key => $value)
				{
					if($key == "img") continue;
					$default .= $key.":\"".$value."\",";
				}
				$default = "var fmp3_default={".rtrim($default, ",")."};";
				
				$flux .= ""
					;
			}
		}
		$flux .= ""
			. "<!-- / " . _FMP3_PREFIX . " -->\n\n"
			;
	}
	
	return ($flux);
}

?>