<?php
#---------------------------------------------------#
#  Plugin  : Jeux                                   #
#  Auteur  : Patrice Vanneufville, 2006             #
#  Gestion des scores : Maieul Rouquette, 2007      #
#  Contact : patrice¡.!vanneufville¡@!laposte¡.!net #
#  Licence : GPL                                    #
#--------------------------------------------------------------------------#
#  Documentation : https://contrib.spip.net/Des-jeux-dans-vos-articles  #
#--------------------------------------------------------------------------#


include_spip('base/jeux_tables');
include_spip('inc/jeux_autoriser');

// Declaration du pipeline "jeux_caracteristiques" qui permet d'ajouter au plugin des jeux tierces
if (!isset($GLOBALS['spip_pipeline']['jeux_caracteristiques']))
	$GLOBALS['spip_pipeline']['jeux_caracteristiques']=''; 

// (pour info : SPIP 2.0 => 12691, SPIP 2.1 => 15133, SPIP 2.2 => ??, SPIP 3.0 => 17743)
if ($GLOBALS['spip_version_code']>=17743) @define('_SPIP30000', 1);

?>