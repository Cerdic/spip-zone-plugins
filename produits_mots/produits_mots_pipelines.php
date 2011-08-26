<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Pouvoir mettre des mots-cle sur les produits
 *
**/
function produits_mots_declarer_liaison_mots($liaisons){
    $liaisons['produits'] = new declaration_liaison_mots('produits', array(
        'exec_formulaire_liaison' => "produit",
        'singulier' => "produits_mots:produit",
        'pluriel'   => "produits_mots:produits",
        'libelle_objet' => "produits_mots:objet_produit",
        'libelle_liaisons_objets' => "produits_mots:item_mots_cles_association_produit",
    ));
    return $liaisons;
}


?>
