<?php

/*
 * P.I.M Agenda
 * Gestion d'un agenda collaboratif
 *
 * Auteur :
 * Cedric Morin, Notre-ville.net
 * (c) 2005,2007 - Distribue sous licence GNU/GPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;	#securite

include_spip('inc/meta');
include_spip('inc/texte');
include_spip('inc/pim_agenda');

charger_generer_url();

/*******************************/
/* GESTION DU FORMULAIRE AGENDA */
/*******************************/

function balise_FORMULAIRE_AGENDA ($p) {
	$p = calculer_balise_dynamique($p,'FORMULAIRE_AGENDA', array('id_rubrique', 'id_agenda', 'id_article', 'id_breve', 'id_syndic', 'ajouter_mot', 'ajouter_groupe', 'afficher_texte'));
	return $p;
}

// verification des droits a faire du forum
function balise_FORMULAIRE_AGENDA_stat($args, $filtres) {

	// recuperer les donnees du forum auquel on repond, false = forum interdit
	list ($idr, $idagenda, $ida, $idb, $ids, $am, $ag, $af, $url) = $args;
	$idr = intval($idr);
	$idagenda = intval($idagenda);
	$ida = intval($ida);
	$idb = intval($idb);
	$ids = intval($ids);

	return
		array($idr, $idagenda, $ida, $idb, $ids, $am, $ag, $af, $url);
}

function balise_FORMULAIRE_AGENDA_dyn(
$id_rubrique, $id_agenda, $id_article, $id_breve, $id_syndic,
$ajouter_mot, $afficher_texte, $url_param_retour)
{
	// Tableau des valeurs servant au calcul d'une signature de securite.
	// Elles seront placees en Input Hidden pour que inc/forum_insert
	// recalcule la meme chose et verifie l'identité des resultats.
	// Donc ne pas changer la valeur de ce tableau entre le calcul de
	// la signature et la fabrication des Hidden
	// Faire attention aussi a 0 != ''

	// ne pas mettre '', sinon le squelette n'affichera rien.
	$previsu = ' ';
	if ($retour_agenda = rawurldecode(_request('retour')))
		$retour_agenda = str_replace('&var_mode=recalcul','',$retour_agenda);
	else {
		// par defaut, on veut prendre url_forum(), mais elle ne sera connue
		// qu'en sortie, on inscrit donc une valeur absurde ("!")
		$retour_agenda = parametre_url(self(),'neweven','');
		$retour_agenda = parametre_url($retour_agenda,'ndate','');
		$retour_agenda = parametre_url($retour_agenda,'var_mode','');
		$retour_agenda = parametre_url($retour_agenda,'id_organisateur','');
		$retour_agenda = parametre_url($retour_agenda,'id_invites','');
		$retour_agenda = str_replace("&amp;","&",$retour_agenda);
		$retour_agenda = parametre_url($retour_agenda,'id_agenda','');
	}
	$script = _request('script')?_request('script'):self();
	$script = parametre_url($script,'id_agenda','');
	$script = parametre_url($script,'neweven','');
	$script = parametre_url($script,'edit','');
	$script = parametre_url($script,'date','');
	$script = str_replace("&amp;","&",$script);
	
	// sauf si on a passe un parametre en argument (exemple : {#SELF})
	if ($url_param_retour) {
			$script = $url_param_retour;
	}
	
	// verifier les droits de modif (seul l'auteur peut modifier l'evenement)
	include_spip('inc/autoriser');
	$droits_modif = autoriser('modifier','pimagenda',$id_agenda);

	// au premier appel (pas de Post-var nommee "retour_forum")
	// memoriser evntuellement l'URL de retour pour y revenir apres
	// envoi du message ; aux appels suivants, reconduire la valeur.
	// Initialiser aussi l'auteur
	$evenement_action = false;
	$insert = _request('evenement_insert');
	$modif = _request('evenement_modif');
	if (!$insert && !$modif) {
		if (_request('neweven')){
			$droits_modif = true;
			$row = array();
			$row['type'] = 'reunion';
			$row['prive'] = 'non';
			$row['crayon'] = 'non';
			if ($ndate = _request('ndate')){
				$ndate = strtotime(urldecode($ndate));
			}
			else
				$ndate = time();
			$row['date_debut']	= date('Y-m-d H:i',$ndate);
			$row['date_fin']	= date('Y-m-d H:i',$ndate+3600);
			$row['titre'] = '';
			$row['descriptif']	= '';
			$row['lieu']	= '';
			$row['id_agenda_source']	= 0;
			$evenement_action = 'evenement_insert';
		}
		else
			$evenement_action = 'evenement_modif';
	} elseif ($droits_modif || ($insert && !$id_agenda)) { // appels ulterieurs
		// gestion des requetes de mises à jour dans la base
		$id_agenda = intval(_request('id_agenda'));
		$supp_evenement = _request('supp_evenement');
		$cancel = _request('cancel');
		if (($insert || $modif)&&(!$cancel)&&(!$supp_evenement)){
			if ( ($insert) && (!$id_agenda) ){
				$id_agenda = spip_abstract_insert("spip_pim_agenda",
					"(id_agenda_source,maj)",
					"(0,NOW())");
				if ($id_agenda==0){
					spip_log("agenda action formulaire agenda : impossible d'ajouter un evenement");
					return;
				}
		 	}
		 	modifier_agenda($id_agenda, $script);
			$evenement_action = 'evenement_modif';
		}
		else if ($supp_evenement){
			PIMAgenda_supprimer_agenda($id_agenda);
			$evenement_action = '';
		}
	}
	if ($evenement_action && $evenement_action!='evenement_insert')
		if (!$row = PIMAgenda_detailler_agenda($id_agenda))
			return false;

	// pour la chaine de hidden
	$script_hidden = $script = str_replace('&amp;', '&', $script);

	if ($evenement_action)
		return array('formulaires/formulaire_agenda_edit', 0,
		array(
			'retour_agenda' => $retour_agenda,
			'url' => $script, # ce sur quoi on fait le action='...'
			'url_post' => $script_hidden, # pour les variables hidden
			'type' =>	$row['type'],
			'prive' => $row['prive'],
			'crayon' => $row['crayon'],
			'date_debut' =>	$row['date_debut'],
			'date_fin' =>	$row['date_fin'],
			'titre' => $row['titre'],
			'descriptif' =>	$row['descriptif'],
			'lieu' =>	$row['lieu'],
			'id_auteur' => $GLOBALS['auteur_session']['id_auteur'],
			'id_agenda' => $id_agenda,
			'evenement_action' => $evenement_action,
			'modif_auth' => $droits_modif?1:0,
			));
	else
		return false;
}

