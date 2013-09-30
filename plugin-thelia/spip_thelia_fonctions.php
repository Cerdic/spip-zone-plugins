<?php

include_spip("inc/charsets");
include_spip('plugins/installer'); // spip_version_compare dans SPIP 3.x
include_spip('inc/plugin'); // spip_version_compare dans SPIP 2.x
if (spip_version_compare($GLOBALS['spip_version_branche'], '3.0.0alpha', '>=')) {
                define('_SPIP3', true);
} else {
                define('_SPIP3', false);
}



function spip_thelia_supprimer_balises_thelia($texte) {
	//suppression des boucles th�lia
	$texte = str_replace("THELIA_", "DUMMY_", $texte);
	//suppression des balises thelia
	$texte = str_replace("THELIA-", "DUMMY-", $texte);
	return $texte;
}

function spip_thelia_demarrer_session_thelia () {
	global $page;
	
	//sauvegarde des variables qui vont �tre modifi�es pour th�lia
	$sav_page = $page;
	$sav_session_navig_lang = $_SESSION['navig']->lang;
	
	//conflit sur la variable $page. 
	$page = new stdclass;
	$page = "";

	include_once("../"._RACINE_THELIA."/classes/Navigation.class.php");
	
	ini_set('arg_separator.output', '&amp;');
	ini_set("url_rewriter.tags","a=href,area=href,frame=src,iframe=src,input=src");
	session_start();
}

function spip_thelia_header_prive($flux) {	
	//si une boite de s�lection spip/th�lia sera affich�e sur la page, il faut d�marrer pr�alablement une session th�lia
	$exec =  $_REQUEST['exec'];
	$id_article= $_REQUEST['id_article'];
	$id_rubrique= $_REQUEST['id_rubrique'];
	if (function_exists('lire_config')) {
		if (($exec=='articles')||($exec=='article')){
			if((lire_config("spip_thelia/produits_articles_spip_thelia", "non") == "oui")||(lire_config("spip_thelia/rubriques_articles_spip_thelia", "non") == "oui"))
				spip_thelia_demarrer_session_thelia();
		}
		else if ((($exec=='naviguer')||($exec='rubrique'))&&($id_rubrique)){
			if((lire_config("spip_thelia/produits_rubriques_spip_thelia", "non") == "oui")||(lire_config("spip_thelia/rubriques_rubriques_spip_thelia", "non") == "oui"))
				spip_thelia_demarrer_session_thelia();
		}
	}


	//on restaure les variables session et request modifi�es pour les plugins suivants sur affichage final
	$page = $sav_page;
	$_SESSION['navig']->lang = $sav_session_navig_lang;
	
	if (!file_exists("../"._RACINE_THELIA."fonctions/moteur.php")&&($_REQUEST['exec']!="")) 
		echo ("erreur : th&eacute;lia introuvable, v&eacute;rifiez que les sous-r&eacute;pertoires de th&eacute;lia et spip sont dans le m&ecirc;me r&eacute;pertoire.");
	if (!function_exists('lire_config'))
		echo ("erreur : le plugin CFG est n'est pas install&eacute;.");
	return $flux;
}

function spip_thelia_insert_head($flux) {
	$flux.="<link rel=\"stylesheet\" href=\""._DIR_PLUGIN_SPIP_THELIA."spipthelia.css\" type=\"text/css\" media=\"projection, screen, tv\" />";
	return $flux;
}


