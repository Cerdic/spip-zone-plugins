<?php

/**
 * Fonction autonome convertissant un document donné en paramètre
 *
 *  Ensemble des actions necessaires à la conversion d'un document en image :
 *  - recupère les informations sur le documents (nom, repertoire, nature)
 *  - determine les informatsions sur le documents finals (nom, respertoire, extension)
 *
 *  Documentation intéressante :
 *  - http://valokuva.org/?p=7
 *  - http://valokuva.org/?p=7#comment-19198
 *
 * @param $id_document identifiant du document à convertir
 */
function inc_doc2img_convertir($id_document) {

	if(function_exists('imagick_readimage') OR class_exists('Imagick')){
	    // NOTE : les repertoires doivent se finir par un /

	    include_spip('inc/documents');
	    include_spip('inc/flock');

	    $config = lire_config('doc2img');

	    //ecrire_config('php::doc2img/'.$id_document.'/statut','encours');

	    //racine du site c'est a dire url_site/
	    //une action se repere à la racine du site
	    $racine_site = _DIR_RACINE;

	    $document = doc2img_document($id_document);

	    //verrouille document ou quitte
	    //si erreur sur verrou alors on quitte le script
	    if (!$fp = @spip_fopen_lock($document['source_url']['absolute'].$document['fullname'],'r',LOCK_EX)) {
	        return "erreur document verrouillé";
	    }

	    //suppresssion d'un eventuel repertoire deja existant
	    //include_spip('base/doc2img_install');
	    //rm($document['cible_url']['absolute']);

	    //suppression dans la base
	    sql_delete(
	        "spip_doc2img",
	        "id_document = ".$id_document
	    );

	    //creation du repertoire cible
	    if (!is_dir($document['cible_url']['absolute']) && !@mkdir($document['cible_url']['absolute'])) {
	        return "erreur impossible de creer le repertoire";
	    }

	    /**
	     * Chargement du document en mémoire
	     * On détermine le nombre de pages du document
	     * On libère la ressource automatiquement si on utilise la class
	     * car on réouvre chaque page par la suitre
	     */
	    if (class_exists('Imagick')) {
	        $version = '2.x';
	        $image = new Imagick($document['source_url']['absolute'].$document['fullname']);
	        $identify = $image->identifyImage();
	        $identify2 = $image->getImageProperties();
	        $nb_pages = $image->getNumberImages();
	        $image->clear();
	        $image->destroy();
	    } else {
	        $version = '0.9';
	        $handle = imagick_readimage($document['source_url']['absolute'].$document['fullname']);
	        $nb_pages = imagick_getlistsize($handle);
	    }

	    //ecrire_config('php::doc2img/'.$id_document.'/pages',$nb_pages);

	    $frame = 0;

	    // chaque page est un fichier qu'on sauve dans la table doc2img indexé
	    // par son numéro de page
	    do {
	        //charge la premiere image

	        //on accede à la page $frame
	        if ($version == '0.9') {
	            imagick_goto($handle, $frame);
	            $handle_frame = @imagick_getimagefromlist($handle);
	        } else {
	        	$image_frame = new Imagick();
	        	if(is_numeric($config['resolution']) && ($config['resolution'] <= '600') && ($config['resolution'] > $identify['resolution']['x'])){
		        	$image_frame->setResolution($config['resolution'],$config['resolution']);
	        	}
				$image_frame->readImage($document['source_url']['absolute'].$document['fullname'].'['.$frame.']');
				$image_frame->setImageFormat($config['format_cible']);
				if(is_numeric($config['compression']) && ($config['compression'] > 50) && ($config['compression'] <= 100)){
					$image_frame->setImageCompressionQuality($config['compression']);
				}
	            $handle_frame = $image_frame;
	        }

	        //calcule des dimensions
	        $dimensions = doc2img_ratio($handle_frame,$version,$config);

	        //nom du fichier cible, c'est à dire la frame (image) indexée
	        $document['frame'] = $document['name'].'-'.$frame.'.'.$config['format_cible'];

	        //on sauvegarde la page
	        if ($version == '0.9') {
	        	//on redimensionne l'image
		        imagick_zoom($handle_frame, $dimensions['largeur'], $dimensions['hauteur']);
	            imagick_writeimage($handle_frame, $document['cible_url']['absolute'].$document['frame']);
	            $taille = filesize($document['cible_url']['absolute'].$document['frame']);

	        } else {
	        	//$image_frame->resizeImage($dimensions['largeur'], $dimensions['hauteur'],Imagick::FILTER_LANCZOS,1);
	            $image_frame->writeImage($document['cible_url']['absolute'].$document['frame']);
	            $taille = $image_frame->getImageLength();
	        }

	        $largeur = $dimensions['largeur'];
			$hauteur = $dimensions['hauteur'];

			//sauvegarde les donnees dans la base
	        if (!sql_insertq(
	            "spip_doc2img",
	            array(
	                "id_document" => $id_document,
	                "fichier" => set_spip_doc($document['cible_url']['relative'].$document['frame']),
	                "page" => $frame,
	                "largeur" => $largeur,
	                "hauteur" => $hauteur,
	                "taille" => $taille
	            )
	        )) {
	            return "erreur base de donnée";
	        }

	        if(($frame == 0) && ($config['logo_auto']=='on') && in_array($config['format_cible'],array('png','jpg'))){
	        	if(
	        		($id_vignette = sql_getfetsel('id_vignette','spip_documents','id_document='.intval($id_document)) == 0)
	        		OR !file_exists(get_spip_doc(sql_getfetsel('fichier','spip_documents','id_document='.intval($id_vignette))))
	        	){
	        		if(is_numeric($id_vignette)){
	        			sql_delete('spip_documents','id_document='.intval($id_vignette));
	        		}
		        	$ajouter_documents = charger_fonction('ajouter_documents', 'inc');
					$x = $ajouter_documents($document['cible_url']['absolute'].$document['frame'], $document['cible_url']['absolute'].$document['frame'],
							    'document', $id, 'vignette', $id_document, $actifs);
	        	}
	        }
	        //on libère la frame
	        if ($version == '0.9') {
	            imagick_free($handle_frame);
	        } else {
	            $image_frame->clear();
	            $image_frame->destroy();
	        }
	        $frame++;
	    } while($frame < $nb_pages );

	    /**
	     * Libération de la ressource pour les anciennes versions
	     */
	    if ($version == '0.9') {
	        imagick_free($handle);
	    }

	    // libération du verrou
	    spip_fclose_unlock($fp);
	    //ecrire_config('doc2img/'.$id_document.'/statut','ok');

	    return true;
	}else{
		return false;
	}
}

