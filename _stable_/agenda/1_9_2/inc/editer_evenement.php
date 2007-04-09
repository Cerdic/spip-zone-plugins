<?php

function Agenda_action_update_repetitions($id_evenement,$repetitions,$liste_mots){
	// evenement source
	$res = spip_query("SELECT * FROM spip_evenements WHERE id_evenement="._q($id_evenement));
	if ($row = spip_fetch_array(($res))){
		$titre = $row['titre'];
		$descriptif = $row['descriptif'];
		$horaire = $row['horaire'];
		$lieu = $row['lieu'];
		$date_debut = strtotime($row['date_debut']);
		$date_fin = strtotime($row['date_fin']);
		$duree = $date_fin - $date_debut;
		$id_evenement_source = $row['id_evenement_source'];
		$id_article = $row['id_article'];
		if ($id_evenement_source!=0)
			return; // pas un evenement source donc rien a faire ici

		$repetitions_updated = array();
		// mettre a jour toutes les repetitions deja existantes ou les supprimer si plus lieu
		$res = spip_query("SELECT id_evenement FROM spip_evenements WHERE id_evenement_source="._q($id_evenement));
		while ($row = spip_fetch_array($res)){
			$date = strtotime(date('Y-m-d',$row['date_debut']));
			if (in_array($date,$repetitions)){
				// il est maintenu, on l'update
				$repetitions_updated[] = $date;
				$update_date_debut = date('Y-m-d',$date)." ".date('H:i:s',$date_debut);
				$update_date_fin = date('Y-m-d H:i:s',strtotime($update_date_debut)+$duree);

				// TODO : prendre en charge la mise a jour uniquement si conforme a l'original
				$update_titre = $titre;
				$update_descriptif = $descriptif;
				$update_lieu = $lieu;

				// mettre a jour l'evenement
				$res=spip_query("UPDATE spip_evenements SET `titre`="._q($update_titre)
				. ",`descriptif`="._q($update_descriptif)
				. ",`lieu`="._q($update_lieu)
				. ",`horaire`="._q($horaire)
				. ",`date_debut`="._q($update_date_debut)
				. ",`date_fin`="._q($update_date_fin)
				. ",`id_article`="._q($id_article)
				. " WHERE `id_evenement` ="._q($row['id_evenement']));
				Agenda_action_update_liste_mots($row['id_evenement'],$liste_mots);
			}
			else {
				// il est supprime
				spip_query("DELETE FROM spip_mots_evenements WHERE id_evenement=".$row['id_evenement']);
				spip_query("DELETE FROM spip_evenements WHERE id_evenement=".$row['id_evenement']);
			}

		}
		// regarder les repetitions a ajouter
		foreach($repetitions as $date){
			if (!in_array($date,$repetitions_updated)){
				$update_date_debut = date('Y-m-d',$date)." ".date('H:i:s',$date_debut);
				$update_date_fin = date('Y-m-d H:i:s',strtotime($update_date_debut)+$duree);
				$update_titre = $titre;
				$update_descriptif = $descriptif;
				$update_lieu = $lieu;

				$id_evenement_new = spip_abstract_insert("spip_evenements",
					"(id_evenement_source,maj)",
					"("._q($id_evenement).",NOW())");
				if ($id_evenement_new==0)
					spip_log("agenda action formulaire article : impossible d'ajouter un evenement repete");
				else {
					// mettre a jour l'evenement
					$res=spip_query("UPDATE spip_evenements SET `titre`="._q($update_titre)
					. ",`descriptif`="._q($update_descriptif)
					. ",`lieu`="._q($update_lieu)
					. ",`horaire`="._q($horaire)
					. ",`date_debut`="._q($update_date_debut)
					. ",`date_fin`="._q($update_date_fin)
					. ",`id_article`="._q($id_article)
					. " WHERE `id_evenement` ="._q($id_evenement_new));

					Agenda_action_update_liste_mots($id_evenement_new,$liste_mots);
				}
			}
		}
	}
}
function Agenda_action_supprime_repetitions($supp_evenement){
	$res = spip_query("SELECT * FROM spip_evenements WHERE id_evenement_source="._q($supp_evenement));
	while ($row = spip_fetch_array($res)){
		$id_evenement = $row['id_evenement'];
		spip_query("DELETE FROM spip_mots_evenements WHERE id_evenement="._q($id_evenement));
		spip_query("DELETE FROM spip_evenements WHERE id_evenement="._q($id_evenement));
	}
}

function Agenda_action_update_liste_mots($id_evenement,$liste_mots){
	// suppression des mots obsoletes
	$cond_in = "";
	if (count($liste_mots))
		$cond_in = "AND " . calcul_mysql_in('id_mot', implode(",",$liste_mots), 'NOT');
	spip_query("DELETE FROM spip_mots_evenements WHERE id_evenement="._q($id_evenement)." ".$cond_in);
	// ajout/maj des nouveaux mots
	foreach($liste_mots as $id_mot){
		if (!spip_fetch_array(spip_query("SELECT * FROM spip_mots_evenements WHERE id_evenement="._q($id_evenement)." AND id_mot="._q($id_mot))))
			spip_query("INSERT INTO spip_mots_evenements (id_mot,id_evenement) VALUES ("._q($id_mot).","._q($id_evenement).")");
	}
}