function spip_thelia_appeler_moteur_thelia($texte) {
	
	include_spip("inc/utils");

	//Obtenir les arguments de requete
	$keys_request = array_keys($_REQUEST);
	
	
	//si pas de boucle ou de balise th�lia ou pas d'action thelia dans la page on sort	
	if (((strpos($texte, "THELIA-") === FALSE) && (strpos($texte, "<THELIA") == FALSE)) && !count(preg_grep("#thelia.*#",$keys_request)))
		return $texte;
	
	//convertion utf-8 vers ISO des variables $_REQUEST
	if(lire_config("spip_thelia/encodage_spip_thelia_post", "non") == "oui") {
		$sauvegarde_request = array();
		foreach ($_REQUEST as $clef => $valeur) {
                $sauvegarde_request[$clef] = $valeur;
				$_REQUEST[$clef]=unicode2charset(charset2unicode($valeur, 'utf-8'),'iso-8859-1');
         }
	}
	//parsonnalisation des variables th�lia
	switch($_REQUEST['page']){
		case 'merci' : $securise=0; $pageret=0; $reset=1; break;
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
		case 'moncompte' : $pageret=1; $securise=1; break;
		case 'nouveau' : $securise=1; break;
		case 'regret' : $pageret=1; break;	
		case 'virement' : $securise=1; $pageret=1; $reset=1; break;
		case 'formulerr' : set_request('errform','1'); break;
	}

	global $page, $res, $id_rubrique;

	//sauvegarde des variables qui vont �tre modifi�es pour th�lia
	$sav_page = $page;
	$sav_session_navig_lang = $_SESSION['navig']->lang;
	
	//conflit sur la variable $page. 
  
	$page = new stdclass;
	$page = "";

	include_once(_RACINE_THELIA."classes/Navigation.class.php");
	
	ini_set('arg_separator.output', '&amp;');
	ini_set("url_rewriter.tags","a=href,area=href,frame=src,iframe=src,input=src");
	session_start();

	//conflit entre spip et th�lia sur la langue en session.
	if ($_SESSION['navig']->lang != '') {
		$_SESSION['navig']->lang=0;
	}

	//concordance des langues entre spip et th�lia 
	//modifiez �ventuellement la liste si vous avez ajout� de nouvelles langues dans Th�lia
	
	switch($_REQUEST['lang']) {
		case 'fr' : $_REQUEST['lang'] = 1; break;
		case 'en' : $_REQUEST['lang'] = 2; break;
		case 'es' : $_REQUEST['lang'] = 3; break;
		default: $_REQUEST['lang'] = 1; break;
	}
	
	//r�affectation des variables de th�lia qui ont �t�es renomm�es dans les squelettes pour �viter les conflits avec spip
	$_REQUEST['action'] = $_REQUEST['thelia_action'];
	$_REQUEST['page'] = $_REQUEST['page_thelia'];

	if (isset($_REQUEST['thelia_article']))
		$_REQUEST['article'] = $_REQUEST['thelia_article'];
	
	//on pr�pare le flux � envoyer au moteur th�lia
	$res = $texte;
	$res = str_replace("THELIA-", "#", $res);

	//avant d'envoyer � th�lia, on convertie en iso pour th�lia
	if(lire_config("spip_thelia/encodage_spip_thelia", "non") == "oui")
		$res = unicode2charset(charset2unicode($res, 'utf-8'),'iso-8859-1');

	//on bloque la sortie vers le navigateur le temps d'y faire quelques substitutions	
	ob_start();
	
	//si version >= 1.3.4 : plus de surcharge dans le plugin, on appelle directement le moteur de Th�lia
	include_once(_RACINE_THELIA."fonctions/moteur.php");

	//Connexion à SPIP à la création du compte Thelia
		if ($_REQUEST['page'] == 'nouveau' || $_REQUEST['page_thelia'] == 'nouveau' || $_REQUEST['action'] == 'transport' || $_REQUEST['action'] == 'paiement' || !$_REQUEST['page']) {
		if ($_SESSION['navig']->connecte == 1 && lire_config("spip_thelia/auth_unique_spip_thelia","non")=="oui")  {
			include_spip('auth/thelia');
			$auteur = creer_auteur_thelia(
				array(
					'login'=>'',
					'pass'=>'',
					'client'=>$_SESSION['navig']->client
				)
			);
			$session = charger_fonction('session','inc');
			$session($auteur);
			$data = pipeline('thelia_authentifie',array("auteur" => $auteur,"statut"=>"nouveau"));
		}
	}

	
	$texte = ob_get_contents();
	ob_end_clean();
	$texte = remplacement_sortie_thelia($texte);

	//au retour de th�lia, on convertie en utf8 pour SPIP
	if(lire_config("spip_thelia/encodage_spip_thelia", "non") == "oui")
		$texte = unicode2charset(charset2unicode($texte, 'iso-8859-1'),'utf-8');
	
	//on restaure les variables session et request modifi�es pour les plugins suivants sur affichage final
	$page = $sav_page;
	$_SESSION['navig']->lang = $sav_session_navig_lang;
	
	//restauration des variables $_REQUEST en utf-8 pour SPIP
	if(lire_config("spip_thelia/encodage_spip_thelia_post", "non") == "oui") {
		foreach ($sauvegarde_request as $clef => $valeur) {
                $_REQUEST[$clef]=$valeur;
        }
    }
	
	return ($texte);	
}

