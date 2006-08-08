<?php

function ThickBox_insert_head($flux){

// on ajoute la class thickbox aux liens de type="image/xxx"

// TODO: ne charger thickbox.js et thickbox.css que si 
// $("a.thickbox,a[@type='image/jpeg'],...").size() > 0)

$flux .=

'
<script type="text/javascript"><!--
$(document).load(function() {
	if ($("a.thickbox,a[@type=\'image/jpeg\'],a[@type=\'image/png\'],a[@type=\'image/gif\']").addClass("thickbox").size()) {
TB_chemin_animation = "'.url_absolue(find_in_path('circle_animation.gif')).'";
TB_chemin_close = "'.url_absolue(find_in_path('close.gif')).'";
TB_chemin_css = "'.url_absolue(find_in_path('thickbox.css')).'";
}} );
// --></script>
<script src=\''.url_absolue(find_in_path('thickbox.js')).'\' type=\'text/javascript\'></script>
';

	return $flux;
}

?>