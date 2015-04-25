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
if (!function_exists('uploader_document')) {
    function uploader_document($files, $objet, $id_objet, $id_document='new') {
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
}