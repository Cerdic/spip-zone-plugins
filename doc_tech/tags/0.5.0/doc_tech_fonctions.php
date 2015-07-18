<?php
/**
 * Fonctions utiles au plugin Documentation technique
 *
 * @plugin     Documentation technique
 * @copyright  2013
 * @author     Teddy Payet
 * @licence    GNU/GPL
 * @package    SPIP\Doc_tech\Fonctions
 */

if (!defined('_ECRIRE_INC_VERSION')) {
    return;
}

function doc_tech_lister_objet()
{
    include_spip('base/objets');
    include_spip('inc/config');
    $objets_principales =  array_keys(lister_tables_principales());
    // On va prendre la langue du site comme référence pour la langue de l'objet
    $langue_site = lire_config('langue_site');

    foreach ($objets_principales as $objet) {
        $type = objet_type($objet);
        // On recherche les onjet ayant une chaîne de langue selon le  type
        // Exemple : lang/forum_fr.php
        // lang/projet_fr.php
        $lang = find_in_path("lang/" . $type . "_" . $langue_site . ".php");
        if ($lang) {
            $liste_objet[] = $type;
        }
    }
    return $liste_objet;
}

?>