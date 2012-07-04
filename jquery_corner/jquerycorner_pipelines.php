<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function jquerycorner_insert_head($flux){
	$les_effets="";
	include_spip('inc/config');
	$conf_jquerycorner = lire_config('jquerycorner');

	for($i=0;$i<=$conf_jquerycorner["nombre"];$i++){
		if($conf_jquerycorner["element".$i]) {
			$les_effets .= "$(\"".$conf_jquerycorner["element".$i]."\").corner(" ;
			if($conf_jquerycorner["param".$i]) {
				$les_effets .= "\"".$conf_jquerycorner["param".$i]."\"" ;
			}
			$les_effets .= ");" ;
		}
	}
	// S'il y a au moins un element
	if($conf_jquerycorner["nombre"]>0) {
		$flux .="\n".'<script src="'.url_absolue(find_in_path('javascript/jquery.corner.js')).'" type="text/javascript"></script>';
		$flux .= "\n".'
<script type="text/javascript">/* <![CDATA[ */
	jQuery(document).ready(function(){
		function jquerycorner_init(){
			'.$les_effets.'
		}
		jquerycorner_init();
		if(typeof onAjaxLoad == "function") onAjaxLoad(jquerycorner_init);
	});
/* ]]> */</script>
';
	}
	return $flux;
}

?>