/**
 * Fonction qui indique si le document a deja été converti
 *
 * @param $id_document identifiant du document à controler
 * @return booleen true / false : true document déjà converti, false sinon
 */
function is_doc2img($id_document) {
    $pages = intval(sql_countsel('spip_doc2img','id_document='.$id_document));
    if ($pages > 0) {
        return true;
    } else  {
        return false;
    }
}


/**
 * Fonction controlant que le document founi peut être converti :
 * - Son extension figure parmi ceux de la configuration
 * - Ce n'est pas un document distant
 *
 *  @param $id_document identifiant du document à controler
 *  @return booleen true/false : true document convertible, false si non
 */
function can_doc2img($id_document = NULL) {
    $info_document = sql_fetsel(
        'extension,mode,distant',
        'spip_documents',
        'id_document = '.$id_document
    );

    //on liste les extensions autorisées depuis CFG
    $types_autorises = explode(',',lire_config("doc2img/format_document",null,true));

    //on controle si le document est convertible ou non
    if (($info_document['mode'] != 'vignette')
    	&& ($info_document['distant'] != 'oui')
    	&& in_array($info_document['extension'],$types_autorises)) {
        return true;
    } else {
        return false;
    }
}

/**
 * Calcul les ratios de taille de l'image finale
 *
 * Vérifie que le document donné en paramètre est bien listé dans les types de documents
 * autorisés à la conversion via CFG
 *
 * @param $id_document identifiant du document à controler
 * @return booleen $resultat : true document convertible, false sinon
 */
function doc2img_ratio(&$handle,$version='0.9',$config=array()) {

    $ratio['largeur'] = $ratio['hauteur'] = 1;

    /**
     * Récupération des dimensions du document d'origine
     */
    if($version == '0.9'){
    	$dimensions['largeur'] = imagick_getwidth($handle);
    	$dimensions['hauteur'] = imagick_getheight($handle);
    }else{
		$dimensions['largeur'] = $handle->getImageWidth();
    	$dimensions['hauteur'] = $handle->getImageHeight();
    }

    //si une largeur seuil a été définie
    if ($largeur = $config['largeur']) {
        $ratio['largeur'] = $largeur / $dimensions['largeur'];
    }

    //si une hauteur seuil a été définie
    if ($hauteur = $config['hauteur']) {
        $ratio['hauteur'] = $hauteur / $dimensions['hauteur'];
    }


    /**
     * Ajustement des ratios si proportion demandée
     * Si agrandissement demandé on prend le plus grand ratio,
     * sinon le plus petit
     */
    if ($config['proportion'] == "on") {
        $ratio['largeur'] = ($config['agrandir'] == 'on') ? max($ratio['hauteur'], $ratio['largeur']) : min($ratio['hauteur'], $ratio['largeur']);
        $ratio['hauteur'] = $ratio['largeur'];
    }

    /**
     * Définition des dimensions définitives
     */
    $dimensions['largeur'] = $ratio['largeur'] * $dimensions['largeur'];
    $dimensions['hauteur'] = $ratio['hauteur'] * $dimensions['hauteur'];

    return $dimensions;
}


/**
 * Fonction pour connaitre les infos fichiers du document
 *
 *  Calcul un tableau :
 *  - avec informations sur le documents (nom, repertoire, nature)
 *  - determine les informations des documents finaux (nom, respertoire, extension)
 *
 * @param $id_document identifiant du document à convertir
 * @return $document : liste de données caractérisant le document
 */
function doc2img_document($id_document) {

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
    if(!is_dir(_DIR_IMG.lire_config('doc2img/repertoire_cible'))){
    	sous_repertoire(_DIR_IMG, lire_config('doc2img/repertoire_cible'));
    }
    $document['cible_url']['relative'] = _DIR_IMG.lire_config('doc2img/repertoire_cible').'/'.$document['name'].'/';
    $document['cible_url']['absolute'] = $racine_site.$document['cible_url']['relative'];

    return $document;
}

?>