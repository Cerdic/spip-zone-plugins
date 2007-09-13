<?php

/*
	Balise #TOTAL_CLICS
	Chryjs (c) 2007
	Plugin pour spip 1.9.2
	Licence GNU/GPL
*/

if (!defined("_ECRIRE_INC_VERSION")) return;	#securite

include_ecrire ('inc_connect.php');

function balise_TOTAL_CLICS($p) {
	return calculer_balise_dynamique($p,'TOTAL_CLICS', array('id_syndic','id_syndic_article'));
}

function balise_TOTAL_CLICS_dyn($id_syndic = 0, $id_syndic_article = 0) {
	/* tenir compte de la langue, c'est pas de la tarte */
include_ecrire('base/db_mysql.php');

if (!empty($id_syndic_article))
{
	$r = spip_query_db("SELECT clic_compteur FROM spip_syndic_articles WHERE id_syndic_article='$id_syndic_article' LIMIT 1");
}
else
{
	$r = spip_query_db("SELECT clic_compteur FROM spip_syndic WHERE id_syndic='$id_syndic' LIMIT 1");
}

$o = spip_fetch_array($r);
return $o['clic_compteur'] ;

}

?>
