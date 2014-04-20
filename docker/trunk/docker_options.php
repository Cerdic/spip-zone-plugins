<?php
// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/getdocument');
include_spip('inc/documents');
include_spip('inc/choisir_mode_document'); // compat core
include_spip('inc/renseigner_document');

// les options à définir depuis ecrire/?exec=configurer_docker
include_spip('inc/config');	
$config = lire_config('docker');
$copie_idem= $config['copie_idem'];
$titrer = $config['titrer'];

//si il faut titrer les documents
if($titrer){
	define('_TITRER_DOCUMENTS', true);
}

//si il faut conserver les noms des fichiers originaux, 
//on surcharge les 2 fonctions nécessaires
if($copie_idem){
	
	function inc_nom_fichier_copie_locale($source, $extension){
		//la fonction surchargée est dans inc/distant.php -> inc_nom_fichier_copie_locale
		return inc_nom_fichier_copie_locale_docker($source, $extension);
	}
}
if($copie_idem OR $titrer){	
	function action_ajouter_un_document($id_document, $file, $objet, $id_objet, $mode){
		//la fonction surchargée est dans  plugins-dist/medias/action/ajouter_documents.php -> action_ajouter_un_document
		return action_ajouter_un_document_docker($id_document, $file, $objet, $id_objet, $mode);
	}
}


// Si on doit conserver une copie locale des fichiers distants, autant que ca
// soit a un endroit canonique -- si ca peut etre bijectif c'est encore mieux,
// mais la tout de suite je ne trouve pas l'idee, etant donne les limitations
// des filesystems
// http://doc.spip.org/@nom_fichier_copie_locale_dist
function inc_nom_fichier_copie_locale_docker($source, $extension){
	include_spip('inc/documents');

	$d = creer_repertoire_documents('distant'); # IMG/distant/
	$d = sous_repertoire($d, $extension); # IMG/distant/pdf/

	// on se place tout le temps comme si on etait a la racine
	if (_DIR_RACINE)
		$d = preg_replace(',^' . preg_quote(_DIR_RACINE) . ',', '', $d);
		
	spip_log("docker_options fichier renomme=".$d.basename($source),"docker");
	
	//renvoie le chemin complet final de type ../IMG/pdf/nom_original.pdf
	return $d.basename($source);
}



/**
 * Ajouter un document (au format $_FILES)
 *
 * http://doc.spip.org/@ajouter_un_document
 *
 * @param int $id_document
 *   document a remplacer, ou pour une vignette, l'id_document de maman
 *   0 ou 'new' pour une insertion
 * @param array $file
 *   proprietes au format $_FILE etendu :
 *     string tmp_name : source sur le serveur
 *     string name : nom du fichier envoye
 *     bool titrer : donner ou non un titre a partir du nom du fichier
 *     bool distant : pour utiliser une source distante sur internet
 *     string mode : vignette|image|documents|choix
 * @param string $objet
 *   objet auquel associer le document
 * @param int $id_objet
 *   id_objet
 * @param string $mode
 *   mode par defaut si pas precise pour le document
 * @return array|bool|int|mixed|string|unknown
 * 	 si int : l'id_document ajouté (opération réussie)
 *   si string : une erreur s'est produit, la chaine est le message d'erreur
 *  
 */
