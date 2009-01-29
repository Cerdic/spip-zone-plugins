<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2009                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');

function exec_documents_edit_dist()
{
	exec_documents_edit_args(intval(_request('id_document')),_request('id_parent'),_request('new'));
}

/**
 * Edition d'un document
 * id_parent est de la forme id_objet|objet (ex : 123|article)
 *
 * @param int $id_document
 * @param string $id_parent
 * @param string $new
 */
function exec_documents_edit_args($id_document, $id_parent, $new){

	$row = false;
	if (!( ($new!='oui' AND (!autoriser('voir','document',$id_document) OR !autoriser('modifier','document', $id_document)))
	       OR ($new=='oui' AND !autoriser('creer','document')) )) {
		if ($new != "oui")
			$row = sql_fetsel("*", "spip_documents", "id_document=$id_document");
		else $row = true;
	}
	if (!$row) {
		include_spip('inc/minipres');
		echo minipres();
	}
	else
		documents_edit_ok($row, $id_document, $id_parent, $new);
}

function documents_edit_ok($row, $id_document, $id_parent, $new)
{
	if ($new != 'oui') {
		$id_document=$row['id_document'];
		$titre=$row['titre']?$row['titre']:$row['fichier'];
		$statut=$row['statut'];
	}
	else {
		$titre = "";
		$statut = "prop";
	}

	$commencer_page = charger_fonction('commencer_page', 'inc');
	pipeline('exec_init',array('args'=>array('exec'=>'documents_edit','id_document'=>$id_document),'data'=>''));

	echo $commencer_page(_T('titre_page_documents_edit', array('titre' => $titre)), "naviguer", "documents");

	echo debut_gauche('', true);
	echo recuperer_fond("modeles/doc",array('id_document'=>$id_document,'largeur'=>180));
	echo pipeline('affiche_gauche',array('args'=>array('exec'=>'documents_edit','id_document'=>$id_document),'data'=>''));
	echo creer_colonne_droite('', true);
	echo pipeline('affiche_droite',array('args'=>array('exec'=>'documents_edit','id_document'=>$id_document),'data'=>''));
	echo debut_droite('', true);

	$contexte = array(
	'icone_retour'=>$new=='oui'?'':icone_inline(_T('icone_retour'), generer_url_ecrire("portfolio","id_document=$id_document"), "doc-24.gif", "rien.gif",$GLOBALS['spip_lang_left']),
	'redirect'=>generer_url_ecrire("portfolio"),
	'titre'=>$titre,
	'new'=>$new == "oui"?$new:$id_document,
	'id_parent'=>$id_parent,
	'config_fonc'=>'documents_edit_config'
	);

	echo recuperer_fond("prive/editer/document", $contexte);

	echo fin_gauche(), fin_page();

}

?>