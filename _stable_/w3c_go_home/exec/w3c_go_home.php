<?php
/*
 * valide_site
 *
 * outil de validation w3c et accessibilite du site
 *
 * Auteur : cedric.morin@yterium.com
 * © 2006 - Distribue sous licence GPL
 *
 */
include_spip('inc/validateur_api');

function exec_w3c_go_home(){
	global $connect_statut;
	$validateurs = array('spip_xhtml_validator');
	$out = "";
	
	include_spip ("inc/presentation");

	//
	// Recupere les donnees
	//

	if ($connect_statut != '0minirezo') {
		debut_page(_L("Validation Site W3C"), "w3c", "w3c");
		debut_gauche();
		debut_droite();
		echo "<strong>"._T('avis_acces_interdit')."</strong>";
		fin_page();
		exit;
	}

	debut_page(_L("Validation Site W3C"), "w3c", "w3c");
	
	$out .= debut_gauche('',true);
	$out .= debut_boite_info(true);
	$action = generer_action_auteur('w3cgh_reset_test',implode('-',$validateurs),generer_url_ecrire('w3c_go_home'));
	$out .= "<a href='$action'>"._L("Tout Reinitialiser")."</a><br/>";
	$out .= fin_boite_info(true);
	
	$out .= debut_droite('',true);

	// utiliser un recuperer_page car sinon les url sont calculees depuis ecrire, avec des redirect
	//include_spip('public/assembler');
	//$xml_sitemap=recuperer_fond('sitemap');
	$sitemap_url = parametre_url(generer_url_public('sitemap'),'var_mode',_request('var_mode'));
	include_spip('inc/xml');
	$sitemap = spip_xml_load($sitemap_url);

	$sitemap = reset($sitemap);
	$sitemap = reset($sitemap);
	if (isset($sitemap['url']) && is_array($sitemap['url']))
		$sitemap=$sitemap['url'];
	else
		$sitemap=array();

	/*
	$table_url[]=generer_url_public("recherche","recherche=conseil");	$urlcount++;
	$table_url[]=generer_url_public("recherche","recherche=municipal"); $urlcount++;
	$table_url[]=generer_url_public("recherche","recherche=ecole");	$urlcount++;
	$table_url[]=generer_url_public("recherche","recherche=mairie");	$urlcount++;
	$table_url[]=generer_url_public("recherche","recherche=permis");	$urlcount++;
	$table_url[]=generer_url_public("article","id_article=6");	$urlcount++;
	*/
		
	$titre_table = _L("Conformit&eacute; du site");
	$icone = "";
	$out .= "<div class='liste'>";
	$out .= bandeau_titre_boite2($titre_table, $icone, $couleur_claire, "black",false);
	$out .= "<table width='100%' cellpadding='3' cellspacing='0' border='0'>";
	
	$vals[] = '';
	$vals[] = 'url';
	$largeurs = array('','');
	$styles = array('arial11', 'arial11');
	foreach($validateurs as $nom){
		$action = generer_action_auteur('w3cgh_reset_test',$nom,generer_url_ecrire('w3c_go_home'));
		$vals[] = validateur_infos($nom)."<br /><a href='$action'>"._L("Reinitialiser")."</a>";
		$largeurs[] = '';
		$styles[] = 'arial11';
	}
	$table[] = $vals;

	if (is_array($sitemap) && count($sitemap)){
		$cpt_ok = 0;
		$id_test = 0;
		foreach($validateurs as $nom)
			$compteur[$nom] = 0;
		foreach($sitemap as $url) {
			$lastmod = strtotime($url['lastmod'][0]);
			$loc = $url['loc'][0];
			$ok=true;
			foreach($validateurs as $nom){
				$etat[$nom] = validateur_test_valide($nom,$loc,$lastmod);
				if (!$etat[$nom]) $ok =false;
				else $compteur[$nom]++;
			}

			$vals = '';
			$vals[] = ++$cpt;
	
			if ($ok){
				$cpt_ok++;
				$puce = 'puce-verte-breve.gif';
			}
			else
				$puce = 'puce-orange-breve.gif';
	
			$s = "<img src='"._DIR_IMG_PACK."$puce' width='7' height='7' border='0'>&nbsp;&nbsp;";
			$s .= "<a href='$loc'>".lignes_longues($loc,50)."</a>";
			$vals[] = $s;
			
			foreach($validateurs as $nom){
				$s = "";
				$url_voir = "#";
				$s .= "<a href='$url_voir'>";
				if ($etat[$nom])
					$s .= "OK (".date('d-m-Y H:i',$etat[$nom]).")</a>";
				else {
					$url_test = generer_url_ecrire('w3cgh_test',"nom=$nom&url=".urlencode($loc),true);
					$s .= "<span id='test_$id_test'></span></a>";
					if ($id_test<10)
						$s .= "<script type='text/javascript'>$('#test_$id_test').append(ajax_image_searching).load('$url_test');</script>";
					// ajouter la methode img en noscript
					$id_test++;
				}
				$vals[] = $s;
			}
			$table[] = $vals;
		}
	}
	$out .= afficher_liste($largeurs, $table, $styles);
	$out .= "</table>";
	$out .= "</div>\n";
	
	foreach($validateurs as $nom)
		$out .= $compteur[$nom]."/$cpt pages conforme selon le validateur $nom<br/>";
	
	$out .= "$cpt_ok/$cpt pages totalement conforme";
	echo $out,fin_gauche(),fin_page();

}

?>