<?php
/*************************************************************************************/
/*                                                                                   */
/*      Thelia	                                                            		 */
/*                                                                                   */
/*      Copyright (c) Octolys Development		                                     */
/*		email : thelia@octolys.fr		        	                             	 */
/*      web : http://www.octolys.fr						   							 */
/*                                                                                   */
/*      This program is free software; you can redistribute it and/or modify         */
/*      it under the terms of the GNU General Public License as published by         */
/*      the Free Software Foundation; either version 2 of the License, or            */
/*      (at your option) any later version.                                          */
/*                                                                                   */
/*      This program is distributed in the hope that it will be useful,              */
/*      but WITHOUT ANY WARRANTY; without even the implied warranty of               */
/*      MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the                */
/*      GNU General Public License for more details.                                 */
/*                                                                                   */
/*      You should have received a copy of the GNU General Public License            */
/*      along with this program; if not, write to the Free Software                  */
/*      Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA    */
/*                                                                                   */
/*************************************************************************************/
?>
<?php
	
	/* Moteur */
	
	/* Le fichier html associŽ au php ( fond ) est parsŽ afin de subsituer les informations au bon endroit */

	include_once("fonctions/boucles.php");
	include_once(_DIR_PLUGINS."plugin-thelia/action-thelia-1_3_3.php");
	include_once("fonctions/substitutions.php");
	
	/* Moteur */
	
	/* Le fichier html associé au php ( fond ) est parsé afin de subsituer les informations au bon endroit */

	include_once("fonctions/filtres.php");
	
	include_once("fonctions/divers.php");
	include_once("classes/Client.class.php");
	include_once("classes/Commande.class.php");
	include_once("classes/Venteprod.class.php");
    include_once("classes/Message.class.php");
	include_once("classes/Messagedesc.class.php");
	include_once("classes/Transzone.class.php");
	include_once("classes/Variable.class.php");	
	include_once("classes/Promo.class.php");
	include_once("classes/Perso.class.php");
	include_once("classes/Smtp.class.php");
	include_once("classes/Cache.class.php");
	include_once("classes/PluginsClassiques.class.php");
	include_once("fonctions/parseur.php");
    
