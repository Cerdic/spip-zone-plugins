<?php
#---------------------------------------------------#
#  Plugin  : E-Learning                             #
#  Auteur  : RastaPopoulos                          #
#  Licence : GPL                                    #
#--------------------------------------------------------------- -#
#  Documentation : https://contrib.spip.net/Plugin-E-learning  #
#-----------------------------------------------------------------#


function cfg_elearning_pre_traiter(&$cfg){

	elearning_mettre_a_jour_les_zones($cfg->val['rubrique_elearning']);
	return null;

}

?>