function remplacement_sortie_thelia($in_thelia) {
	//renommage action en thelia_action. m�thode provisoire � revoir.
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
	$in_thelia = str_replace("?fond=", "?page=", $in_thelia);

	return $in_thelia;
}

function spip_thelia_affiche_milieu($flux) {
	$exec =  $flux['args']['exec'];
	$id_article= $_REQUEST['id_article'];
	$id_rubrique= $_REQUEST['id_rubrique'];
	if (function_exists('lire_config')) {
		if (($exec=='article')||($exec=='articles')){
			if((lire_config("spip_thelia/produits_articles_spip_thelia", "non") == "oui")||(lire_config("spip_thelia/rubriques_articles_spip_thelia", "non") == "oui"))
				$flux['data'] .= spip_thelia_formulaire_article($id_article, spip_thelia_article_editable($id_article),'articles');
		}
		else if ((($exec=='naviguer')||($exec=='rubrique'))&&($id_rubrique)){
			if((lire_config("spip_thelia/produits_rubriques_spip_thelia", "non") == "oui")||(lire_config("spip_thelia/rubriques_rubriques_spip_thelia", "non") == "oui"))
				$flux['data'] .= spip_thelia_formulaire_rubrique($id_rubrique, spip_thelia_rubrique_editable($id_rubrique),'rubriques');
		}
	}  
	return $flux;
}

function spip_thelia_article_editable($id_article){
	return autoriser('modifier','article',$id_article);
}

function spip_thelia_rubrique_editable($id_rubrique) {
	return autoriser('modifier','rubrique',$id_rubrique);
}

