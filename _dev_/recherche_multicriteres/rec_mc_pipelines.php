<?php
function rec_mc_ajouter_boutons($boutons_admin) {
    // si on est admin
    if ($GLOBALS['connect_statut'] == "0minirezo") {
       // on voit le bouton comme  sous-menu de "naviguer"
       $boutons_admin['configuration']->sousmenu['cfg&cfg=Recherche multicritere']= new Bouton("cles24.png", _T('Recherches Multi-crit&egrave;res') );
    }
    return $boutons_admin;
}
?>