function action_ajouter_un_document_docker($id_document, $file, $objet, $id_objet, $mode) {

	$source = $file['tmp_name'];
	$nom_envoye = $file['name'];

	// passer en minuscules le nom du fichier, pour eviter les collisions
	// si le file system fait la difference entre les deux il ne detectera
	// pas que Toto.pdf et toto.pdf
	// et on aura une collision en cas de changement de file system
	#$file['name'] = strtolower(translitteration($file['name']));
	
	// Pouvoir definir dans mes_options.php que l'on veut titrer tous les documents par d?faut
	if (!defined('_TITRER_DOCUMENTS')) { define('_TITRER_DOCUMENTS', false); }

	$titrer = isset($file['titrer'])?$file['titrer']:_TITRER_DOCUMENTS;
	$mode = ((isset($file['mode']) AND $file['mode'])?$file['mode']:$mode);

	include_spip('inc/modifier');
	if (isset($file['distant']) AND $file['distant'] AND !in_array($mode,array('choix','auto','image','document'))) {
		include_spip('inc/distant');
		$file['tmp_name'] = _DIR_RACINE . copie_locale($source);
		$source = $file['tmp_name'];
		unset($file['distant']);
	}

	// Documents distants : pas trop de verifications bloquantes, mais un test
	// via une requete HEAD pour savoir si la ressource existe (non 404), si le
	// content-type est connu, et si possible recuperer la taille, voire plus.
	if (isset($file['distant']) AND $file['distant']) {
		include_spip('inc/distant');
		if (is_array($a = renseigner_source_distante($source))) {

			$champs = $a;
			# NB: dans les bonnes conditions (fichier autorise et pas trop gros)
			# $a['fichier'] est une copie locale du fichier

			unset($champs['type_image']);
		}
		// on ne doit plus arriver ici, car l'url distante a ete verifiee a la saisie !
		else {
			spip_log("Echec du lien vers le document $source, abandon");
			return $a; // message d'erreur
		}
	}
	else { // pas distant

		$champs = array(
			'distant' => 'non'
		);
		
		$type_image = ''; // au pire
		$champs['titre'] = '';
		if ($titrer){
			$titre = substr($nom_envoye,0, strrpos($nom_envoye, ".")); // Enlever l'extension du nom du fichier
			$titre = preg_replace(',[[:punct:][:space:]]+,u', ' ', $titre);
			$champs['titre'] = preg_replace(',\.([^.]+)$,', '', $titre);
		}

		if (!is_array($fichier = fixer_fichier_upload($file, $mode)))
			return is_string($fichier)?$fichier:_T("medias:erreur_upload_type_interdit",array('nom'=>$file['name']));
		
		$champs['inclus'] = $fichier['inclus'];
		$champs['extension'] = $fichier['extension'];
		$champs['fichier'] = $fichier['fichier'];

		/**
		 * Récupère les informations du fichier
		 * -* largeur
		 * -* hauteur
		 * -* type_image
		 * -* taille
		 * -* ses metadonnées si une fonction de metadatas/ est présente
		 */
		$infos = renseigner_taille_dimension_image($champs['fichier'],$champs['extension']);
		if (is_string($infos))
			return $infos; // c'est un message d'erreur !
		
		$champs = array_merge($champs,$infos);

		// Si mode == 'choix', fixer le mode image/document
		if (in_array($mode,array('choix','auto'))) {
			$choisir_mode_document = charger_fonction('choisir_mode_document','inc');
			$mode = $choisir_mode_document($champs, $champs['inclus'] == 'image', $objet);
		}
		$champs['mode'] = $mode;

		if (($test = verifier_taille_document_acceptable($champs))!==true){
			spip_unlink($champs['fichier']);
			return $test; // erreur sur les dimensions du fichier
		}

		unset($champs['type_image']);
		unset($champs['inclus']);
		$champs['fichier'] = set_spip_doc($champs['fichier']);
	}

	// si le media est pas renseigne, le faire, en fonction de l'extension
	if (!isset($champs['media'])){
		$champs['media'] = sql_getfetsel('media_defaut','spip_types_documents','extension='.sql_quote($champs['extension']));
	}
	
	// lier le parent si necessaire
	if ($id_objet=intval($id_objet) AND $objet)
		$champs['parents'][] = "$objet|$id_objet";

	// "mettre a jour un document" si on lui
	// passe un id_document
	if ($id_document=intval($id_document)){
		unset($champs['titre']); // garder le titre d'origine
		unset($champs['date']); // garder la date d'origine
		unset($champs['descriptif']); // garder la desc d'origine
		// unset($a['distant']); # on peut remplacer un doc statique par un doc distant
		// unset($a['mode']); # on peut remplacer une image par un document ?
	}

	include_spip('action/editer_document');
	// Installer le document dans la base
	if (!$id_document){
		if ($id_document = document_inserer())
			spip_log ("ajout du document ".$file['tmp_name']." ".$file['name']."  (M '$mode' T '$objet' L '$id_objet' D '$id_document')",'medias');
		else
			spip_log ("Echec insert_document() du document ".$file['tmp_name']." ".$file['name']."  (M '$mode' T '$objet' L '$id_objet' D '$id_document')",'medias'._LOG_ERREUR);
	}
	if (!$id_document)
		return _T('medias:erreur_insertion_document_base',array('fichier'=>"<em>".$file['name']."</em>"));
	
	document_modifier($id_document,$champs);
	
	
//++ ajout plugin docker
		$row = sql_select('titre,credits','spip_documents','id_document='.sql_quote($id_document));

		if(!isset($row['titre'])){
			$path_parts = pathinfo($source);
			$extension = $path_parts ? $path_parts['extension'] : '';
			if (isset($row['credits'])) $nom_envoye = basename($row['credits']);
			// retourne le nom du fichier, mon code
			$nom_envoye = preg_replace('#(?:.*)[^:]/(.*)#Umis','$1',$source);
			$fichier = "$extension/$nom_envoye";
			
			$insert['titre'] = '';
			if ($titrer){
				$titre = substr($nom_envoye,0, strrpos($nom_envoye, ".")); // Enlever l'extension du nom du fichier
				$titre = preg_replace(',[[:punct:][:space:]]+,u', ' ', $titre);
				$insert['titre'] = preg_replace(',\.([^.]+)$,', '', $titre);
			}
			spip_log("source=$source et local=$local et fichier=$fichier et id_document=$id_document et nom_envoye=$nom_envoye et row['credits']=".$row['credits'],"titrer_document");
			include_spip('inc/modifier');
			document_modifier($id_document,$insert);
		}


	// permettre aux plugins de faire des modifs a l'ajout initial
	// ex EXIF qui tourne les images si necessaire
	// Ce plugin ferait quand même mieux de se placer dans metadata/jpg.php
	pipeline('post_edition',
		array(
			'args' => array(
				'table' => 'spip_documents', // compatibilite
				'table_objet' => 'documents',
				'spip_table_objet' => 'spip_documents',
				'type' =>'document',
				'id_objet' => $id_document,
				'champs' => array_keys($champs),
				'serveur' => '', // serveur par defaut, on ne sait pas faire mieux pour le moment
				'action' => 'ajouter_document',
				'operation' => 'ajouter_document', // compat <= v2.0
			),
			'data' => $champs
		)
	);

	return $id_document ;
}


?>
