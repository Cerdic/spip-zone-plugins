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
include_spip('inc/agenda_filtres');

function PIMAgenda_date_debut_fin($annee,$mois,$jour,$type){
	if ($type=='jour'){
		$ts_start=strtotime("$annee-$mois-01 00:00:00");
		$ts_start+=($jour-1)*24*60*60;
		$ts_fin=$ts_start+24*60*60;
	} else
	if ($type=="semaine"){
		$ts_start=strtotime("$annee-$mois-01 01:00:00");
		$ts_start+=($jour-1)*24*60*60;
		while (date('w',$ts_start)!=1) $ts_start-=24*60*60;
		$ts_fin=$ts_start+7*24*60*60+60*60;
		$ts_start-=2*60*60;
	} else
	if ($type=='mois'){
		$ts_start=strtotime("$annee-$mois-01 00:00:00");
		if ($mois<'12')
			$ts_fin=strtotime("$annee-".($mois+1)."-01 00:00:00");
		else
			$ts_fin=strtotime(($annee+1)."-$mois-01 00:00:00");
	}
	return array($ts_start,$ts_fin);	
}
function PIMAgenda_ajoute_creneaux_horaires($urlbase,$ts_start,$ts_fin,$type,$partie_cal,$echelle){
	if ($echelle<=120)
		$freq_creneaux=60*60;
	else
		$freq_creneaux=60*60;

	$today=date('Y-m-d');
	// creneaux pour ajout uniquement si ajouter_id_article present
	if (($type!='mois')&&($partie_cal!='sansheure')&&$partie_cal)
	{
		$heuremin='08';$heuremax='20';
		if ($partie_cal=='matin'){
			$heuremin='04';$heuremax='15';
		}
		if ($partie_cal=='soir'){
			$heuremin='12';$heuremax='23';
		}
		for ($j=$ts_start;$j<=$ts_fin;$j+=$freq_creneaux){
			$heure=date('H',$j);
			if (($heure>=$heuremin)&&($heure<=$heuremax)){
				$url=parametre_url($urlbase,'ndate',urlencode(date('Y-m-d H:i',$j)));
				$creneau=date('Y-m-d H:i:s',$j);
				if (date('Y-m-d',$j)==$today)
					Agenda_memo_full($creneau,$creneau,preg_replace(",\s+,","&nbsp;",date('H:i',$j)." "._T('agenda:ajouter_un_evenement')), " ", "", $url,'calendrier-creneau-today');
				else if (date('w',$j)==0)
					Agenda_memo_full($creneau,$creneau,preg_replace(",\s+,","&nbsp;",date('H:i',$j)." "._T('agenda:ajouter_un_evenement')), " ", "",$url,'calendrier-creneau-sunday');
				else
					Agenda_memo_full($creneau,$creneau,preg_replace(",\s+,","&nbsp;",date('H:i',$j)." "._T('agenda:ajouter_un_evenement')), " ", "",$url,'calendrier-creneau');
			}
		}
	}
}

function PIMAgenda_memorise_evenement($id_agenda,$urlbase, $row, $categorie, $affiche_auteur = true){
	$is_evt=(in_array($row['type'],array('evenement','anniversaire','rappel')))
					||($row['date_debut']<$datestart && $row['date_fin']>$datefin);

	$url=parametre_url($urlbase,'id_agenda',$row['id_agenda']);
	$url=parametre_url($url,'ajouter_id_article',$row['id_article']);
	
	$titre = $row['titre'];
	$descriptif = $row['descriptif'];
	$lieu = $row['lieu'];
	$texte = "";
	
	if($affiche_auteur){
		$res2 = spip_query("SELECT spip_auteurs.nom FROM spip_auteurs LEFT JOIN spip_pim_agenda_auteurs ON spip_auteurs.id_auteur=spip_pim_agenda_auteurs.id_auteur WHERE spip_pim_agenda_auteurs.id_agenda='".$row['id_agenda']."'");
		if ($row2 = spip_fetch_array($res2))
		  $texte=$row2['nom'].":<br/>";
		else
			$texte="????:<br/>";
	}
	
	$texte .= wordwrap(entites_html($row['titre'],ENT_QUOTES),15,"<br />\n");
	if (($type!='mois')&&(!$is_evt))
		$texte.="<hr />" . wordwrap(entites_html($row['descriptif'],ENT_QUOTES),15, "<br />\n");
	if (strlen($texte)==0) $texte=_L("(sans objet)");

	if (isset($categorie[$row['type']]))
		$categorie = $categorie[$row['type']];
	else 
		$categorie = reset($categorie);

	if ($id_agenda==$row['id_agenda'])
		$categorie.='-selection';

	if (!$is_evt)
		Agenda_memo_full($row['date_debut'], $row['date_fin'], $titre, $descriptif, $lieu, $url, $categorie);
	else
		Agenda_memo_evt_full($row['date_debut'], $row['date_fin'], $titre, $descriptif, $lieu, $url, $categorie);
}

