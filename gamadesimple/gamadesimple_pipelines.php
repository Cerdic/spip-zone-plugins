<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

function gamadesimple_config($public=null){
	include_spip("inc/filtres");
	$config = @unserialize($GLOBALS['meta']['gamadesimple_config']);
	if (!is_array($config))
		$config = array();
	$config = array_merge(array(
		'ua_code' => '',
		'subdomain' => 'non',
		'many_domains'=>'non',
		'display'=>'non',
		'campaign'=>'non',
	),$config);

	if ((is_null($public) AND test_espace_prive()) OR $public===false) {
		$config = array_merge($config,array(
			'ua_code' => '',
			'subdomain' => 'non',
			'many_domains'=>'oui',
			'display'=>'oui',
			'campaign'=>'oui',
		));
	}
	return $config;	
}

function gamadesimple_insert_head($flux) {
		$config = @unserialize($GLOBALS['meta']['gamadesimple']);
        $ua_code = $config['ua_code'];
        $subdomain = $config['subdomain'];
        $many_domain = $config['many_domains'];
        $display = $config['display'];
        $campaign = $config['campaign'];
         
		$gacode = "<!-- Google Analytics Tracking -->\n";
		if(strlen($ua_code)>=9){
			$gacode .= "<script type=\"text/javascript\">\n";
			$gacode .= "var _gaq = _gaq || [];\n";
			$gacode .= "_gaq.push(['_setAccount', '".$ua_code."']);\n";
			if($subdomain==1||$many_domain==1)
				$gacode .= "_gaq.push(['_setDomainName', '".$_SERVER['HTTP_HOST']."']);\n";
			if($many_domain==1)
				$gacode .= "_gaq.push(['_setAllowLinker', true]);\n";
			if($campaign==1)
				$gacode .= "_gaq.push(['_setCampNameKey', 'utm_name']);\n";
			$gacode .= "_gaq.push(['_trackPageview']);\n";
			$gacode .= "(function() {\n";
			$gacode .= "var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;\n";
			if($display==1){
				$gacode .= "ga.src = ('https:' == document.location.protocol ? 'https://' : 'http://') + 'stats.g.doubleclick.net/dc.js';\n";
			}else{
				$gacode .= "ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';\n";
			}
			$gacode .= "var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);\n";
			$gacode .= "})();\n";
			$gacode .= "</script>";	
		}else{
			$gacode .= "\n<!-- WARNING : Please go to GA Made Simple settings -->";
		
		}
   return $flux.$gacode;
}