function spip_thelia_formulaire_article($id_article, $flag_editable, $script) {

	global $spip_lang_right;
 	include_spip("inc/presentation");
	include_spip('public/assembler');
	include_spip('inc/charsets');

	global $spip_lang_left, $spip_lang_right, $options;
	global $connect_statut, $options,$connect_id_auteur, $couleur_claire ;

	$out = "<div id='editer_produit-$id_article'>";
	$out .= "<a name='produit'></a>";
	
	// Quel est le bon titre
	if (lire_config("spip_thelia/produits_articles_spip_thelia", "non") != "oui") {
		$titre = _T('spipthelia:rubriques_associees_article');
	} elseif (lire_config("spip_thelia/rubriques_articles_spip_thelia", "non") != "oui") {
			$titre = _T('spipthelia:produits_associes_article');
		} else {
			$titre = _T('spipthelia:produits_et_rubriques_associes_article');			
			}
			
	if (function_exists('bouton_block_depliable')) {  // SPIP2.0
		if ($flag_editable) {
			if (_request('edit')||_request('neweven'))
				$bouton = bouton_block_depliable($titre,true,"produitsarticle");
			else
				$bouton = bouton_block_depliable($titre,false,"produitsarticle");
		}
		$out .= debut_cadre_enfonce("../"._DIR_PLUGIN_SPIP_THELIA."/img_pack/logo_thelia_petit.png", true, "", $bouton);
	
	} else {
		if (_request('edit')||_request('neweven'))
			$bouton = bouton_block_visible("produitsarticle").$titre;
		else
			$bouton = bouton_block_invisible("produitsarticle").$titre;	
		$out .= debut_cadre_enfonce("../"._DIR_PLUGIN_SPIP_THELIA."/img_pack/logo_thelia_petit.png", true, "", $bouton);
	
		//
		// Afficher les produits associes
		//
		$out .= afficher_rubriques_objet('article',$id_rubrique);

		$out .= afficher_produits_objet('article',$id_article);

		$out .= debut_block_invisible('produitsarticle');
	}

	
	
	if (_SPIP3)
		$link = generer_action_auteur('produits_article',"$id_article",generer_url_ecrire('article','id_article='.$id_article));
	else 
		$link = generer_action_auteur('produits_article',"$id_article",generer_url_ecrire('articles','id_article='.$id_article));
	
	$out .= "<form method='POST' action='$link'>\n";
	$out .= form_hidden($link);
	
	//masquer provisoirement les warning de session de Th�lia en attendant une correction
	//Th�lia retourne des warning de session (headers already sent) car elle d�marre trop tard, mais on ne l'utilise pas, on se contente de lister les produits
	$sav_error_reporting = error_reporting(E_ERROR);
	
	//on bloque la sortie vers le navigateur le temps d'y faire quelques substitutions	
	$res = recuperer_fond("fonds/produits_associes_article",array("id_article" => $id_article));
	$res = str_replace("THELIA-", "#", $res);
	
	//avant d'envoyer � th�lia, on convertie en iso pour th�lia
	$res = unicode2charset(charset2unicode($res, 'utf-8'),'iso-8859-1');
	ob_start();
	chdir('..');
	include_once(_RACINE_THELIA."fonctions/moteur.php");
	chdir('ecrire');
	$texte = ob_get_contents();
	ob_end_clean();
	$texte = remplacement_sortie_thelia($texte);

	//au retour de th�lia, on convertit en utf8 pour SPIP
	if (!is_utf8($texte)) $texte = unicode2charset(charset2unicode($texte, 'iso-8859-1'),'utf-8');
	$out .= $texte;
	
	//remettre le niveau d'erreur pr�c�dent
	error_reporting($sav_error_reporting);
	
	$out .= "</form>\n";	

	$out .= fin_block();

	$out .= fin_cadre_enfonce(true);
	$out .= "</div><br/>";
	return $out;
}

