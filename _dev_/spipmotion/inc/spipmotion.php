<?php
/*
 * SPIPmotion
 * Gestion de l'encodage des videos directement dans spip
 *
 * Auteurs :
 * Quentin Drouet
 * 2006 - Distribue sous licence GNU/GPL
 *
 */
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/actions'); // *action_auteur et determine_upload
include_spip('inc/date');
include_spip('inc/documents');
include_spip('base/abstract_sql');

function spipmotion_afficher_insertion_videos($id_article) {
	global $connect_id_auteur, $connect_statut;
	$id_article = _request('id_article');

	$s = "";
	// Ajouter le formulaire d'ajout de videos
	$s .= "\n<p>";
	$s .= "\n";
	$s .= afficher_videos_colonne($id_article, 'article', true);
	return $s;
}

function afficher_videos_colonne($id, $type="article", $flag_modif = true) {
	global $connect_id_auteur, $connect_statut, $options;

	// seuls cas connus : exec=articles_edit ou breves_edit
	$script = $type.'s_edit';

	// videos associees
	$res = spip_query("SELECT docs.id_document FROM spip_documents AS docs, spip_documents_".$type."s AS l WHERE l.id_".$type."=$id AND l.id_document=docs.id_document AND docs.mode='' ORDER BY docs.id_document");

	$videos_lies = array();
	while ($row = spip_fetch_array($res))
		$videos_lies[]= $row['id_document'];

	if (count($videos_lies)) {
		$res = spip_query("SELECT DISTINCT id_vignette FROM spip_documents WHERE id_document in (".join(',', $videos_lies).")");
		while ($v = spip_fetch_array($res))
			$vignettes[]= $v['id_vignette'];
		$docs_exclus = ereg_replace('^,','',join(',', $vignettes).','.join(',', $videos_lies));

		if ($docs_exclus) $docs_exclus = "AND l.id_document NOT IN ($docs_exclus) ";
	} else $docs_exclus = '';

	$encoder_videos = charger_fonction('encoder_videos', 'inc');
	$joindre_videos = charger_fonction('joindre_videos', 'inc');

	// Ajouter nouveau document
	$ret .= "</div><p>&nbsp;</p>\n<a name='videos'></a>\n<a name='portfolio_videos'></a>\n";

		if ($GLOBALS['meta']["documents_article"] != 'non') {
			$titre_cadre = _T('spipmotion:bouton_ajouter_videos');
			$ret .= debut_cadre_enfonce("doc-24.gif", true, "creer.gif", $titre_cadre);
			$ret .= $encoder_videos($script, "id_$type=$id", $id, "", 'videos',$type,'',0,generer_url_ecrire("documents_colonne","id=$id&type=$type",true));
			$ret .= $joindre_videos($script, "id_$type=$id", $id, _T('info_telecharger_ordinateur'), 'videos',$type,'',0,generer_url_ecrire("documents_colonne","id=$id&type=$type",true));
			$ret .= fin_cadre_enfonce(true);
		}

		// Afficher les videos lies
		$ret .= "<p></p><div id='liste_documents_videos'>\n";

		foreach($videos_lies as $doc) {
			$ret .= afficher_case_document_videos($doc, $id, $script, $type, $id_doc_actif == $doc);
		}
		$ret .= "</div>";
	

	$ret .= "<script src='"._DIR_PLUGIN_SPIPMOTION."/javascript/async_encode.js' type='text/javascript'></script>\n";
	$ret .= <<<EOF
	<script type='text/javascript'>
	$("form.form_encode").async_encode(async_encode_article_edit)
	</script>
EOF;
	return $ret;
}
//
// Affiche le raccourci <doc123|left>
// et l'insere quand on le clique
//
// http://doc.spip.org/@affiche_raccourci_doc
function affiche_raccourci_doc_videos($doc, $id, $align) {
	if ($align) {
		$pipe = "|$align";

		if ($GLOBALS['browser_barre'])
			$onclick = "\nondblclick='barre_inserer(\"&lt;$doc$id$pipe&gt;\", document.formulaire.texte);'\ntitle=\"". entites_html(_T('double_clic_inserer_doc'))."\"";
	} else {
		$align='center';
	}
	return "\n<div align='$align'$onclick>&lt;$doc$id$pipe&gt;</div>\n";
}


// Est-ce que le document est inclus dans le texte ?
// http://doc.spip.org/@est_inclus
function est_inclus_videos($id_document) {
	return is_array($GLOBALS['doublons_documents_inclus']) ?
		in_array($id_document,$GLOBALS['doublons_documents_inclus']) : false;
}


//
// Afficher un document sous forme de ligne depliable (pages xxx_edit)
//
// TODO: il y a du code a factoriser avec inc/documenter

