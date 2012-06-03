<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Fonction autonome convertissant un document donné en paramètre
 *
 *  Ensemble des actions necessaires à la conversion d'un document en image :
 *  - recupère les informations sur le documents (nom, repertoire, nature)
 *  - determine les informations sur le documents final (nom, repertoire, extension)
 *
 *  Documentation intéressante :
 *  - http://valokuva.org/?p=7
 *  - http://valokuva.org/?p=7#comment-19198
 *
 * @param int $id_document identifiant du document à convertir
 * @param string $type méthode à utiliser :
 * 		- full converti tout le document
 * 		- vignette converti la première page en vignette du document
 */
function inc_doc2img_convertir($id_document,$type='full') {
	if(!in_array($type,array('full','vignette')))
		$type = 'full';
	
	if(class_exists('Imagick')){
	    // NOTE : les repertoires doivent se finir par un /

	    include_spip('inc/documents');
	    include_spip('inc/flock');
		include_spip('inc/config');

	    $config = lire_config('doc2img',array());

	    $document = doc2img_document($id_document);
		spip_log($document,'doc2img');

	    /**
	     * Chargement du document en mémoire
	     * On détermine le nombre de pages du document
	     * On libère la ressource automatiquement si on utilise la class
	     * car on réouvre chaque page par la suite
	     */
		$image = new Imagick($document['fichier']);
		$identify = $image->identifyImage();
		$identify2 = $image->getImageProperties();
		$nb_pages = $image->getNumberImages();
		$image->clear();
		$image->destroy();

	    $frame = 0;

		$resolution = $config['resolution'] ? $config['resolution'] : 150;
		
		$ajouter_documents = charger_fonction('ajouter_documents', 'action');
		
		if($type == 'full'){
			/**
			 * Est ce que ce document a déja été converti
			 * Si oui, on supprime son ancienne conversion
			 */
			$documents_doc2img = sql_select('L1.id_document',
											'spip_documents AS L1 LEFT JOIN spip_documents_liens AS L2 ON L1.id_document=L2.id_document',
											'L1.mode="doc2img" AND L2.objet="document" AND L2.id_objet='.intval($id_document));
			
			$documents_a_supprimer = array();
			while($document_doc2img = sql_fetch($documents_doc2img)){
				spip_log($document_doc2img,'doc2img');
				$documents_a_supprimer[] = $document_doc2img['id_document'];
			}
			if(count($documents_a_supprimer) > 0){
				spip_log('On supprime les documents','doc2img');
				spip_log($documents_a_supprimer,'doc2img');
				$supprimer_document = charger_fonction('supprimer_document','action');
				foreach ($documents_a_supprimer as $id_document) {
					$supprimer_document($id_document); // pour les orphelins du contexte, on traite avec la fonction existante
				}
			}
		    // chaque page est un fichier qu'on sauve dans la table doc2img indexé
		    // par son numéro de page
		    do {
		    	spip_log("Conversion de la page $frame",'doc2img');
		        //on accede à la page $frame
	        	$image_frame = new Imagick();
	        	if(is_numeric($resolution) && ($resolution <= '600') && ($resolution > $identify['resolution']['x'])){
		        	$image_frame->setResolution($resolution,$resolution);
	        	}
				$image_frame->readImage($document['fichier'].'['.$frame.']');
				$image_frame->setImageFormat($config['format_cible']);
				if(is_numeric($config['compression']) && ($config['compression'] > 50) && ($config['compression'] <= 100)){
					$image_frame->setImageCompressionQuality($config['compression']);
				}
	
		        //calcule des dimensions
		        //$dimensions = doc2img_ratio($image_frame,$config);
	
		        //nom du fichier cible, c'est à dire la frame (image) indexée
		        $frame_name = $document['name'].'-'.$frame.'.'.$config['format_cible'];
				$dest = $document['cible_url'].$frame_name;
		        //on sauvegarde la page
		        
	        	//$image_frame->resizeImage($dimensions['largeur'], $dimensions['hauteur'],Imagick::FILTER_LANCZOS,1);
	            $image_frame->writeImage($dest);
				
				/**
				 * On ajoute le document dans la table spip_documents avec comme type "doc2img"
				 * Il sera automatiquement lié au document original
				 */
				$files = array(array('tmp_name'=>$dest,'name'=>$frame_name));
				$x = $ajouter_documents('new', $files,'document', $id_document, 'doc2img');

		        if(($frame == 0) && ($config['logo_auto']=='on') && in_array($config['format_cible'],array('png','jpg'))){
		        	if(
		        		($id_vignette = sql_getfetsel('id_vignette','spip_documents','id_document='.intval($id_document)) == 0)
		        		OR !file_exists(get_spip_doc(sql_getfetsel('fichier','spip_documents','id_document='.intval($id_vignette))))
		        	){
						$frame_tmp = $document['cible_url'].$document['name'].'-logo.'.$config['format_cible'];
						$image_frame->writeImage($frame_tmp);
						$files = array(array('tmp_name'=>$frame_tmp,'name'=>$frame_name));
						spip_log('On va ajouter une vignette','doc2img');
		        		if(is_numeric($id_vignette)){
		        			$supprimer_document = charger_fonction('supprimer_document','action');
							$supprimer_document($id_vignette);
							spip_log('suppression de la vignette '.$id_vignette,'doc2img');
		        		}
						$x = $ajouter_documents($id_document, $files,'document', $id_document, 'vignette');
						spip_log($x,'doc2img');
						spip_log('On ajouter une vignette '.$x,'doc2img');
		        	}
		        }
		        //on libère la frame
	            $image_frame->clear();
	            $image_frame->destroy();
		        $frame++;
		    } while($frame < $nb_pages );
	    }else{
	    	do {
	    		if(in_array($config['format_cible'],array('png','jpg'))){
			        //on accede à la page $frame
		        	$image_frame = new Imagick();
		        	if(is_numeric($resolution) && ($resolution <= '600') && ($resolution > $identify['resolution']['x'])){
			        	$image_frame->setResolution($resolution,$resolution);
		        	}
					$image_frame->readImage($document['fichier'].'['.$frame.']');
					$image_frame->setImageFormat($config['format_cible']);
					if(is_numeric($config['compression']) && ($config['compression'] > 50) && ($config['compression'] <= 100)){
						$image_frame->setImageCompressionQuality($config['compression']);
					}
		
			        //nom du fichier cible, c'est à dire la frame (image) indexée
			        $document['frame'] = $document['name'].'-'.$frame.'.'.$config['format_cible'];
		
			        //on sauvegarde la page
		            $image_frame->writeImage($document['cible_url'].$document['frame']);
		        	if(
		        		($id_vignette = sql_getfetsel('id_vignette','spip_documents','id_document='.intval($id_document)) == 0)
		        		OR !file_exists(get_spip_doc(sql_getfetsel('fichier','spip_documents','id_document='.intval($id_vignette))))
		        	){
		        		if(is_numeric($id_vignette)){
		        			sql_delete('spip_documents','id_document='.intval($id_vignette));
		        		}
						
						$x = $ajouter_documents($document['cible_url'].$document['frame'], $document['cible_url'].$document['frame'],
								    'document', $id, 'vignette', $id_document, $actifs);
		        	}
		            $image_frame->clear();
		            $image_frame->destroy();
	            }else{
					spip_log("DOC2IMG : le format de sortie sélectionné dans la configuration ne permet pas de créer une vignette",'doc2img');
	            }
		        $frame++;
		    } while($frame < 1 );
	    }

	    // libération du verrou
	    spip_fclose_unlock($fp);
	    return true;
	}else{
		spip_log('Erreur Doc2Img : La class doc2img n est pas disponible');
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
        'id_document = '.intval($id_document)
    );

    //on liste les extensions autorisées depuis CFG
    $types_autorises = explode(',',lire_config('doc2img/format_document','pdf,tiff,bmp'));

    //on controle si le document est convertible ou non
    if (!in_array($info_document['mode'],array('doc2img','vignette'))
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
function doc2img_ratio($handle,$config=array()) {

    $ratio['largeur'] = $ratio['hauteur'] = 1;

    /**
     * Récupération des dimensions du document d'origine
     */
	$dimensions['largeur'] = $handle->getImageWidth();
	$dimensions['hauteur'] = $handle->getImageHeight();

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
    $fichier = sql_fetsel(
        '*',
        'spip_documents',
        'id_document='.$id_document
    );

    //chemin relatif du fichier
    $fichier = get_spip_doc($fichier['fichier']);

    //nom complet du fichier : recherche ce qui suit le dernier / et retire ce dernier
    // $resultat[0] = $resultat[1]/$resultat[2].$resultat[3]
    preg_match('/(.*)\/(.*)\.(.\w*)/i', $fichier, $result);

    //url relative du repertoire contenant le fichier , on retire aussi le / en fin
    $document['fichier'] = $fichier;

    //information sur le nom du fichier
    $document['extension'] = $fichier['extension'];
    $document['name'] = $result[2];
    $document['fullname'] = basename($fichier);

    //creation du repertoire cible
    //url relative du repertoire cible
    if(!is_dir(_DIR_VAR."cache-doc2img")){
    	sous_repertoire(_DIR_VAR,"cache-doc2img");
    }
    $document['cible_url'] = _DIR_VAR."cache-doc2img".'/';

    return $document;
}

?>