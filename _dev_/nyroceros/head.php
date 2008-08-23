<?php

function Nyro_insert_head($flux){
	include_spip("inc/filtres");
	$config = @unserialize($GLOBALS['meta']['nyroceros']);
	if (!is_array($config))
		$config = array();
	$config = array_merge(array(
		'traiter_toutes_images' => 'oui',
		'selecteur_galerie' => '#documents_portfolio .nyroceros',
		'selecteur_commun' => '.nyroceros',
		'bgcolor' => '#000000',
		'preload' => 'oui'
	), $config);

	$flux .='
<script src=\''.url_absolue(find_in_path('js/jquery.nyroModal-1.2.8.js')).'\' type=\'text/javascript\'></script>
<script type="text/javascript"><!--
// Inside the function "this" will be "document" when called by ready() 
// and "the ajaxed element" when called because of onAjaxLoad 
if (window.jQuery)
(function($){
var init_f = function() {';

if ($config['traiter_toutes_images'] == 'oui') {
	$flux .='
// selectionner tous les liens vers des images
$("a[@type=\'image/jpeg\'],a[@type=\'image/png\'],a[@type=\'image/gif\']",this)
.addClass("nyroceros") // noter qu\'on l\'a vue
.attr("onclick","") // se debarrasser du onclick de SPIP
.nyroModal(); // activer le nyro
';
}

$flux .= '
// passer le portfolio en mode galerie de nyro
$("'.$config['selecteur_galerie'].'", this)
.attr("rel","galerie-portfolio");

// charger nyro sur autre chose
$("'.$config['selecteur_commun'].'").nyroModal({bgColor: "'.$config['bgcolor'].'"});

'
. ($config['preload'] == 'non')
  ? ''
  : '
  // preload images
  $.fn.preload = function() {
    var url;
    return this.each(function() {
      if ((url = $(this).attr("href")) && url.match(/\.(jpg|jpeg|png|gif)$/)) {
        var img = new Image;
        img.src = url;
      }
    });
  }

  $.fn.nyroModal.settings.endShowContent = function(elts,settings) {
    $(".nyroModalNext").preload();
  }
  
  $(".nyroceros[@rel]:eq(0)").preload();
  ')

.'
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
