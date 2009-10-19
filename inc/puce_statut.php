<?php

/**
*
 * Plugin « Puce active pour les articles syndiqués»
 * Licence GNU/GPL
 * 
  */

if (!defined("_ECRIRE_INC_VERSION")) return;

require _DIR_RESTREINT . 'inc/puce_statut.php';


// Surcharge de la fonction puce_statut_syndic_article_dist recopié en grande partie sur la fonction puce_statut_site_dist
function puce_statut_syndic_article($id, $statut, $id_rubrique, $type, $ajax='') {
	global $lang_objet;
	static $coord = array('publie' => 1,
			      'dispo' => 0,
			      'refuse' => 2,
				  'off' => 2);

	$lang_dir = lang_dir($lang_objet);
	$puces = array(
		       0 => 'puce-orange-breve.gif',
		       1 => 'puce-verte-breve.gif',
		       2 => 'puce-poubelle-breve.gif',
		       3 => 'puce-rouge-anim.gif');

	//	Permet de prendre en compte le statut 'off'	   des articles syndiqués
	if ($statut == 'off')
		$anim = 'rouge-anim';
	else
		$anim = 'poubelle-breve';
			   
	switch ($statut) {
		case 'dispo':
			$clip = 0;
			$puce = $puces[0];
			$title = _T('paas:texte_statut_synd_dispo');
			break;
		case 'publie':
			$clip = 1;
			$puce = $puces[1];
			$title = _T('paas:texte_statut_synd_publie');
			break;
		case 'refuse':			
			$clip = 2;
			$puce = 'puce-' . $anim .'.gif';
			$title = _T('paas:texte_statut_synd_poubelle');
			break;
		case 'off':
		default:		
			$clip = 3;
			$puce = 'puce-' . $anim .'.gif';
			$title = _T('paas:texte_statut_synd_off');
			break;
	}

	$type1 = "statut$type$id"; 
	$inser_puce = http_img_pack($puce, $title, "id='img$type1' class='puce' style='margin: 1px;'");
	
	//Recherche l'id_syndic pour empecher l'affichage dynamique de la puce si pas de droit sur modifier site
	$cond = "id_syndic_article=" . intval($id);
	$row = sql_fetsel("id_syndic", "spip_syndic_articles", $cond);	
	
	if (!autoriser('publierdans','rubrique',$id_rubrique) OR !autoriser('modifier','site',$row['id_syndic'])
	OR !_ACTIVER_PUCE_RAPIDE)
		return $inser_puce;

	$titles = array(
			  "orange" => _T('paas:texte_statut_synd_dispo'),
			  "verte" => _T('paas:texte_statut_synd_publie'),
			  "poubelle" => _T('paas:texte_statut_synd_poubelle'));
	// Alignement des puces		  
	$clip = 1+ (9*$coord[$statut]);

	if ($ajax){
		return 	"<span class='puce_site_fixe'>"
		. $inser_puce
		. "</span>"
		// Remplacement à la volée du style css overflow du div 'id=voir' dans exec/sites.php car cela cachait une partie des puces.
		. "<script type='text/javascript'>
		$('#voir').css('overflow','visible');
		</script>"
		. "<span class='puce_site_popup' id='statutdecal$type$id' style='margin-left: -$clip"."px;'>"
		// Voir les explications sur la fonction plus bas
		. afficher_script_statut_paas($id, $type, -1, $puces[0], 'dispo', $titles['orange'])
		. afficher_script_statut_paas($id, $type, -10, $puces[1], 'publie', $titles['verte'])
	  	. afficher_script_statut_paas($id, $type, -19, $puces[2], 'refuse', $titles['poubelle'])
		  . "</span>";
	}

	$nom = "puce_statut_";

	if ((! _SPIP_AJAX)) 
	  $over ='';
	else {
		// Envoi vers le fichier exec/paas_puce_statut.php
		// C'est mieux que de surcharger le fichier exec/puce_statut.php
	  $action = generer_url_ecrire('paas_puce_statut',"",true);
	  $action = "if (!this.puce_loaded) { this.puce_loaded = true; prepare_selec_statut('$nom', '$type', '$id', '$action'); }";
	  $over = "\nonmouseover=\"$action\"";
	}

	return 	"<span class='puce_site' id='$nom$type$id' dir='$lang_dir'$over>"
	. $inser_puce
	. '</span>';

}




// Modification de la fonction afficher_script_statut appelé ci-dessus
//Le changement de statut des articles syndiqués est effectué via le fichier action/instituer_syndic d'ou le remplacement de la variable $type qui est égale à syndic_article par 'syndic'
function afficher_script_statut_paas($id, $type, $n, $img, $statut, $titre, $act='') {
	$i = http_wrapper($img);
	$h = generer_action_auteur("instituer_syndic","$id-$statut"); // Modif ici 
	$h = "javascript:selec_statut('$id', '$type', $n, '$i', '$h');";
	$t = supprimer_tags($titre);
	return "<a href=\"$h\"\ntitle=\"$t\"$act><img src='$i' $t[3] alt=' '/></a>";	

}


?>