<?php

/*
 Dans votre plugin vous creez un fichier exec :
        
          -sur fast_plugin : exec/demo.php 
         - dans le repertoire fast_plugin/css/demo.css (mettez ici votre css)
         - dans le repertoire fast_plugin/js/demo.js (mettez ici votre js)
         - pour la page elle meme : fast_plugin/fonds/demo.html
        
Dans le fichier exec/demo.php Nous alolons avoir :

if (!defined("_ECRIRE_INC_VERSION")) return;
function exec_demo()
	{
		// ligne non obligatoire
		// mais plus propre si on souhaite utiliser 
		// correctement les pipelines
		pipeline('exec_init',array('args'=>array('exec'=>'demo'),'data'=>''));
		
		// appel sur le pipeline fast_plug
		// le type peut être simple(la page entière vient du html) ou
		// complet -> on appelle les différents pipelines de presentation
	 	pipeline('fast_plug',array('args'=>array('exec'=>'demo','type'=>'simple','template'=>'lnr'),'data'=>''));
 
}
*/



/* 
 * la fonction get_fast_plugin() retourne un tableau
 * avec la configuration des pages utilisant le plugin 
 * et donc le pipeline fast_plug.
 * 
 * Pour chacune des pages nous configurons certains parametres
 * "nom_de_la_page"=>array("plugin"=>"nom_plugin",
							"statut"=>"0minirezo",
							"allowed"=>"1,2"
							"bouton"=>"sous_menu,chemin_image,titre")
							
Concernant le statut 4 valeurs sont possibles :
	- tous 
	- admin
	- admin_restreint
	- aucun

Si le statut n'est pas renseigné on stop le traitement

Dans allowed on liste : les autorisations specifiques délivré sur l'id de l'auteur 
les valeurs sont separes par des virgules . Ces valeurs valeurs sont cumulatives avec
'statut'. Par exemple si le statut est admin_complet et 'allowed'=> '2,7' cette 'page'
sera accessible a tous les admin complet et auteurs ayant comme id 2 et 7. On peux 
noter  que l'on peux mettre le statut a 'aucun' et allowed => '6,24' alors cette 'page'
sera accessible aux auteurs ayant comme id 6 et 24. Ideal poru sécuriser une partie du site


 * 
 *  
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

function get_fast_plugin(){
	return array("source"=>array("plugin"=>"test",
								"statut"=>"tous",
								"bouton"=>"naviguer,chemin_image,template"),
				  "paul"=>array("plugin"=>"test",
								"statut"=>"admin",
								"allowed"=>"2",
								"bouton"=>"naviguer,chemin_image,Paul"),
					"totto"=>array("plugin"=>"test",
								"statut"=>"aucun",
								"allowed"=>"3,6",
								"bouton"=>"auteurs,chemin_image,la page totto"),
				"sylvain"=>array("plugin"=>"test",
								 "statut"=>"admin",
								 "bouton"=>"accueil,chemin_image,la page a sylvain"),
	
				);
}


/* On renseigne les headers avec les js et les css */
function fast_plugin_header_prive($flux){
	$page = get_fast_plugin();
	$exec = $_GET["exec"];
	
	if (autoriser('acces','fast_plugin',$exec)) {
		$plugin = $page[$exec]["plugin"];
		$flux .="<script type='text/javascript' src='../plugins/$plugin/js/$exec.js'></script>";
		$flux .="<link type='text/css' rel='stylesheet' href='../plugins/$plugin/css/$exec.css' />
		";
	}
	
	return $flux;
}

/* On met en place les boutons si nécessaires */

/* La liste des boutons
- $boutons_admin[’accueil’] => A suivre
- $boutons_admin[’naviguer’] => Edition
- $boutons_admin[’forum’] => Forum
- $boutons_admin[’auteurs’] => Auteurs
- $boutons_admin[’statistiques_visites’] => Statistiques
- $boutons_admin[’configuration’] => Configuration
- $boutons_admin[’aide_index’] => Aide
- $boutons_admin[’visiter’] => Visiter
 */


