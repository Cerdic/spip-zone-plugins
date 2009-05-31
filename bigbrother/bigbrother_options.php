<?php
#---------------------------------------------------#
#  Plugin  : Big Brother                            #
#  Auteur  : RastaPopoulos                          #
#  Licence : GPL                                    #
#------------------------------------------------- -#

// Chargement de la librairie de fonctions
find_in_path('bigbrother.php', 'inc/', true);


// Si la config est ok, à chaque hit, on teste s'il faut enregistrer la visite ou pas
if (lire_config('bigbrother/enregistrer_visite') == 'oui')
	bigbrother_tester_la_visite_du_site();

?>
