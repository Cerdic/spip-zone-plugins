<?php
function orthogoogle_ajouter_boutons($boutons_admin) {
    // si on est admin
    if ($GLOBALS['connect_statut'] == "0minirezo") {
       // on voit le bouton comme  sous-menu de "naviguer"
       $boutons_admin['naviguer']->sousmenu['cfg&cfg=orthogoogle']= new Bouton(_DIR_PLUGIN_ORTHOGOOGLE."googiespell-24.png", _T('orthogoogle') );
    }
    return $boutons_admin;
}
?>
