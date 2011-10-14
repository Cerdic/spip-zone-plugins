<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function jquerymasonry_insert_head($flux){
	$executer="";
	$styles="";
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
	$flux .= "\n".'<script src="'.url_absolue(find_in_path('javascript/jquery.masonry.js')).'" type="text/javascript"></script>';
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

	return $flux;
}

?>