<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function balise_POPUP_dist($p) {
	return calculer_balise_dynamique($p, POPUP, array());
}

function balise_POPUP_dyn($param='', $page=false, $width=false, $height=false, $title='') {
	spipopup_config();
	if(!$page) $page = POPUP_SKEL;
	$page = str_replace('.html','',$page);
	if(!$width) $width = POPUP_WIDTH;
	if(!$height) $height = POPUP_HEIGHT;
	if (preg_match(_RACCOURCI_URL, $param, $match)) {
		if(in_array($match[1], array('art', 'article', 'breve', 'auteur', 'syndic', 'mot'))){
			$type = ($match[1] == 'art') ? 'article' : $match[1];
			$param = "id_$type=".$match[2];
		}
	}
	$url = generer_url_public($page, $param);
	$url_nopopup = generer_url_public($type,$param);
	$_title = (strlen($title) ? $title." " : '')._T('spipopup:nouvelle_fenetre');
	echo "$url_nopopup\" onclick=\"_popup_set('$url',$width,$height);return false;\" title=\"".$_title;
}
?>