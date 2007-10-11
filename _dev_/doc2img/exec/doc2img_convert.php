<?php
    include_spip('base/compat193');

    //racine du site c'est … dire url_site/ecrire/..
    $racine_site = getcwd().'/..';
    //document … traiter
    $id_document = _request('id_document');

    //format d'exportation (issu de cfg)
    $id_type = 2; //PNG
 
    //on recup‚re l'url du document
    $sql = "SELECT fichier FROM spip_documents WHERE id_document=".$id_document;	
    $res = sql_fetch(spip_query($sql));
         
    //nom complet du fichier : recherche ce qui suit le dernier / et retire ce dernier
    $document['fullname'] = substr(strrchr($res['fichier'], "/"),1);
    //url relative du repertoire contenant le fichier
    $document['source_url'] = substr($res['fichier'],0,strlen($res['fichier'])-strlen($document['fullname']));
    //d‚compose nom.extension
    $file_array = explode(".",$document['fullname']);
    $document['extension'] = $file_array[1];
    $document['name'] = $file_array[0];
    
    spip_log('nom du fichier : '.$document['name'] ,'doc2img');
    
    //cr‚ation du repertoire cible
    //url relative du repertoire cible
    $document['cible_url'] = 'IMG/doc2img/'.$document['name'];
    //si le repertoire existe on ne genere pas les images, url absolue
    if (@mkdir($racine_site.'/'.$document['cible_url'])) {
    
        //charge le document dans imagick
        spip_log('charge le document','doc2img');
        $handle = imagick_readimage($racine_site.'/'.$document['source_url'].'/'.$document['fullname']);
        
        //genere l'ensemble des images dans un sous repertoire du nom du fichier parent
        imagick_writeimages($handle, $racine_site.'/'.$document['cible_url'].'/'.$document['name'].'.png');
    
        //chaque page est un fichier on sauve dans la table doc2img chacun des ces nouveaux fichier 
    
        for ($frame = 0 ; $frame < imagick_getlistsize($handle); $frame++ ) {
            //nom du fichier cible
            $document['frame'] = $document['cible_url'].'/'.$document['name'].'-'.$frame.'.png';
            //sauvegarde les donn‚es dans la base
            $sql = "INSERT INTO spip_doc2img VALUES('',".$id_document.",".$id_type.",'".$document['frame']."');";    
            spip_query($sql);
        }
    }
    
    //recharge la page appelante    
    header("Location: ".$_SERVER['HTTP_REFERER']);
?>