function spip_thelia_formulaire_rubrique($id_rubrique, $flag_editable, $script) {

  	global $spip_lang_right;
 	include_spip("inc/presentation");
	include_spip('public/assembler');
	include_spip('inc/charsets');

	global $spip_lang_left, $spip_lang_right, $options;
	global $connect_statut, $options,$connect_id_auteur, $couleur_claire ;

	// Quel est le bon titre	
	if (lire_config("spip_thelia/produits_rubriques_spip_thelia", "non") != "oui") {
		$titre = _T('spipthelia:rubriques_associees_rubrique');
	} elseif (lire_config("spip_thelia/rubriques_rubriques_spip_thelia", "non") != "oui") {
			$titre = _T('spipthelia:produits_associes_rubrique');
		} else {
			$titre = _T('spipthelia:produits_et_rubriques_associes_rubrique');			
			}
			
	$out = "<div id='editer_produit-$id_rubrique'>";
	$out .= "<a name='produit'></a>";
	if (function_exists('bouton_block_depliable')) { // SPIP2.0
		if ($flag_editable) {
			if (_request('edit')||_request('neweven'))
				$bouton = bouton_block_depliable($titre,true,"produitsrubrique");
			else
				$bouton = bouton_block_depliable($titre,false,"produitsrubrique");
		}
		$out .= debut_cadre_enfonce("../"._DIR_PLUGIN_SPIP_THELIA."/img_pack/logo_thelia_petit.png", true, "", $bouton);

	} else {
		if (_request('edit')||_request('neweven'))
			$bouton = bouton_block_visible("produitsrubrique").$titre;
		else
			$bouton = bouton_block_invisible("produitsrubrique").$titre;		
		$out .= debut_cadre_enfonce("../"._DIR_PLUGIN_SPIP_THELIA."/img_pack/logo_thelia_petit.png", true, "", $bouton);

		//
		// Afficher les produits associes
		//
		$out .= afficher_rubriques_objet('rubrique',$id_rubrique);

		$out .= afficher_produits_objet('rubrique',$id_rubrique);
		
		$out .= debut_block_invisible('produitsrubrique');
	}

	
	
	if (_SPIP3)
		$link = generer_action_auteur('produits_rubrique',"$id_rubrique",generer_url_ecrire('rubrique&id_rubrique='.$id_rubrique,"",false,true));
	else
		$link = generer_action_auteur('produits_rubrique',"$id_rubrique",generer_url_ecrire('naviguer&id_rubrique='.$id_rubrique,"",false,true));
		
	$out .= "<form method='POST' action='$link'>\n";
	$out .= form_hidden($link);
	
	//masquer provisoirement les warning de session de Th�lia en attendant une correction
	//Th�lia retourne des warning de session (headers already sent) car elle d�marre trop tard, mais on ne l'utilise pas, on se contente de lister les produits
	$sav_error_reporting = error_reporting(E_ERROR);
	
	//on bloque la sortie vers le navigateur le temps d'y faire quelques substitutions	
	$res = recuperer_fond("fonds/produits_associes_rubrique",array("id_rubrique" => $id_rubrique));
	$res = str_replace("THELIA-", "#", $res);
	
	//avant d'envoyer � th�lia, on convertie en iso pour th�lia
	$res = unicode2charset(charset2unicode($res, 'utf-8'),'iso-8859-1');
	ob_start();
	chdir('..');
	include_once(_RACINE_THELIA."fonctions/moteur.php");
	chdir('ecrire');
	$texte = ob_get_contents();
	ob_end_clean();
	$texte = remplacement_sortie_thelia($texte);

	//au retour de th�lia, on convertit en utf8 pour SPIP
	if (!is_utf8($texte)) $texte = unicode2charset(charset2unicode($texte, 'iso-8859-1'),'utf-8');
	$out .= $texte;

	//remettre le niveau d'erreur pr�c�dent
	error_reporting($sav_error_reporting);
	
	$out .= "</form>\n";	

	$out .= fin_block();
	$out .= fin_cadre_enfonce(true);
	$out .= "</div><br/>";
	return $out;
}

function afficher_produits_objet($type, $id) {

	if (!preg_match(',^[a-z]*$,',$type)) return '';

	$result = determiner_produits_objet($type,$id);
	if (!spip_num_rows($result)) return '';

	$table = array();

	while ($row = spip_fetch_array($result)) {
		$vals = array();
		if (!is_utf8($row['titre'])) $row['titre'] = unicode2charset(charset2unicode($row['titre'], 'iso-8859-1'),'utf-8');
		
		$puce = ($row['ligne'])?find_in_path('images/puce-verte.gif'):find_in_path('images/puce-orange.gif');
		$etat = ($row['ligne'])?_T('spipthelia:produit_en_ligne'):_T('spipthelia:produit_non_publie');
		$url = generer_url_ecrire('spip_thelia_catalogue','thelia_url='.urlencode('produit_modifier.php?ref='.$row['ref'].'&rubrique='.$row['rubrique']));
		$link = "<a class='product_details' href='%s' target='_blank'>%s</a>";
				
		$vals[] = sprintf($link,$url,"<img src='$puce' alt='$etat'/>");
		$vals[] = sprintf($link,$url,$row['titre']);
		$vals[] = sprintf(number_format($row['prix'],2));
		
		$table[] = $vals;
	}

	$largeurs = array('14', '', '', '', '', '');
	$styles = array('arial11', 'arial2', 'arial11', 'arial11', 'arial11', 'arial1');

	$t = afficher_liste($largeurs, $table, $styles);
	if ($spip_display != 4)
		$t = $tranches
			. "<table width='100%' cellpadding='3' cellspacing='0' border='0'>"
			. "<thead><tr><th>&nbsp;</th><th>". _T('spipthelia:nom_du_produit'). "</th><th>". _T('spipthelia:prix'). "</th></tr></head><tbody>"
			. $t
			. "</tbody></table>";
	return "<div class='liste'>$t</div>\n";
}

