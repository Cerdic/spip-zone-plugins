<?php

function numero_affiche_droite($flux){
	if ($flux['args']['exec']=='naviguer'){
		$path = "";
		if (version_compare($GLOBALS['spip_version_code'],'2','<'))
			$path= _DIR_RESTREINT_ABS;
		global $spip_lang_right;
		$out = "";
		$id_rubrique = $flux['args']['id_rubrique'];
		$out .= debut_cadre_relief('',true);
		$securiser_action = charger_fonction('securiser_action','inc');
		$out .= "<h4 style='margin-bottom:0;'>Rubriques</h4>";
		$url = $securiser_action('renumeroter',"rubrique-$id_rubrique",$path . str_replace('&amp;','&',self(true)));
		$out .= "<div><a href='$url'><img src='"._DIR_PLUGIN_NUMERO."img_pack/numerote.gif' width='48' height='24' alt='Re-numeroter' /></a>";
		$url = $securiser_action('denumeroter',"rubrique-$id_rubrique",$path . str_replace('&amp;','&',self(true)));
		$out .= "&nbsp;<a href='$url'><img src='"._DIR_PLUGIN_NUMERO."img_pack/denumerote.gif' width='48' height='24' alt='Enlever la numerotation' /></a></div>";
		$out .= "<h4 style='margin-bottom:0;'>Articles</h4>";
		$url = $securiser_action('renumeroter',"article-$id_rubrique",$path . str_replace('&amp;','&',self(true)));
		$out .= "<div><a href='$url'><img src='"._DIR_PLUGIN_NUMERO."img_pack/numerote.gif' width='48' height='24' alt='Re-numeroter' /></a>";
		$url = $securiser_action('denumeroter',"article-$id_rubrique",$path . str_replace('&amp;','&',self(true)));
		$out .= "&nbsp;<a href='$url'><img src='"._DIR_PLUGIN_NUMERO."img_pack/denumerote.gif' width='48' height='24' alt='Enlever la numerotation' /></a></div>";
		$out .= fin_cadre_relief(true);
		$flux['data'].= $out;
	}
	return $flux;
}

?>