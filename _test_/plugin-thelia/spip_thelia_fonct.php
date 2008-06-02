<?php
function spip_thelia_supprimer_balises_thelia($texte) {
	//suppression des boucles thélia
	$texte = str_replace("THELIA_", "DUMMY_", $texte);
	//suppression des balises thelia
	$texte = str_replace("THELIA-", "DUMMY-", $texte);
	return $texte;

}
function spip_thelia_header_prive($flux) {
	if (!file_exists("../fonctions/moteur.php")&&($_REQUEST['exec']!="")) 
		echo ("erreur : th&eacute;lia introuvable, v&eacute;rifiez que les sous-r&eacute;pertoires de th&eacute;lia et spip sont dans le m&ecirc;me r&eacute;pertoire.");
	if (!function_exists('lire_config'))
		echo ("erreur : le plugin CFG est n'est pas install&eacute;.");
	return $flux;
}

function spip_thelia_insert_head($flux) {
	$flux.="<link rel=\"stylesheet\" href=\""._DIR_PLUGIN_SPIP_THELIA."spipthelia.css\" type=\"text/css\" media=\"projection, screen, tv\" />";
	return $flux;
}

function spip_thelia_appeler_moteur_thelia($texte)
{
	include_spip('inc/charsets');

	//récupérer la version de thélia : valeurs possibles :
	//	- supérieure ou égale à 1.3.4 = "after_1_3_4" (par défaut)  
	//	- 1.3.3 = "1_3_3"

	if (function_exists('lire_config')) {
		$version_thelia = lire_config("spip_thelia/version_thelia_spip_thelia", "after_1_3_4");
	} else { 
		echo ("erreur : le plugin CFG est n'est pas install&eacute;.");
		return $texte;
	}
	
	//si pas de boucle ou de balise thélia dans la page on sort	
	if ((strpos($texte, "THELIA-") === FALSE) && (strpos($texte, "<THELIA") == FALSE))
		return $texte;
	
	//convertion utf-8 vers ISO des variables $_REQUEST
	$sauvegarde_request ="";
	foreach ($_REQUEST as $clef => $valeur) {
                $sauvegarde_request[$clef] = $valeur;
		$_REQUEST[$clef]=unicode2charset(charset2unicode($valeur, 'utf-8'),'iso-8859-1');
            }

	//parsonnalisation des variables thélia
	switch($_REQUEST['page']){
		case 'panier' : $securise=0; $pageret=1; break;
		case 'adresse' : $securise=1; $pageret=1; break;
		case 'cheque' : $securise=1; $pageret=1;$reset=1; break;
		case 'commande' : $securise=1; $pageret=1; $panier=1; $transport=1; break;
		case 'commande_detail' : $securise=1; break;
		case 'commande_visualiser' : $securise=1; break;
		case 'compte_modifier' : $formulaire=1; $securise=1; $obligetelfixe=1; break;
		case 'compte_modifiererr' : $formulaire=1; $securise=1; $obligetelfixe=1; break;
		case 'connexion' : $pageret=0; break;
		case 'livraison_adresse' : $securise=1; break;
		case 'livraison_modifier' : $securise=1; break;
		case 'moncompte' : $pageret=1; break;
		case 'nouveau' : $securise=1; break;
		case 'regret' : $pageret=1; break;	
		case 'virement' : $securise=1; $pageret=1; $reset=1; break;
	}
	
	global $page;
	
	//sauvegarde des variables qui vont être modifiées pour thélia
	$sav_page = $page;
	$sav_session_navig_lang = $_SESSION['navig']->lang;
	

	//conflit sur la variable $page. 
	$page = new stdclass;
	$page = "";
	
	

	include_once("classes/Navigation.class.php");
	
	session_start();

	//conflit entre spip et thélia sur la langue en session.
	if ($_SESSION['navig']->lang != '') {
		$_SESSION['navig']->lang=0;
	}

	//concordance des langues entre spip et thélia 
	//modifiez éventuellement la liste si vous avez ajouté de nouvelles langues dans Thélia
	

	switch($_REQUEST['lang']) {
		case 'fr' : $_REQUEST['lang'] = 1; break;
		case 'en' : $_REQUEST['lang'] = 2; break;
		case 'es' : $_REQUEST['lang'] = 3; break;
		default: $_REQUEST['lang'] = 1; break;
	}
	
	//réaffectation des variables de thélia qui ont étées renommées dans les squelettes pour éviter les conflits avec spip
	$_REQUEST['action'] = $_REQUEST['thelia_action'];
	$_REQUEST['page'] = $_REQUEST['page_thelia'];
	if (isset($_REQUEST['thelia_article']))
		$_REQUEST['article'] = $_REQUEST['thelia_article'];
	
	
	//on prépare le flux à envoyer au moteur thélia
	$res = $texte;
	$res = str_replace("THELIA-", "#", $res);

	//avant d'envoyer à thélia, on convertie en iso pour thélia
	$res = unicode2charset(charset2unicode($res, 'utf-8'),'iso-8859-1');

	//on bloque la sortie vers le navigateur le temps d'y faire quelques substitutions	
	ob_start();
	
	if ($version_thelia == "1_3_3") {
		//si version = 1.3.3 : surcharge dans le plugin
		include_once(_DIR_PLUGIN_SPIP_THELIA."moteur-thelia-1_3_3.php");
	} else {
		//si version >= 1.3.4 : plus de surcharge dans le plugin, on appelle directement le moteur de Thélia
		include_once("fonctions/moteur.php");
	}

	$texte = ob_get_contents();
	ob_end_clean();
	$texte = remplacement_sortie_thelia($texte);

	//au retour de thélia, on convertie en utf8 pour SPIP
	$texte = unicode2charset(charset2unicode($texte, 'iso-8859-1'),'utf-8');
	

	//on restaure les variables session et request modifiées pour les plugins suivants sur affichage final
	$page = $sav_page;
	$_SESSION['navig']->lang = $sav_session_navig_lang;
	

	//restauration des variables $_REQUEST en utf-8 pour SPIP
	foreach ($sauvegarde_request as $clef => $valeur) {
                $_REQUEST[$clef]=$valeur;
        }
	
	return ($texte);	
}