function fast_plugin_ajouter_boutons($boutons_admin){
	
	$page = get_fast_plugin();
	foreach ($page as $cle=>$valeur) {
		if (autoriser('acces','fast_plugin',$cle)) {
			if ($page[$cle]["bouton"]){
				$bouton = explode(",",$page[$cle]["bouton"]);
				list($menu, $img, $texte) = $bouton;
				$boutons_admin[$menu]->sousmenu[$cle] = new Bouton($img,$texte);
			}
		}
	}
	return $boutons_admin;
}




// donner la possibilite de passer des variables 'GET'
// mettre en place un systeme d'autorisation pour limiter 
// l'acces a certaines des pages (soit dans l'appel , soit 
// en passant par un outil de config  )

function fast_plugin_fast_plug($flux){
	

	// traitement des parametres envoye par le plugin 
	// $exec : exec = demo
	// $type : affichage complet(appel des pipelines classiques) ou simple
	// $template : on souhaite un autre affichage que l'admin de spip, on precise le template
	// $fond : doit on utiliser un fond particulier ?
	$exec = $flux["args"]["exec"];
	$type = $flux["args"]["type"];
	$template = $flux["args"]["template"];
	$fond = $flux["args"]["fond"];
	
	
	// chargemenr debut de page de spip
	include_spip('inc/presentation');
	include_spip('inc/utils');
	$commencer_page = charger_fonction('commencer_page', 'inc');
	
	
	// On verifie que la personne a bien acces a cette page
	$exec_get = $_GET["exec"];
	if (!autoriser('acces','fast_plugin',$exec_get)){
		echo $commencer_page();
		die("pas les droits");
	}
	
	if(!$fond) $fond = $exec;
	
	// on recupere la page 
	$page =recuperer_fond("fonds/$fond");
	
	// utilisation d'un template ou utilisation classique
	if ($template){
		
		// pour les templates on place dans la page de template 
		// le commentaire html <!-- content_here --> 
		// reste l'inclusion du css qui ne peut pour le 
		// momment ne peut être dans un fichier de plugin prevoir une autre
		// position , un repertoire css a la racine ?
		
		echo $commencer_page();
		$new_template =  recuperer_fond("fonds/template/$template/$template");
		$new_template =  str_replace("<!-- content_here -->",$page,$new_template);
		echo $new_template;
	}else{
		echo $commencer_page();
		if ($type=='complet'){
			echo debut_gauche('', true);
			echo pipeline('affiche_gauche',array('args'=>array('exec'=>$exec),'data'=>''));
			echo creer_colonne_droite('', true);
			echo pipeline('affiche_droite',array('args'=>array('exec'=>$exec),'data'=>''));
			echo debut_droite('', true);
			echo pipeline('affiche_milieu',array('args'=>array('exec'=>$exec),'data'=>''));
			echo $page;
			echo fin_gauche(), fin_page();
		}
		else if ($type=='simple'){
			echo $page;
			echo fin_page();
		}
	}
	
	return $flux;
}

// fonction gerant les autorisations pour le plugin 
// fast plugin
function autoriser_fast_plugin_acces_dist($faire, $type, $exec){
	
	// les infos sur l'auteur
	$statut_auteur = $GLOBALS['connect_statut'];
	$statut_type = $GLOBALS['connect_toutes_rubriques']; 
	$id = $GLOBALS['auteur_session']['id_auteur'];
	
	
	$pages = get_fast_plugin();
	$droit = $pages[$exec]["statut"];
	$allowed = $pages[$exec]["allowed"];
	if ($allowed) $allowed = explode(',',$allowed);
	
	// si allowed on test les droits de l'auteur
	if ($allowed){
		if(in_array($id,$allowed)) return true;
	}
	
	// si les droits ne sont pas renseigne 
	// on bloque le processus idem si droit='aucun' et allowed pas renseigne
	if (!$droit) return false;
	if ($droit=='aucun' && !$allowed) return false;
	
	
	// si les droits sont pour tous c'est bon
	if ($droit=='tous') return true;

	
	// les droits admin
	if ($droit=='admin' && $statut_auteur=="0minirezo" && $statut_type) return true;
	if ($droit=='admin_restreint' && $statut_auteur=="0minirezo" && $statut_type) return true;
	

	// les droits admin_restreint
	if ($droit=='admin_restreint' && $statut_auteur=="0minirezo") return true;
	
	 
	
	return false;
	
}



?>