function determiner_produits_objet($type, $id) {
	$les_produits = array();
	if (!preg_match(',^[a-z]*$,',$type)) return $les_produits;

	$result = spip_query("SELECT titre,ref,prix,ligne,rubrique 
		FROM spip_produits_{$type}s 
		JOIN produit ON produit.id = spip_produits_{$type}s.id_produit 
		JOIN produitdesc ON produitdesc.id = spip_produits_{$type}s.id_produit 
		WHERE id_{$type}="._q($id));

	return $result;
}

function afficher_rubriques_objet($type, $id) {

	if (!preg_match(',^[a-z]*$,',$type)) return '';

	$result = determiner_rubriques_objet($type,$id);
	if (!spip_num_rows($result)) return '';

	$table = array();

	while ($row = spip_fetch_array($result)) {
		$vals = array();
		if (!is_utf8($row['titre'])) $row['titre'] = unicode2charset(charset2unicode($row['titre'], 'iso-8859-1'),'utf-8');
		
		$puce = ($row['ligne'])?find_in_path('images/puce-verte.gif'):find_in_path('images/puce-orange.gif');
		$etat = ($row['ligne'])?_T('spipthelia:rubrique_en_ligne'):_T('spipthelia:rubrique_non_publiee');
		$url = generer_url_ecrire('spip_thelia_catalogue','thelia_url='.urlencode('parcourir.php?parent='.$row['rubrique']));
		$link = "<a class='product_details' href='%s' target='_blank'>%s</a>";
				
		$vals[] = sprintf($link,$url,"<img src='$puce' alt='$etat'/>");
		$vals[] = sprintf($link,$url,$row['titre']);
		
		$table[] = $vals;
	}

	$largeurs = array('14', '', '', '', '', '');
	$styles = array('arial11', 'arial2', 'arial11', 'arial11', 'arial11', 'arial1');

	$t = afficher_liste($largeurs, $table, $styles);
	if ($spip_display != 4)
		$t = $tranches
			. "<table width='100%' cellpadding='3' cellspacing='0' border='0'>"
			. "<thead><tr><th>&nbsp;</th><th>". _T('spipthelia:nom_de_la_rubrique'). "</th></tr></head><tbody>"
			. $t
			. "</tbody></table>";
	return "<div class='liste'>$t</div>\n";
}

function determiner_rubriques_objet($type, $id) {
	$les_produits = array();
	if (!preg_match(',^[a-z]*$,',$type)) return $les_produits;

	$result = spip_query("SELECT titre,ligne,rubrique 
		FROM spip_rubriquesthelia_{$type}s 
		JOIN rubrique ON rubrique.id = spip_rubriquesthelia_{$type}s.id_rubriquethelia
		JOIN rubriquedesc ON rubriquedesc.rubrique = spip_rubriquesthelia_{$type}s.id_rubriquethelia
		WHERE id_{$type}="._q($id));

	return $result;
}

?>
