<?php
// insertion du Js de SnowStorm dans le <head> du document (#INSERT_HEAD)
function snowstorm_insert_head($flux) {
	return $flux.'<script type="text/javascript" src="'.find_in_path('script/snowstorm.js').'"></script>'."\n";
}
?>