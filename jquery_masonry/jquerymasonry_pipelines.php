<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function jquerymasonry_insert_head($flux){
	// compat : jusqu'à SPIP 3.0 compris utiliser jquerymasonry v 2.1.0, après: la derniere maj (3.3.2)
	include_spip('plugins/installer'); // spip_version_compare dans SPIP 3.x
	include_spip('inc/plugin'); // spip_version_compare dans SPIP 2.x
	$js_acharger = spip_version_compare('[2.1;3.0]', spip_version()) ? 'jquery.masonry.js' : 'jquery.masonry_2.1.0.js';
	
	$executer="";
	$styles="";
	include_spip('inc/config');
	$conf_jquerymasonry = lire_config('jquerymasonry');

	for($i=0;$i<=$conf_jquerymasonry["nombre"];$i++){
		if($conf_jquerymasonry["container".$i]) {
			$largeur = $conf_jquerymasonry["largeur".$i] + 2*$conf_jquerymasonry["marge".$i] + 10;  // 10px pour les bordures eventuelles
			if($conf_jquerymasonry["multicolonne".$i] != "on") {
				$styles .= "\n".$conf_jquerymasonry["container".$i]." ".$conf_jquerymasonry["items".$i]."{width:".$conf_jquerymasonry["largeur".$i]."px;margin:".$conf_jquerymasonry["marge".$i]."px;float:left;}\n" ;
			}
			$executer .= "$(\"".$conf_jquerymasonry["container".$i]."\").masonry({" ;
			$executer .= "itemSelector:'".$conf_jquerymasonry["items".$i]."'," ;
			if($conf_jquerymasonry["multicolonne".$i] == "on") {
				$executer .= "columnWidth:".$largeur."," ;
			}
			$executer .= "isRTL:".(lang_dir()=="rtl"?"true":"false")."," ;
			$executer .= "isAnimated:".($conf_jquerymasonry["animation".$i]=="on"?"true":"false") ;
			$executer .= "});" ;
		}
	}

	// S'il y a au moins un element
	if($conf_jquerymasonry["nombre"]>0) {
		$flux .= "\n".'<script src="'.url_absolue(find_in_path('javascript/'.$js_acharger)).'" type="text/javascript"></script>';
		if($conf_jquerymasonry["multicolonne".$i] != "on") {
			$flux .= "\n".'<style type="text/css">'.$styles.'</style>';
		}
		$flux .= "\n".'
<script type="text/javascript">/* <![CDATA[ */
	jQuery(document).ready(function(){
		function jquerymasonry_init(){
			'.$executer.'
		}
		jquerymasonry_init();
		if(typeof onAjaxLoad == "function") onAjaxLoad(jquerymasonry_init);
	});
/* ]]> */</script>
';
	}
	return $flux;
}

?>
