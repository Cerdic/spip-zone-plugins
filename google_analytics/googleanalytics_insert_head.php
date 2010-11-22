<?php


function googleanalytics_insert_head($flux){
	$id_google = lire_config('googleanalytics/idGoogle');
	if (!$id_google || $id_google == '_' || $id_google == 'UA-xxxxxx') {
		return $flux;
	}
	else {

	$flux .= '
<script type="text/javascript">
	var _gaq = _gaq || [];
	_gaq.push(["_setAccount", "UA-823028-18"]);
	_gaq.push(["_trackPageview"]);

	(function() {
		var ga = document.createElement("script"); ga.type = "text/javascript"; ga.async = true;
		ga.src = ("https:" == document.location.protocol ? "https://ssl" : "http://www") + ".google-analytics.com/ga.js";
		var s = document.getElementsByTagName("script")[0]; s.parentNode.insertBefore(ga, s);
	})();
</script>'."\n";

		return $flux;
	}
}

?>
