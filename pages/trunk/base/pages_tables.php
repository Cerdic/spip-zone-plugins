<?php
#---------------------------------------------------#
#  Plugin  : Pages                                  #
#  Auteur  : RastaPopoulos                          #
#  Licence : GPL                                    #
#--------------------------------------------------------------- -#
#  Documentation : http://www.spip-contrib.net/Plugin-Pages       #
#-----------------------------------------------------------------#

if (!defined("_ECRIRE_INC_VERSION")) return;

function pages_declarer_tables_objets_sql($tables){
	
	$tables['spip_articles']['field']['page'] = "VARCHAR(255) DEFAULT '' NOT NULL";
	return $tables;

}

?>