function remplacement_sortie_thelia($in_thelia)
{
    	//renommage action en thelia_action. méthode provisoire à revoir.
	$in_thelia = str_replace("adresse.php?action", "adresse.php?thelia_action", $in_thelia);
	$in_thelia = str_replace("cheque.php?action", "cheque.php?thelia_action", $in_thelia);
	$in_thelia = str_replace("commande.php?action", "commande.php?thelia_action", $in_thelia);
	$in_thelia = str_replace("commande_detail.php?action", "commande_detail.php?thelia_action", $in_thelia);
	$in_thelia = str_replace("commande_visualiser.php?action", "commande_visualiser.php?thelia_action", $in_thelia);
	$in_thelia = str_replace("compte_modifier.php?action", "compte_modifier.php?thelia_action", $in_thelia);
	$in_thelia = str_replace("compte_modifiererr.php?action", "compte_modifiererr.php?thelia_action", $in_thelia);
	$in_thelia = str_replace("connexion.php?action", "connexion.php?thelia_action", $in_thelia);
	$in_thelia = str_replace("creercompte.php?action", "creercompte.php?thelia_action", $in_thelia);
	$in_thelia = str_replace("formulerr.php?action", "formulerr.php?thelia_action", $in_thelia);
	$in_thelia = str_replace("imgpop?action", "imgpop?thelia_action", $in_thelia);
	$in_thelia = str_replace("livraison_adresse.php?action", "livraison_adresse.php?thelia_action", $in_thelia);
	$in_thelia = str_replace("mdpoublie.php?action", "mdpoublie.php?thelia_action", $in_thelia);
	$in_thelia = str_replace("merci.php?action", "merci.php?thelia_action", $in_thelia);
	$in_thelia = str_replace("moncompte.php?action", "moncompte.php?thelia_action", $in_thelia);
	$in_thelia = str_replace("nouveau.php?action", "nouveau.php?thelia_action", $in_thelia);
	$in_thelia = str_replace("panier.php?action", "panier.php?thelia_action", $in_thelia);
	$in_thelia = str_replace("produit.php?action", "produit.php?thelia_action", $in_thelia);
	$in_thelia = str_replace("regret.php?action", "regret.php?thelia_action", $in_thelia);
	$in_thelia = str_replace("virement.php?action", "virement.php?thelia_action", $in_thelia);

	return $in_thelia;
}
function spip_thelia_ajouter_boutons($boutons_admin){
	// si on est admin
	if ($GLOBALS['connect_statut'] == "0minirezo") {
	  	// on voit le bouton dans la barre "naviguer"
	  	$boutons_admin['naviguer']->sousmenu['spip_thelia_catalogue']= new Bouton(
		_DIR_PLUGIN_SPIP_THELIA.'img_pack/logo_thelia_petit.png', 'Catalogue Th&eacute;lia');
	}
	return $boutons_admin;
}
function spip_thelia_affiche_milieu($flux) {
	$exec =  $flux['args']['exec'];
	$id_article= $_REQUEST['id_article'];
	$id_rubrique= $_REQUEST['id_rubrique'];
	if ($exec=='articles'){
		$flux['data'] .= spip_thelia_formulaire_article($id_article, article_editable($id_article),'articles');
	}else if (($exec=='naviguer')&&($id_rubrique)){
		$flux['data'] .= spip_thelia_formulaire_rubrique($id_rubrique, rubrique_editable($id_rubrique),'rubriques');
	}
	return $flux;
}
function article_editable($id_article){
	return autoriser('modifier','article',$id_article);
}
function rubrique_editable($id_rubrique){
	return autoriser('modifier','rubrique',$id_rubrique);
}
function spip_thelia_formulaire_article($id_article, $flag_editable, $script){

  	global $spip_lang_right;
 	include_spip("inc/presentation");
	include_spip('public/assembler');
	include_spip('inc/charsets');

	global $spip_lang_left, $spip_lang_right, $options;
	global $connect_statut, $options,$connect_id_auteur, $couleur_claire ;

	$out = "<div id='editer_produit-$id_article'>";
	$out .= "<a name='produit'></a>";
	if ($flag_editable) {
		if (_request('edit')||_request('neweven'))
			$bouton = bouton_block_visible("produitsarticle");
		else
			$bouton = bouton_block_invisible("produitsarticle");
	}
	
	$out .= debut_cadre_enfonce("../"._DIR_PLUGIN_SPIP_THELIA."/img_pack/logo_thelia_petit.png", true, "", $bouton._T('spipthelia:produits_associes_article'));

	$out .= debut_block_invisible('produitsarticle');
	
	$link = generer_action_auteur('produits_article',"$id_article",_DIR_RESTREINT_ABS.($retour?(str_replace('&amp;','&',$retour)):generer_url_ecrire('articles&id_article='.$id_article,"",false,true)));
	$out .= "<form method='POST' action='$link' style='float:$spip_lang_right'>\n";
	$out .= form_hidden($link);
	
	//
	// Afficher les produits associes
	//
	
	//on bloque la sortie vers le navigateur le temps d'y faire quelques substitutions	
	$res = recuperer_fond("fonds/produits_associes_article","id_article=".$id_article);
	$res = str_replace("THELIA-", "#", $res);
	
	//avant d'envoyer à thélia, on convertie en iso pour thélia
	$res = unicode2charset(charset2unicode($res, 'utf-8'),'iso-8859-1');
	ob_start();
	chdir('..');
	include_once("fonctions/moteur.php");
	chdir('ecrire');
	$texte = ob_get_contents();
	ob_end_clean();
	$texte = remplacement_sortie_thelia($texte);

	//au retour de thélia, on convertit en utf8 pour SPIP
	$texte = unicode2charset(charset2unicode($texte, 'iso-8859-1'),'utf-8');
	$out .= $texte;

	$out .= "</form>\n";	

	$out .= fin_block();

	$out .= fin_cadre_enfonce(true);
	$out .= "</div><br/>";
	return $out;
}

