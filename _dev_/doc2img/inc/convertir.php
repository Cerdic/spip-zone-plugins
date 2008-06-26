<?php

/*! \brief fonction qui indique si le document a deja été converti
 *
 *  
 *  \param $id_document identifiant du document à controler
 *  \return booleen $resultat : true document converti, false sinon
 */
function is_doc2img($id_document) {
    $pages = sql_countsel('spip_doc2img','id_document='.$id_document,'id_doc2img');
    if (is_numeric($pages)) {        
        return true;
    } else  {
        return false;
    }
}


/*! \brief fonction controlant que le document founit est convertible
 *
 *  Vérifie que le document donné en paramétre est bien listé dans les types de documents autorisés à la conversion via CFG
 *  
 *  \param $id_document identifiant du document à controler
 *  \return booleen $resultat : true document convertible, false sinon
 */
function can_doc2img($id_document = NULL) {
    
    include_spip('cfg_options');    

    $extension = sql_getfetsel(
        'extension',
        'spip_documents',
        'id_document = '.$id_document
    );

    //on liste les extensions autorisées depuis CFG
    $types_autorises = explode(',',lire_config("doc2img/format_document",null,true));
    
    //on controle si le document est convertible ou non    
    if (in_array($extension,$types_autorises)) {
        return true;
    } else {
        return false;
    }
}

/*! \brief calcul les ratios de taille de l'image finale
 *
 *  Vérifie que le document donné en paramétre est bien listé dans les types de documents autorisés à la conversion via CFG
 *  
 *  \param $id_document identifiant du document à controler
 *  \return booleen $resultat : true document convertible, false sinon
 */
function doc2img_ratio(&$handle) {

    //on determine les dimensions des frames
    $proportion = (lire_config('doc2img/proportion'));
    
    //par défaut le ratio faut 1
    $ratio['largeur'] = $ratio['hauteur'] = 1;

    //dimensions du document d'origine
    $dimensions['largeur'] = imagick_getwidth($handle);
    $dimensions['hauteur'] = imagick_getheight($handle);    

    //si une largeur seuil a été définie 
    if ($largeur = lire_config('doc2img/largeur')) {
        $ratio['largeur'] = $largeur / $documents['largeur'];
    }
    
    //si une hauteur seuil a été définie
    if ($hauteur = lire_config('doc2img/hauteur')) {
        $ratio['hauteur'] = $hauteur / $documents['hauteur'];
    }
    

    //ajustement des ratio si proportion demandée
    if (lire_config('doc2img/proportion') == "on") {
        //si agrandiessement demandée on prend le plus grand ratio, sinon le plus petit
        $ratio['largeur'] = (lire_config('doc2img/agrandir')) ? max($ratio['hauteur'], $ratio['largeur']) : min($ratio['hauteur'], $ratio['largeur']);
        $ratio['hauteur'] = $ratio['largeur']; 
    }
        
    //defini les nouvelles dimensions
    $dimensions['largeur'] = $ratio['largeur'] * imagick_getwidth($handle);
    $dimensions['hauteur'] = $ratio['hauteur'] * imagick_getheight($handle);

    return $dimensions;
}

/*! \brief fonction autonome convertissant un document donné en paramétre
 *
 *  Ensemble des actions necessaires à la conversion d'un document en image :
 *  - recupére les informations sur le documents (nom, repertoire, nature)
 *  - determine les informatsions sur le documents finals (nom, respertoire, extension) 
 *  
 * \param $id_document identifiant du document à convertir  
 */   
