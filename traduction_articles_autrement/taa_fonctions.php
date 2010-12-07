<?php

// changement de droits dans le cas de la médiathèque formulaires_joindre_document_charger_dist

function formulaires_joindre_document_charger($id_document='new',$id_objet=0,$objet='',$mode = 'auto',$galerie = false, 	$proposer_media=true, $proposer_ftp=true){

	include_spip('formulaires/joindre_document');
	$valeurs = array();
	$mode = joindre_determiner_mode($mode,$id_document,$objet);
	
	$valeurs['id'] = $id_document;
	$valeurs['mode'] = $mode;
	
	$valeurs['url'] = 'http://';
	$valeurs['fichier_upload'] = '';
	
	$valeurs['_options_upload_ftp'] = '';
	$valeurs['_dir_upload_ftp'] = '';
	
	$valeurs['joindre_upload']=''; 
	$valeurs['joindre_distant']=''; 
	$valeurs['joindre_ftp']='';
	$valeurs['joindre_mediatheque']='';

	$valeurs['editable'] = ' ';
 	if (intval($id_document)){
		$valeurs['editable'] = autoriser('modifier','document',$id_document)?' ':'';
	}
 	
	$valeurs['editable'] = 'ok';
	
	$valeurs['proposer_media'] = is_string($proposer_media) ? (preg_match('/^(false|non|no)$/i', $proposer_media) ? false : true) : $proposer_media;
	$valeurs['proposer_ftp'] = is_string($proposer_ftp) ? (preg_match('/^(false|non|no)$/i', $proposer_ftp) ? false : true) : $proposer_ftp;
	
	# regarder si un choix d'upload FTP est vraiment possible
	if (
	 $valeurs['proposer_ftp']
	 AND test_espace_prive() # ??
	 AND ($mode == 'document' OR $mode == 'choix') # si c'est pour un document
	 //AND !$vignette_de_doc		# pas pour une vignette (NB: la ligne precedente suffit, mais si on la supprime il faut conserver ce test-ci)
	 AND $GLOBALS['flag_upload']
	 ) {
		include_spip('inc/actions');
		if ($dir = determine_upload('documents')) {
			// quels sont les docs accessibles en ftp ?
			$valeurs['_options_upload_ftp'] = joindre_options_upload_ftp($dir, $mode);
			// s'il n'y en a pas, on affiche un message d'aide
			// en mode document, mais pas en mode image
			if ($valeurs['_options_upload_ftp'] OR ($mode == 'document' OR $mode=='choix'))
				$valeurs['_dir_upload_ftp'] = "<b>".joli_repertoire($dir)."</b>";
		}
	}
	// On ne propose le FTP que si on a des choses a afficher
	$valeurs['proposer_ftp'] = ($valeurs['_options_upload_ftp'] or $valeurs['_dir_upload_ftp']);
	
	if ($galerie){
		# colonne documents ou portfolio ?
		$valeurs['_galerie'] = $galerie;
	}
	if ($objet AND $id_objet){
		$valeurs['id_objet'] = $id_objet;
		$valeurs['objet'] = $objet;
		$valeurs['refdoc_joindre'] = '';
		
		// changement de droits dans le cas de la médiathèque pour les articles
		if ($valeurs['editable'] and objet!='articles'){
			include_spip('inc/autoriser');
			$valeurs['editable'] = autoriser('modifier',$objet,$id_objet)?' ':'';
		}
	}
		$valeurs['editable'] = 'ok';
	return $valeurs;
}

// http://doc.spip.org/@puce_statut_article_dist
function puce_statut_article($id, $statut, $id_rubrique, $type='article', $ajax = false) {
	global $lang_objet;
	
	static $coord = array('publie' => 2,
			      'prepa' => 0,
			      'prop' => 1,
			      'refuse' => 3,
			      'poubelle' => 4);

	$lang_dir = lang_dir($lang_objet);
	if (!$id) {
	  $id = $id_rubrique;
	  $ajax_node ='';
	} else	$ajax_node = " id='imgstatut$type$id'";


	$inser_puce = puce_stat($statut, " width='9' height='9' style='margin: 1px;'$ajax_node");

	if (!autoriser('publierdans', 'rubrique', $id_rubrique)
	OR !_ACTIVER_PUCE_RAPIDE)
		return $inser_puce;

	$titles = array(
			  "blanche" => _T('texte_statut_en_cours_redaction'),
			  "orange" => _T('texte_statut_propose_evaluation'),
			  "verte" => _T('texte_statut_publie'),
			  "rouge" => _T('texte_statut_refuse'),
			  "poubelle" => _T('texte_statut_poubelle'));

	$clip = 1+ (11*$coord[$statut]);

	if ($ajax){
		return 	"<span class='puce_article_fixe'>"
		. $inser_puce
		. "</span>"
		. "<span class='puce_article_popup' id='statutdecal$type$id' style='margin-left: -$clip"."px;'>"
		  . afficher_script_statut($id, $type, -1, 'puce-blanche.gif', 'prepa', $titles['blanche'])
		  . afficher_script_statut($id, $type, -12, 'puce-orange.gif', 'prop', $titles['orange'])
		  . afficher_script_statut($id, $type, -23, 'puce-verte.gif', 'publie', $titles['verte'])
		  . afficher_script_statut($id, $type, -34, 'puce-rouge.gif', 'refuse', $titles['rouge'])
		  . afficher_script_statut($id, $type, -45, 'puce-poubelle.gif', 'poubelle', $titles['poubelle'])
		  . "</span>";
	}

	$nom = "puce_statut_";

	if ((! _SPIP_AJAX) AND $type != 'article') 
	  $over ='';
	else {

	  $action = generer_url_ecrire('puce_statut',"",true);
	  $action = "if (!this.puce_loaded) { this.puce_loaded = true; prepare_selec_statut('$nom', '$type', '$id', '$action'); }";
	  $over = "\nonmouseover=\"$action\"";
	}

	return 	"<span class='puce_article' id='$nom$type$id' dir='$lang_dir'$over>"
	. $inser_puce
	. '</span>';
}

function puce_stat($statut, $atts='') {
	switch ($statut) {
		case 'publie':
			$img = 'puce-verte.gif';
			$alt = _T('info_article_publie');
			return http_img_pack($img, $alt, $atts);
		case 'prepa':
			$img = 'puce-blanche.gif';
			$alt = _T('info_article_redaction');
			return http_img_pack($img, $alt, $atts);
		case 'prop':
			$img = 'puce-orange.gif';
			$alt = _T('info_article_propose');
			return http_img_pack($img, $alt, $atts);
		case 'refuse':
			$img = 'puce-rouge.gif';
			$alt = _T('info_article_refuse');
			return http_img_pack($img, $alt, $atts);
		case 'poubelle':
			$img = 'puce-poubelle.gif';
			$alt = _T('info_article_supprime');
			return http_img_pack($img, $alt, $atts);
	}
	return http_img_pack($img, $alt, $atts);
}
?>