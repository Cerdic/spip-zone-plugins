<?php
#---------------------------------------------------#
#  Plugin  : Jeux                                   #
#  Auteur  : Patrice Vanneufville, 2006             #
#  Gestion des scores : Maieul Rouquette, 2007      #
#  Contact : patrice¡.!vanneufville¡@!laposte¡.!net #
#  Licence : GPL                                    #
#--------------------------------------------------------------------------#
#  Documentation : http://www.spip-contrib.net/Des-jeux-dans-vos-articles  #
#--------------------------------------------------------------------------#

if (version_compare($GLOBALS['spip_version_code'],'1.9300','>=')) @define('_SPIP19300', 1);
if (version_compare($GLOBALS['spip_version_code'],'1.9200','>=')) @define('_SPIP19200', 1);
else @define('_SPIP19100', 1);

include_spip('base/jeux_tables');
include_spip('inc/jeux_autoriser');

// Declaration du pipeline "jeux_caracteristiques" qui permet de declarer au plugin des jeux tierces
$GLOBALS['spip_pipeline']['jeux_caracteristiques']=''; 

// raccourcis compatible SPIP 1.9x et 2.x
function jeux_fetsel($sel, $t, $w=false, $o='date DESC', $l=1) {
	if(defined('_SPIP19300')) return sql_fetsel($sel, $t, $w, $o, $l);
	return spip_fetch_array(spip_query("SELECT $sel FROM $t".($w?" WHERE $w":'').($o?" ORDER BY $o":'').($l?" LIMIT $l":'')));
}

// filtre de compatibilite avec SPIP 1.92
function puce_compat192($couleur) {
	if (defined('_SPIP19300')) return $couleur;
	return http_img_pack("puce-$couleur.gif", "puce $couleur", " style='margin: 1px;'");
}

?>