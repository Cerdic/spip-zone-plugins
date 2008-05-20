<?php
#---------------------------------------------------#
#  Plugin  : Big Brother                            #
#  Auteur  : RastaPopoulos                          #
#  Licence : GPL                                    #
#------------------------------------------------- -#

find_in_path('bigbrother_tables.php', 'base/', true);
find_in_path('bigbrother.php', 'inc/', true);

// A chaque hit, on teste s'il faut enregistrer la visite ou pas
bigbrother_tester_la_visite_du_site();

?>
