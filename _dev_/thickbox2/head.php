<?php

function ThickBox1_insert_head($flux){

// on ajoute la class thickbox aux liens de type="image/xxx"

// TODO: ne charger thickbox.js et thickbox.css que si 
// $("a.thickbox,a[@type='image/jpeg'],...").size() > 0)
if(!isset($GLOBALS["spip_pipeline"]["insert_js"]))

$flux = ThickBox1_header_prive($flux);

else

$flux .=
'
<link rel="stylesheet" href="'.url_absolue(find_in_path('thickbox.css')).'" type="text/css" media="projection, screen" />';

return $flux;
}

function ThickBox1_header_prive($flux) {

$flux .=

'
<script src=\''.url_absolue(find_in_path('javascript/thickbox.js')).'\' type=\'text/javascript\'></script>
<link rel="stylesheet" href="'.url_absolue(find_in_path('thickbox.css')).'" type="text/css" media="projection, screen" />
<script type="text/javascript"><!--
// Inside the function "this" will be "document" when called by ready() 
// and "the ajaxed element" when called because of onAjaxLoad 
var init_f = function() {
	if ($("a.thickbox,a[@type=\'image/jpeg\'],a[@type=\'image/png\'],a[@type=\'image/gif\']",this).addClass("thickbox").size()) {
		TB_chemin_animation = "'.url_absolue(find_in_path('circle_animation.gif')).'";
		TB_chemin_close = "'.url_absolue(find_in_path('close.gif')).'";
		TB_chemin_css = "'.url_absolue(find_in_path('thickbox.css')).'";
		TB_init(this);
	};
}
//onAjaxLoad is defined in private area only
if(typeof onAjaxLoad == "function") onAjaxLoad(init_f);
$(document).ready(init_f);
// --></script>';

return $flux;

}

function ThickBox1_insert_js($flux){
// on ajoute la class thickbox aux liens de type="image/xxx"

// TODO: ne charger thickbox.js et thickbox.css que si 
// $("a.thickbox,a[@type='image/jpeg'],...").size() > 0)

if($flux['type']=='fichier') 
  $flux["data"]["ThickBox1"] = "thickbox";

if($flux['type']=='inline')
  $flux["data"]["ThickBox1"] =
'
<script type="text/javascript"><!--
// Inside the function "this" will be "document" when called by ready() 
// and "the ajaxed element" when called because of onAjaxLoad 
var init_f = function() {
	if ($("a.thickbox,a[@type=\'image/jpeg\'],a[@type=\'image/png\'],a[@type=\'image/gif\']",this).addClass("thickbox").size()) {
		TB_chemin_animation = "'.url_absolue(find_in_path('circle_animation.gif')).'";
		TB_chemin_close = "'.url_absolue(find_in_path('close.gif')).'";
		TB_chemin_css = "'.url_absolue(find_in_path('thickbox.css')).'";
		TB_init(this);
	};
}
//onAjaxLoad is defined in private area only
if(typeof onAjaxLoad == "function") onAjaxLoad(init_f);
$(document).ready(init_f);
// --></script>';

	return $flux;
}

function ThickBox1_verifie_js_necessaire($flux) {

//var_dump($flux["page"]);
$page = $flux["page"]["texte"];
$necessaire = preg_match(",<a[^>]+type\s*=\s*['\"]image/(?:jpeg|png|gif)['\"],iUs",$page) ||
              preg_match(",<a[^>]+class\s*=\s*['\"].*\bthickbox\b.*['\"],iUs",$page);

$flux["data"]["ThickBox1"] = $necessaire;

return $flux;
  
}

?>
