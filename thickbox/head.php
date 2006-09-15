<?php

function ThickBox_insert_head($flux){

// on ajoute la class thickbox aux liens de type="image/xxx"

// TODO: ne charger thickbox.js et thickbox.css que si 
// $("a.thickbox,a[@type='image/jpeg'],...").size() > 0)

$flux .=

'
<script src=\''.url_absolue(find_in_path('thickbox.js')).'\' type=\'text/javascript\'></script>
<link rel="stylesheet" href="'.url_absolue(find_in_path('thickbox.css')).'" type="text/css" media="projection, screen" />

<script type="text/javascript"><!--
$(document).ready(function() {
	alert(10);
	if ($("a.thickbox,a[@type=\'image/jpeg\'],a[@type=\'image/png\'],a[@type=\'image/gif\']").addClass("thickbox").size()) {
TB_chemin_animation = "'.url_absolue(find_in_path('circle_animation.gif')).'";
TB_chemin_close = "'.url_absolue(find_in_path('close.gif')).'";
TB_chemin_css = "'.url_absolue(find_in_path('thickbox.css')).'";
	TB_init();
}
} );
// --></script>';

	return $flux;
}

?>