function Agenda_action_formulaire_article($id_article,$id_evenement, $c=NULL){
	include_spip('base/abstract_sql');
	// gestion des requetes de mises a jour dans la base
	$insert = _request('evenement_insert',$c);
	$modif = _request('evenement_modif',$c);
	if (($insert || $modif)){

		if ( ($insert) && (!$id_evenement) ){
			$id_evenement = spip_abstract_insert("spip_evenements",
				"(id_evenement_source,maj)",
				"('0',NOW())");
			if ($id_evenement==0){
				spip_log("agenda action formulaire article : impossible d'ajouter un evenement");
				return 0;
			}
	 	}
		if ($id_article){
			// mettre a jour le lien evenement-article
			spip_query("UPDATE spip_evenements SET id_article="._q($id_article)." WHERE id_evenement="._q($id_evenement));
	 	}
		$titre = _request('evenement_titre',$c);
		$descriptif = _request('evenement_descriptif',$c);
		$lieu = _request('evenement_lieu',$c);
		$horaire = _request('evenement_horaire',$c);
		if ($horaire!='oui') $horaire='non';

		// pour les cas ou l'utilisateur a saisi 29-30-31 un mois ou ca n'existait pas
		$maxiter=4;
		$st_date_deb=FALSE;
		$jour_debut=_request('jour_evenement_debut',$c);
		// test <= car retour strtotime retourne -1 ou FALSE en cas d'echec suivant les versions
		while(($st_date_deb<=FALSE)&&($maxiter-->0)) {
			$date_deb=_request('annee_evenement_debut',$c).'-'._request('mois_evenement_debut',$c).'-'.($jour_debut--)
				.' '._request('heure_evenement_debut',$c).':'._request('minute_evenement_debut',$c);
			$st_date_deb=strtotime($date_deb);
		}
		$date_deb=format_mysql_date(date("Y",$st_date_deb),date("m",$st_date_deb),date("d",$st_date_deb),date("H",$st_date_deb),date("i",$st_date_deb), $s=0);

		// pour les cas ou l'utilisateur a saisi 29-30-31 un mois ou ca n'existait pas
		$maxiter=4;
		$st_date_fin=FALSE;
		$jour_fin=_request('jour_evenement_fin',$c);
		// test <= car retour strtotime retourne -1 ou FALSE en cas d'echec suivant les versions
		while(($st_date_fin<=FALSE)&&($maxiter-->0)) {
			$st_date_fin=_request('annee_evenement_fin',$c).'-'._request('mois_evenement_fin',$c).'-'.($jour_fin--)
				.' '._request('heure_evenement_fin',$c).':'._request('minute_evenement_fin',$c);
			$st_date_fin=strtotime($st_date_fin);
		}
		$st_date_fin = max($st_date_deb,$st_date_fin);
		$date_fin=format_mysql_date(date("Y",$st_date_fin),date("m",$st_date_fin),date("d",$st_date_fin),date("H",$st_date_fin),date("i",$st_date_fin), $s=0);

		// mettre a jour l'evenement
		$res=spip_query("UPDATE spip_evenements SET `titre`="._q($titre)
		. ",`descriptif`="._q($descriptif)
		. ",`lieu`="._q($lieu)
		. ",`horaire`="._q($horaire)
		. ",`date_debut`="._q($date_deb)
		. ",`date_fin`="._q($date_fin)
		. " WHERE `id_evenement` ="._q($id_evenement));

		// les mots cles : par groupes
		$res = spip_query("SELECT * FROM spip_groupes_mots WHERE evenements='oui' ORDER BY titre");
		$liste_mots = array();
		while ($row = spip_fetch_array($res,SPIP_ASSOC)){
			$id_groupe = $row['id_groupe'];
			$id_mot_a = _request("evenement_groupe_mot_select_$id_groupe",$c); // un array
			if (is_array($id_mot_a) && count($id_mot_a)){
				if ($row['unseul']=='oui')
					$liste_mots[] = intval(reset($id_mot_a));
				else
					foreach($id_mot_a as $id_mot)
						$liste_mots[] = intval($id_mot);
			}
		}

		Agenda_action_update_liste_mots($id_evenement,$liste_mots);

		// gestion des repetitions
		if (($repetitions = _request('selected_date_repetitions',$c))!=NULL){
			$repetitions = explode(',',$repetitions);
			$rep = array();
			foreach($repetitions as $key=>$date){
				if (preg_match(",[0-9][0-9]?/[0-9][0-9]?/[0-9][0-9][0-9][0-9],",$date)){
					$date = explode('/',$date);
					$date = $date[2]."/".$date[0]."/".$date[1];
					$date = strtotime($date);
				}
				else {
					$date = preg_replace(",[0-2][0-9]:[0-6][0-9]:[0-6][0-9]\s*(UTC|GMT)(\+|\-)[0-9]{4},","",$date);
					$date = explode(' ',$date);
					$date = strtotime($date[2]." ".$date[1]." ".$date[3]);
				}
				if (!in_array($date,$repetitions))
					$rep[] = $date;
			}
			$repetitions = $rep;
		}
		else
			$repetitions = array();
		Agenda_action_update_repetitions($id_evenement, $repetitions, $liste_mots);
	}
	return $id_evenement;
}

function Agenda_action_supprime_evenement($id_article,$supp_evenement){
	$res = spip_query("SELECT * FROM spip_evenements WHERE id_article="._q($id_article)." AND id_evenement="._q($supp_evenement));
	if ($row = spip_fetch_array($res)){
		spip_query("DELETE FROM spip_mots_evenements WHERE id_evenement="._q($supp_evenement));
		spip_query("DELETE FROM spip_evenements WHERE id_evenement="._q($supp_evenement));
	}
	Agenda_action_supprime_repetitions($supp_evenement);
	$id_evenement = 0;
	return $id_evenement;
}

?>