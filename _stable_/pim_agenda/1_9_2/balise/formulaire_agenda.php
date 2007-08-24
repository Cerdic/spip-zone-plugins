<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2006                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;	#securite

include_spip('inc/meta');
include_spip('inc/session');
include_spip('inc/acces');
include_spip('inc/texte');
include_spip('inc/lang');
include_spip('inc/mail');
include_spip('inc/forum');
include_spip('base/abstract_sql');
spip_connect();

charger_generer_url();

/*******************************/
/* GESTION DU FORMULAIRE AGENDA */
/*******************************/

function balise_FORMULAIRE_AGENDA ($p) {
	include_spip('inc/pim_agenda_gestion');

	$p = calculer_balise_dynamique($p,'FORMULAIRE_AGENDA', array('id_rubrique', 'id_agenda', 'id_article', 'id_breve', 'id_syndic', 'ajouter_mot', 'ajouter_groupe', 'afficher_texte'));
	return $p;
}

// verification des droits a faire du forum
function balise_FORMULAIRE_AGENDA_stat($args, $filtres) {

	// Note : ceci n'est pas documente !!
	// $filtres[0] peut contenir l'url sur lequel faire tourner le formulaire
	// exemple dans un squelette article.html : [(#FORMULAIRE_FORUM|forum)]
	// ou encore [(#FORMULAIRE_FORUM|forumspip.php)]

	// le denier arg peut contenir l'url sur lequel faire le retour
	// exemple dans un squelette article.html : [(#FORMULAIRE_FORUM{#SELF})]

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
$ajouter_mot, $ajouter_groupe, $afficher_texte, $url_param_retour)
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
		$retour_agenda = parametre_url($retour_agenda,'id_agenda','');
		$retour_agenda = parametre_url($retour_agenda,'id_organisateur','');
		$retour_agenda = parametre_url($retour_agenda,'id_invites','');
		$retour_agenda = str_replace("&amp;","&",$retour_agenda);
	}
	$script = self();
	$script = parametre_url($script,'neweven','');
	$script = parametre_url($script,'edit','');
	$script = parametre_url($script,'date','');
	$script = str_replace("&amp;","&",$script);
	
	// sauf si on a passe un parametre en argument (exemple : {#SELF})
	if ($url_param_retour) {
			$script = $url_param_retour;
	}
	
	// verifier les droits de modif (seul l'auteur peut modifier l'evenement)
	$auteur = $GLOBALS['auteur_session']['nom'];
	$email_auteur = $GLOBALS['auteur_session']['email'];
	$id_auteur = $GLOBALS['auteur_session']['id_auteur'];
	$droits_modif = false;
	if (spip_fetch_array(spip_query("SELECT * FROM spip_pim_agenda_auteurs WHERE id_agenda=".spip_abstract_quote($id_agenda)." AND id_auteur=".spip_abstract_quote($id_auteur))))
		$droits_modif = true;
	//var_dump("SELECT * FROM spip_pim_agenda_auteurs WHERE id_agenda=".spip_abstract_quote($id_agenda)." AND id_auteur=".spip_abstract_quote($id_auteur));
	//var_dump($droits_modif);

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
			$type = 'reunion';
			$prive = 'non';
			$crayon = 'non';
			$id_article	= 0;
			if ($ndate = _request('ndate'))
				$ndate = strtotime(urldecode($ndate));
			else
				$ndate = time();
			$date_debut	= date('Y-m-d H:i',$ndate);
			$date_fin	= date('Y-m-d H:i',$ndate+3600);
			$titre = '';
			$descriptif	= '';
			$lieu	= '';
			$id_agenda_source	= 0;
			$evenement_action = 'evenement_insert';
		}
		else {
			$res = spip_query("SELECT * FROM spip_pim_agenda WHERE id_agenda=$id_agenda");
			if ($row = spip_fetch_array($res)){
				$type = $row['type'];
				$prive = $row['prive'];
				$crayon = $row['crayon'];
				$id_article	= $row['id_article'];
				$date_debut	= $row['date_debut'];
				$date_fin	= $row['date_fin'];
				$titre	= $row['titre'];
				$descriptif	= $row['descriptif'];
				$lieu	= $row['lieu'];
				$id_agenda_source	= $row['id_agenda_source'];
				$evenement_action = 'evenement_modif';
			}
		}

	} elseif ($droits_modif || ($insert && !$id_agenda)) { // appels ulterieurs
		// gestion des requetes de mises à jour dans la base
		$id_agenda = intval(_request('id_agenda'));
		$supp_evenement = _request('supp_evenement');
		$cancel = _request('cancel');
		if (($insert || $modif)&&(!$cancel)&&(!$supp_evenement)){
			/*$id_article = intval(_request('id_article'));
			if (!$id_article){
				$id_article = intval(_request('ajouter_id_article'));
			}*/
			if ( ($insert) && (!$id_agenda) ){
				$id_agenda = spip_abstract_insert("spip_pim_agenda",
					"(id_agenda_source,maj)",
					"('0',NOW())");
				if ($id_agenda==0){
					spip_log("agenda action formulaire agenda : impossible d'ajouter un evenement");
					return;
				}
		 	}
		 	/*if ($id_article){
				// mettre a jour le lien evenement-article
				spip_query("UPDATE spip_evenements SET id_article=$id_article WHERE id_evenement=$id_evenement");
		 	}*/
	
			// Recuperer le message a previsualiser
			$type = addslashes(_request('type_eve'));
			$prive = addslashes(_request('prive'));
			$crayon = addslashes(_request('crayon'));
			$id_organisateur = intval(_request('organisateur'));
			$id_invites = _request('invites');
			$titre = addslashes(_request('evenement_titre'));
			$descriptif = addslashes(_request('evenement_descriptif'));
			$lieu = addslashes(_request('evenement_lieu'));
			
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
			$query="UPDATE spip_pim_agenda SET `type`='$type', `titre`='$titre',`descriptif`='$descriptif',`lieu`='$lieu',`date_debut`='$date_deb',`date_fin`='$date_fin',`prive`='$prive',`crayon`='$crayon', `idx`='1' WHERE `id_agenda` = '$id_agenda';";
			$res=spip_query($query);
	
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
			spip_query("DELETE FROM spip_pim_agenda_auteurs WHERE id_agenda=$id_agenda");
			spip_query("INSERT INTO spip_pim_agenda_auteurs (id_agenda,id_auteur) VALUES ($id_agenda,$id_organisateur)");

			// les invites
			$cond_in = "";
			if (count($id_invites))
				$cond_in = "AND" . calcul_mysql_in('id_auteur', implode(",",$id_invites), 'NOT');
			spip_query("DELETE FROM spip_pim_agenda_invites WHERE id_agenda=$id_agenda $cond_in");
			// ajout/maj des nouveaux invites
			if (is_array($id_invites))
				foreach($id_invites as $id_invite){
					if (!spip_fetch_array(spip_query("SELECT * FROM spip_pim_agenda_invites WHERE id_agenda=$id_agenda AND id_auteur=$id_invite")))
						spip_query("INSERT INTO spip_pim_agenda_invites (id_agenda,id_auteur) VALUES ($id_agenda,$id_invite)");
				}
			$evenement_action = 'evenement_modif';

			
			// Envoi des messages d'invitation par messagerie interne et mail
			$envoi=false;
			$message_titre=_T('pimagenda:texte_agenda');
			$message_auteur=$id_organisateur;
			$message_date_heure=date("Y-m-d H:i:s");
			$redirect_url = parametre_url($script,'id_agenda',$id_agenda);
			$message_texte="Vous &ecirc;tes invit&eacute;s le <a href='$redirect_url'>".date("d-m-Y",$st_date_deb)." &agrave; ".date("H:i",$st_date_deb)."</a> (dur&eacute;e ".date("H:i",$st_date_fin-$st_date_deb).")";
			if ($modif){
				if ($st_date_deb!=($st_last=strtotime($row_anc['date_debut']))){
					$envoi=true;
					$message_texte="L'invitation du ".date("d-m-Y",$st_last)." &agrave; ".date("H:i",$st_last)." a &eacute;t&eacute; deplac&eacute;e le <a href='$redirect_url'>".date("d-m-Y",$st_date_deb)." &agrave; ".date("H:i",$st_date_deb)."</a> (dur&eacute;e ".date("H:i",$st_date_fin-$st_date_deb).")";
				}
				else if ($st_date_fin!=($st_last=strtotime($row_anc['date_fin']))){
					$envoi=true;
					$message_texte="La dur&eacute;e de l'invitation du <a href='$redirect_url'>".date("d-m-Y",$st_date_deb)." &agrave; ".date("H:i",$st_date_deb)."</a> a &eacute;t&eacute; modifi&eacute;e (nouvelle dur&eacute;e ".date("H:i",$st_date_fin-$st_date_deb).")";
				}
			}
			if ( ($modif && $envoi) || ($insert)){
				$id_message = spip_abstract_insert("spip_messages",
						"(titre,texte,type,date_heure,date_fin,rv,statut,id_auteur,maj)",
						"(".spip_abstract_quote($message_titre).",".spip_abstract_quote($message_texte).",'normal','$message_date_heure','$message_date_heure','non','publie',$message_auteur,NOW())");

				$head="From: agenda@".$_SERVER["HTTP_HOST"]."\n";
				$message_texte = supprimer_tags($message_texte) . "\n".url_absolue($redirect_url);
				include_spip('inc/charset');
				$trans_tbl = get_html_translation_table (HTML_ENTITIES);
				$trans_tbl = array_flip ($trans_tbl);
				// mettre le texte dans un charset acceptable
				$mess_iso = unicode2charset(charset2unicode($message_texte),'iso-8859-1');
				// regler les entites si il en reste
				$mess_iso = strtr($mess_iso, $trans_tbl);
				
				if ($id_message!=0 && is_array($id_invites) && count($id_invites)){
					foreach($id_invites as $value){
						$id_dest=spip_abstract_quote($value);
						spip_query("INSERT INTO spip_auteurs_messages (id_message, id_auteur, vu) VALUES ($id_message, $id_dest,'non');");
						if ($row=spip_fetch_array(spip_query("SELECT email FROM spip_auteurs WHERE id_auteur=$id_dest"))){
							if ($row['email']){
								mail($row['email'],$message_titre,$mess_iso,$head);
								#spip_log("mail: Dest:".$row['email']." $head Sujet:$message_titre $mess_iso");
							}
						}
					}
				}
			}


			
			
			// relecture de la base
			$res = spip_query("SELECT * FROM spip_pim_agenda WHERE id_agenda=$id_agenda");
			if ($row = spip_fetch_array($res)){
				$type = $row['type'];
				$prive = $row['prive'];
				$crayon = $row['crayon'];
				$id_article	= $row['id_article'];
				$date_debut	= $row['date_debut'];
				$date_fin	= $row['date_fin'];
				$titre	= $row['titre'];
				$descriptif	= $row['descriptif'];
				$lieu	= $row['lieu'];
				$id_agenda_source	= $row['id_agenda_source'];
			}
		}
		else if ($supp_evenement){
			/*$id_article = intval(_request('id_article'));
			if (!$id_article)
				$id_article = intval(_request('ajouter_id_article'));*/
			$res = spip_query("SELECT * FROM spip_pim_agenda WHERE id_agenda=$id_agenda");
			if ($row = spip_fetch_array($res)){
				spip_query("DELETE FROM spip_mots_pim_agenda WHERE id_agenda=$id_agenda");
				spip_query("DELETE FROM spip_pim_agenda WHERE id_agenda=$id_agenda");
			}
		}
		
	}

	// pour la chaine de hidden
	$script_hidden = $script = str_replace('&amp;', '&', $script);
	/*foreach ($ids as $id => $v)
		$script_hidden = parametre_url($script_hidden, $id, $v, '&');*/

	if ($evenement_action)
		return array('formulaires/formulaire_agenda_edit', 0,
		array(
			'auteur' => $auteur,
			'email_auteur' => $email_auteur,
			'retour_agenda' => $retour_agenda,
			'url' => $script, # ce sur quoi on fait le action='...'
			'url_post' => $script_hidden, # pour les variables hidden
			'url_site' => ($url_site ? $url_site : "http://"),
			'alea' => $alea,
			'hash' => $hash,
			'ajouter_groupe' => $ajouter_groupe,
			'ajouter_mot' => (is_array($ajouter_mot) ? $ajouter_mot : array($ajouter_mot)),
			'type' =>	$type,
			'prive' => $prive,
			'crayon' => $crayon,
			'id_article' =>	$id_article,
			'date_debut' =>	$date_debut,
			'date_fin' =>	$date_fin,
			'titre' => $titre,
			'descriptif' =>	$descriptif,
			'lieu' =>	$lieu,
			'id_auteur' => $id_auteur,
			'id_agenda' => $id_agenda,
			'evenement_action' => $evenement_action,
			'modif_auth' => $droits_modif?1:0,
			));
	else
		return false;
		
}


?>
