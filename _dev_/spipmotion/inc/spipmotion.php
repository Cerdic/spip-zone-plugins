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
		
	// seuls cas connus : article, breve ou rubrique
	if ($script==NULL){
		$script = $type.'s_edit';
		if (!test_espace_prive())
			$script = parametre_url(self(),"show_docs",'');
	}

	$encoder_videos = charger_fonction('encoder_videos', 'inc');

	// Ajouter nouveau document
	$ret .= "<br /><div id='video_form'><div id='videos'></div>\n<div id='portfolio_videos'></div>\n";
	if (!isset($GLOBALS['meta']["documents_$type"]) OR $GLOBALS['meta']["documents_$type"]!='non') {
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

		$ret .= "</div>";
	
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
?>