<?

function action_odt2spip_importe() {
    global $visiteur_session, $spip_version_code;;
    
    $id_auteur = $visiteur_session['id_auteur'];
    $arg = _request('arg');
    $args = explode(":",$arg);
	
    // le 1er element de _request('arg') est id_rubrique=XXX
    $Targs = explode("=", $args[0]);
    $id_rubrique = $Targs[1];
    $hash = _request('hash');
    
    $redirect = _request('redirect');
	if ($redirect==NULL) $redirect="";
    
    include_spip("inc/securiser_action");
    
    if (!autoriser('creerarticledans', 'rubrique', $id_rubrique)) die(_T('avis_non_acces_page'));
		
	// les chemins a utiliser
	$rep_IMG = _DIR_RACINE._NOM_PERMANENTS_ACCESSIBLES;
    
	// ss-rep temporaire specifique de l'auteur en cours: tmp/odt2spip/id_auteur/ => le creer si il n'existe pas
    $base_dezip = _DIR_TMP."odt2spip/";   // avec / final
    if (!is_dir($base_dezip)) if (!mkdir($base_dezip,0777)) die (_T('odtspip:err_repertoire_tmp'));  
    $rep_dezip = $base_dezip.$id_auteur.'/';
    if (!is_dir($rep_dezip)) if (!mkdir($rep_dezip,0777)) die (_T('odtspip:err_repertoire_tmp'));  
    
    $rep_pictures = $rep_dezip."Pictures/";
    
	// parametres de conversion de taille des images : cm -> px (en 96 dpi puisque c'est ce que semble utiliser Writer)
    $conversion_image = 96/2.54;
    
	// traitement d'un fichier odt envoye par $_POST 
    $fichier_zip = addslashes($_FILES['fichier_odt']['name']);
    if ($_FILES['fichier_odt']['name'] == '' 
        OR $_FILES['fichier_odt']['error'] != 0
        OR !move_uploaded_file($_FILES['fichier_odt']['tmp_name'], $rep_dezip.$fichier_zip)
       )  die(_T('odtspip:err_telechargement_fichier'));

  // dezipper le fichier odt a la mode SPIP
    include_spip("inc/pclzip");
    $zip = new PclZip($rep_dezip.$fichier_zip);
	  $ok = $zip->extract(
        PCLZIP_OPT_PATH, $rep_dezip,
        PCLZIP_OPT_SET_CHMOD, _SPIP_CHMOD,
        PCLZIP_OPT_REPLACE_NEWER
	  );
	  if ($zip->error_code < 0) {
		    spip_log('charger_decompresser erreur zip ' . $zip->error_code .' pour fichier ' . $rep_dezip.$fichier_zip);
		    die($zip->errorName(true));  //$zip->error_code
	  }
    
	// variables en dur pour xml en entree et xslt utilisee
	// $xml_entre = $rep_dezip.'content.xml';  // chemin du fichier xml a lire  !!! ce chemin absolu ne fonctionne pas pour PHP4 !!!
	$xml_entre = _DIR_TMP.'odt2spip/'.$id_auteur.'/content.xml';  // chemin du fichier xml a lire
	$xslt_texte = _DIR_PLUGIN_ODT2SPIP.'inc/odt2spip.xsl'; //'inc/odt2spip_texte.xsl';  // chemin de la xslt a utiliser pour le texte
    
	// fichier de sortie
	$fichier_sortie = $rep_dezip.'snippet_odt2spip.xml';

	// determiner si le plugin enluminure_typo ou intertitres_enrichis est present & actif
    include_spip('inc/plugin');
    $Tplugins = liste_plugin_actifs();
    $intertitres_riches = ((array_key_exists('TYPOENLUMINEE', $Tplugins) OR array_key_exists('INTERTITRESTDM', $Tplugins)) ? 'oui' : 'non'); 
    
	// appliquer la transformation XSLT sur le fichier content.xml
	// daterminer si on est en php 4 ou php 5 pour choisir les fonctions xslt a utiliser
	// on est php5: utiliser les fonctions de la classe XSLTProcessor
	// verifier que l'extension xslt est active
	if (!class_exists('XSLTProcessor')) die(_T('odtspip:err_extension_xslt'));
        $proc = new XSLTProcessor();

		// passage d'un parametre a la xslt
        $proc->setParameter(null, 'IntertitresRiches', $intertitres_riches);   
        
        $xml = new DOMDocument();
        $xml->load($xml_entre);
        $xsl = new DOMDocument();
        $xsl->load($xslt_texte);
        $proc->importStylesheet($xsl); // attachement des r�gles xsl
        
      // lancer le parseur
        if (!$xml_sortie = $proc->transformToXml($xml)) die(_T('odtspip:err_transformation_xslt'));

	// traitements complementaires du flux de sortie
    // remplacer les &gt; et &lt;
    $a_remplacer = array('&#60;','&#62;','&lt;','&gt;', '"', "<date/>");
    $remplace = array('<','>','<','>', "'", '<date>'.(date("Y-m-d H:i:s")).'</date>');
    $xml_sortie = str_replace($a_remplacer, $remplace, $xml_sortie);
    
    // virer les sauts de ligne multiples
    $xml_sortie = preg_replace('/([\r\n]{2})[ \r\n]*/m', "$1", $xml_sortie);
        
    // traiter les images: dans tous les cas il faut les integrer dans la table documents 
    // en 2.0 c'est mode image + les fonctions de snippets font la liaison => on bloque la liaison en filant un id_article vide
	$id_article_tmp = '';    
	preg_match_all('/<img([;a-zA-Z0-9\.]*)/', $xml_sortie, $match, PREG_PATTERN_ORDER);
	if (@count($match) > 0) {
		include_spip('inc/ajouter_document');
        $T_images = array();
        foreach($match[1] as $ch) {
            $Tdims = explode(';;;', $ch);
            $img = $Tdims[0];
            if (file_exists($rep_pictures.$img)) {
              // retailler l'image en fct des parametres ;;;largeur;;;hauteur;;;
                $largeur = round($Tdims[1]*$conversion_image);
                $hauteur = round($Tdims[2]*$conversion_image);
                odt2spip_retailler_img($rep_pictures.$img, $largeur, $hauteur);
				// integrer l'image comme document spip
                $ajouter_documents = charger_fonction('ajouter_documents','inc');
                $type = 'image';
                if ($id_document = $ajouter_documents($rep_pictures.$img, $img, "article", $id_article_tmp, $type, 0, $toto='')) { 
				// uniformiser la sortie: si on est en 1.9.2 inc_ajouter_documents_dist() retourne le type de fichier (extension) alors qu'en 2.0 c'est l'id_document
					if (!is_numeric($id_document)) {
						$Ttmp = explode('.', $img);
                        $nom_fic = $Ttmp[0];
                        $id_document = sql_getfetsel("id_document","spip_documents","fichier LIKE '%$nom_fic%' ORDER BY maj DESC LIMIT 1");
                    }
                    $xml_sortie = str_replace($ch, $id_document, $xml_sortie);
                    $T_images[] = $id_document;
                };
            }
        }
    }
    
	//finalement enregistrer le contenu dans /tmp/odt2spip/id_auteur/snippet_odt2spip.xml
    if (!file_put_contents($fichier_sortie, $xml_sortie)) die(_T('odtspip:err_enregistrement_fichier_sortie').$fichier_sortie);

	// generer l'article a partir du fichier xml de sortie (code pompe sur plugins/snippets/action/snippet_importe.php)
    include_spip('inc/snippets');
		$table = $id = 'articles';
		$contexte = $args[0];
		$source = $fichier_sortie;
    if (!$f = snippets_fonction_importer($table)) die(_T('odtspip:err_import_snippet'));
    include_spip('inc/xml');
    $arbre = spip_xml_load($source, false);
    $translations = $f($id,$arbre,$contexte);
    snippets_translate_raccourcis_modeles($translations);
    $id_article = $translations[0][2]; 

	// si on est en 2.0 passer le statut de l'article en prepa
    sql_updateq('spip_articles', array('statut' => 'prop'), 'id_article='.$id_article);
    
	// si necessaire attacher le fichier odt original a l'article et lui mettre un titre signifiant
    if (_request('attacher_odt') == '1') {
		// recuperer le titre
        preg_match('/<titre>(.*?)<\/titre>/', $xml_sortie, $match);
        $titre = $match[1];
        if (!isset($ajouter_documents)) $ajouter_documents = charger_fonction('ajouter_documents','inc');
        $id_doc_odt = $ajouter_documents($rep_dezip.$fichier_zip, $fichier_zip, "article", $id_article, 'document', 0, $toto='');
        if (!is_numeric($id_doc_odt)) {
            $Tfic = explode('.', $fichier_zip);
            $fichier_zip_av_extension = $Tfic[0];
            $id_doc_odt = sql_getfetsel("id_document","spip_documents","fichier LIKE '%$fichier_zip_av_extension%' ORDER BY maj DESC LIMIT 1");
        }
		sql_updateq('spip_documents', array('titre' => $titre, 'descriptif' => _T('odtspip:cet_article_version_odt')), 'id_document='.$id_doc_odt);
    }
    
	// vider le contenu du rep de dezippage
    effacer_repertoire_temporaire($rep_dezip);
    
	// aller sur la page de l'article qui vient d'etre cree
    redirige_par_entete(str_replace("&amp;","&",urldecode($redirect.$id_article)));
}

