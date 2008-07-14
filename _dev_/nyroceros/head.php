<?php

function Nyro_insert_head($flux){
	include_spip("inc/filtres");

	$flux .='
<script src=\''.url_absolue(find_in_path('js/jquery.nyroModal-1.2.8.js')).'\' type=\'text/javascript\'></script>
<script type="text/javascript"><!--
// Inside the function "this" will be "document" when called by ready() 
// and "the ajaxed element" when called because of onAjaxLoad 
if (window.jQuery)
(function($){
var init_f = function() {
	// selectionner tous les liens vers des images
	$("a[@type=\'image/jpeg\'],a[@type=\'image/png\'],a[@type=\'image/gif\']",this)
	.addClass("nyroceros") // noter qu\'on l\'a vue
	.attr("onclick","") // se debarrasser du onclick de SPIP
	.nyroModal(); // activer le nyro

	// passer le portfolio en mode galerie de nyro
	$("#documents_portfolio .nyroceros", this)
	.attr("rel","galerie-portfolio");
}
//onAjaxLoad is defined in private area only
if(typeof onAjaxLoad == "function") onAjaxLoad(init_f);
	$(init_f);
 })(jQuery);
 // --></script>
<link rel="stylesheet" href="'.url_absolue(find_in_path('styles/nyroModal.full.css')).'" type="text/css" media="projection, screen, tv" />
';
	return $flux;
}

?>
