<?php
/**
 * PhotoSPIP
 * Modification d'images dans SPIP
 *
 * Auteurs :
 * kent1 (kent1@arscenic.info -  http://www.kent1.info)
 *
 * © 2008-2012 - Distribue sous licence GNU/GPL
 * Pour plus de details voir le fichier COPYING.txt
 *
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/presentation');

function exec_image_edit_dist()
{
	exec_image_edit_args(intval(_request('id_document')),_request('mode'));
}

/**
 * Edition d'une image
 *
 * @param int $id_document
 * @param string $mode
 */
function exec_image_edit_args($id_document, $mode){

	$row = false;
	if (!( (!autoriser('voir','document',$id_document) OR !autoriser('modifier','document', $id_document)))) {
		$row = sql_fetsel("*", "spip_documents", "id_document=".intval($id_document));
	}
	if (!$row) {
		include_spip('inc/minipres');
		echo minipres();
	}
	else
		image_edit_ok($row, $id_document, $mode);
}

function image_edit_ok($row, $id_document, $mode)
{

	$id_document=$row['id_document'];
	$titre=$row['titre']?$row['titre']:basename($row['fichier']);
	$statut=$row['statut'];
	
	if (defined('_AJAX') AND _AJAX){
		$contexte = array(
		'retour'=>'',//generer_url_ecrire("portfolio"),
		'new'=>$id_document,
		'mode'=>$mode,
		'config_fonc'=>'image_edit_config',
		'titre'=>$titre,
		'mode'=>$mode
		);

		include_spip('inc/actions');
		// faire le retour ajax et le passer dans le pipeline "image_edit"
		// (sans s a document, pour preparer la migration vers l'extension medias de SPIP core)
		ajax_retour(
			pipeline('affiche_milieu',
				array('args'=>array('exec'=>'image_edit','id_document'=>$id_document),
				'data'=>recuperer_fond("prive/editer/image_popup", $contexte))
			)
		);
		return;
	}

	$commencer_page = charger_fonction('commencer_page', 'inc');
	pipeline('exec_init',array('args'=>array('exec'=>'image_edit','id_document'=>$id_document),'data'=>''));

	echo $commencer_page(_T('photospip:titre_page_image_edit', array('titre' => $titre)), "naviguer", "images");

	echo debut_gauche('', true);
	$boite = pipeline ('boite_infos', array('data' => '',
		'args' => array(
			'type'=>'document',
			'id' => $id_document,
			'row' => $row
		)
	));

	echo debut_boite_info(true). $boite . fin_boite_info(true);
	
	echo recuperer_fond("prive/navigation/image_edit",array('id_document'=>$id_document,'mode'=>$mode));
	echo pipeline('affiche_gauche',array('args'=>array('exec'=>'image_edit','id_document'=>$id_document,'mode'=>$mode),'data'=>''));
	echo creer_colonne_droite('', true);
	echo pipeline('affiche_droite',array('args'=>array('exec'=>'image_edit','id_document'=>$id_document,'mode'=>$mode),'data'=>''));
	echo debut_droite('', true);

	$redirect = _request('retour') ? _request('retour') : generer_url_ecrire("portfolio");
	$contexte = array(
		'icone_retour'=>icone_inline(_T('icone_retour'),$redirect, find_in_path("images/document-24.png"), "rien.gif",$GLOBALS['spip_lang_left']),
		'retour'=>$redirect,//generer_url_ecrire("portfolio"),
		'titre'=>$titre,
		'new'=>$id_document,
		'mode'=>$mode,
		'config_fonc'=>'image_edit_config'
	);

	$milieu = recuperer_fond("prive/editer/image", $contexte);
	echo pipeline('affiche_milieu',array('args'=>array('exec'=>'image_edit','id_document'=>$id_document,'mode'=>$mode),'data'=>$milieu));

	echo fin_gauche(), fin_page();

}

?>
