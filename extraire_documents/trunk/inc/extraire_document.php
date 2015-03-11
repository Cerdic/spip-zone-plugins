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

    //Determiner les mime type non standard comme les vnd (docx, ....)
    //http://fr.wikipedia.org/wiki/Type_MIME
    $finfo = finfo_open(FILEINFO_MIME_TYPE,_DIR_PLUGIN_EXTRAIREDOC."finfo/magic"); // Demande le mime type
    $mime = finfo_file($finfo, _DIR_RACINE.$fichier);

    //Si on ne reconnait pas le mime type, on teste sur la base par défaut
    if ($mime == "application/octet-stream") {
        $finfo = finfo_open(FILEINFO_MIME_TYPE); // Demande le mime type
        $mime = finfo_file($finfo, _DIR_RACINE.$fichier);
    }
    finfo_close($finfo);

    //Extraire le contenu selon le mimetype
    include_spip('extract/'.str_replace('/','_',$mime));

    $contenu = false;
    if (function_exists($extraire = "extraire_".str_replace('/','_',$mime)))
        $contenu = $extraire($fichier);

    return array(
        'mime-type' => $mime,
        'contenu' => $contenu
    );
}