function analyse($res){
	global $formulaire, $formconnex;

	// substition simples
	$res = substitutions($res);	
	
	// laisser les infos pour les connectés ou non connectÈs
	$res = filtre_connecte($res);	
	

	// traitement dans le cas d'un formulaire
	if(isset($_GET['errform']) && $_GET['errform'] == "1") $res = traitement_formulaire($res);

	// effectue le nombre de passe nécessaire afin de traiter toutes les boucles et sous boucles

	$res = preg_replace("|<THELIA([^>]*)>\n|Us", "<THELIA\\1>", $res);
	
	while(strstr($res, "<THELIA")) {
		$boucles = pre($res);
		$res = boucle_simple($res, $boucles);
		$res = post($res);
	}

	// boucles avec sinon
	$res = str_replace("BTHELIA", "THELIA", $res);
	$res = boucle_sinon(explode("\n", $res));

	// boucles
	
	while(strstr($res, "<THELIA")) {
		$boucles = pre($res);
		$res = boucle_simple($res, $boucles);
		$res = post($res);
	}
	
	// on envoie le résultat
	
	return $res;

}

	// initialisation des variables du couple php/html
	if(!isset($lang)) $lang="";
	if(!isset($affilie)) $affilie="";
	if(!isset($parsephp)) $parsephp="";
	if(!isset($securise)) $securise=0;
	if(!isset($panier)) $panier=0;
	if(!isset($pageret)) $pageret=0;	
	if(!isset($reset)) $reset=0;
	if(!isset($transport)) $transport=0;
	if(!isset($obligetelfixe)) $obligetelfixe=0;
	if(!isset($obligetelport)) $obligetelport=0;
	if(!isset($pagesess)) $pagesess=0;
	if(!$lang)
		if(!isset($_REQUEST['lang'])) $lang=""; else $lang=$_REQUEST['lang'];
	if(!isset($_REQUEST['action'])) $action=""; else $action=$_REQUEST['action'];
	if(!isset($_REQUEST['append'])) $append=0; else $append=$_REQUEST['append'];
	if(!isset($_REQUEST['id'])) $id="";	else $id=$_REQUEST['id'];
	if(!isset($_REQUEST['id_parrain'])) $id_parrain=""; else $id_parrain=$_REQUEST['id_parrain'];	
	if(!isset($_REQUEST['nouveau'])) $nouveau=""; else $nouveau=$_REQUEST['nouveau'];	
	if(!isset($_REQUEST['ref'])) $ref=""; else $ref=$_REQUEST['ref'];	
	if(!isset($_REQUEST['quantite'])) $quantite=""; else $quantite=$_REQUEST['quantite'];	
	if(!isset($_REQUEST['article'])) $article=""; else $article=$_REQUEST['article'];	
	if(!isset($_REQUEST['type_paiement'])) $type_paiement=""; else $type_paiement=$_REQUEST['type_paiement'];	
	if(!isset($_REQUEST['code'])) $code=""; else $code=$_REQUEST['code'];	

	if(!isset($_REQUEST['entreprise'])) $entreprise=""; else $entreprise=$_REQUEST['entreprise'];	
	if(!isset($_REQUEST['siret'])) $siret=""; else $siret=$_REQUEST['siret'];
	if(!isset($_REQUEST['parrain'])) $parrain=""; else $parrain=$_REQUEST['parrain'];
	if(!isset($_REQUEST['motdepasse1'])) $motdepasse1=""; else $motdepasse1=$_REQUEST['motdepasse1'];	
	if(!isset($_REQUEST['motdepasse2'])) $motdepasse2=""; else $motdepasse2=$_REQUEST['motdepasse2'];
	if(!isset($_REQUEST['raison'])) $raison=""; else $raison=$_REQUEST['raison'];	
	if(!isset($_REQUEST['prenom'])) $prenom=""; else $prenom=$_REQUEST['prenom'];	
	if(!isset($_REQUEST['libelle'])) $libelle=""; else $libelle=$_REQUEST['libelle'];		
	if(!isset($_REQUEST['nom'])) $nom=""; else $nom=$_REQUEST['nom'];		
	if(!isset($_REQUEST['adresse1'])) $adresse1=""; else $adresse1=$_REQUEST['adresse1'];	
	if(!isset($_REQUEST['adresse2'])) $adresse2=""; else $adresse2=$_REQUEST['adresse2'];	
	if(!isset($_REQUEST['adresse3'])) $adresse3=""; else $adresse3=$_REQUEST['adresse3'];
	if(!isset($_REQUEST['cpostal'])) $cpostal=""; else $cpostal=$_REQUEST['cpostal'];
	if(!isset($_REQUEST['ville'])) $ville=""; else $ville=$_REQUEST['ville'];	
	if(!isset($_REQUEST['pays'])) $pays=""; else $pays=$_REQUEST['pays'];		
	if(!isset($_REQUEST['telfixe'])) $telfixe=""; else $telfixe=$_REQUEST['telfixe'];	
	if(!isset($_REQUEST['telport'])) $telport=""; else $telport=$_REQUEST['telport'];	
	if(!isset($_REQUEST['tel'])) $tel=""; else $tel=$_REQUEST['tel'];	
	if(!isset($_REQUEST['email1'])) $email1=""; else $email1=$_REQUEST['email1'];	
	if(!isset($_REQUEST['email2'])) $email2=""; else $email2=$_REQUEST['email2'];	
	if(!isset($_REQUEST['email'])) $email=""; else $email=$_REQUEST['email'];	
	if(!isset($_REQUEST['motdepasse'])) $motdepasse=""; else $motdepasse=$_REQUEST['motdepasse'];	
	if(!isset($_REQUEST['adresse'])) $adresse=""; else $adresse=$_REQUEST['adresse'];	
	if(!isset($_REQUEST['id_rubrique'])) $id_rubrique=""; else $id_rubrique=$_REQUEST['id_rubrique'];	
	if(!isset($_REQUEST['id_dossier'])) $id_dossier=""; else $id_dossier=$_REQUEST['id_dossier'];	
	if(!isset($_REQUEST['nouveaute'])) $nouveaute=""; else $nouveaute=$_REQUEST['nouveaute'];	
	if(!isset($_REQUEST['promo'])) $promo=""; else $promo=$_REQUEST['promo'];	
	if(!isset($_REQUEST['page'])) $page=""; else $page=$_REQUEST['page'];	
	if(!isset($_REQUEST['totbloc'])) $totbloc=""; else $totbloc=$_REQUEST['totbloc'];	
	if(!isset($_REQUEST['id_contenu'])) $id_contenu=""; else $id_contenu=$_REQUEST['id_contenu'];	
	if(!isset($_REQUEST['caracdisp'])) $caracdisp=""; else $caracdisp=$_REQUEST['caracdisp'];	
	if(!isset($_REQUEST['reforig'])) $reforig=""; else $reforig=$_REQUEST['reforig'];	
	if(!isset($_REQUEST['motcle'])) $motcle=""; else $motcle=$_REQUEST['motcle'];	
	if(!isset($_REQUEST['id_produit'])) $id_produit=""; else $id_produit=$_REQUEST['id_produit'];	
	if(!isset($_REQUEST['classement'])) $classement=""; else $classement=$_REQUEST['classement'];	
	if(!isset($_REQUEST['prixmin'])) $prixmin=""; else $prixmin=$_REQUEST['prixmin'];	
	if(!isset($_REQUEST['prixmax'])) $prixmax=""; else $prixmax=$_REQUEST['prixmax'];	
	if(!isset($_REQUEST['id_image'])) $id_image=""; else $id_image=$_REQUEST['id_image'];	
	if(!isset($_REQUEST['declinaison'])) $declinaison=""; else $declinaison=$_REQUEST['declinaison'];	
	if(!isset($_REQUEST['declidisp'])) $declidisp=""; else $declidisp=$_REQUEST['declidisp'];	
	if(!isset($_REQUEST['declival'])) $declival=""; else $declival=$_REQUEST['declival'];	
	if(!isset($_REQUEST['declistock'])) $declistock=""; else $declistock=$_REQUEST['declistock'];	
	if(!isset($_REQUEST['commande'])) $commande=""; else $commande=$_REQUEST['commande'];	
	if(!isset($_REQUEST['caracteristique'])) $caracteristique=""; else $caracteristique=$_REQUEST['caracteristique'];	
	if(!isset($_REQUEST['caracval'])) $caracval=""; else $caracval=$_REQUEST['caracval'];	
	
	
	
	
	
	// création de la session si non existante
	
	if(! isset($_SESSION["navig"])){
	 	$_SESSION["navig"] = new Navigation();
	 	$_SESSION["navig"]->lang="1";	
	 }	
	
	// URL précédente
	if(isset($_SERVER['HTTP_REFERER'])) $_SESSION["navig"]->urlprec = $_SERVER['HTTP_REFERER']; 
	
	// Page retour
	if($_SERVER['QUERY_STRING']) $qpt="?"; else $qpt="";
	
	if($pageret && isset($_SERVER['HTTP_REFERER'])) $_SESSION["navig"]->urlpageret =  $_SERVER['PHP_SELF'] . $qpt . $_SERVER['QUERY_STRING'];
	else if($_SESSION["navig"]->urlpageret=="") $_SESSION["navig"]->urlpageret = "index.php";

	// Langue
	if($lang) $_SESSION["navig"]->lang = $lang;
	else if(!$_SESSION["navig"]->lang) $_SESSION["navig"]->lang=1;
	
	// Affiliation
	if($affilie != "") $_SESSION["navig"]->affilie = $affilie;

	// fonctions à éxecuter avant le moteur
	modules_fonction("demarrage");
		
	// actions

	switch($action){
		case 'ajouter' : ajouter($ref, $quantite, $append, $nouveau); break;
		case 'supprimer' : supprimer($article); break;
		case 'modifier' : modifier($article, $quantite); break;
		case 'connexion' : connexion($email,$motdepasse); break;	
		case 'deconnexion' : deconnexion(); break;	
		case 'paiement' : paiement($type_paiement); break;	
		case 'transport' : transport($id); break;	
		case 'creercompte' : creercompte($raison, $entreprise, $siret, $prenom, $nom, $adresse1, $adresse2, $adresse3, $cpostal, $ville, $pays, $telfixe, $telport, $email1, $email2, $motdepasse1, $motdepasse2, $parrain); break;	
		case 'modifiercompte' : modifiercompte($raison, $entreprise, $siret, $prenom, $nom, $adresse1, $adresse2, $adresse3, $cpostal, $ville, $pays, $telfixe, $telport, $email1, $email2, $motdepasse1, $motdepasse2); break;	
		case 'creerlivraison' : creerlivraison($id, $libelle, $raison, $prenom, $nom, $adresse1, $adresse2, $adresse3, $cpostal, $ville, $tel, $pays); break;
        case 'supprimerlivraison' : supprimerlivraison($id);
		case 'modifierlivraison' : modifierlivraison($id, $libelle, $raison, $prenom, $nom, $adresse1, $adresse2, $adresse3, $cpostal, $ville, $tel, $pays); break;
		case 'modadresse' : modadresse($adresse); break;
		case 'codepromo' : codepromo($code); break;
		case 'chmdp' : chmdp($email); break;
	}


	// Sécurisation
	if($securise && ! $_SESSION["navig"]->connecte) { header("Location: spip.php?page=connexion"); exit; }

	// Vérif transport 
	if($transport && ! $_SESSION["navig"]->commande->transport) { header("Location: spip.php?page=commande"); exit; }
	
	// Vérif panier
	if($panier && ! $_SESSION["navig"]->panier->nbart) { header("Location: index.php"); exit; } 
	
	// fonctions à éxecuter avant le moteur
	modules_fonction("pre");
	
	// chargement du squelette	
	$res = str_replace("THELIA-", "#", $texte);
	
	
	// fonctions à éxecuter avant les inclusions
	modules_fonction("inclusion");
		
	// inclusion
	$res = inclusion(explode("\n", $res));
		
		
	// inclusions des plugins
	modules_fonction("action");
	
	// Résultat envoyé au navigateur

	$res =  analyse($res);

    $res = filtres($res);

	// inclusions des plugins filtres
	modules_fonction("post");

	if($parsephp == 1){
    	$res=str_replace('<'.'?php','<'.'?',$res);
    	$res='?'.'>'.trim($res).'<'.'?';
    	$res = eval($res);
	}
		
	echo $res; //iconv("ISO-8859-1", "UTF-8", $res);
	
	// Reset de la commande
	if($reset){
            $_SESSION["navig"]->commande = new Commande();
            $_SESSION["navig"]->panier = new Panier();	
	}


?>