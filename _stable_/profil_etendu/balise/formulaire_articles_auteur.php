<?php

if (!defined("_ECRIRE_INC_VERSION")) return;	#securite

include_spip('base/abstract_sql');


function balise_FORMULAIRE_ARTICLES_AUTEUR($p) {
//  return calculer_balise_dynamique($p,'FORMULAIRE_ARTICLES_AUTEUR',array('id_rubrique'));
  return calculer_balise_dynamique($p,'FORMULAIRE_ARTICLES_AUTEUR',array());
}

function balise_FORMULAIRE_ARTICLES_AUTEUR_stat($args, $filtres) {
/*
	// Pas d'id_rubrique ? Erreur de squelette
	if (!$args[0] && !$args[1])
		return erreur_squelette(
			_T('zbug_champ_hors_motif',
				array ('champ' => '#FORMULAIRE_ARTICLES_AUTEUR',
					'motif' => 'RUBRIQUES')), '');

	if ($args[1])
	    return (array($args[1]));
	else
	    return (array($args[0]));
*/
	return array($args[0]);
}

//function balise_FORMULAIRE_ARTICLES_AUTEUR_dyn($id_rubrique) {
function balise_FORMULAIRE_ARTICLES_AUTEUR_dyn($url='') {

/*	$page_lot=5;
	$debut_page=intval($_REQUEST['debut_page']);
	if (!$GLOBALS["auteur_session"]) 
		return array('formulaires/formulaire_articles_auteur', 0,
			array(
//						'id_rubrique' => $id_rubrique,
						'statut' => "publie",
						'debut_page'=>$debut_page,
						'page_lot'=>$page_lot
				));
	
*/	
	global $connect_id_auteur,$connect_toutes_rubriques;
	include_spip("inc/auth");
	$connect_id_auteur=$GLOBALS['auteur_session']['id_auteur'];
	auth_rubrique();
	$auteur_statut=$GLOBALS["auteur_session"]["statut"];
	$id_auteur_session = $GLOBALS['auteur_session']['id_auteur'];
//	$flag_modif=(($auteur_statut == "0minirezo") && ($connect_toutes_rubriques OR acces_rubrique($id_rubrique)));
	$flag_modif=(($auteur_statut == "0minirezo") && ($connect_toutes_rubriques));

	if ($flag_modif)
		return array('formulaires/formulaire_articles_auteur', 0,
			array(
//						'id_rubrique' => $id_rubrique,
						'id_auteur' => $id_auteur_session,
						'auteur_statut' => $auteur_statut,
						'url' => $url,
//						'debut_page'=>$debut_page,
//						'page_lot'=>$page_lot
				));
	else 
//		if((($auteur_statut == "1comite") ) && ($connect_toutes_rubriques OR acces_rubrique($id_rubrique)))
		if($auteur_statut == "1comite")
			return array('formulaires/formulaire_articles_auteur', 0,
					array(
//						'id_rubrique' => $id_rubrique,
						'id_auteur' => $id_auteur_session,
						'auteur_statut' => $auteur_statut,
						'statut' => "prop",
						'url' => $url,
//						'debut_page'=>$debut_page,
//						'page_lot'=>$page_lot
				));
		elseif($auteur_statut == "6forum")
			return array('formulaires/formulaire_articles_auteur', 0,
					array(
//						'id_rubrique' => $id_rubrique,
						'url' => $url,
						'id_auteur' => $id_auteur_session,
						'statut' => "publie",
						'auteur_statut' => $auteur_statut,
//						'debut_page'=>$debut_page,
//						'page_lot'=>$page_lot
				));

		else
			return array('formulaires/formulaire_articles_auteur', 0,
					array(
//						'id_rubrique' => $id_rubrique,
						'id_auteur' => 0,
						'statut' => "publie",
						'url' => $url,
//						'debut_page'=>$debut_page,
//						'page_lot'=>$page_lot
				));
}

