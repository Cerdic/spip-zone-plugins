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

function inc_spipmotion_dist($id_article) {
	global $connect_id_auteur, $connect_statut;
	$id_article = _request('id_article');

	// Ajouter le formulaire d'ajout de videos
	$s = afficher_videos_colonne($id_article, 'article', true);
	return $s;
}

function afficher_videos_colonne($id, $type="article", $flag_modif = true) {
	
	include_spip('inc/autoriser');
	// il faut avoir les droits de modif sur l'article pour pouvoir uploader !
	if (!autoriser('joindredocument',$type,$id))
		return "";
		
	include_spip('inc/presentation'); // pour l'aide quand on appelle afficher_videos_colonne depuis un squelette
	// seuls cas connus : article, breve ou rubrique
	if ($script==NULL){
		$script = $type.'s_edit';
		if (!test_espace_prive())
			$script = parametre_url(self(),"show_docs",'');
	}
	$id_document_actif = _request('show_docs');
	
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
	$ret .= "<br /><div id='video_form'><div id='videos'></div>\n<div id='portfolio_videos'></div>\n";
	if (!isset($GLOBALS['meta']["documents_$type"]) OR $GLOBALS['meta']["documents_$type"]!='non') {
		$ret .= $joindre_videos(array(
			'cadre' => 'enfonce',
			'icone' => 'doc-24.gif',
			'fonction' => 'creer.gif',
			'titre' => _T('spipmotion:bouton_ajouter_videos'),
			'script' => $script,
			'args' => "id_$type=$id",
			'id' => $id,
			'intitule' => _T('info_telecharger'),
			'mode' => 'videos',
			'type' => $type,
			'ancre' => '',
			'id_document' => 0,
			'iframe_script' => generer_url_ecrire("documents_colonne","id=$id&type=$type",true)
		));
		$ret .= $encoder_videos(array(
			'cadre' => 'enfonce',
			'icone' => 'doc-24.gif',
			'fonction' => 'creer.gif',
			'titre' => _T('spipmotion:bouton_encoder_videos'),
			'script' => $script,
			'args' => "id_$type=$id",
			'id' => $id,
			'intitule' => _T('info_encoder'),
			'mode' => 'videos',
			'type' => $type,
			'ancre' => '',
			'id_document' => 0,
			'iframe_script' => generer_url_ecrire("documents_colonne_video","id=$id&type=$type",true)
		));
	}

		// Afficher les videos lies
		$ret .= "<div id='liste_documents_videos'>\n";

		foreach($videos_lies as $doc) {
			$id_document = $doc['id_document'];
			$deplier = $id_document_actif==$id_document;
			$ret .= afficher_case_document_videos($doc, $id, $script, $type, $deplier);
		}
		$ret .= "</div></div>";
	
  if (test_espace_prive()){
	$ret .= "<script src='"._DIR_PLUGIN_SPIPMOTION."/javascript/async_encode.js' type='text/javascript'></script>\n";
	$ret .= <<<EOF
	<script type='text/javascript'>
	$("form.form_encode").async_encode(async_encode_article_edit)
	</script>
EOF;
}
	return $ret;
	
}

function afficher_videos_joindre($id, $type="article", $flag_modif = true) {

	include_spip('inc/presentation'); // pour l'aide quand on appelle afficher_videos_colonne depuis un squelette
	// seuls cas connus : article, breve ou rubrique
	if ($script==NULL){
		$script = $type.'s_edit';
		if (!test_espace_prive())
			$script = parametre_url(self(),"show_videos",'');
	}
	$id_document_actif = _request('show_videos');

	$joindre_videos = charger_fonction('joindre_videos', 'inc');

	// Ajouter nouveau document
	if (!isset($GLOBALS['meta']["documents_$type"]) OR $GLOBALS['meta']["documents_$type"]!='non') {
		$ret = $joindre_videos(array(
			'cadre' => 'enfonce',
			'icone' => 'doc-24.gif',
			'fonction' => 'creer.gif',
			'titre' => _T('spipmotion:bouton_ajouter_videos'),
			'script' => $script,
			'args' => "id_$type=$id",
			'id' => $id,
			'intitule' => _T('info_telecharger'),
			'mode' => 'videos',
			'type' => $type,
			'ancre' => '',
			'id_document' => 0,
			'iframe_script' => generer_url_ecrire("documents_colonne","id=$id&type=$type",true)
		));
	}
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
	global $spip_lang_right;

	charger_generer_url();

	$res = spip_query("SELECT docs.*,l.vu FROM spip_documents AS docs JOIN spip_documents_".$type."s AS l ON l.id_document=docs.id_document WHERE l.id_$type="._q($id)." AND l.id_document="._q($id_document));
	if (!$document = spip_fetch_array($res)) return "";

	$id_vignette = $document['id_vignette'];
	$extension = $document['extension'];
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
	
	$result = spip_query("SELECT titre,inclus FROM spip_types_documents WHERE extension="._q($extension));
	if ($letype = spip_fetch_array($result)) {
		$type_inclus = $letype['inclus'];
		$type_titre = $letype['titre'];
	}

	//
	// Afficher un document
	//
	$ret = "";
	if (!$mode) {
		if ($options == "avancees") {

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
		<span style='font-family:arial,helvetica,sans-serif'>";
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
		$raccourci_doc .= "</span></div>\n";

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