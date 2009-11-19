<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

/* les 3 pipelines d'affichage passe par la meme fonction */
function fast_plugin_affiche_milieu($flux){
	$flux['data'] .= flux_data_fast_plugin('milieu');
	return $flux;
}

function fast_plugin_affiche_droite($flux){
	$flux['data'] .= flux_data_fast_plugin('droite');
	return $flux;
}

function fast_plugin_affiche_gauche($flux){
	$flux['data'] .= flux_data_fast_plugin('gauche');
	return $flux;
}

function flux_data_fast_plugin($colonne){
	include_spip('inc/cfg_config');
	$page = lire_config("metapack::affiche_colonne");
	$exec = _request('exec');
	if (count($page)==0) $page = array();
	foreach ($page as $cle=>$valeur) {
		if($valeur['colonne']==$colonne){
			$liste = explode(',',$valeur['page']);
			if ($valeur['page']== $exec 	OR $valeur['page']== '*' OR in_array($exec,$liste)){
				include_spip('inc/'.$valeur['inc']);
				$retour .= $valeur['fonction']();
			}
		}
	}
	return $retour;
}
/* fin de la partie concernant les pipelines d'affichage */

/* partie gerant la creation de page dans l'admin grace a fast_plugin */
/* On renseigne les headers avec les js et les css */
function fast_plugin_header_prive($flux){
	$page = get_fast_plugin();
	$exec = $_GET["exec"];
	
	if (autoriser('acces','fast_plugin',$exec)) {
		$plugin = $page[$exec]["plugin"];
		
		if (file_exists("../plugins/$plugin/js/$exec.js"))	$flux .="<script type='text/javascript' src='../plugins/$plugin/js/$exec.js'></script>";
		if (file_exists("../plugins/$plugin/css/$exec.css"))$flux .="<link type='text/css' rel='stylesheet' href='../plugins/$plugin/css/$exec.css' />";
		
		/* ajout des css / jss */
		if ($page[$exec]["addCss"]){
			$a = explode(",",$page[$exec]["addCss"]);
			for ($i = 0; $i < count($a); $i++) {
				$file = $a[$i];
				if (file_exists($file))$flux .="<link type='text/css' rel='stylesheet' href='$file' />";
			}
		}
		
	if ($page[$exec]["addJS"]){
			$a = explode(",",$page[$exec]["addJS"]);
			for ($i = 0; $i < count($a); $i++) {
				$file = $a[$i];
				if (file_exists($file)) $flux .="<script type='text/javascript' src='$file'></script>";;
			}
		}
		
	}
	
	return $flux;
}



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
	$exec_get = _request("exec");
	if (!autoriser('acces','fast_plugin',$exec_get)){
		echo $commencer_page();
		die("pas les droits");
	}
	
	if(!$fond) $fond = $exec;
	
	// on recupere la page 
	$page =recuperer_fond("fonds/$fond",array_merge( $_GET, $_POST ));
	
	// utilisation d'un template ou utilisation classique
	if ($template){
		
		// pour les templates on place dans la page de template 
		// le commentaire html <!-- content_here --> 
		// reste l'inclusion du css qui ne peut pour le 
		// momment ne peut être dans un fichier de plugin prevoir une autre
		// position , un repertoire css a la racine ?
		
		echo $commencer_page();
		pipeline('exec_init',array('args'=>array('exec'=>"$exec"),'data'=>''));
		$new_template =  recuperer_fond("fonds/template/$template/$template",array_merge( $_GET, $_POST ));
		$new_template =  str_replace("<!-- content_here -->",$page,$new_template);
		echo $new_template;
	}else{
		echo $commencer_page();
		pipeline('exec_init',array('args'=>array('exec'=>"$exec"),'data'=>''));
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
	
	// si les droits ne sont pas renseigne on bloque le processus 
	if (!$droit) return false;
	
	// si allowed on test les droits de l'auteur
	if ($allowed && in_array($id,$allowed))return true;
	
	// pour les admin restreint 
	if ($statut_type != 1 && $statut_auteur=="0minirezo") $statut_auteur = 'admin_restreint';

	$acces = array(	"tous" => 0,"admin_restreint" => 1,"admin" => 2 ,"aucun" => 4);
	$type_acces = array("1comite" => 0,"admin_restreint" => 1,"0minirezo" => 2 );
	
	
	if($type_acces[$statut_auteur] >= $acces[$droit]) return true;
	
	return false;
	
}


function get_fast_plugin(){
	include_spip('inc/cfg_config');
	$tab = lire_config("metapack::fast_plugin");
	if (count($tab)==0) return array();
	return $tab;

}




?>