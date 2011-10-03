<?php

function xspf_liste_skins($skin=''){
	$liste = find_all_in_path('prive/jw_skins/', '[.]swf',array());
	$options = "<option>"._T('xspf:mplskin_normal')."</option>\n";
	foreach($liste as $nom => $swf){
		if($skin == url_absolue($swf)){
			$options .= "<option value='".url_absolue($swf)."' selected='selected'>$nom</option>\n";
		}else{
			$options .= "<option value='".url_absolue($swf)."'>$nom</option>\n";
		}
	}
	return $options;
}
?>