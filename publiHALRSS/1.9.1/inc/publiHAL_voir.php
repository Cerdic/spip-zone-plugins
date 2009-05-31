<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');
include_spip('inc/actions');

// http://doc.spip.org/@afficher_syndic_articles
function publiHAL_afficher_syndic_articles($titre_table, $requete, $id = 0) {
	global $connect_statut, $spip_lang_right;

	$col = (($connect_statut == '0minirezo') ? 3 :  2) + ($id==0);
	$tmp_var = 't_' . substr(md5(join(' ',$requete)), 0, 4);

	if (!$requete['FROM']) $requete['FROM']= 'spip_syndic_articles';

	if (!$id) {
			$largeurs = array(7, '' );
			$styles = array('','arial11');
	} else {
			$largeurs = array(7,'');
			$styles = array('','arial11');
	}
	if ($connect_statut == '0minirezo') {
			$largeurs[] = '60';
			$styles[] = 'arial1';
			$largeurs[] = '70';
			$styles[] = 'arial1';
			//++
//			$largeurs[] = '80';
//			$styles[] = 'arial1';
//++
	}

	return affiche_tranche_bandeau($requete, "site-24.gif", $col, "#999999", "white", $tmp_var, $titre_table, $obligatoire, $largeurs, $styles, 'publiHAL_afficher_syndic_articles_boucle', array($tmp_var, $id));
}

function publiHAL_afficher_syndic_articles_boucle($row, &$my_sites, $bof, $redirect)
{
	global  $connect_statut, $spip_lang_right;

	$vals = '';

	$id_syndic_article=$row["id_syndic_article"];
	$id_syndic=$row["id_syndic"];
	$titre=safehtml($row["titre"]);
	$url=$row["url"];
	$date=$row["date"];
	$lesauteurs=typo($row["lesauteurs"]);
	$statut=$row["statut"];
	$descriptif=safehtml($row["descriptif"]);

	if ($statut=='publie') {
			$puce='puce-verte.gif';
	}
	else if ($statut == "refuse") {
			$puce = 'puce-poubelle.gif';
	}

	else if ($statut == "dispo") { // moderation : a valider
			$puce = 'puce-rouge.gif';
	}

	else if ($statut == "off") { // feed d'un site en mode "miroir"
			$puce = 'puce-rouge-anim.gif';
	}

	$vals[] = http_img_pack($puce, $statut, "width='7' height='7'");

	$s = "<a href='$url'>$titre</a>";

	$date = affdate_court($date);
	if (strlen($lesauteurs) > 0) $date = $lesauteurs.', '.$date;
	$s.= " ($date)";

	// Tags : d'un cote les enclosures, de l'autre les liens
	if($e = afficher_enclosures($row['tags']))
		$s .= ' '.$e;

	// descriptif
	if (strlen($descriptif) > 0) {
		// couper un texte vraiment tres long
		if (strlen($descriptif) > 500)
			$descriptif = safehtml(spip_substr($descriptif, 0, 300)).' (...)';
		else
			$descriptif = safehtml($descriptif);
		$s .= '<div class="arial1">'
			# 385px = largeur de la colonne ou s'affiche le texte
			. filtrer('image_reduire',$descriptif, 385, 550)
			. '</div>';
	}

	// tags
	if ($tags = afficher_tags($row['tags']))
		$s .= "<div style='float:$spip_lang_right;'>&nbsp;<em>"
			. $tags . '</em></div>';

	// source
	if (strlen($row['url_source']))
		$s .= "<div style='float:$spip_lang_right;'>"
		. propre("[".$row['source']."->".$row['url_source']."]")
		. "</div>";
	else if (strlen($row['source']))
		$s .= "<div style='float:$spip_lang_right;'>"
		. typo($row['source'])
		. "</div>";

	$vals[] = $s;

//	// on n'affiche pas la colonne 'site' lorsqu'on regarde un site precis
//	if ($GLOBALS['exec'] != 'sites') {
//		// $my_sites cache les resultats des requetes sur les sites
//		if (!$my_sites[$id_syndic])
//			$my_sites[$id_syndic] = spip_fetch_array(spip_query("SELECT nom_site, moderation, miroir FROM spip_syndic WHERE id_syndic=$id_syndic"));
//
//		$aff = $my_sites[$id_syndic]['nom_site'];
//		if ($my_sites[$id_syndic]['moderation'] == 'oui')
//			$aff = "<i>$aff</i>";
//			
//		$s = "<a href='" . generer_url_ecrire("sites","id_syndic=$id_syndic") . "'>$aff</a>";
//
//		$vals[] = $s;
//	}
 
//+++
	if ($connect_statut == '0minirezo'){
//		if (!$my_sites[$id_syndic])
//			$my_sites[$id_syndic] = spip_fetch_array(spip_query("SELECT nom_site, moderation, miroir FROM spip_syndic WHERE id_syndic=$id_syndic"));

//		$aff = $my_sites[$id_syndic]['nom_site'];
//		if ($my_sites[$id_syndic]['moderation'] == 'oui')
//			$aff = "<i>$aff</i>";
			
		$s = "<center><a href='" . generer_url_ecrire("publihal_publi","id_syndic_article=$id_syndic_article") . "'><b>Editer la publication $id_syndic_article</b></a></center>";

		$vals[] = $s;
		}
//+++
	
	if ($connect_statut == '0minirezo'){
		list($tmp_var, $id) = $redirect;
		$redirect = ($tmp_var . '=' . intval(_request($tmp_var)))
		. (!$id ? '' : "&id_syndic=$id");

		if ($statut == "publie"){
		  $s =  "[<a href='". redirige_action_auteur('instituer_syndic',"$id_syndic_article-refuse", $GLOBALS['exec'], $redirect) . "'><font color='black'>"._T('info_bloquer_lien')."</font></a>]";
		
		}
		else if ($statut == "refuse"){
		  $s =  "[<a href='". redirige_action_auteur('instituer_syndic',"$id_syndic_article-publie", $GLOBALS['exec'], $redirect) . "'>"._T('info_retablir_lien')."</a>]";
		}
//		else if ($statut == "off"
//		AND $my_sites[$id_syndic]['miroir'] == 'oui') {
//			$s = '('._T('syndic_lien_obsolete').')';
//		}
		else /* 'dispo' ou 'off' (dans le cas ancien site 'miroir') */
		{
		  $s = "[<a href='". redirige_action_auteur('instituer_syndic',"$id_syndic_article-publie", $GLOBALS['exec'], $redirect) . "'>"._T('info_valider_lien')."</a>]";
		}
		$vals[] = $s;
	}
		
	return $vals;
}
?>