<?php
if (!defined("_ECRIRE_INC_VERSION")) return; #securite

function balise_XITI($p) {
	return calculer_balise_dynamique($p, 'XITI', array());
}

function balise_XITI_stat($args, $filtres) {
	$page_xiti=$args[0];
	return array($page_xiti);
}

function balise_XITI_dyn($page_xiti) {
	lire_meta('xiti_config');
	$GLOBALS['xiti_config']=unserialize($GLOBALS['meta']['xiti_config']);
	/*$req=sql_select("id_xiti","spip_phpasso_config");	
	$ligne=sql_fetch($req);
	$id_xiti=$ligne['id_xiti'];*/
	
	$id_xiti=$GLOBALS['xiti_config']['id_xiti'];
	$logo_xiti=$GLOBALS['xiti_config']['logo_xiti'];
	$width=$GLOBALS['xiti_config']['width'];
	$height=$GLOBALS['xiti_config']['height'];
	
	if ($id_xiti!="") {
		echo '
			<a href="http://www.xiti.com/xiti.asp?s='.$id_xiti.'" title="WebAnalytics">
			<script type="text/javascript">
			<!--
			Xt_param = \'s='.$id_xiti.'&p='.$page_xiti.'\';
			try {Xt_r = top.document.referrer;}
			catch(e) {Xt_r = document.referrer; }
			Xt_h = new Date();
			Xt_i = \'<img width="'.$width.'" height="'.$height.'" border="0" alt="" \';
			Xt_i += \'src="http://logv21.xiti.com/'.$logo_xiti.'.xiti?\'+Xt_param;
			Xt_i += \'&hl=\'+Xt_h.getHours()+\'x\'+Xt_h.getMinutes()+\'x\'+Xt_h.getSeconds();
			if(parseFloat(navigator.appVersion)>=4)
			{Xt_s=screen;Xt_i+=\'&r=\'+Xt_s.width+\'x\'+Xt_s.height+\'x\'+Xt_s.pixelDepth+\'x\'+Xt_s.colorDepth;}
			document.write(Xt_i+\'&ref=\'+Xt_r.replace(/[<>"]/g, \'\').replace(/&/g, \'$\')+\'" title="Internet Audience">\');
			//-->
			</script>
			<noscript>
			Mesure d\'audience ROI statistique webanalytics par <img width="'.$width.'" height="'.$height.'" src="http://logv21.xiti.com/'.$logo_xiti.'.xiti?s='.$id_xiti.'&p='.$page_xiti.'" alt="WebAnalytics" />
			</noscript></a>
		';
	}
}
?>