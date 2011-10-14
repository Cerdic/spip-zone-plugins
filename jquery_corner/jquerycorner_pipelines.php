<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function jquerycorner_insert_head($flux){
	$les_effets="";
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
	$flux .="\n".'<script src="'.url_absolue(find_in_path('javascript/jquery.corner.js')).'" type="text/javascript"></script>';
	$flux .= '
<script type="text/javascript">/* <![CDATA[ */
	jQuery(document).ready(function(){'.$les_effets.'});
/* ]]> */</script>
';
	return $flux;
}

?>