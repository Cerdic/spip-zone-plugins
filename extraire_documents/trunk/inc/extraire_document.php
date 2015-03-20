<?php

/**
 * Extraire le contenu d'un document donné
 *
 *
 * @param $document le document à trairer avec au moins un id et un fichier
 * @return Sdata un tableau de donnée, si non traité alors false
 */
function inc_extraire_document($document = array()) {

    if ( (!isset($document['id_document'])) || (!is_numeric($document['id_document'])) )
        return false;

    if ( (!isset($document['fichier'])) || (!is_numeric($document['fichier'])) )
        $document =  sql_fetsel("id_document,fichier", "spip_documents", "id_document = ".$document['id_document']);

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

    //Ne pas traiter si la mémoire est insuffisante
    //On doit avoir au moins 3 fois la taille du fichier de disponible avant traitement (choix empirique)
    //http://stackoverflow.com/questions/10208698/checking-memory-limit-in-php
    $memory_used = memory_get_usage();
    $memory_limit = ini_get('memory_limit');
    $file_size = filesize(_DIR_RACINE.$fichier);
    if (preg_match('/^(\d+)(.)$/', $memory_limit, $matches)) {
        if ($matches[2] == 'M') {
            $memory_limit = (int)$matches[1] * 1024 * 1024; // nnnM -> nnn MB
        } else if ($matches[2] == 'K') {
            $memory_limit = (int)$matches[1] * 1024; // nnnK -> nnn KB
        }
    }
    $memory_available = $memory_limit-$memory_used-3*$file_size;

    if ($memory_available < 0)
        return false;

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
