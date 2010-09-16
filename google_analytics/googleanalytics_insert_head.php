<?php


function googleanalytics_insert_head($flux){
$id_google = lire_config('googleanalytics/idGoogle');
if (!$id_google || $id_google == '_' || $id_google == 'UA-xxxxxx') {
		return $flux;
	}
	else {

$flux .= '
<script type="text/javascript">
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src=\'" + gaJsHost + "google-analytics.com/ga.js\' type=\'text/javascript\'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
var pageTracker = _gat._getTracker("'.$id_google.'");
pageTracker._initData();
pageTracker._trackPageview();
</script>';
return $flux;
}
}

?>
