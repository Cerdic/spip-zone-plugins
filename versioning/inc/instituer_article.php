<?php

/***************************************************************************\
 * 						Gestion du versioning 							   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('versioning_fonctions');

// http://code.spip.net/@inc_instituer_article_dist
function inc_instituer_article_dist($id_article, $statut=-1)
{
	if ($statut == -1) return demande_publication($id_article);

	if( (isACopy($id_article)) && (isArticleOrigPublished($id_article)) ) // Si l'article est une copie d'un autre et que l'article original est publi� en ligne
	{
		if($statut == 'archi') // Statut archiv�
		{
			$res = 
			"\n<div style='text-align: center;' id='instituer_article-$id_article'>" .
			"\n<a href='". $GLOBALS['meta']['adresse_site'] ."/ecrire/?exec=revenir_new_version&id_article=$id_article'>" .
			_T('versioning:revenir_version') .
			"\n</a>" .
			"\n</div>";
		}
		else
		{
			$res = 
			"\n<div style='text-align: center;' id='instituer_article-$id_article'>\n" .
			"\n<a href='". $GLOBALS['meta']['adresse_site'] ."/ecrire/?exec=publier_new_version&id_article=$id_article'>" .
			_T('versioning:publier_version') .
			"\n</a>" .
			"\n</div>";
		}
	}
	else
	{	
		$res =
		"\n<div style='text-align: center;' id='instituer_article-$id_article'>" .
		"<b>" .
		_T('texte_article_statut') .
		"</b>" .
		"\n<select name='statut_nouv' size='1' class='fondl'\n" .
		"onchange=\"this.nextSibling.nextSibling.src='" .
		_DIR_IMG_PACK .
		"' + puce_statut(options[selectedIndex].value);" .
		" setvisibility('valider_statut', 'visible');\">\n" .
		"<option"  . mySel("prepa", $statut)  ." style='background-color: white'>" ._T('texte_statut_en_cours_redaction') ."</option>\n" .
		"<option"  . mySel("prop", $statut)  . " style='background-color: #FFF1C6'>" ._T('texte_statut_propose_evaluation') ."</option>\n" .
		"<option"  . mySel("publie", $statut)  . " style='background-color: #B4E8C5'>" ._T('texte_statut_publie') ."</option>\n" .
		"<option"  . mySel("poubelle", $statut) .
		http_style_background('rayures-sup.gif')  . '>'  ._T('texte_statut_poubelle') ."</option>\n" .
		"<option"  . mySel("refuse", $statut)  . " style='background-color: #FFA4A4'>" ._T('texte_statut_refuse') ."</option>\n" .		
		"</select>" .
		" &nbsp; " .
		http_img_pack("puce-".puce_statut($statut).'.gif', "", " class='puce'") .
		"  &nbsp;\n" .
		"<span class='visible_au_chargement' id='valider_statut'>" .
		"<input type='submit' value='"._T('bouton_valider')."' class='fondo' />" .
		"</span>" .
		aide("artstatut")
		. '</div>';  
	}
	
	return redirige_action_auteur('instituer_article',$id_article,'articles', "id_article=$id_article", $res, " method='post'");
}


// http://code.spip.net/@demande_publication
function demande_publication($id_article)
{
	return debut_cadre_relief('',true) .
		"<div style='text-align: center'>" .
		"<b>" ._T('texte_proposer_publication') . "</b>" .
		aide ("artprop") .
			redirige_action_auteur('instituer_article', "$id_article-prop",
			'articles',
			"id_article=$id_article",
			("<input type='submit' class='fondo' value=\"" . 
			    _T('bouton_demande_publication') .
			    "\" />\n"),
			"method='post'") .
		"</div>" .
		fin_cadre_relief(true);
}

?>
