<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Insertion dans le pipeline jqueryui_forcer (plugin jQueryUI)
 * 
 * On ajoute le chargement des js nécessaires
 * @param array $plugins Un tableau des scripts déjà demandé au chargement
 * @retune array $plugins Le tableau complété avec les scripts que l'on souhaite 
 */

function elfinder_jqueryui_plugins($scripts){
    $scripts[] = "jquery.ui.draggable";
    $scripts[] = "jquery.ui.droppable";
    $scripts[] = "jquery.ui.selectable";
    return $scripts;
} 
?>
