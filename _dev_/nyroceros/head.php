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
<script src=\''.url_absolue(find_in_path('js/nyromodal.js')).'\' type=\'text/javascript\'></script>
<script type="text/javascript">/* <![CDATA[ */
var nyro_traiter_toutes_images="'.($config['traiter_toutes_images'] == 'oui'?'true':'false').'";
var nyro_bgcolor="'.$config['bgcolor'].'";
var nyro_selecteur_galerie="'.$config['selecteur_galerie'].'";
var nyro_selecteur_commun="'.$config['selecteur_commun'].'";
var nyro_preload="'.($config['preload'] == 'non'?'false':'true').'";
//onAjaxLoad is defined in private area only
if (window.jQuery)
(function($){if(typeof onAjaxLoad == "function") onAjaxLoad(nyro_init);
	$(nyro_init);
 })(jQuery);
/* ]]> */</script>
<link rel="stylesheet" href="'.url_absolue(find_in_path('styles/nyroModal.full.css')).'" type="text/css" media="all"/>
';
	return $flux;
}

?>
