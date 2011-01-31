<?php
/*
 * Plugin Abomailmanss
 * (c) 2009 SPIP
 * Distribue sous licence GPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('inc/abomailmans');
include_spip('inc/distant');

// 20 minutes de repit avant abomailmans (+0 a 10 minutes de cron)
#define('_DELAI_ABOMAILMANS_MESSAGERIE', 60 * 20);

// Les abomailmans de chaque liste peuvent se faire par cron
// base sur les champs remplis de chaque liste
// automatique tout les /periodicite/ jours

function genie_abomailmans_envois_dist($time) {
	
	// Les listes dont la date_envoi < maintenant+periodicite
	//pour tester on peut mettre � MINUTE penser � remettre � DAY !!
	//$where = "periodicite!='' AND desactive='0' AND email!=''
	//AND date_envoi < DATE_SUB(NOW(), INTERVAL periodicite DAY)"; 
	//$id_liste = sql_getfetsel("id_abomailman", "spip_abomailmans", $where, '', "date_envoi", "1");
	 
	//if ($id_liste) {
	//	spip_log("il faut traiter liste id=$id_liste","abomailmans");
	//	$res2 = liste_a_jour($id_liste);
	//} else $res2 = true;
	
# nul, si la t�che n�a rien � faire
# positif, si la t�che a �t� trait�e
# n�gatif, si la t�che a commenc�, mais doit se poursuivre. Cela permet d�effectuer des t�ches par lots (pour �viter des timeout sur les ex�cutions des scripts PHP � cause de traitements trop longs).
# Dans ce cas l�, le nombre n�gatif indiqu� correspond au nombre de secondes d�intervalle pour la prochaine ex�cution.

	//return ($res1 OR $res2) ? 0 : $id_liste;
}	 
	
	

function liste_a_jour($id_liste) {
	$t = sql_fetsel("*", "spip_abomailmans", "id_abomailman=$id_liste");
	if(!$t) { 
		spip_log("requete null ...","abomailmans");
		return;
	} else spip_log("envoi test� avec cron abomailmans","abomailmans");
		
	$datas = array();
	$nom_site = lire_meta("nom_site");
	$email_webmaster = lire_meta("email_webmaster");
	$charset = lire_meta('charset');


		$sujet=$t['titre']; 
		$date_envoi=$t['date_envoi']; 
		$email_receipt=$t['email'];
		$modele_defaut=$t['modele_defaut'];
		
		$recuptemplate = explode('&',$modele_defaut);
			
			include_spip('abomailmans_fonctions');
			$paramplus = recup_param($modele_defaut); //pour url
			$periodicite=intval($t['periodicite']);
		//la page � envoyer doit �tre test� � maintenant moins periodicite
			$time = time() - (3600 * 24 * $periodicite);
		//construction du query
		 	 	parse_str($paramplus,$query);
		 	 	$query['id_abomailman'] = $t['id_abomailman'];
				$query['template'] = $recuptemplate[0];
				$query['date'] = date('Y-m-d H:i:s', $time);
				 
		//on peut verifier le fond grace � l'url
		$url_genere = generer_url_public('abomailman_template',$query,'&'); 
		$fond = recuperer_fond('abomailman_template',$query);
	
	$body = array(
	'html'=>$fond,
	); 
	
	//Si la page renvoie un contenu
	if (strlen($fond) > 10) {
				
		// email denvoi depuis config facteur
		if ($GLOBALS['meta']['facteur_adresse_envoi'] == 'oui'
			  AND $GLOBALS['meta']['facteur_adresse_envoi_email'])
				$from_email = $GLOBALS['meta']['facteur_adresse_envoi_email'];
			else
				$from_email = $email_webmaster;
		// nom denvoi depuis config facteur
		if ($GLOBALS['meta']['facteur_adresse_envoi'] == 'oui'
			  AND $GLOBALS['meta']['facteur_adresse_envoi_nom'])
				$from_nom = $GLOBALS['meta']['facteur_adresse_envoi_nom'];
			else
				$from_nom = $nom_site;
				
		if (abomailman_mail($from_nom, $from_email, "", $email_receipt, $sujet,$body, true, $charset)) {
			spip_log("envoi ok = $url_genere tous les $periodicite jours sujet =".$sujet,"abomailmans");
			}
	}
	else spip_log("maintenant=".date('Y-m-d H:i:s', time())." date demande = ".$query['date']." non envoye =$url_genere : rien de neuf depuis $periodicite jours","abomailmans"); 
	
	// Noter que l'envoi est OK meme si envoi echoue faute de contenu, on reessaiera dans /periodicite/ jours
	sql_updateq("spip_abomailmans", array("date_envoi" => date('Y-m-d H:i:s', time())), "id_abomailman=".$t['id_abomailman']);
	
	return false; # c'est bon
}

?>