function modifier_agenda($id_agenda, $script){
	spip_log("modification de l'agenda $id_agenda par ".$GLOBALS['auteur_session']['id_auteur'],'pimagenda');
	// memoriser les anciennes valeurs pour la notification
	$row_prev = PIMAgenda_detailler_agenda($id_agenda, true);

	// Recuperer le message a previsualiser
	$type = _request('type_eve');
	$prive = _request('prive');
	$crayon = _request('crayon');
	$id_organisateurs = _request('orga_A'); // on ne prend pas en compte les groupes organisateurs
	$id_invites = _request('invite_A');
	$groupes_invites = _request('invite_G');
	$lien_donnees = _request('lien_donnee');
	$titre = _request('evenement_titre');
	$descriptif = _request('evenement_descriptif');
	$lieu = _request('evenement_lieu');
	
	if (!strlen(trim($titre))) $titre= _T('pimagenda:evenement_sans_titre');
	
	// pour les cas ou l'utilisateur a saisi 29-30-31 un mois ou ca n'existait pas
	$maxiter=4;
	$st_date_deb=FALSE;
	$jour_debut=_request('jour_evenement_debut');
	// test <= car retour strtotime retourne -1 ou FALSE en cas d'echec suivant les versions
	while(($st_date_deb<=FALSE)&&($maxiter-->0)) {
		$date_deb=_request('annee_evenement_debut')."-"._request('mois_evenement_debut')."-".($jour_debut--)." "._request('heure_evenement_debut').":"._request('minute_evenement_debut');
		$st_date_deb=strtotime($date_deb);
	}
	$date_debut=format_mysql_date(date("Y",$st_date_deb),date("m",$st_date_deb),date("d",$st_date_deb),date("H",$st_date_deb),date("i",$st_date_deb), $s=0);

	// pour les cas ou l'utilisateur a saisi 29-30-31 un mois ou ca n'existait pas
	$maxiter=4;
	$st_date_fin=FALSE;
	$jour_fin=_request('jour_evenement_fin');
	// test <= car retour strtotime retourne -1 ou FALSE en cas d'echec suivant les versions
	while(($st_date_fin<=FALSE)&&($maxiter-->0)) {
		$st_date_fin=_request('annee_evenement_fin')."-"._request('mois_evenement_fin')."-".($jour_fin--)." "._request('heure_evenement_fin').":"._request('minute_evenement_fin');
		$st_date_fin=strtotime($st_date_fin);
	}
	$st_date_fin = max($st_date_deb,$st_date_fin);
	$date_fin=format_mysql_date(date("Y",$st_date_fin),date("m",$st_date_fin),date("d",$st_date_fin),date("H",$st_date_fin),date("i",$st_date_fin), $s=0);

	$row_anc = spip_fetch_array(spip_query("SELECT * FROM spip_pim_agenda WHERE id_agenda=$id_agenda"));
	// mettre a jour l'evenement
	$res=spip_query("UPDATE spip_pim_agenda SET type="._q($type).
	", titre="._q($titre).
	",descriptif="._q($descriptif)
	.",lieu="._q($lieu)
	.",date_debut="._q($date_deb)
	.",date_fin="._q($date_fin)
	.",prive="._q($prive)
	.",crayon="._q($crayon)
	.",idx='1' WHERE id_agenda="._q($id_agenda));

	// les mots cles : par groupes
	$query = "SELECT * FROM spip_groupes_mots WHERE pim_agenda='oui' ORDER BY titre";
	$res = spip_query($query);
	$liste_mots = array();
	while ($row = spip_fetch_array($res,SPIP_ASSOC)){
		$id_groupe = $row['id_groupe'];
		$id_mot_a = _request("evenement_groupe_mot_select_$id_groupe"); // un array
		if (is_array($id_mot_a) && count($id_mot_a)){
			if ($row['unseul']=='oui')
				$liste_mots[] = intval(reset($id_mot_a));
			else 
				foreach($id_mot_a as $id_mot)
					$liste_mots[] = intval($id_mot);
		}
	}
	// suppression des mots obsoletes
	$cond_in = "";
	if (count($liste_mots))
		$cond_in = "AND" . calcul_mysql_in('id_mot', implode(",",$liste_mots), 'NOT');
	spip_query("DELETE FROM spip_mots_pim_agenda WHERE id_agenda=$id_agenda $cond_in");
	// ajout/maj des nouveaux mots
	foreach($liste_mots as $id_mot){
		if (!spip_fetch_array(spip_query("SELECT * FROM spip_mots_pim_agenda WHERE id_agenda=$id_agenda AND id_mot=$id_mot")))
			spip_query("INSERT INTO spip_mots_pim_agenda (id_mot,id_agenda) VALUES ($id_mot,$id_agenda)");
	}
	
	// l'organisateur
	spip_query("DELETE FROM spip_pim_agenda_auteurs WHERE id_agenda="._q($id_agenda));
	$insert = "("._q($id_agenda)."," . join("),("._q($id_agenda).",",array_map('_q',$id_organisateurs)).")";
	spip_query("INSERT INTO spip_pim_agenda_auteurs (id_agenda,id_auteur) VALUES $insert");

	// les invites individuels
	$cond_in = "";
	if (count($id_invites))
		$cond_in = " AND" . calcul_mysql_in('id_auteur', implode(",",$id_invites), 'NOT');
	spip_query("DELETE FROM spip_pim_agenda_invites WHERE id_agenda="._q($id_agenda)."$cond_in");
	// ajout/maj des nouveaux invites
	if (is_array($id_invites))
		foreach($id_invites as $id_invite){
			if (!spip_fetch_array(spip_query("SELECT * FROM spip_pim_agenda_invites WHERE id_agenda="._q($id_agenda)." AND id_auteur="._q($id_invite))))
				spip_query("INSERT INTO spip_pim_agenda_invites (id_agenda,id_auteur) VALUES ("._q($id_agenda).","._q($id_invite).")");
		}

	// les groupes invites
	$cond_in = "";
	if (count($groupes_invites))
		$cond_in = " AND" . calcul_mysql_in('id_groupe', implode(",",$groupes_invites), 'NOT');
	spip_query("DELETE FROM spip_pim_agenda_groupes_invites WHERE id_agenda="._q($id_agenda)."$cond_in");
	// ajout/maj des nouveaux groupes invites
	if (is_array($groupes_invites))
		foreach($groupes_invites as $id_groupe){
			if (!spip_fetch_array(spip_query("SELECT * FROM spip_pim_agenda_groupes_invites WHERE id_agenda="._q($id_agenda)." AND id_groupe="._q($id_groupe))))
				spip_query("INSERT INTO spip_pim_agenda_groupes_invites (id_agenda,id_groupe) VALUES ("._q($id_agenda).","._q($id_groupe).")");
		}
	
	// les donnees liees
	$cond_in = "";
	if (count($lien_donnees))
		$cond_in = " AND" . calcul_mysql_in('id_donnee', implode(",",$lien_donnees), 'NOT');
	spip_query("DELETE FROM spip_forms_donnees_pim_agenda WHERE id_agenda="._q($id_agenda)."$cond_in");
	// ajout/maj des nouvelles donnees
	if (is_array($lien_donnees))
		foreach($lien_donnees as $id_donnee){
			if (!spip_fetch_array(spip_query("SELECT * FROM spip_forms_donnees_pim_agenda WHERE id_agenda="._q($id_agenda)." AND id_donnee="._q($id_donnee))))
				spip_query("INSERT INTO spip_forms_donnees_pim_agenda (id_agenda,id_donnee) VALUES ("._q($id_agenda).","._q($id_donnee).")");
		}

	$notifier_pim_agenda = charger_fonction('notifier_pim_agenda','inc');
	$notifier_pim_agenda('modifier',$id_agenda,$row_prev, $script);
}

?>