<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2007                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/actions'); // *action_auteur et determine_upload
include_spip('inc/date');
include_spip('base/abstract_sql');

// http://doc.spip.org/@afficher_documents_colonne
function afficher_documents_auteurs_colonne($id, $type="auteur",$script=NULL) {
	include_spip('inc/minipres'); // pour l'aide quand on appelle afficher_documents_colonne depuis un squelette
	include_spip('inc/presentation'); // pour l'aide quand on appelle afficher_documents_colonne depuis un squelette
	
	// seuls cas connus : article, breve ou rubrique
	if ($script==NULL){
		$script = $type.'s_edit';
		if (_DIR_RESTREINT)
			$script = parametre_url(self(),"show_docs",'');
	}
	$id_document_actif = _request('show_docs');

	/// Ajouter nouvelle image
	$ret .= "<a name='images'></a>\n";
	$titre_cadre = _T('bouton_ajouter_image').aide("ins_img");

	$joindre = charger_fonction('joindre_auteurs', 'inc');
	$ret .= debut_cadre_relief("image-24.gif", true, "creer.gif", $titre_cadre);
	$ret .= $joindre($script, "id_$type=$id", $id, _T('info_telecharger'),'vignette',$type,'',0,generer_url_ecrire("documents_colonne_auteurs","id=$id&type=$type",true));

	$ret .= fin_cadre_relief(true);

	//// Documents associes
	$res = spip_query("SELECT docs.id_document FROM spip_documents AS docs, spip_documents_".$type."s AS l WHERE l.id_".$type."=$id AND l.id_document=docs.id_document AND docs.mode='document' ORDER BY docs.id_document");

	$documents_lies = array();
	while ($row = spip_fetch_array($res))
		$documents_lies[]= $row['id_document'];

	if (count($documents_lies)) {
		$res = spip_query("SELECT DISTINCT id_vignette FROM spip_documents WHERE id_document in (".join(',', $documents_lies).")");
		while ($v = spip_fetch_array($res))
			$vignettes[]= $v['id_vignette'];
		$docs_exclus = preg_replace('/^,/','',join(',', $vignettes).','.join(',', $documents_lies));

		if ($docs_exclus) $docs_exclus = "AND l.id_document NOT IN ($docs_exclus) ";
	} else $docs_exclus = '';

	//// Images sans documents
	$images_liees = spip_query("SELECT docs.id_document FROM spip_documents AS docs, spip_documents_".$type."s AS l "."WHERE l.id_".$type."=$id AND l.id_document=docs.id_document ".$docs_exclus."AND docs.mode='vignette' ORDER BY docs.id_document");

	$ret .= "\n<p></p><div id='liste_images'>";
	while ($doc = spip_fetch_array($images_liees)) {
		$id_document = $doc['id_document'];
		$deplier = $id_document_actif==$id_document;
		$ret .= afficher_case_document_auteurs($id_document, $id, $script, $type, $deplier);
	}

	/// Ajouter nouveau document
	$ret .= "</div><p>&nbsp;</p>\n<a name='documents'></a>\n<a name='portfolio'></a>\n";
	if (!isset($GLOBALS['meta']["documents_$type"]) OR $GLOBALS['meta']["documents_$type"]!='non') {
		$titre_cadre = _T('bouton_ajouter_document').aide("ins_doc");
		$ret .= debut_cadre_enfonce("doc-24.gif", true, "creer.gif", $titre_cadre);
		$ret .= $joindre($script, "id_$type=$id", $id, _T('info_telecharger_ordinateur'), 'document',$type,'',0,generer_url_ecrire("documents_colonne_auteurs","id=$id&type=$type",true));
		$ret .= fin_cadre_enfonce(true);
	}

	// Afficher les documents lies
	$ret .= "<p></p><div id='liste_documents'>\n";

	foreach($documents_lies as $doc) {
		$id_document = $doc['id_document'];
		$deplier = $id_document_actif==$id_document;
		$ret .= afficher_case_document_auteurs($doc, $id, $script, $type, $deplier);
	}
	$ret .= "</div>";
  if (!_DIR_RESTREINT){
	  $ret .= "<script src='"._DIR_JAVASCRIPT."async_upload.js' type='text/javascript'></script>\n";
	  $ret .= <<<EOF
	    <script type='text/javascript'>
	    $("form.form_upload").async_upload(async_upload_article_edit)
	    </script>
EOF;
  }
	return $ret;
}

//
// Afficher un document sous forme de ligne depliable (pages xxx_edit)
//
// TODO: il y a du code a factoriser avec inc/documenter

