<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function afficher_rubriques($id_parent) {
	$q = sql_select('titre, id_rubrique','spip_rubriques','id_parent='.$id_parent);
	while($r = sql_fetch($q)) {
		$lien = parametre_url(generer_url_ecrire("bureau_explorer"),'id_parent',$r['id_rubrique'],'&');

		$form .= '<div class="icone">'
			.'<a class="ouvre" href="'.$lien.'">'
			.'<img src="'.find_in_path('images/rubrique-24.gif').'" />'.$r['titre']
			.'</a></div>';

	}

	$a = sql_select('titre,id_article','spip_articles','id_rubrique='.$id_parent);
	while ($art = sql_fetch($a)) {
		$lien = parametre_url(generer_url_ecrire("bureau_article"),'id_article',$art['id_article'],'&');
		$form .= '<div class="icone">'
			.'<a class="ouvre" href="'.$lien.'">'
			.'<img src="'.find_in_path('images/article-24.gif').'" /><br />'.$art['titre']
			.'</a></div>';
	}
	return $form;
}

function inc_bureau_explorer_dist($id_parent) {
	$res = afficher_rubriques($id_parent);
	include_spip('inc/charsets');
	return $res;
}
