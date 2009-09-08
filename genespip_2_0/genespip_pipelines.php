<?php
$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
define('_DIR_PLUGIN_GENESPIP',(_DIR_PLUGINS.end($p)));

/* public static */
function genespip_ajouterBoutons($boutons_admin) {
    // si on est admin
    if ($GLOBALS['connect_statut'] == "0minirezo" or $GLOBALS['connect_statut'] == "1comite") {
      // on voit le bouton dans la barre "naviguer"
      $boutons_admin['naviguer']->sousmenu['genespip']= new Bouton(
        "../"._DIR_PLUGIN_GENESPIP."/img_pack/arbre.png",  // icone
        _T('genespip:titre_menu_genespip') //titre
        );
    }
    return $boutons_admin;
}
/* public static */
function genespip_ajouterOnglets($flux) {
    $rubrique = $flux['args'];
    return $flux;
}

?>