function convertir_document($id_document) {

    // NOTE : les repertoires doivent se finir par un /

    include_spip('cfg_options');
    include_spip('inc/documents');
    include_spip('inc/flock');

    //racine du site c'est a dire url_site/
    //une action se repere à la racine du site 
    $racine_site = getcwd().'/';

    spip_log('doc2img à convertir : '.$id_document ,'doc2img');

    //on recupere l'url du document
    $fichier = sql_getfetsel(
        'fichier',
        'spip_documents',
        'id_document='.$id_document
    );


    //chemin relatif du fichier
    $fichier = get_spip_doc($fichier); 

    //nom complet du fichier : recherche ce qui suit le dernier / et retire ce dernier
    // $resultat[0] = $resultat[1]/$resultat[2].$resultat[3]
    preg_match('/(.*)\/(.*)\.(.\w*)/i', $fichier, $result);

    //url relative du repertoire contenant le fichier , on retire aussi le / en fin
    $document['source_url']['relative'] = $result[1].'/';
    $document['source_url']['absolute'] = $racine_site.$document['source_url']['relative'];
    
    //information sur le nom du fichier
    $document['extension'] = $result[3];
    $document['name'] = $result[2];
    $document['fullname'] = $result[2].'.'.$result[3];
    
    //creation du repertoire cible
    //url relative du repertoire cible
    $document['cible_url']['relative'] = _DIR_IMG.lire_config('doc2img/repertoire_cible').'/'.$document['name'].'/';
    $document['cible_url']['absolute'] = $racine_site.$document['cible_url']['relative'];

    spip_log($document,'doc2img');

    //verrouille document ou quitte
    //si erreur sur verrou alors on quitte le script
    if (!$fp = @spip_fopen_lock($document['source_url']['absolute'].$document['fullname'],'r',LOCK_EX)) {
        spip_log('verouillé '.$id_document,'doc2img');
        return false;
    }
        
    //suppresssion d'un eventuel repertoire deja existant
    include_spip('base/doc2img_install');
    rm($document['cible_url']['absolute']);

    //suppression dans la base 
    sql_delete(
        "spip_doc2img",
        "id_document = ".$id_document
    );

    //creation du repertoire cible
    if (!@mkdir($document['cible_url']['absolute'])) {
        spip_log('erreur repertoire '.$id_document,'doc2img');    
        return false;
    }
        
    //charge le document dans imagick
    //determine le nombre de pages dans le document
    if (class_exists('Imagick')) {
        //version 2.x
        $image = new Imagick($document['source_url']['absolute'].$document['fullname']);  
        $nb_pages = $image->getNumberImages();
        spip_log($document['source_url']['absolute'].$document['fullname'].' -> '.$nb_pages,'doc2img');

    } else {
        //version 0.9
        $handle = imagick_readimage($document['source_url']['absolute'].$document['fullname']);    
        $nb_pages = imagick_getlistsize($handle);
    }

    
    //determine l'extension à utiliser
    $extension = lire_config('doc2img/format_cible');
    
    //charge la premiere image
    spip_log($id_document.'-0','doc2img');
    //on accede à la page $frame
    if (@imagick_goto($handle, 0)) {
        $handle_frame = @imagick_getimagefromlist($handle);
    } else {
        $image_frame = $image->current();
    }

    //chaque page est un fichier qu'on sauve dans la table doc2img indéxé par son numéro de page    
    do {
        //calcule des dimensions
        //$dimensions = doc2img_ratio($handle_frame);
                
        //on redimensionne l'image
        //imagick_zoom($handle_frame, $dimensions['largeur'], $dimensions['hauteur']);
        
        //nom du fichier cible, c'est à dire la frame (image) indexée
        $document['frame'] = $document['name'].'-'.$frame.'.'.$extension;
        
        //on sauvegarde la page
        if (!@imagick_writeimage($handle_frame,  $document['cible_url']['absolute'].$document['frame']))
            $image_frame->writeImage($document['cible_url']['absolute'].$document['frame']);

        //sauvegarde les donnees dans la base        
        sql_insertq(
            "spip_doc2img",
            array(
                "id_document" => $id_document,
                "fichier" => set_spip_doc($document['cible_url']['relative'].$document['frame']),
                "page" => $frame
            )
        );
        
        //on libére la frame
        if (!@imagick_free($handle_frame)) {
            $image_frame->clear();
            $image_frame->destroy();
        }
        
        //on charge la frame suivante
        spip_log($id_document.'-'.$frame,'doc2img');
        //on accede à la page $frame
        if (@imagick_goto($handle, $frame)) {
            $handle_frame = @imagick_getimagefromlist($handle);
        } else {
            $image->nextImage();
            $image_frame = $image->current();
        }  
        $frame++;
    } while($frame < $nb_pages );
    
    //on libére les ressources
    if (!@imagick_free($handle)) {
        $image->clear();
        $image->destroy();        
    }
    
    // libération du verrou
    spip_fclose_unlock($fp);

    spip_log($id_document." document ok",'doc2img');        

    return true;
}

?>