function puce_admin_article($id, $statut, $id_rubrique,$connect_statut,$connect_id_auteur) {
//	include_spip('inc/minipres');
	include_spip('inc/presentation');
return puce_statut_article($id, $statut, $id_rubrique);
	
	global $spip_lang_left, $browser_name,$connect_id_rubrique,$connect_toutes_rubriques;
	if ($connect_statut == '0minirezo' AND ($connect_toutes_rubriques OR acces_rubrique($id_rubrique))) {
	switch ($statut) {
	case 'publie':
		$clip = 2;
		$puce = 'verte';
		$title = _T('info_article_publie');
		break;
	case 'prepa':
		$clip = 0;
		$puce = 'blanche';
		$title = _T('info_article_redaction');
		break;
	case 'prop':
		$clip = 1;
		$puce = 'orange';
		$title = _T('info_article_propose');
		break;
	case 'refuse':
		$clip = 3;
		$puce = 'rouge';
		$title = _T('info_article_refuse');
		break;
	case 'poubelle':
		$clip = 4;
		$puce = 'poubelle';
		$title = _T('info_article_supprime');
		break;
	}
	$puce = "puce-$puce.gif";
	
	  // les versions de MSIE ne font pas toutes pareil sur alt/title
	  // la combinaison suivante semble ok pour tout le monde.
	  $titles = array(
			  "blanche" => _T('texte_statut_en_cours_redaction'),
			  "orange" => _T('texte_statut_propose_evaluation'),
			  "verte" => _T('texte_statut_publie'),
			  "rouge" => _T('texte_statut_refuse'),
			  "poubelle" => _T('texte_statut_poubelle'));
	  $action = "onmouseover=\"montrer('statutdecalarticle$id');\"";
	  $inser_puce = "<div class='puce_article' id='statut$id'$dir_lang>"
			. "<div class='puce_article_fixe' $action>" .
		  http_img_pack("$puce", "", "id='imgstatutarticle$id' border='0' style='margin: 1px;'") ."</div>"
			. "<div class='puce_article_popup' id='statutdecalarticle$id' onmouseout=\"cacher('statutdecalarticle$id');\" style=' margin-left: -".((11*$clip)+1)."px;'>"
		  . http_href_img("javascript:selec_statut($id, 'article', -1,'" . _DIR_IMG_PACK . "puce-blanche.gif', 'prepa');",
				  "puce-blanche.gif", 
				  "title=\"$titles[blanche]\"",
				  "",'','',
				  $action)
		  . http_href_img("javascript:selec_statut($id, 'article', -12,'" . _DIR_IMG_PACK . "puce-orange.gif', 'prop');",
				  "puce-orange.gif", 
				  "title=\"$titles[orange]\"",
				  "",'','',
				  $action)
		  . http_href_img("javascript:selec_statut($id, 'article', -23,'" . _DIR_IMG_PACK . "puce-verte.gif', 'publie');",
				  "puce-verte.gif", 
				  "title=\"$titles[verte]\"",
				  "",'','',
				  $action)
		  . http_href_img("javascript:selec_statut($id, 'article', -34,'" . _DIR_IMG_PACK . "puce-rouge.gif', 'refuse');",
				  "puce-rouge.gif", 
				  "title=\"$titles[rouge]\"",
				  "",'','',
				  $action)
		  . http_href_img("javascript:selec_statut($id, 'article', -45,'" . _DIR_IMG_PACK . "puce-poubelle.gif', 'poubelle');",
				  "puce-poubelle.gif", 
				  "title=\"$titles[poubelle]\"",
				  "",'','',
				  $action)
			. "</div></div>";
	} 
	elseif ((($connect_statut == '1comite') OR ($connect_statut == '6forum')) 
			AND ($connect_toutes_rubriques OR acces_rubrique($id_rubrique))) {
	switch ($statut) {
	case 'prepa':
		$clip = 0;
		$puce = 'blanche';
		$title = _T('info_article_redaction');
		break;
	case 'prop':
		$clip = 1;
		$puce = 'orange';
		$title = _T('info_article_propose');
		break;
	case 'poubelle':
		$clip = 2;
		$puce = 'poubelle';
		$title = _T('info_article_supprime');
		break;
	case 'refuse':
		$clip = 3;
		$puce = 'rouge';
		$title = _T('info_article_refuse');
		break;
	case 'publie':
		$clip = 4;
		$puce = 'verte';
		$title = _T('info_article_publie');
		break;
	}
	$puce = "puce-$puce.gif";
	
		$query = "SELECT * FROM spip_auteurs_articles WHERE id_article=".$id." AND id_auteur=".$connect_id_auteur;
		$result_auteur = spip_query($query);
		$flag_auteur = (spip_num_rows($result_auteur) > 0);
		if (!$flag_auteur)
			$inser_puce = http_img_pack("$puce", "", "id='imgstatutarticle$id' border='0' style='margin: 1px;'");
		else {
		  $titles = array(
				  "blanche" => _T('texte_statut_en_cours_redaction'),
				  "orange" => _T('texte_statut_propose_evaluation'),
				  "poubelle" => _T('texte_statut_poubelle'));
		  $action = "onmouseover=\"montrer('statutdecalarticle$id');\"";
		  $inser_puce = "<div class='puce_article' id='statut$id'$dir_lang>"
				. "<div class='puce_article_fixe' $action>" .
			  http_img_pack("$puce", "", "id='imgstatutarticle$id' border='0' style='margin: 1px;'") ."</div>"
				. "<div class='puce_article_popup_redac' id='statutdecalarticle$id' onmouseout=\"cacher('statutdecalarticle$id');\" style=' margin-left: -".((11*$clip)+1)."px;'>"
			  . http_href_img("javascript:selec_statut($id, 'article', -1,'" . _DIR_IMG_PACK . "puce-blanche.gif', 'prepa');",
					  "puce-blanche.gif", 
					  "title=\"$titles[blanche]\"",
					  "",'','',
					  $action)
			  . http_href_img("javascript:selec_statut($id, 'article', -12,'" . _DIR_IMG_PACK . "puce-orange.gif', 'prop');",
					  "puce-orange.gif", 
					  "title=\"$titles[orange]\"",
					  "",'','',
					  $action)
			  . http_href_img("javascript:selec_statut($id, 'article', -23,'" . _DIR_IMG_PACK . "puce-poubelle.gif', 'poubelle');",
					  "puce-poubelle.gif", 
					  "title=\"$titles[poubelle]\"",
					  "",'','',
					  $action)
				. "</div></div>";
			}	
	}
	else {
		$inser_puce = http_img_pack("$puce", "", "id='imgstatutarticle$id' border='0' style='margin: 1px;'");
	}
	return $inser_puce;
}

?>