<?php
function spixplorer_ajouter_boutons($boutons_admin) {
    // si on est admin
    if ($GLOBALS['connect_statut'] == "0minirezo") {
       // on voit le bouton comme  sous-menu de "naviguer"
       $boutons_admin['accueil']->sousmenu['spixplorer'] =
       	new Bouton(find_in_path('_img/spixplorer.png'), _T('explorateur_spip') );
    }
    return $boutons_admin;
}
?>
