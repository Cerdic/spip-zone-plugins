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
include_spip('inc/actions');

function exec_w3c_go_home(){
	global $connect_statut,$spip_lang_right;
	
	$validateurs = isset($GLOBALS['meta']['w3cgh_validateurs_actifs'])?unserialize($GLOBALS['meta']['w3cgh_validateurs_actifs']):array();
	$out = "";
	
	include_spip ("inc/presentation");

	//
	// Recupere les donnees
	//

	if ($connect_statut != '0minirezo') {
		debut_page(_T("w3cgh:titre_page"), "w3c", "w3c");
		debut_gauche();
		debut_droite();
		echo "<strong>"._T('avis_acces_interdit')."</strong>";
		fin_page();
		exit;
	}

	debut_page(_T("w3cgh:titre_page"), "w3c", "w3c");
	$out .= "<script type='text/javascript'><!--
	var a_tester;
	function tests(max){
		$('#annuler').show();
		a_tester = $('.test');
		perform_tests(max);
	}
	function perform_tests(max){
		var compteur=0;
		var nbitems = 10;
		var next_shot = max-10;
		if (next_shot<=0)
			next_shot=-1;
		if (max==0) next_shot = 0;
		else if (max<10) nbitems = max;
		if (nbitems>0){
			$(a_tester).lt(nbitems).each(function(){
				var elt = $(this);
				var url = elt.attr('rel');
				url = url.replace('&amp;','&');
				/* on relance a mi chemin : toujours entre 5 et 15 tests en cours */
				if (compteur==5){
					elt.toggleClass('process').toggleClass('test').append(ajax_image_searching).load(url,function(){ elt.toggleClass('process'); perform_tests(next_shot);});
				}
				else {
					elt.toggleClass('process').toggleClass('test').append(ajax_image_searching).load(url,function(){ elt.toggleClass('process');});
				}
				compteur++;
			});
			a_tester=a_tester.gt(nbitems-1);
		}
		if (next_shot==-1)
			$('#annuler').hide();
	}
	function annule_tests(){
		a_tester = undefined;
		$('.process').html('');
		$('#annuler').hide();
		return false;
	}
	function ferme_rapport(origine){
		$('#rapport_test').html('');
		window.location.hash = origine;
		return false;
	}
	function affiche_rapport(url,origine){
		url = url.replace('&amp;','&');
		$('#rapport_test').html(\"<div style='text-align:$spip_lang_right' class='verdana2'><a href='#' onclick='return ferme_rapport(\"+'\"'+origine+'\"'+\");'>"._T('icone_retour')."</a></div>\"
		+\"<iframe src='\"+url+\"' style='width:100%;height:600px;'></iframe>\");
		window.location.hash = 'rapport_test';
		return false;
	}
	--></script>";
	
	$out .= "<div id='rapport_test'></div>";
	$out .= debut_gauche('',true);
	$out .= debut_boite_info(true);
	$out .= w3cgh_formulaire_choix_validateurs();
	$action = generer_action_auteur('w3cgh_reset_test',implode('-',$validateurs),generer_url_ecrire('w3c_go_home'));
	$out .= "<a href='$action'>"._T("w3cgh:reset_all")."</a><br/>";
	$out .= "<p class='verdana2'>";
	$out .= "<a href='#' onclick='tests(10);'>"._T("w3cgh:tester_10")."</a><br/>";
	$out .= "<a href='#' onclick='tests(0);'>"._T("w3cgh:tester_tout")."</a><br/>";
	$out .= "</p>";
	$out .= "\n<p align='$spip_lang_right' id='annuler' style='display:none;'><input type='submit' name='annuler' class='fondo' onclick='annule_tests();' value='"._T('w3cgh:bouton_arreter')."' /></p>";
	$out .= fin_boite_info(true);
	
	// utiliser un recuperer_page car sinon les url sont calculees depuis ecrire, avec des redirect
	$sitemap_url = parametre_url(generer_url_public('w3cgh_sitemap'),'var_mode',_request('var_mode'));
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
		
	$vals[] = '';
	$vals[] = 'url';
	$largeurs = array('','');
	$styles = array('arial11', 'arial11');
	foreach($validateurs as $nom){
		$action = generer_action_auteur('w3cgh_reset_test',$nom,generer_url_ecrire('w3c_go_home'));
		$vals[] = validateur_infos($nom)."<br /><a href='$action'>"._T("w3cgh:reset")."</a>";
		$largeurs[] = '';
		$styles[] = 'arial11';
		$url_test[$nom] = generer_url_ecrire('w3cgh_test',"nom=$nom&url=");
		$url_affiche[$nom] = generer_url_ecrire('w3cgh_affiche',"nom=$nom&url=",true);
		$url_voir[$nom] = generer_url_ecrire('w3cgh_voir',"nom=$nom&url=");
	}
	$table[] = $vals;
	$noscript = _request('noscript');

	if (is_array($sitemap) && count($sitemap)){
		$cpt_ok = 0;
		$id_test = 0;
		$time_mark = time();
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
	
/*			if ($ok){
				$cpt_ok++;
				$puce = 'puce-verte-breve.gif';
				$alt = "OK";
			}
			else{
				$puce = 'puce-orange-breve.gif';
				$alt = "";
			}
*/	
			$s = "";
			//$s = "<img src='"._DIR_IMG_PACK."$puce' width='7' height='7' style='border:0' alt='$alt' />&nbsp;&nbsp;";
			$s .= "<a href='$loc'>".lignes_longues($loc,50)."</a>";
			$vals[] = $s;
			
			foreach($validateurs as $nom){
				$s = "";
				$loce = urlencode($loc);
				$url_affiche = $url_affiche[$nom].$loce;
				$url_voir = $url_voir[$nom].$loce;
				$id_test++;
				if ($etat[$nom]){
					$s .= "<a href='$url_voir' id='t$id_test' onclick='return affiche_rapport(\"$url_voir\",\"t$id_test\")'>";
					$s .= "OK (".date('d-m-Y H:i',$etat[$nom]).")</a>";
				}
				else {
					$url_test = $url_test[$nom].$loce;
					$s .= "<a href='$url_voir' id='t$id_test' onclick='return affiche_rapport(\"$url_voir\",\"t$id_test\")' rel='$url_test' class='test'></a>";
					// la methode img en noscript
					if ($noscript){
						$url_test = parametre_url($url_test,'var_mode','image');
						$url_test = parametre_url($url_test,'time',$time_mark); // eviter de taper dans le cache navigateur
						$s .= "<noscript><a href='$url_voir' ><img src='$url_test' alt='test'/></a></noscript>";
					}
				}
				$vals[] = $s;
			}
			$table[] = $vals;
		}
	}
	
	$out .= "<p class='verdana2'>";
	foreach($validateurs as $nom)
		$out .= _T('w3cgh:resultat_pages_conformes',array('nb'=>$compteur[$nom],'tot'=>$cpt,'nom'=>$nom))."<br/>";
	
	$out .= _T('w3cgh:resultat_pages_completement_conformes',array('nb'=>$cpt_ok,'tot'=>$cpt));
	$out .= "</p>";

	$out .= debut_droite('',true);
	$titre_table = _T("w3cgh:titre_tableau_conformite");
	$icone = "";
	$out .= "<div class='liste'>";
	$out .= bandeau_titre_boite2($titre_table, $icone, $couleur_claire, "black",false);
	$out .= "<table width='100%' cellpadding='3' cellspacing='0' border='0'>";
	$out .= afficher_liste($largeurs, $table, $styles);
	$out .= "</table>";
	$out .= "</div>\n";
	
	echo $out,fin_gauche(),fin_page();

}

function w3cgh_formulaire_choix_validateurs(){
	global $spip_lang_right;
	$validateurs_actifs = isset($GLOBALS['meta']['w3cgh_validateurs_actifs'])?unserialize($GLOBALS['meta']['w3cgh_validateurs_actifs']):array();
	$out = "";
	$out .= "<b>"._T('w3cgh:titre_formulaire_choix_validateur')."</b><br />";
	$action = generer_action_auteur("w3cgh_selectionne","",generer_url_ecrire('w3c_go_home'));
	$out .= "<form action='$action'><div>";
	$out .= form_hidden($action);
	$liste = validateur_liste();
	foreach ($liste as $validateur){
		$out .= "<label for='choix_$validateur'>" .
		boutonne('checkbox',
			'validateurs[]',
			$validateur,
			(in_array($validateur,$validateurs_actifs) ? ' checked="checked" ' : '') .
			"id='choix_$validateur'") .
		"&nbsp;" . 
		validateur_infos($validateur) .
		"</label>" .
		"<br />";	
	}
	$out .= "\n<p align='$spip_lang_right'><input type='submit' name='Changer' class='fondo' value='"._T('bouton_changer')."' /></p>";
	$out .= "</div></form>";
	return $out;
}

?>