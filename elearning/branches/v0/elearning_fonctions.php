<?php
#---------------------------------------------------#
#  Plugin  : E-Learning                             #
#  Auteur  : RastaPopoulos                          #
#  Licence : GPL                                    #
#--------------------------------------------------------------- -#
#  Documentation : https://contrib.spip.net/Plugin-E-learning  #
#-----------------------------------------------------------------#


// Appelle l'élément du core pour chercher une rubrique, mais en lui donnant le "name" qu'on veut
function elearning_chercher_rubrique($msg, $rubrique_elearning, $name){
	
	$select = chercher_rubrique($msg, 0, $rubrique_elearning, 'article', 0, '', 0, 'form_simple');
	$select = preg_replace('/<select.*?>/is', '<select name="'.$name.'" id="'.$name.'">', $select);
	$select = preg_replace('/<input[[:blank:]]+type=[\'"]hidden[\'"].*?id=[\'"]id_parent[\'"].*?\/>/is', '<input type="hidden" name="'.$name.'" id="id_parent" value="'.$rubrique_elearning.'" />', $select);
	return $select;
	
}


// On inclue la librairie pour les zones
find_in_path('elearning_zones.php', 'inc/', true);

?>
