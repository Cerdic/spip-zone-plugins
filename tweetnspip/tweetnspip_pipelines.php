<?php

function tweetnspip_insert_head($flux){
	include_spip("inc/filtres");
	// Initialisation des valeurs de config
	$config = @unserialize($GLOBALS['meta']['tweetnspip']);
	if (!is_array($config))
		$config = array();
	$config = array_merge(array(
		'username' => 'MrWaz',
		'numtweets' => '5',
		'loadertext' => 'Charge les tweets...',
		'slidein' => 'true',
		'showheading' => 'true',
		'headingtext' => 'Derniers tweets',
		'showprofilelink' => 'true'
	), $config);
	// Insertion des librairies js
	$flux .='<script src="'.url_absolue(find_in_path('javascript/jquery.twitter.js')).'" type="text/javascript"></script>';
	
	//
	$flux .='<script type="text/javascript">
        <!--//--><![CDATA[//><!--
        $(document).ready(function() {
                $("#twitter").getTwitter({
                        userName: "'.$config['username'].'",
                        numTweets: '.$config['numtweets'].',
                        loaderText: "'.$config['loadertext'].'",
                        slideIn: '.$config['slidein'].',
                        showHeading: '.$config['showheading'].',
                        headingText: "'.$config['headingtext'].'",
                        showProfileLink: '.$config['showprofilelink'].'
                        });
                });
        //--><!]]>
	</script>';
	// Inclusion des styles propres a tweetnspip
	$flux .='<link rel="stylesheet" href="'.url_absolue(find_in_path('styles/jquery.twitter.css')).'" media="all" type="text/css" />';
	return $flux;
}

?>