function spip_thelia_formulaire_rubrique($id_rubrique, $flag_editable, $script){

  	global $spip_lang_right;
 	include_spip("inc/presentation");
	include_spip('public/assembler');
	include_spip('inc/charsets');

	global $spip_lang_left, $spip_lang_right, $options;
	global $connect_statut, $options,$connect_id_auteur, $couleur_claire ;

	$out = "<div id='editer_produit-$id_rubrique'>";
	$out .= "<a name='produit'></a>";
	if ($flag_editable) {
		if (_request('edit')||_request('neweven'))
			$bouton = bouton_block_visible("produitsrubrique");
		else
			$bouton = bouton_block_invisible("produitsrubrique");
	}
	
	$out .= debut_cadre_enfonce("../"._DIR_PLUGIN_SPIP_THELIA."/img_pack/logo_thelia_petit.png", true, "", $bouton._T('spipthelia:produits_associes_rubrique'));
	$out .= debut_block_invisible('produitsrubrique');
	
	$link = generer_action_auteur('produits_rubrique',"$id_rubrique",_DIR_RESTREINT_ABS.($retour?(str_replace('&amp;','&',$retour)):generer_url_ecrire('naviguer&id_rubrique='.$id_rubrique,"",false,true)));
	$out .= "<form method='POST' action='$link' style='float:$spip_lang_right'>\n";
	$out .= form_hidden($link);
	
	//
	// Afficher les produits associes
	//
	
	//on bloque la sortie vers le navigateur le temps d'y faire quelques substitutions	
	$res = recuperer_fond("fonds/produits_associes_rubrique","id_rubrique=".$id_rubrique);
	$res = str_replace("THELIA-", "#", $res);
	
	//avant d'envoyer à thélia, on convertie en iso pour thélia
	$res = unicode2charset(charset2unicode($res, 'utf-8'),'iso-8859-1');
	ob_start();
	chdir('..');
	include_once("fonctions/moteur.php");
	chdir('ecrire');
	$texte = ob_get_contents();
	ob_end_clean();
	$texte = remplacement_sortie_thelia($texte);

	//au retour de thélia, on convertit en utf8 pour SPIP
	$texte = unicode2charset(charset2unicode($texte, 'iso-8859-1'),'utf-8');
	$out .= $texte;

	$out .= "</form>\n";	

	$out .= fin_block();
	$out .= fin_cadre_enfonce(true);
	$out .= "</div><br/>";
	return $out;
}
?>