<?php
include_spip('base/abstract_sql');
/*
CREATE TABLE spip_favtextes (
 id_favtxt bigint(21) NOT NULL auto_increment,
 id_auth int(11) NOT NULL default '0',
 id_texte int(11) NOT NULL default '0',
 PRIMARY KEY  (id_favtxt)
) ;

*/

/*
function mesFavoris_manageFavorites($texte, $id_article) {
// Show icons only if user is logged
if($GLOBALS['auteur_session']) {
 $newtexte = $texte.getAddFav($id_article).getDelFav($id_article); 
 return $newtexte;
}
else {
 return $texte;
} 
}



function getAddFav($id_article) {
	$addFav="location.href='?action=mesFavoris_addFavorite&id_article=$id_article'";
	return "&nbsp;<img src=\"plugins/mesFavoris/image/fav_add.png\" title=\"Add to favorites\" alt=\"Add to favorites\" width=\"30\" height=\"30\" onclick=\"".$addFav."\">";
}

function getDelFav($id_article) {
	$delFav="location.href='?action=mesFavoris_delFavorite&id_article=$id_article'";
	return "&nbsp;<img src=\"plugins/mesFavoris/image/fav_del.png\" title=\"Remove from favorites\" alt=\"Remove from favorites\" width=\"30\" height=\"30\" onclick=\"".$delFav."\">";
}
*/

?>