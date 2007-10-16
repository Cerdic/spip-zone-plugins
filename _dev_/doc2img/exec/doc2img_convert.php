<?php

function exec_doc2img_convert_dist(){

    include_spip('base/compat193');
    include_spip('cfg_options');

    //racine du site c'est a dire url_site/ecrire/..
    $racine_site = getcwd().'/..';
    //document a traiter
    $id_document = _request('id_document');
    spip_log('doc2img � convertir : '.$id_document ,'doc2img');
    //format d'exportation (issu de cfg)
    $extension = lire_config('doc2img/format_cible');

    //on recupere l'url du document
    $sql = "SELECT fichier FROM spip_documents WHERE id_document=".$id_document;
    $res = sql_fetch(spip_query($sql));

    //nom complet du fichier : recherche ce qui suit le dernier / et retire ce dernier
    $document['fullname'] = substr(strrchr($res['fichier'], "/"),1);
    //url relative du repertoire contenant le fichier
    $document['source_url'] = substr($res['fichier'],0,strlen($res['fichier'])-strlen($document['fullname']));
    //decompose nom.extension
    $file_array = explode(".",$document['fullname']);
    $document['extension'] = $file_array[1];
    $document['name'] = $file_array[0];

    spip_log('nom du fichier : '.$document['name'] ,'doc2img');

    //creation du repertoire cible
    //url relative du repertoire cible
    $document['cible_url'] = lire_config('doc2img/repertoire_cible').'/'.$document['name'];

    //si le repertoire existe on ne genere pas les images, url absolue
    if (@mkdir($racine_site.'/'.$document['cible_url'])!==false) {

        //charge le document dans imagick
        spip_log('charge le document','doc2img');
        $handle = imagick_readimage($racine_site.'/'.$document['source_url'].'/'.$document['fullname']);

        //on determine les dimensions des frames
        //si les proportions sont gard�es
        $largeur = (lire_config('doc2img/largeur')) ? lire_config('doc2img/largeur') : imagick_getwidth($handle) ;
        $hauteur = (lire_config('doc2img/hauteur')) ? lire_config('doc2img/hauteur') : imagick_getheight($handle);
        $proportion = (lire_config('doc2img/proportion')) ? true : false;
    
        $ratio_largeur = $largeur / imagick_getwidth($handle);
        $ratio_hauteur = $hauteur / imagick_getheight($handle);

        spip_log('largeur_ratio :'.$ratio_largeur,'doc2img');
        spip_log('hauteur_ratio :'.$ratio_hauteur,'doc2img');
        spip_log('proportion :'.$proportion,'doc2img');
                
        //determine les ratio de taille
        if ($proportion == true) {
            $ratio_largeur = ($ratio_largeur < $ratio_hauteur) ? $ratio_largeur : $ratio_hauteur;
            $ratio_hauteur = $ratio_largeur; 
        }
        
        //defini les nouvelles dimensions
        $largeur = $ratio_largeur * imagick_getwidth($handle);
        $hauteur = $ratio_hauteur * imagick_getheight($handle);
                 
        spip_log('largeur_source :'.imagick_getwidth($handle),'doc2img');
        spip_log('hauteur_source :'.imagick_getheight($handle),'doc2img');

        spip_log('largeur_cible :'.$largeur,'doc2img');
        spip_log('hauteur_cible :'.$hauteur,'doc2img');
        
        //chaque page est un fichier on sauve dans la table doc2img chacun des ces nouveaux fichier
        for ($frame = 0 ; $frame < imagick_getlistsize($handle); $frame++ ) {
            //on accede � la page $frame
            imagick_goto($handle, $frame);
            $handle_frame = imagick_getimagefromlist($handle);
            //on redimensionne l'image
            if (!($ratio_largeur == 1) || !($ratio_hauteur ==1)) {
                //imagick_sample($handle, $largeur, $hauteur);
                imagick_resize($handle_frame, $largeur, $hauteur, IMAGICK_FILTER_UNKNOWN, 0 );
            }
            //nom du fichier cible, c'est � dire la frame (image) index�e
            $document['frame'] = $document['cible_url'].'/'.$document['name'].'-'.$frame.'.'.$extension;
            //on sauvegarde la page
            imagick_writeimage($handle_frame, $racine_site.'/'.$document['frame']);
            //sauvegarde les donnees dans la base
            $sql = "INSERT INTO spip_doc2img (id_doc2img,id_document,fichier) VALUES('',".$id_document.",'".$document['frame']."');";
            //print $sql;
            spip_query($sql);
            //on lib�re la frame
            imagick_free($handle_frame);                
        }
        
        //on lib�re les ressources
        imagick_free($handle );
    }

    //recharge la page appelante
    header("Location: ".$_SERVER['HTTP_REFERER']);
}
?>
