<?php

function affichage_galerie_auteur($id_auteur) {
	include_spip('inc/presentation');
	include_spip('inc/layer'); # pour le js des fleches

	$html =
		http_script("\nvar ajax_image_searching = \n'<div style=\"float: ".$GLOBALS['spip_lang_right'].";\"><img src=\"".url_absolue(_DIR_IMG_PACK."searching.gif")."\" alt=\"\" /></div>';")
		. http_script('', _DIR_JAVASCRIPT . 'layer.js','')
		. http_script('', _DIR_JAVASCRIPT . 'spip_barre.js','')
		. http_script('', _DIR_JAVASCRIPT . 'presentation.js','')
		. afficher_documents_colonne($id_auteur, 'auteur', url_absolue(self()));
	return $html;
}
function exec_documents_colonne()
{
	global $id, $type, $show_docs;
	$id = intval($id);

	if (!($type == 'article' 
		? autoriser('modifier','article',$id)
		: autoriser('publierdans','rubrique',$id))) {
		include_spip('inc/minipres');
		echo minipres();
		exit;
	}

	include_spip("inc/documents");
	include_spip("inc/presentation");

	// TODO: return au lieu de echo
	$documents = explode(",",$show_docs);
	if ($type = 'auteur'){
		$script = $type."_infos";
	}
	else{
		$script = $type."s_edit";
	}
	$res = "";
	foreach($documents as $doc) {
		$res .= afficher_case_document($doc, $id, $script, $type, $deplier = false);
	}

	ajax_retour("<div class='upload_answer upload_document_added'>".
	$res.
	"</div>",false);
}

function inc_legender($id_document, $document, $script, $type, $id, $ancre, $deplier=false) {
	include_spip('inc/legender');
	// + securite (avec le script exec=legender ca vient de dehors)
	if (!preg_match('/^\w+$/',$type, $r)) {
	  return;
	}
	// premier appel
	if ($document) {
		$flag = $deplier;
	} else
	// retour d'Ajax
	if ($id_document) {
		$res = spip_query("SELECT * FROM spip_documents WHERE id_document = " . intval($id_document));
		$document = spip_fetch_array($res);
		$document['vu'] = 'non';
		$res = spip_query("SELECT vu FROM spip_documents_".$type."s WHERE id_$type=$id AND id_document=".intval($id_document));
		if ($row = spip_fetch_array($res))
			$document['vu'] = $row['vu'];
		$flag = 'ajax';
	}
	else
		return;

	$descriptif = $document['descriptif'];
	$titre = $document['titre'];
	$date = $document['date'];

	if ($document['mode'] == 'vignette') {
		$supp = 'image-24.gif';
		$label = _T('entree_titre_image');
		$taille = $vignette = '';
	  
	} else {
		$supp = 'doc-24.gif';
		$label = _T('entree_titre_document');
		$taille = formulaire_taille($document);
		$vignette = vignette_formulaire_legender($id_document, $document, $script, $type, $id, $ancre);
	}

	$entete = basename($document['fichier']);
	if (($n=strlen($entete)) > 20)
		$entete = substr($entete, 0, 10)."...".substr($entete, $n-10, $n);
	if (strlen($document['titre']))
		$entete = "<b>". lignes_longues(typo($titre),25) . "</b>";

	$contenu = '';
	if ($descriptif)
	  $contenu .=  lignes_longues(propre($descriptif),25)  . "<br />\n" ;
	if ($document['largeur'] OR $document['hauteur'])
	  $contenu .= _T('info_largeur_vignette',
		     array('largeur_vignette' => $document['largeur'],
			   'hauteur_vignette' => $document['hauteur']));
	else
	  $contenu .= taille_en_octets($document['taille']) . ' - ';

	if ($date) $contenu .= "<br />\n" . affdate($date);

	$corps =
	  (!$contenu ? '' :
	   "<br /><div class='verdana1' style='text-align: center;'>$contenu</div>") .
	  "<b>$label</b><br />\n" .

	  "<input type='text' name='titre_document' class='formo' value=\"".entites_html($titre).
	  "\" size='40'	onfocus=\"changeVisible(true, 'valider_doc$id_document', 'block', 'block');\" /><br /><br />\n" .
	  date_formulaire_legender($date, $id_document) .
	  "<br />\n<b>".
	  _T('info_description_2').
	  "</b><br />\n" .
	  "<textarea name='descriptif_document' rows='4' class='formo' cols='*' onfocus=\"changeVisible(true, 'valider_doc$id_document', 'block', 'block');\">" .
	    entites_html($descriptif) .
	  "</textarea>\n" .
	  $taille;

	$att_bouton = " class='fondo spip_xx-small'";
	$att_span = " id='valider_doc$id_document' "
	. ($flag == 'ajax' ? '' : "class='display_au_chargement'")
	.  "style='text-align:"
	.  $GLOBALS['spip_lang_right']
	. ($flag == 'ajax' ? ';display:block' : "")
	. "'";


		$corps = ajax_action_post("legender", $id_document, $script, "show_docs=$id_document&id_$type=$id#legender-$id_document", $corps, _T('bouton_enregistrer'), $att_bouton, $att_span, "&id_document=$id_document&id=$id&type=$type&ancre=$ancre")
		  . "<br class='nettoyeur' />";
	
	$corps .=  $vignette . "\n\n";

	$texte = _T('icone_supprimer_document');
	$s = ($ancre =='documents' ? '': '-');
	if (preg_match('/_edit$/', $script)){
		if ($id==0)
			$action = redirige_action_auteur('supprimer', "document-$id_document", $script, "id_$type=$id#$ancre");
		else
			$action = redirige_action_auteur('documenter', "$s$id/$type/$id_document", $script, "id_$type=$id&type=$type&s=$s#$ancre");
	}
	else if (preg_match('/_infos$/', $script)){
		if ($id==0)
			$action = redirige_action_auteur('supprimer', "document-$id_document", $script, "id_$type=$id#$ancre");
		else
			$action = redirige_action_auteur('documenter', "$s$id/$type/$id_document", $script, "id_$type=$id&type=$type&s=$s#$ancre");
	}
	else if (preg_match('/ADD-an-event$/', $script)){
		if ($id==0)
			$action = redirige_action_auteur('supprimer', "document-$id_document", $script, "id_$type=$id#$ancre");
		else
			$action = redirige_action_auteur('documenter', "$s$id/$type/$id_document", $script, "id_$type=$id&type=$type&s=$s#$ancre");
	}
	else {
		if (!_DIR_RESTREINT)
			$action = ajax_action_auteur('documenter', "$s$id/$type/$id_document", $script, "id_$type=$id&type=$type&s=$s#$ancre", array($texte));
		else{
			$redirect = str_replace('&amp;','&',$script);
			$action = generer_action_auteur('documenter', "$s$id/$type/$id_document", $redirect);
			$action = "<a href='$action'>$texte</a>";
		}
	}

	// le cas $id<0 correspond a un doc charge dans un article pas encore cree,
	// et ca buggue si on propose de supprimer => on ne propose pas
	if (!($id < 0) && $document['vu']=='non')
		$corps .= icone_horizontale($texte, $action, $supp, "supprimer.gif", false);

	$corps = "<div class='verdana1' style='color: "
	. $GLOBALS['couleur_foncee']
	. "; border: 1px solid "
	. $GLOBALS['couleur_foncee']
	. "; padding: 5px; margin: 3px; background-color: white;'>"
	. block_parfois_visible("legender-aff-$id_document", $entete, $corps, "text-align:center;", $flag)
	. "</div>";

	return ajax_action_greffe("legender-$id_document", $corps);
}

?>
