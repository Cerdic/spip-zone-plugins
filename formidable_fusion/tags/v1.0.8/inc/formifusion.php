<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

/*
 * Liste tous les types d'échanges (export et import) existant pour les formulaires
 *
 * @return array Retourne un tableau listant les types d'échanges
 */
function fusions_formulaire_lister_disponibles(){
    // On va chercher toutes les fonctions existantes
    $liste = find_all_in_path('fusionner/formulaire/', '.+[.]php$');
    $types_fusion = array('fusionner'=>array());
    if (count($liste)){
        foreach ($liste as $fichier=>$chemin){
            $type_fusion = preg_replace(',[.]php$,i', '', $fichier);           
            if ($f = charger_fonction('fusionner', "fusionner/formulaire/$type_fusion", true)){
                $types_fusion['fusionner'][$type_fusion] = $type_fusion;
            }
        }
    }
    return $types_fusion;
}

?>
