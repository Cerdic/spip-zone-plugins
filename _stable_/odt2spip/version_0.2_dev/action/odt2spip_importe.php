<?php

function action_odt2spip_importe() {
    global $visiteur_session;
    
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

	// ss-rep temporaire specifique de l'auteur en cours: tmp/odt2spip/id_auteur/ => le creer si il n'existe pas
    $base_dezip = _DIR_TMP."odt2spip/";   // avec / final
    if (!is_dir($base_dezip)) if (!sous_repertoire(_DIR_TMP,'odt2spip')) die (_T('odtspip:err_repertoire_tmp'));  
    $rep_dezip = $base_dezip.$id_auteur.'/';
    if (!is_dir($rep_dezip)) if (!sous_repertoire($base_dezip,$id_auteur)) die (_T('odtspip:err_repertoire_tmp'));
    
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
    
	// Creation du fichier necessaire a snippets
	  $odt2spip_generer_sortie = charger_fonction('odt2spip_generer_sortie','inc');
	  list($fichier_sortie,$xml_sortie) = $odt2spip_generer_sortie($id_auteur,$rep_dezip);

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
        if (!isset($ajouter_documents)) 
        	$ajouter_documents = charger_fonction('ajouter_documents','inc');
        
        // la y'a un bogue super-bizarre avec la fonction spip_abstract_insert() qui est donnee comme absente lors de l'appel de ajouter_document()
        if (!function_exists('spip_abstract_insert')) include_spip('base/abstract_sql');
        $id_doc_odt = $ajouter_documents($rep_dezip.$fichier_zip, $fichier_zip, "article", $id_article, 'document', 0, $toto='');

        $c = array(
        	'titre' => $titre,
        	'descriptif' => _T('odtspip:cet_article_version_odt')
        );
        include_spip('inc/modifier');
        revision_document($id_doc_odt,$c);
    }
    
    if (!function_exists('effacer_repertoire_temporaire')) include_spip('inc/getdocument');
	// vider le contenu du rep de dezippage
    effacer_repertoire_temporaire($rep_dezip);
    
	// aller sur la page de l'article qui vient d'etre cree
    redirige_par_entete(parametre_url(str_replace("&amp;","&",urldecode($redirect)),'id_article',$id_article,'&'));
}
?>