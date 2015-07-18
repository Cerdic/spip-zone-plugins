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

function uploadhtml5_uploader_document($objet, $id_objet, $files, $id_document='new', $mode = 'auto') {

    // tester l'autorisation d'ajout de document
    include_spip('inc/autoriser');
    /* S'il n'y a pas d'id_objet, c'est qu'on crée un nouveau
       document. Les autorisations seront gérées en aval dans
       ajouter_document. */
    if ($id_objet AND (!autoriser('joindredocument',$objet,$id_objet)))
        return false;

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
            $mode
        );
    }
}

/**
 * Uploader un logo sur un objet en spip 3.0/3.1
 *
 * @param mixed $objet
 * @param mixed $id_objet
 * @param mixed $fichier
 * @access public
 * @return mixed
 */
function uploadhtml5_uploader_logo($objet, $id_objet, $fichier) {

    // Autorisation de mettre un logo?
    include_spip('inc/autoriser');
    if (!autoriser('iconifier',$objet,$id_objet))
        return false;

    include_spip('action/editer_logo');
    // Version SPIP 3.1 de cette fonction:
    if (function_exists('logo_modifier'))
        return logo_modifier($objet, $id_objet, 'on', $fichier);


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

/**
 * Convertir les formats de logo accepté en mime_type
 *
 * @param mixed $type Liste des formats à convertir en mime type, séparé par une virgule.
 * @access public
 * @global mixed $formats_logos
 * @return mixed Liste des mimes types séparé par une virgule.
 */
function trouver_mime_type($type) {

    // Si le type est logo on récupère automatiquement les formats de
    // logo défini par SPIP
    if ($type == 'logo') {
        global $formats_logos;
        $type = $formats_logos;
    }
    else {
        // on explode pour passer $type dans sql_in
        $type = explode(',', $type);
    }

    // On récupère les mimes types demandé par la fonction
    $mime_type = sql_allfetsel('mime_type', 'spip_types_documents', sql_in('extension', $type));

    // Simplifier le tableau
    $mime_type = array_column($mime_type, 'mime_type');

    // Renvoyer une chaine utilisable
    return implode(',', $mime_type);
}
