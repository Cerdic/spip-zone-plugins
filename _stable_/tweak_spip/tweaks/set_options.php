<?php
function set_options_header_prive($flux) {
	return $flux. <<<JAVASCRIPT
<script type="text/javascript"><!--
// des que le DOM est pret...
$(document).ready(function(){
 if ($('a.icone26').length) {
	$("#displayfond").hide();
	$("a.icone26[@href*=set_options]").hide();
 }
});
//--></script>
JAVASCRIPT;
}
?>
