<?php

/**
 * Extraire le contenu d'un document donné
 *
 *
 * @param $id_document le document à trairer
 * @return Sdata un tableau de donnée, si non traité alors false
 */
function inc_extraire_document($id_document = 0) {

    if ( (!isset($id_document)) || (!is_numeric($id_document)) )
        return false;

    $document =  sql_fetsel("*", "spip_documents", "id_document = ".$id_document);

    if (empty($document))
        return false;

    include_spip('inc/distant');
    include_spip('inc/documents');

    //Obtenir le fichier pour extraction
    if (!$fichier = copie_locale(get_spip_doc($document['fichier']), 'test'))
        return false;

    //Déterminer le format MIME pour définir le bon extracteur
    //Pour PHP < 5.3, il faut installer la PECL http://pecl.php.net/package/Fileinfo
    //Pour PHP >= 5.3, c'est chargé en natif
    $finfo = finfo_open(FILEINFO_MIME_TYPE); // Demande le mime type
    $mime = finfo_file($finfo, _DIR_RACINE.$fichier);
    finfo_close($finfo);

    return array('mime-type' => $mime);
}