// retailler une image : (ne gere que les images GIF, JPG et PNG)
// $img_ini = CHEMIN+NOM_FICHIER de l'img initiale, $l et $h = largeur et hauteur max de l'image finale
// gestion de la transparence des PNG : code de matt1walsh@gmail.com sur http://fr2.php.net/manual/fr/function.imagecopyresampled.php
function odt2spip_retailler_img($img_ini, $l = '', $h = 400) {
    if (!file_exists($img_ini)) return 'Le fichier '.$img_ini.' n\'existe pas';
	// determiner le type de fonction de creation d'image a utiliser 
    $param_img = getimagesize($img_ini);
    $type_img = $param_img[2];
    switch ($type_img) {
        case 1 :
            $fct_creation_ext = 'imagecreatefromgif';
            $fct_ecrire = 'imagegif';
        break;
        case 2 :
            $fct_creation_ext = 'imagecreatefromjpeg';
            $fct_ecrire = 'imagejpeg';
        break;
        case 3 :
            $fct_creation_ext = 'imagecreatefrompng';
            $fct_ecrire = 'imagepng';
        break;
        default :
            return;
        break;
    } 
	// calculer le ratio a appliquer aux dimensions initiales
    $l_ini = $param_img[0];
    $h_ini = $param_img[1];
    $ratio = ($l != '' ? (abs($l_ini - $l) >= abs($h_ini - $h) ? $l/$l_ini : $h/$h_ini) : $h/$h_ini); 
    $img_nv = imagecreatetruecolor($l_ini*$ratio, $h_ini*$ratio); 
    $img_acopier = $fct_creation_ext($img_ini);
	
    // gerer la transparence pour les images PNG (le mec qui a trouv� ce code est g�nial! :-)
    if ($type_img == 3) {
        imagecolortransparent($img_nv, imagecolorallocate($img_nv, 0, 0, 0));
        imagealphablending($img_nv, false);
        imagesavealpha($img_nv, true);
    }
    imagecopyresampled($img_nv, $img_acopier, 0, 0, 0, 0, $l_ini*$ratio, $h_ini*$ratio, $l_ini, $h_ini);                     
	// sauvegarder l'image et eventuellement detruire le fichier image initial
    $fct_ecrire($img_nv, $img_ini);
    imagedestroy($img_nv);
    imagedestroy($img_acopier);
}
?>