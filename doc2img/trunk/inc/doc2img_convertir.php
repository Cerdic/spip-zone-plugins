<?php
/**
 * Plugin Doc2img
 * Conversion du fichier
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

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
function inc_doc2img_convertir($id_document,$opt='full') {
	spip_log('conversion du doc '.$id_document,'docimg');
	@set_time_limit(0);
	if(!in_array($opt,array('full','vignette'))){
		if(isset($opt['options']) && in_array($opt['options'],array('full','vignette')))
			$type = $opt['options'];
		else
			$type = 'full';
	}else
		$type = $opt;

	$ret = array();
	if(class_exists('Imagick')){
	    include_spip('inc/documents');
		include_spip('inc/config');
		include_spip('action/editer_document');

		/**
		 * Si cette action est lancée en CRON, on ne peut supprimer les documents ensuite
		 * TODO trouver mieux
		 */
		if(!isset($GLOBALS['visiteur_session'])
		OR !is_array($GLOBALS['visiteur_session']))
			$GLOBALS['visiteur_session'] = sql_fetsel('*','spip_auteurs','webmestre="oui"');

	    $config = lire_config('doc2img',array());
		$format_cible = $config['format_cible'] ? $config['format_cible'] : 'png';
	    $document = doc2img_document($id_document);

	    /**
	     * Chargement du document en mémoire
	     * On détermine le nombre de pages du document
	     * On libère la ressource automatiquement si on utilise la class
	     * car on réouvre chaque page par la suite
	     */

	    $frame = 0;

		$resolution = (isset($config['resolution']) && intval($config['resolution']) > 150) ? $config['resolution'] : 150;
		$ajouter_documents = charger_fonction('ajouter_documents', 'action');

		if($type == 'full'){
			try{
				$image = new Imagick($document['fichier']);
				$identify = $image->identifyImage();
				$nb_pages = $image->getNumberImages();
				$image->clear();
				$image->destroy();

				/**
				 * Est ce que ce document a déja été converti
				 * Si oui, on supprime son ancienne conversion
				 */
				$documents_doc2img = sql_select('L1.id_document',
												'spip_documents AS L1 LEFT JOIN spip_documents_liens AS L2 ON L1.id_document=L2.id_document',
												'L1.mode="doc2img" AND L2.objet="document" AND L2.id_objet='.intval($id_document));

				$documents_a_supprimer = array();
				while($document_doc2img = sql_fetch($documents_doc2img)){
					$documents_a_supprimer[] = $document_doc2img['id_document'];
				}
				if(count($documents_a_supprimer) > 0){
					$supprimer_document = charger_fonction('supprimer_document','action');
					foreach ($documents_a_supprimer as $id_document_supprimer) {
						$supprimer_document($id_document_supprimer);
					}
				}
				unset($documents_a_supprimer,$documents_doc2img,$identify);
			    // chaque page est un fichier qu'on sauve dans la table doc2img indexé
			    // par son numéro de page
			    do {
			    	$image_frame = new Imagick();
			        //on accede à la page $frame

						if(is_numeric($resolution) && ($resolution <= '600'))
				        	$image_frame->setResolution($resolution,$resolution);

						$image_frame->readImage($document['fichier'].'['.$frame.']');
						$image_frame->setImageFormat($format_cible);

						if(is_numeric($config['compression']) && ($config['compression'] > 50) && ($config['compression'] <= 100))
							$image_frame->setImageCompressionQuality($config['compression']);

				        //calcule des dimensions
				        //$dimensions = doc2img_ratio($image_frame,$config);

				        //nom du fichier cible, c'est à dire la frame (image) indexée
				        $frame_name = $document['name'].'-'.$frame.'.'.$format_cible;
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
						unset($files);
				        if(($frame == 0) && ($config['logo_auto']=='on') && in_array($format_cible,array('png','jpg'))){
				        	$id_vignette = $document['id_vignette'];
							$frame_tmp = $document['cible_url'].$document['name'].'-logo.'.$format_cible;
							$image_frame->writeImage($frame_tmp);
							$files = array(array('tmp_name'=>$frame_tmp,'name'=>$frame_name));
			        		if(is_numeric($id_vignette) && ($id_vignette > 0))
			        			$vignette = $ajouter_documents($id_vignette, $files,'', 0, 'vignette');	
			        		else
								$vignette = $ajouter_documents('new', $files,'', 0, 'vignette');

							if (is_numeric(reset($vignette)) AND $id_vignette = reset($vignette))
								document_modifier($id_document,array("id_vignette" => intval($id_vignette)));
							spip_unlink($document['cible_url'].$frame_tmp);
							unset($vignette,$files);
				        }

				        spip_unlink($document['cible_url'].$frame_name);
			            unset($frame_name,$dest);
						$frame++;
						document_modifier(reset($x),array('page'=>$frame));
						unset($x);
						$image_frame->clear();
						$image_frame->destroy();
						$invalider = true;
					} while($frame < $nb_pages);
				}
			catch ( ImagickException $e ){
				    spip_log('On a une erreur','docimg');
					spip_log($e,'docimg');
			}
	    }else{
	    	try{
		    	do {
		    		if(in_array($format_cible,array('png','jpg'))){
			        	$image_frame = new Imagick();
			        	if(is_numeric($resolution) && ($resolution <= '600')){
			        		spip_log('set resolution','docimg');
				        	$image_frame->setResolution($resolution,$resolution);
			        	}
						$image_frame->readImage($document['fichier'].'['.$frame.']');
						$image_frame->setImageFormat($format_cible);
						if(is_numeric($config['compression']) && ($config['compression'] > 50) && ($config['compression'] <= 100)){
							$image_frame->setImageCompressionQuality($config['compression']);
						}

				        //nom du fichier cible, c'est à dire la frame (image) indexée
				        $frame_name = $document['name'].'-logo.'.$format_cible;

				        //on sauvegarde la page
			            $image_frame->writeImage($document['cible_url'].$frame_name);
						$image_frame->clear();
			            $image_frame->destroy();

						$files = array(array('tmp_name'=>$document['cible_url'].$frame_name,'name'=>$frame_name));
						$id_vignette = $document['id_vignette'];
			        	if(is_numeric($id_vignette) && ($id_vignette > 0))
		        			$x = $ajouter_documents($id_vignette, $files,'', 0, 'vignette');	
		        		else
							$x = $ajouter_documents('new', $files,'', 0, 'vignette');

						if (is_numeric(reset($x)) AND $id_vignette = reset($x))
							document_modifier($id_document,array("id_vignette" => intval($id_vignette)));

						spip_unlink($document['cible_url'].$frame_name);
		            }else
						spip_log("DOC2IMG : le format de sortie sélectionné dans la configuration ne permet pas de créer une vignette",'docimg');
					$invalider = true;
			        $frame++;
			    } while($frame < 1 );
			}catch ( ImagickException $e ){
				    spip_log('On a une erreur','docimg');
					spip_log($e,'docimg');
			}
	    }
		$ret['success'] = true;
		if($invalider){
			include_spip('inc/invalideur');
			suivre_invalideur('id_document="$id_document"');
		}
	    return $ret;
	}else{
		spip_log('Erreur Doc2Img : La class doc2img n est pas disponible');
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
        'fichier,extension,id_vignette',
        'spip_documents',
        'id_document='.$id_document
    );

    //chemin relatif du fichier
    $fichier_reel = get_spip_doc($fichier['fichier']);

    //url relative du repertoire contenant le fichier , on retire aussi le / en fin
    $document['fichier'] = $fichier_reel;

    //information sur le nom du fichier
    $document['extension'] = $fichier['extension'];
    $document['name'] = basename($fichier_reel);
	$document['id_vignette'] = $fichier['id_vignette'];

    //creation du repertoire cible
    //url relative du repertoire cible
    if(!is_dir(_DIR_VAR."cache-doc2img"))
    	sous_repertoire(_DIR_VAR,"cache-doc2img");
	
    $document['cible_url'] = _DIR_VAR."cache-doc2img".'/';

    return $document;
}

?>