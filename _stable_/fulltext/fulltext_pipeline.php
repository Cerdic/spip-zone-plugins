<?php
function fulltext_ajouter_boutons($boutons_admin) {
    // si on est admin
    if ($GLOBALS['connect_statut'] == "0minirezo") {    		
        $boutons_admin['configuration']->sousmenu['fulltext']= new Bouton('../'._DIR_PLUGIN_FULLTEXT.'/fulltext-22.png', 'Fulltext' );
    } 
    return $boutons_admin;				 
}



?>