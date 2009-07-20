<?php

function woopra_insert_head($flux) {
	$script_woopra = <<<EOF
<!-- Woopra Code Start -->
<script type="text/javascript">
    var _wh = ((document.location.protocol=='https:') ? "https://sec1.woopra.com" : "http://static.woopra.com");
    document.write(unescape("%3Cscript src='" + _wh + "/js/woopra.js' type='text/javascript'%3E%3C/script%3E"));
</script>
<!-- Woopra Code End -->
EOF;
	return $flux.$script_woopra;
}

?>