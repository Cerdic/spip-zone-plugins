<?php

function ThickBox_insert_head($flux){

	$flux .='
<style type="text/css" media="all">
@import "'.url_absolue(find_in_path('thickbox.css')).'";
</style>

<script type="text/javascript"><!--
TB_chemin_animation = "'.url_absolue(find_in_path('circle_animation.gif')).'";
TB_chemin_close = "'.url_absolue(find_in_path('close.gif')).'";
// --></script>
<script src="'.url_absolue(find_in_path('thickbox.js')).'" type="text/javascript"></script>
';

	return $flux;
}

?>