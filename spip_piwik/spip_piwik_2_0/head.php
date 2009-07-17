<?php


function piwik_insert_head($flux){
	$id_piwik=lire_config('piwik/idPiwik');
	$url_piwik=lire_config('piwik/UrlPiwik');
	
	if($url_piwik && $id_piwik){
		$flux .= '
		<script type="text/javascript">
		var pkBaseURL = (("https:" == document.location.protocol) ? "https://'.$url_piwik.'" : "http://'.$url_piwik.'"); document.write(unescape("%3Cscript src=\'" + pkBaseURL + "piwik.js\' type=\'text/javascript\'%3E%3C/script%3E"));
		</script>
		<script type="text/javascript">
		<!--
		piwik_action_name = \'\';
		piwik_idsite = '.$id_piwik.';
		piwik_url = pkBaseURL + "piwik.php";
		piwik_log(piwik_action_name, piwik_idsite, piwik_url);
		//-->
		</script>';
	}		
	return $flux;
}

?>