// http://doc.spip.org/@afficher_case_document
function afficher_case_document_videos($id_document, $id, $script, $type, $deplier=false) {
	global $connect_id_auteur, $connect_statut;
	global $options, $couleur_foncee, $spip_lang_left, $spip_lang_right;

	charger_generer_url();

	$document = spip_fetch_array(spip_query("SELECT * FROM spip_documents WHERE id_document = " . intval($id_document)));

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

	// le doc est-il appele dans le texte ?
	$doublon = est_inclus_videos($id_document);

	$cadre = strlen($titre) ? $titre : basename($document['fichier']);

	$result = spip_query("SELECT * FROM spip_types_documents WHERE id_type=$id_type");
	if ($letype = @spip_fetch_array($result)) {
		$type_extension = $letype['extension'];
		$type_inclus = $letype['inclus'];
		$type_titre = $letype['titre'];
	}

	//
	// Afficher un document
	//
	$ret = "";
	if (!$mode) {
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
		$ret .= debut_cadre_enfonce("doc-24.gif", true, "", lignes_longues(typo($cadre),30));

		//
		// Affichage de la vignette
		//
		$ret .= "\n<div align='center'>";
		$ret .= document_et_vignette($document, $url, true); 
		$ret .= '</div>';
		$ret .= "\n<div class='verdana1' style='text-align: center; color: black;'>\n";
		$ret .= ($type_titre ? $type_titre : 
		      ( _T('info_document').' '.majuscules($type_extension)));
		$ret .= "</div>";

		// Affichage du raccourci <doc...> correspondant
		if (!$doublon) {
			$ret .= "\n<div style='padding:2px; font-size: 10px; font-family: arial,helvetica,sans-serif'>";
			if ($options == "avancees" AND ($type_inclus == "embed" OR $type_inclus == "image") AND $largeur > 0 AND $hauteur > 0) {
				$ret .= "<b>"._T('info_inclusion_vignette')."</b><br />";
			}
			$ret .= "<div style='color: 333333'>"
			. affiche_raccourci_doc_videos('video', $id_document, 'left')
			. affiche_raccourci_doc_videos('video', $id_document, 'center')
			. affiche_raccourci_doc_videos('video', $id_document, 'right')
			. "</div>\n";
			$ret .= "</div>";

			if ($options == "avancees" AND ($type_inclus == "embed" OR $type_inclus == "image") AND $largeur > 0 AND $hauteur > 0) {
				$ret .= "<div style='padding:2px; font-size: 10px; font-family: arial,helvetica,sans-serif'>";
				$ret .= "<b>"._T('info_inclusion_directe')."</b><br />";
				$ret .= "<div style='color: 333333'>"
				. affiche_raccourci_doc_videos('emb', $id_document, 'left')
				. affiche_raccourci_doc_videos('emb', $id_document, 'center')
				. affiche_raccourci_doc_videos('emb', $id_document, 'right')
				. "</div>\n";
				$ret .= "</div>";
			}
		} else {
			$ret .= "<div style='padding:2px;'><font size='1' face='arial,helvetica,sans-serif'>".
			  affiche_raccourci_doc_videos('doc', $id_document, '').
			  "</font></div>";
		}

		$legender = charger_fonction('legender', 'inc');
		$ret .= $legender($id_document, $document, $script, $type, $id, "document$id_document", $deplier);

		$ret .= fin_cadre_enfonce(true);
		}
	}

	//
	// Afficher une image inserable dans l'article
	//
	else if ($mode == 'vignette') {
	
		$ret .= debut_cadre_relief("image-24.gif", true, "", lignes_longues(typo($cadre),30));

		//
		// Preparer le raccourci a afficher sous la vignette ou sous l'apercu
		//
		$raccourci_doc = "<div style='padding:2px;'>
		<font size='1' face='arial,helvetica,sans-serif'>";
		if (strlen($descriptif) > 0 OR strlen($titre) > 0)
			$doc = 'doc';
		else
			$doc = 'img';
		if (!$doublon) {
			$raccourci_doc .=
				affiche_raccourci_doc_videos($doc, $id_document, 'left')
				. affiche_raccourci_doc_videos($doc, $id_document, 'center')
				. affiche_raccourci_doc_videos($doc, $id_document, 'right');
		} else {
			$raccourci_doc .= affiche_raccourci_doc_videos($doc, $id_document, '');
		}
		$raccourci_doc .= "</font></div>\n";

		//
		// Afficher un apercu (pour les images)
		//
		if ($type_inclus == 'image') {
			$ret .= "<div style='text-align: center; padding: 2px;'>\n";
			$ret .= document_et_vignette($document, $url, true);
			$ret .= "</div>\n";
			if (!$doublon)
				$ret .= $raccourci_doc;
		}

		if ($doublon)
			$ret .= $raccourci_doc;

		$legender = charger_fonction('legender', 'inc');
		$ret .= $legender($id_document, $document, $script, $type, $id, "document$id_document");
		
		$ret .= fin_cadre_relief(true);
	}
	return $ret;
}
?>