function PIMAgenda_affiche_evenements($texte){
	$flag_editable = true;
	global $visu_evenements;
	global $auteur_session;
	$type = _request('type');
	$partie_cal = _request('partie_cal');
	if (!$type) $type='semaine';
	$id_agenda = intval(_request('id_agenda'));
	$ajouter_id_article = intval(_request('ajouter_id_article'));
	global $annee,$mois,$jour;
	$annee = intval(_request('annee'));
	$mois = intval(_request('mois'));
	$jour = intval(_request('jour'));

	//if ($flag_editable)
	//	Agenda_action_formulaire_article();

	$visu_evenements=array();

	if ((!$annee)||(!$mois)||(!$jour)){
		if (!$id_agenda){ // pas d'id_evenement--> date du jour
			$stamp=time();
		}
		else { // date de l'evenement
			$query = "SELECT date_debut FROM spip_pim_agenda WHERE id_agenda=$id_agenda";
			$res = spip_query($query);
			if ($row = spip_fetch_array($res))
				$stamp=strtotime($row['date_debut']);
			else 
				$stamp=time();
		}
		$annee=date('Y',$stamp);
		$mois=date('m',$stamp);
		$jour=date('d',$stamp);
 	}

 	$urlbase=self();
 	$urlbase=parametre_url($urlbase,'edit','');
 	$urlbase=parametre_url($urlbase,'del','');
 	$urlbase=parametre_url($urlbase,'ndate','');
 	$urlbase=parametre_url($urlbase,'id_agenda','');
 	$urlbase=parametre_url($urlbase,'neweven','1');
 	
	// creation des boites creneaux horaires pour ajout rapide
	list($ts_start,$ts_fin) = PIMAgenda_date_debut_fin($annee,$mois,$jour,$type);
	if ($flag_editable)
		PIMAgenda_ajoute_creneaux_horaires($urlbase,$ts_start,$ts_fin,$type,$partie_cal,_request('echelle'));


	$categorie_concerne=array('reunion'=>'calendrier-reunions','rendez-vous'=>'calendrier-rdv','evenement'=>'calendrier-evenements','anniversaire'=>'calendrier-anniversaire','rappel'=>'calendrier-rappel');
	$categorie_info=array('reunion'=>'calendrier-info','rendez-vous'=>'calendrier-info','evenement'=>'calendrier-info','anniversaire'=>'calendrier-info','rappel'=>'calendrier-info');

	$datestart=date('Y-m-d H:i:s',$ts_start-24*60*60);
	$datefin=date('Y-m-d H:i:s',$ts_fin+24*60*60);

 	$urlbase=parametre_url($urlbase,'neweven','');
	$urlbase=parametre_url($urlbase,'annee',$annee);
	$urlbase=parametre_url($urlbase,'mois',$mois);
	$urlbase=parametre_url($urlbase,'jour',$jour);
	
	$id_auteur = 0;
	if (is_array($auteur_session))
		$id_auteur = $auteur_session['id_auteur'];
	if($id_auteur){
		// tous les evenements organises par le visiteur logge
		$res = spip_query("SELECT * 
								FROM spip_pim_agenda AS agenda
					 LEFT JOIN spip_pim_agenda_auteurs ON agenda.id_agenda=spip_pim_agenda_auteurs.id_agenda 
							 WHERE spip_pim_agenda_auteurs.id_auteur=$id_auteur
							 	 AND ((agenda.date_debut>='$datestart' AND agenda.date_debut<='$datefin') 
							 				OR (agenda.date_fin>='$datestart' AND agenda.date_fin<='$datefin')
							 				OR (agenda.date_debut<'$datestart' AND agenda.date_fin>'$datefin'))
							 ORDER BY agenda.date_debut;");
		while ($row = spip_fetch_array($res)){
			PIMAgenda_memorise_evenement($id_agenda,$urlbase, $row, $categorie_concerne, false);
			$visu_evenements[$row['id_agenda']]=1;
		}
	
		// tous les evenements auxquels le visiteur logge est invite
		$res = spip_query("SELECT * 
								FROM spip_pim_agenda AS agenda
					 LEFT JOIN spip_pim_agenda_invites ON agenda.id_agenda=spip_pim_agenda_invites.id_agenda 
							 WHERE spip_pim_agenda_invites.id_auteur=$id_auteur
							 	 AND ((agenda.date_debut>='$datestart' AND agenda.date_debut<='$datefin')
							 				OR (agenda.date_fin>='$datestart' AND agenda.date_fin<='$datefin')
							 				OR (agenda.date_debut<'$datestart' AND agenda.date_fin>'$datefin'))
							 ORDER BY agenda.date_debut;");
		while ($row = spip_fetch_array($res)){
			if (!isset($visu_evenements[$row['id_agenda']])){
				PIMAgenda_memorise_evenement($id_agenda,$urlbase, $row, $categorie_concerne);
				$visu_evenements[$row['id_agenda']]=1;
			}
		}
	
		// TBD : tous les evenements publies pour le visiteur logge
		// en attendant : tous les evenements restants, non prives
		$res = spip_query("SELECT * 
								FROM spip_pim_agenda AS agenda
					 LEFT JOIN spip_pim_agenda_auteurs AS auteur ON agenda.id_agenda=auteur.id_agenda 
							 WHERE auteur.id_auteur!=$id_auteur
							 	 AND ((agenda.date_debut>='$datestart' AND agenda.date_debut<='$datefin')
							 				OR (agenda.date_fin>='$datestart' AND agenda.date_fin<='$datefin')
							 				OR (agenda.date_debut<'$datestart' AND agenda.date_fin>'$datefin'))
							 	 AND agenda.prive!='oui'
							 ORDER BY agenda.date_debut;");
		while ($row = spip_fetch_array($res)){
			if (!isset($visu_evenements[$row['id_agenda']])){
				PIMAgenda_memorise_evenement($id_agenda,$urlbase, $row, $categorie_info);
				$visu_evenements[$row['id_agenda']]=1;
			}
		}
	}
	
	global $spip_ecran;
	$spip_ecran = 'etroit';
	$s = "<span class='agenda-calendrier'>\n";
	// attention : bug car $type est modifie apres cet appel !
	$s .= Agenda_affiche_full(1,'', $type, 'calendrier-creneau','calendrier-creneau-today','calendrier-creneau-sunday','calendrier-reunions','calendrier-rdv','calendrier-evenements-selection','calendrier-evenements','calendrier-anniversaire-selection','calendrier-anniversaire','calendrier-rappel-selection','calendrier-rappel','calendrier-info-selection','calendrier-info','calendrier-reunions-selection','calendrier-rdv-selection');
	$s .= "</span>";

	return $s;
}

function balise_AGENDA_VISU ($p) {return calculer_balise_dynamique($p,'AGENDA_VISU', array());
}

// filtres[0] = url destination apres logout [(#URL_LOGOUT|url)]
function balise_AGENDA_VISU_stat ($args, $filtres) {
	return array($filtres[0]);
}

function balise_AGENDA_VISU_dyn($cible) {

	return PIMAgenda_affiche_evenements('');
}
?>
