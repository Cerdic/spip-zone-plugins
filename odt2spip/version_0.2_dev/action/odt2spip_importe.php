<?php

/**
 * Créer un article à partir d'un fichier au format odt
 *
 * @author cy_altern
 * @license GNU/LGPL
 *
 * @package plugins
 * @subpackage odt2spip
 * @category import
 *
 * @version $Id$
 *
 */
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Création de l'article et redirection vers celui-ci
 *
 * Le fichier .odt est envoyé par un formulaire, ainsi que des informations sur
 * la rubrique dans laquelle créer l'article, un flag qui indique s'il faut joindre
 * le document à l'article créé, etc..
 * Cette fonction s'assure avant tout que l'utilisateur peut y ajouter un article.
 * Le fichier .odt est traité et transformé en article.
 * En fin de traitement, on est redirigé vers l'article qui vient d'être créé.
 *
 * {@internal
 * Un répertoire temporaire, spécifique à l'utilisateur en cours, est utilisé et
 * créé si nécessaire. Il est supprimé en fin de traitement.
 * Le format odt correspond à une archive .zip, et regroupe le contenu dans un fichier
 * content.xml : ce fichier est transformé par XSLT afin de générer un texte
 * utilisant les balises SPIP pour sa mise en forme.
 * }}
 * 
 */
function action_odt2spip_importe() {
	global $visiteur_session;

	$id_auteur = $visiteur_session['id_auteur'];
	$arg = _request('arg');
	$args = explode(":", $arg);

	// le 1er élément de _request('arg') est id_rubrique=XXX
	$Targs = explode("=", $args[0]);
	$id_rubrique = intval($Targs[1]);
	$hash = _request('hash');

	$redirect = _request('redirect');
	if ($redirect == NULL) {
		$redirect = "";
	}

	include_spip("inc/securiser_action");

	if (!autoriser('creerarticledans', 'rubrique', $id_rubrique)) {
		die(_T('avis_non_acces_page'));
	}

	// ss-rep temporaire specifique de l'auteur en cours: tmp/odt2spip/id_auteur/
	// => le créer s'il n'existe pas
	$base_dezip = _DIR_TMP . "odt2spip/";	  // avec / final
	if (!is_dir($base_dezip) AND !sous_repertoire(_DIR_TMP, 'odt2spip')) {
		die (_T('odtspip:err_repertoire_tmp'));
	}
	$rep_dezip = $base_dezip . $id_auteur . '/';
	if (!is_dir($rep_dezip) AND !sous_repertoire($base_dezip, $id_auteur)) {
		die (_T('odtspip:err_repertoire_tmp'));
	}
    $rep_pictures = $rep_dezip."Pictures/";
    
	// paramètres de conversion de taille des images : cm -> px (en 96 dpi puisque c'est ce que semble utiliser Writer)
	$conversion_image = 96/2.54;
    
	// traitement d'un fichier odt envoye par $_POST
	$fichier_zip = addslashes($_FILES['fichier_odt']['name']);
	if ($_FILES['fichier_odt']['name'] == '' 
	    OR $_FILES['fichier_odt']['error'] != 0
	    OR !move_uploaded_file($_FILES['fichier_odt']['tmp_name'], $rep_dezip . $fichier_zip)) {
		die(_T('odtspip:err_telechargement_fichier'));
	}

	// dezipper le fichier odt a la mode SPIP
	include_spip("inc/pclzip");
	$zip = new PclZip($rep_dezip . $fichier_zip);
	$ok = $zip->extract(
			PCLZIP_OPT_PATH, $rep_dezip,
			PCLZIP_OPT_SET_CHMOD, _SPIP_CHMOD,
			PCLZIP_OPT_REPLACE_NEWER
	);
	if ($zip->error_code < 0) {
		spip_log('charger_decompresser erreur zip ' . 
				$zip->error_code . ' pour fichier ' . $rep_dezip . $fichier_zip);
		die($zip->errorName(true));	 //$zip->error_code
	}

	// Création de l'array avec les parametres de l'article
	$odt2spip_generer_sortie = charger_fonction('odt2spip_generer_sortie', 'inc');
	$Tarticle = $odt2spip_generer_sortie($id_auteur, $rep_dezip);
//	list($fichier_sortie, $xml_sortie) = $odt2spip_generer_sortie($id_auteur, $rep_dezip);
/*

	// générer l'article à partir du fichier xml de sortie
	// (code pompé sur plugins/snippets/action/snippet_importe.php)
	include_spip('inc/snippets');
	$table = $id = 'articles';
	$contexte = $args[0];
	$source = $fichier_sortie;
	if (!$f = snippets_fonction_importer($table)) {
		die(_T('odtspip:err_import_snippet'));
	}
	include_spip('inc/xml');
	$arbre = spip_xml_load($source, false);
	$translations = $f($id, $arbre, $contexte);
	snippets_translate_raccourcis_modeles($translations);
	$id_article = $translations[0][2];

	// si on est en 2.0 passer le statut de l'article en prepa
	sql_updateq('spip_articles', array('statut' => 'prop'), 'id_article=' . $id_article);
*/
	// créer l'article
	include_spip('action/editer_article');
	$id_article = article_inserer($id_rubrique);
	// le remplir
	article_modifier($id_article, $Tarticle);

	// si nécessaire attacher le fichier odt original à l'article
	// et lui mettre un titre signifiant
	if (_request('attacher_odt') == '1') {
		$titre = $Tarticle['titre'];
		if (!isset($ajouter_documents)) {
			$ajouter_documents = charger_fonction('ajouter_documents','action');
		}
		
		// la y'a un bogue super-bizarre avec la fonction spip_abstract_insert()
		// qui est donnée comme absente lors de l'appel de ajouter_document()
		if (!function_exists('spip_abstract_insert')) {
			include_spip('base/abstract_sql');
		}
		$id_doc_odt = $ajouter_documents($rep_dezip . $fichier_zip, $fichier_zip,
					"article", $id_article, 'document', 0, $toto='');

		$c = array(
			'titre' => $titre,
			'descriptif' => _T('odtspip:cet_article_version_odt')
		);
		include_spip('inc/modifier');
		objet_modifier_champs('document',$id_doc_odt,$c);
	}

	// vider le contenu du rep de dezippage
	if (!function_exists('effacer_repertoire_temporaire')) {
		include_spip('inc/getdocument');
	}
	effacer_repertoire_temporaire($rep_dezip);
	
	// aller sur la page de l'article qui vient d'être créée
	redirige_par_entete(
			parametre_url(
					str_replace("&amp;", "&", urldecode($redirect)),
					'id_article', $id_article, '&'
			)
	);
}

?>