// http://doc.spip.org/@afficher_case_document
function afficher_case_document_auteurs($id_document, $id, $script, $type, $deplier=false) {
	global $options, $couleur_foncee, $spip_lang_left, $spip_lang_right;

	charger_generer_url();
	$res = spip_query("SELECT docs.*,l.vu FROM spip_documents AS docs JOIN spip_documents_".$type."s AS l ON l.id_document=docs.id_document WHERE l.id_$type="._q($id)." AND l.id_document="._q($id_document));
	if (!$document = spip_fetch_array($res)) return "";
	//$document = spip_fetch_array(spip_query("SELECT * FROM spip_documents WHERE id_document = " . intval($id_document)));

	$id_vignette = $document['id_vignette'];
	$id_type = $document['id_type'];
	$titre = $document['titre'];
	$descriptif = $document['descriptif'];
	$url = generer_url_document($id_document);
	$fichier = $document['fichier'];
	$largeur = $document['largeur'];
	$hauteur = $document['hauteur'];
	$taille = $document['taille'];
	$mode = $document['mode'];
	$distant = $document['distant'];

	// le doc est-il appele dans le texte ?
	$doublon = est_inclus($id_document);

	$cadre = strlen($titre) ? $titre : basename($document['fichier']);

	$result = spip_query("SELECT titre,inclus,extension FROM spip_types_documents WHERE id_type=$id_type");
	if ($letype = @spip_fetch_array($result)) {
		$type_extension = $letype['extension'];
		$type_inclus = $letype['inclus'];
		$type_titre = $letype['titre'];
	}

	//
	// Afficher un document
	//
	$ret = "";
	if ($mode == 'document') {
		if ($options == "avancees") {
			# 'extension', a ajouter dans la base quand on supprimera spip_types_documents
			switch ($id_type) {
				case 1:
					$document['extension'] = "jpg";
					break;
				case 2:
					$document['extension'] = "png";
					break;
				case 3:
					$document['extension'] = "gif";
					break;
			}

		$ret .= "<a id='document$id_document' name='document$id_document'></a>\n";
		$ret .= debut_cadre_enfonce("doc-24.gif", true, "", lignes_longues(typo($cadre),20));

		//
		// Affichage de la vignette
		//
		$ret .= "\n<div align='center'>";

		// Signaler les documents distants par une icone de trombone
		$ret .= ($document['distant'] == 'oui')
			? "\n<img src='"._DIR_IMG_PACK.'attachment.gif'."'\n\t style='float: $spip_lang_right;'\n\talt=\"$fichier\"\n\ttitle=\"$fichier\" />\n"
			:'';

		$ret .= document_et_vignette($document, $url, true); 
		$ret .= '</div>';
		$ret .= "\n<div class='verdana1' style='text-align: center; color: black;'>\n";
		$ret .= ($type_titre ? $type_titre : 
		      ( _T('info_document').' '.majuscules($type_extension)));
		$ret .= "</div>";

		// Affichage du raccourci <doc...> correspondant
		$raccourci = '';
		if ($options == "avancees" AND ($type_inclus == "embed" OR $type_inclus == "image") AND $largeur > 0 AND $hauteur > 0) {
			$raccourci .= "<b>"._T('info_inclusion_vignette')."</b><br />";
		}
		$raccourci .= "<div style='color: 333333'>"
		. affiche_raccourci_doc('doc', $id_document, 'left')
		. affiche_raccourci_doc('doc', $id_document, 'center')
		. affiche_raccourci_doc('doc', $id_document, 'right')
		. "</div>\n";

		if ($options == "avancees" AND ($type_inclus == "embed" OR $type_inclus == "image") AND $largeur > 0 AND $hauteur > 0) {
			$raccourci .= "<div style='padding:2px; ' class='arial1 spip_xx-small'>";
			$raccourci .= "<b>"._T('info_inclusion_directe')."</b><br />";
			$raccourci .= "<div style='color: 333333'>"
			. affiche_raccourci_doc('emb', $id_document, 'left')
			. affiche_raccourci_doc('emb', $id_document, 'center')
			. affiche_raccourci_doc('emb', $id_document, 'right')
			. "</div>\n";
			$raccourci .= "</div>";
		}

		$raccourci = $doublon
			? affiche_raccourci_doc('doc', $id_document, '')
			: $raccourci;

		$ret .= "\n<div style='padding:2px; ' class='arial1 spip_xx-small'>"
			. $raccourci."</div>\n";

		$legender = charger_fonction('legender_docs_auteurs', 'inc');
		$ret .= $legender($id_document, $document, $script, $type, $id, "document$id_document", $deplier);

		$ret .= fin_cadre_enfonce(true);
		}
	}

	//
	// Afficher une image inserable dans l'article
	//
	else if ($mode == 'vignette') {
	
		$ret .= debut_cadre_relief("image-24.gif", true, "", lignes_longues(typo($cadre),20));

		//
		// Afficher un apercu (pour les images)
		//
		if ($type_inclus == 'image') {
			$ret .= "<div style='text-align: center; padding: 2px;'>\n";
			$ret .= document_et_vignette($document, $url, true);
			$ret .= "</div>\n";
		}

		//
		// Preparer le raccourci a afficher sous la vignette ou sous l'apercu
		//
		$raccourci = "";
		if (strlen($descriptif) > 0 OR strlen($titre) > 0)
			$doc = 'doc';
		else
			$doc = 'img';

		$raccourci .=
			affiche_raccourci_doc($doc, $id_document, 'left')
			. affiche_raccourci_doc($doc, $id_document, 'center')
			. affiche_raccourci_doc($doc, $id_document, 'right');

		$raccourci = $doublon
			? affiche_raccourci_doc($doc, $id_document, '')
			: $raccourci;

		$ret .= "\n<div style='padding:2px; ' class='arial1 spip_xx-small'>"
			. $raccourci."</div>\n";


		$legender = charger_fonction('legender_docs_auteurs', 'inc');
		$ret .= $legender($id_document, $document, $script, $type, $id, "document$id_document", $deplier);
		
		$ret .= fin_cadre_relief(true);
	}
	return $ret;
}

?>