<?php

function ThickBox1_insert_head($flux){

// on ajoute la class thickbox aux liens de type="image/xxx"

// TODO: ne charger thickbox.js et thickbox.css que si 
// jQuery("a.thickbox,a[@type='image/jpeg'],...").size() > 0)

$flux .=

'
<script src="'.url_absolue(find_in_path('thickbox.js')).'" type="text/javascript"></script>'
.
'<link rel="stylesheet" href="'.url_absolue(find_in_path('thickbox.css')).'" type="text/css" media="projection, screen" />

<script type="text/javascript"><!--
// Inside the function "this" will be "document" when called by ready() 
// and "the ajaxed element" when called because of onAjaxLoad 
var init_f = function() {
	if (jQuery("a.thickbox,a[@type=\'image/jpeg\'],a[@type=\'image/png\'],a[@type=\'image/gif\']",this).addClass("thickbox").size()) {
		TB_chemin_animation = "'.url_absolue(find_in_path('circle_animation.gif')).'";
		TB_chemin_close = "'.url_absolue(find_in_path('close.gif')).'";
		TB_chemin_css = "'.url_absolue(find_in_path('thickbox.css')).'";
		TB_init(this);
	};
}
//onAjaxLoad is defined in private area only
if(typeof onAjaxLoad == "function") onAjaxLoad(init_f);
jQuery(init_f);
// --></script>';

	return $flux;
}

?>
