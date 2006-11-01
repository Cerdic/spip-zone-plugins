<?php
/*
 * snippets
 * Gestion d'import/export XML de contenu
 *
 * Auteurs :
 * Cedric Morin
 * © 2006 - Distribue sous licence GNU/GPL
 *
 */

function snippets_affiche_droite($flux){
	include_spip('inc/snippets');
	$args = $flux['args'];
	$out = "";
	$retour = _DIR_RESTREINT_ABS . self(false);
	if ($args['exec']=='articles_tous') {
		$out.=boite_snippets('articles','articles',"",$retour);
		$out.=boite_snippets('rubriques','rubriques',"",$retour);
	}
	if ($args['exec']=='articles') {
		$out.=boite_snippets('articles',$args['id_article'],"",$retour);
	}
	if ($args['exec']=='naviguer') {
		$out.=boite_snippets('articles','articles',"id_rubrique=".$args['id_rubrique'],$retour);
		$out.=boite_snippets('rubriques',$args['id_rubrique'],"",$retour);
	}
	$flux['data'].=$out;
	return $flux;
}

?>