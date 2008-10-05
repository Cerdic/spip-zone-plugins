<?php

/***************************************************************************\
 * 						Gestion du versioning 							   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('versioning_fonctions');

function inc_afficher_toutes_versions_dist($type,$id_article,$flag)
{
	$resultat = '';
	 
	// 1. On vérifie que l'article courant est bien l'original (ce n'est pas une copie)
	if(!isACopy($id_article))
	{
		$icone_new_version = _DIR_VERSIONING_IMG . "article_new_version-24.png" ;		
		
		$titre_boite = majuscules(_T('versioning:afficher_autres_versions')) . aide ("#");		
				
		$bouton = (!$flag
						? ''
						: (($flag === 'ajax')
							? bouton_block_visible("versioning$type")
							: bouton_block_invisible("versioning$type")))
			     . $titre_boite;
			     
		$reponse = afficher_autres_versions($id_article) ;	     
			     
			     
		$res = '<div><div>&nbsp;</div>'
			. debut_cadre_enfonce("$icone_new_version", true, "", $bouton)			
			. ($flag === 'ajax' ?
				debut_block_visible("versioning$type") :
				debut_block_invisible("versioning$type")) 	
			. $reponse     
			. $res
			. fin_block()
			. fin_cadre_enfonce(true)
			. '</div>' ;
			
		return ajax_action_greffe("versioning-$id_article", $res);		
	}
	
	return resultat;
}

function afficher_autres_versions($id_article_orig)
{	
	$toutes_versions = spip_query("SELECT * FROM spip_articles WHERE version_of=$id_article_orig");
	
	if(spip_num_rows($toutes_versions) > 0)
	{
		$res = "\n<div class='liste'>"
			. "\n<table width='100%' cellpadding='3' cellspacing='0' border='0'>" ;
	
		while($autre_version = spip_fetch_array($toutes_versions))
		{
			$res .= formater_autre_version($autre_version) ;						
		}

		$res .= afficher_liste($largeurs, $les_autres_versions, $styles)
			. "</table></div>";
	}
	else
	{
		$res ='';
	}
	
	return $res;
}

function formater_autre_version($autre_version)
{
	$id_article = $autre_version['id_article'];	
	$titre = $autre_version['titre'];
	$descriptif = $autre_version['descriptif'];
	$date = $autre_version['date'];

	$apres_formatage = "<tr><td>" 
					 . majuscules(affdate($date))
					 . "</td><td>"
					 . "<a href='" 
					 . generer_url_ecrire("articles","id_article=$id_article") 
					 . "'" 
					 . " title='" . attribut_html(typo($descriptif)) . "'>"
					 . $id_article . ". &nbsp;"
					 . typo($titre)
					 . "</a>"
					 . "</td></tr>";
					 
	return $apres_formatage;	
}
