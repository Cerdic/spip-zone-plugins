<?php
/*	*********************************************************************
	*
	* Copyright (c) 2007
	* Xavier Burot
	*
	* SPIP-ALBUM : Programme d'affichage de photos
	*
	* Ce programme est un logiciel libre distribue sous licence GNU/GPL.
	*
	*********************************************************************
*/

if (!defined("_ECRIRE_INC_VERSION")) return;

function album_entete($public=true){
	$entete ='

<!-- Elements necessaires au plugin SPIP-Album -->
<link rel="stylesheet" href="'.url_absolue(find_in_path('css/album.css')).'" type="text/css" media="projection, screen, tv" />
<script type="text/javascript" src="'.url_absolue(find_in_path('javascript/interface.js')).'"></script>
<script type="text/javascript">
var init_ib = function(){
	$.ImageBox.init(
		{
			loaderSRC: "'.url_absolue(find_in_path('img_pack/loading.gif')).'",
			closeHTML: "<img src=\''.url_absolue(find_in_path('img_pack/fermerlabel.gif')).'\' />"
		}
	);
};
';
	if (!$public) $entete .='//onAjaxLoad est utilise seulement dans la partie privee
if(typeof onAjaxLoad =="function") onAjaxLoad(init_ib);';
	$entete .='$(document).ready(init_ib);
</script>

';
	return $entete;
}

function album_insert_head($flux) {
	$flux .= album_entete();
/*	'

<!-- Elements necessaires au plugin SPIP-Album -->
<link rel="stylesheet" href="'.url_absolue(find_in_path('css/album.css')).'" type="text/css" media="projection, screen, tv" />
<script type="text/javascript" src="'.url_absolue(find_in_path('javascript/interface.js')).'"></script>
<script type="text/javascript">
var init_ib = function(){
	$.ImageBox.init(
		{
			loaderSRC: "'.url_absolue(find_in_path('img_pack/loading.gif')).'",
			closeHTML: \'<img src="'.url_absolue(find_in_path('img_pack/fermerlabel.gif')).'" />\'
		}
	);
};
//onAjaxLoad est utilise seulement dans la partie privee
if(typeof onAjaxLoad =="function") onAjaxLoad(init_ib);
$(document).ready(init_ib);
</script>
';*/
	return $flux;
}

function album_header_prive($flux){
	$exec = _request('exec');
	if ($exec == 'articles')
//		$flux .= album_insert_head($flux);
		$flux .= album_entete(false);
	return $flux;
}
?>