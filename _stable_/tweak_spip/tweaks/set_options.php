<?php
function set_options_header_prive($flux) {
	return $flux. <<<JAVASCRIPT
<script type="text/javascript"><!--
// des que le DOM est pret...
$(document).ready(function(){
	 if ($('a.icone26').length) {
		$("#displayfond").hide();
		$.each($('a.icone26'), function(i, a) {
			if ($(a).attr('href').indexOf('set_options')>=0) {
				$(a).hide(); 
				return false;
			}
		});
	 }
});
//--></script>
JAVASCRIPT;
}
?>
