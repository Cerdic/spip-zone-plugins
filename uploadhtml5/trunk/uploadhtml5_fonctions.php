<?php
/**
 * Fonctions utiles au plugin Formulaire upload html5
 *
 * @plugin     Formulaire upload html5
 * @copyright  2014
 * @author     Phenix
 * @licence    GNU/GPL
 * @package    SPIP\Uploadhtml5\Fonctions
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Uploader et lier des documents à un objet SPIP
 *
 * @param mixed $files $_FILES envoyer par un formulaire had hoc
 * @param mixed $objet
 * @param mixed $id_objet
 * @param string $id_document Dans le cas ou l'on veux remplacer un document.
 * @access public
 */

function uploadhtml5_uploader_document($files, $objet, $id_objet, $id_document='new') {
    // On va créer le tableau des documents.
    $docs = array();
    foreach ($files as $doc) {
        // pas de fichier vide
        if (!empty($doc['name']))
            $docs[] = $doc;
    }

    // On fait un test au cas ou
    if (!empty($docs)) {
        // On ajoute les documents a un objet SPIP.
        $ajouter_documents = charger_fonction('ajouter_documents','action');
        $ajouter_documents(
            $id_document,
            $docs,
            $objet, // Article, rubrique, autre objet
            $id_objet,
            'document'
        );
    }
}

function uploadhtml5_uploader_logo($objet, $id_objet, $fichier) {

    include_spip('action/editer_logo');
    // Version SPIP 3.1 de cette fonction:
    if (function_exists('logo_modifier'))
        return logo_modifier($objet, $id_objet, 'on', _DIR_RACINE.$fichier);


    include_spip('action/iconifier');
    $chercher_logo = charger_fonction('chercher_logo','inc');
    $ajouter_image = charger_fonction('spip_image_ajouter','action');

    $type = type_du_logo(id_table_objet($objet));
    $logo = $chercher_logo($id_objet, id_table_objet($objet));

    if ($logo)
        spip_unlink($logo[0]);

    // Dans le cas d'un tableau, on présume que c'est un $_FILES et on passe directement
    if (is_array($fichier))
        $err = $ajouter_image($type."on".$id_objet," ", $fichier, true);
    else
        // Sinon, on caviarde la fonction ajouter_image
        $err = $ajouter_image($type."on".$id_objet," ", array('tmp_name' => $fichier), true);

    if ($err)
        return $err;
    else
        return true;

}