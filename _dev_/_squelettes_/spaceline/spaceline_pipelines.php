<?php
function spaceline_ajouter_boutons($boutons_admin) {
    if ($GLOBALS['connect_statut'] == "0minirezo") {
       $boutons_admin['configuration']->sousmenu['cfg&cfg=Squelettes Spaceline']= new Bouton("../plugins/spaceline/terre2.gif", _T('Spaceline') );
    }
    return $boutons_admin;
}
?>