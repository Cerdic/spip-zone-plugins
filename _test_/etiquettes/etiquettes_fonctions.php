<?php
#---------------------------------------------------#
#  Plugin  : Étiquettes                             #
#  Auteur  : RastaPopoulos                          #
#  Licence : GPL                                    #
#--------------------------------------------------------------- -#
#  Documentation : http://www.spip-contrib.net/Plugin-Etiquettes  #
#-----------------------------------------------------------------#

function etiquettes_position_quot($valeur){
	
	if (($position = strpos($valeur, "&quot;")) === false)
		return 100000;
	else
		return $position;
	
}

?>
