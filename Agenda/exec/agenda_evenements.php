<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');
include_spip('inc/agenda_filtres');
include_spip('inc/agenda_gestion');

function date_debut_fin($annee,$mois,$jour,$type){
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
function ajoute_creneaux_horaires($urlbase,$ts_start,$ts_fin,$type,$partie_cal,$echelle){
	if ($echelle<=120)
		$freq_creneaux=30*60;
	else
		$freq_creneaux=60*60;

	$today=date('Y-m-d');
	// creneaux pour ajout uniquement si ajouter_id_article present
	if (($type!='mois')&&($partie_cal!='sansheure'))
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

function affiche_evenements_agenda($flag_editable){
	global $visu_evenements;
	$type = _request('type');
	$partie_cal = _request('partie_cal');
	if (!$type) $type='semaine';
	$id_evenement = intval(_request('id_evenement'));
	$ajouter_id_article = intval(_request('ajouter_id_article'));
	global $annee,$mois,$jour;
	$annee = intval(_request('annee'));
	$mois = intval(_request('mois'));
	$jour = intval(_request('jour'));

	if ($flag_editable)
		Agenda_action_formulaire_article();

	$visu_evenements=array();

	if ((!$annee)||(!$mois)||(!$jour)){
		if (!$id_evenement){ // pas d'id_evenement--> date du jour
			$stamp=time();
		}
		else { // date de l'evenement
			$query = "SELECT date_debut FROM spip_evenements WHERE id_evenement=$id_evenement";
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
 	$urlbase=parametre_url($urlbase,'id_evenement','');
 	$urlbase=parametre_url($urlbase,'neweven','1');
 	
	//$urlbase=str_replace("&amp;","&",$urlbase);

	// creation des boites creneaux horaires pour ajout rapide
	list($ts_start,$ts_fin) = date_debut_fin($annee,$mois,$jour,$type);
	if ($flag_editable)
		ajoute_creneaux_horaires($urlbase,$ts_start,$ts_fin,$type,$partie_cal,$echelle);


	$categorie_concerne=array('plage'=>'calendrier-plage','evenement'=>'calendrier-evenement');
	$categorie_info=array('plage'=>'calendrier-plage-info','evenement'=>'calendrier-evenement-info');

	$datestart=date('Y-m-d H:i:s',$ts_start-24*60*60);
	$datefin=date('Y-m-d H:i:s',$ts_fin+24*60*60);

	// tous les evenements
	$query = "SELECT * 
							FROM spip_evenements AS evenements
				 LEFT JOIN spip_evenements_articles AS J ON evenements.id_evenement=J.id_evenement
						 WHERE ((evenements.date_debut>='$datestart' AND evenements.date_debut<='$datefin') 
						 		OR (evenements.date_fin>='$datestart' AND evenements.date_fin<='$datefin')
						 		OR (evenements.date_debut<'$datestart' AND evenements.date_fin>'$datefin'))
						 ORDER BY evenements.date_debut;";
	$res = spip_query($query);
 	$urlbase=parametre_url($urlbase,'neweven','');
	$urlbase=parametre_url($urlbase,'annee',$annee);
	$urlbase=parametre_url($urlbase,'mois',$mois);
	$urlbase=parametre_url($urlbase,'jour',$jour);
	while ($row = spip_fetch_array($res)){
		$is_evt=($row['horaire']!='oui')
						||($row['date_debut']<$datestart && $row['date_fin']>$datefin);
		$concerne=(!$ajouter_id_article) || ($ajouter_id_article==$row['id_article']);

		$url=parametre_url($urlbase,'id_evenement',$row['id_evenement']);
		$url=parametre_url($url,'ajouter_id_article',$row['id_article']);
		
		$titre = $row['titre'];
		$descriptif = $row['descriptif'];
		$lieu = $row['lieu'];
		
		$texte=wordwrap(entites_html($row['titre'],ENT_QUOTES),15,"<br />\n");
		if (($type!='mois')&&(!$is_evt))
			$texte.="<hr />" . wordwrap(entites_html($row['descriptif'],ENT_QUOTES),15, "<br />\n");
		if (strlen($texte)==0) $texte=_L("(sans objet)");

		if ($concerne)	$categorie = $categorie_concerne;
		else						$categorie = $categorie_info;
		if ($is_evt) 		$categorie = $categorie['evenement'];
		else 						$categorie = $categorie['plage'];
		if ($id_evenement==$row['id_evenement'])
			$categorie.='-selection';

		if (!$is_evt)
			Agenda_memo_full($row['date_debut'], $row['date_fin'], $titre, $descriptif, $lieu, $url, $categorie);
		else{
			//if ($type!='mois')
			//	Agenda_memo_evt_full($row['date_debut'], $row['date_debut'], Agenda_rendu_boite($titre,$descriptif,$lieu), "", "", $url, $categorie);
			//else
				Agenda_memo_evt_full($row['date_debut'], $row['date_fin'], $titre, $descriptif, $lieu, $url, $categorie);
		}
		$visu_evenements[$row['id_evenement']]=1;
	}

	$s = "<span class='agenda-calendrier'>\n";
	// attention : bug car $type est modifie apres cet appel !
	$s .= Agenda_affiche_full(1,'', $type, 'calendrier-creneau','calendrier-creneau-today','calendrier-creneau-sunday','calendrier-plage','calendrier-evenement','calendrier-plage-info','calendrier-evenement-info','calendrier-plage-selection','calendrier-evenement-selection');
	$s .= "</span>";

	return $s;
}


function visu_evenement_agenda($id_evenement,$flag_editable){
	$out = "";
	$ndate = _request('ndate');
	$del = _request('del');

	if ($id_evenement!=NULL){
		$query = "SELECT spip_evenements.* FROM spip_evenements WHERE spip_evenements.id_evenement='$id_evenement';";
		$res = spip_query($query);
		if ($row = spip_fetch_array($res)){
			if (!isset($neweven)){
				$fid_evenement=$row['id_evenement'];
				$ftitre=attribut_html($row['titre']);
				$flieu=attribut_html($row['lieu']);
				$fhoraire=attribut_html($row['horaire']);
				$fdescriptif=attribut_html($row['descriptif']);
				$fstdatedeb=strtotime($row['date_debut']);
				$fstdatefin=strtotime($row['date_fin']);
			}
	 	}
		$out .= "<div class='agenda-visu-evenement'>";
		$query = "SELECT * FROM spip_articles AS articles LEFT JOIN spip_evenements_articles AS J ON J.id_article=articles.id_article WHERE J.id_evenement=$id_evenement";
		$res2 = spip_query($query);
		if ($row2 = spip_fetch_array($res2)){
			$out .= "<div class='article-evenement'>";
			$out .= "<a href='".generer_url_ecrire('articles',"id_article=".$row2['id_article'])."'>";
			$out .= http_img_pack("article-24.gif", "", "width='24' height='24' border='0'");
			$out .= entites_html($row2['titre'])."</a>";
			$out .= "</div>\n";
		}
		
		$out .= "<div class='agenda-visu-evenement-bouton-fermer'>";
		$url=self();
		$url=parametre_url($url,'edit','');
		$url=parametre_url($url,'neweven','');
		$url=parametre_url($url,'del','');
		$url=parametre_url($url,'id_evenement','');

		$out .= "<a href='$url'><img src='"._DIR_PLUGIN_AGENDA_EVENEMENTS."/img_pack/croix.png' width='12' height='12' style='border:none;'></a>";
		$out .= "</div>\n";

		$fobjet = entites_html($fobjet,ENT_QUOTES);
		$flieu = entites_html($flieu,ENT_QUOTES);
		$fdescription = entites_html($fdescription,ENT_QUOTES);

		$out .= "<div class='titre-titre'>Titre</div><div class='titre-visu'>$ftitre &nbsp;</div>\n";
		$out .= "<div class='lieu-titre'>Lieu</div><div class='lieu-visu'>$flieu &nbsp;</div>\n";
		$out .= "<div class='horaire-titre'>&nbsp;</div>";
		$out .= "<div class='date-titre'>Date </div>";
		$out .= "<div class='date-visu'>";
		$out .= "Du ".affdate_jourcourt(date("Y-m-d H:i",$fstdatedeb));
		if ($fhoraire=='oui')
			$out .= " &agrave; ".date("H:i",$fstdatedeb);
		$out .= " <br/>\n";
		$out .= "Au ".affdate_jourcourt(date("Y-m-d H:i",$fstdatefin));
		if ($fhoraire=='oui')
			$out .= " &agrave; ".date("H:i",$fstdatefin);
		$out .= " <br/>\n";
		$out .= "</div>\n";
		$out .= "<div class='descriptif-titre'>Description</div><div class='descriptif-visu'>$fdescriptif &nbsp;</div>\n";

		$out .=  "<div class='agenda_mots_cles'>";
		$query = "SELECT * FROM spip_groupes_mots WHERE evenements='oui' ORDER BY titre";
		$res = spip_query($query);
		$sep = "";
		while ($row = spip_fetch_array($res,SPIP_ASSOC)){
			$id_groupe = $row['id_groupe'];
			$query = "SELECT mots.titre FROM spip_mots_evenements AS mots_evenements
								LEFT JOIN spip_mots AS mots ON mots.id_mot=mots_evenements.id_mot 
								WHERE mots.id_groupe=$id_groupe AND mots_evenements.id_evenement=$id_evenement";
			$row2 = spip_fetch_array(spip_query($query));
			if ($row2){
				$out .= $sep . supprimer_numero($row['titre'])."&nbsp;:&nbsp;".supprimer_numero($row2['titre']);
				$sep = "\n, ";
			}
		}
		$out .= "</div>\n";

		if ($flag_editable){
			$url=self();
			$url=parametre_url($url,'edit','');
			$url=parametre_url($url,'neweven','');
			$url=parametre_url($url,'del','');
			$url=parametre_url($url,'id_evenement','');
			if ($del==1)	{ //---------------Suppression RDV ------------------------------
			  $out .= "<form name='edition_rdv' action='$url' method='post'>";
			  //$out .= "<input type='hidden' name='redirect' value='$url' />\n";
			  $out .= "<input type='hidden' name='id_evenement' value='$fid_evenement' />\n";
			  $out .= "<input type='hidden' name='suppr' value='1' />\n";
			  $out .= "<div class='edition-bouton'>";
			  $out .= "<input type='submit' name='submit' value='Annuler' />";
			  $out .= "<input type='submit' name='submit' value='Confirmer la suppression' />";
			  $out .= "</div></form>";
	  	}
	  	else {
				$url=parametre_url($url,'id_evenement',$id_evenement);
			  $out .= "<form name='edition_rdv' action='$url' method='post'>";
			  //$out .= "<input type='hidden' name='redirect' value='$url' />\n";
			  $out .= "<input type='hidden' name='id_evenement' value='$fid_evenement' />\n";
			  $out .= "<input type='hidden' name='edit' value='1' />\n";
			  $out .= "<div class='edition-bouton'>";
				$out .= "<div style='text-align:$spip_lang_right'><input type='submit' name='modifier' value='"._T('bouton_modifier')."' class='fondo'></div>";
			  $out .= "</div></form>";
	  	}
		}
		$out .= "</div>\n";
	}
	return $out;
}

function exec_agenda_evenements_dist(){
	// s'assurer que les tables sont crees
	Agenda_install();

	$ajouter_id_article = intval(_request('ajouter_id_article'));
	$flag_editable = article_editable($ajouter_id_article);

	global $visu_evenements;
	$type = _request('type');
	if (!$type) $type='semaine';
	$id_evenement = intval(_request('id_evenement'));
	$edit = _request('edit');
	$neweven = _request('neweven');

	$annee = intval(_request('annee'));
	$mois = intval(_request('mois'));
	$jour = intval(_request('jour'));
	$date = date("Y-m-d", time());
	if ($annee&&$mois&&$jour)
		$date = date("Y-m-d", strtotime("$annee-$mois-$jour"));


		
	if ($type == 'semaine') {
	
		//$GLOBALS['afficher_bandeau_calendrier_semaine'] = true;
		$titre = _T('titre_page_calendrier',
			array('nom_mois' => nom_mois($date), 'annee' => annee($date)));
	}
  elseif ($type == 'jour') {
		$titre = nom_jour($date)." ". affdate_jourcourt($date);
  }
	else {
		$titre = _T('titre_page_calendrier',
		    array('nom_mois' => nom_mois($date), 'annee' => annee($date)));
	}

  debut_page($titre, "redacteurs", "calendrier","",$css);
	barre_onglets("calendrier", "evenements");
	echo Agenda_date_insert_js_calendar_placeholder("_debut");
	echo Agenda_date_insert_js_calendar_placeholder("_fin");

	$out = "<div>";
	if ($ajouter_id_article){
		$query = "SELECT * FROM spip_articles AS articles WHERE id_article=$ajouter_id_article";
		$res2 = spip_query($query);
		if ($row2 = spip_fetch_array($res2)){
			$out .= "<div style=' width:750px; font-size: 18px; color: #9DBA00; font-weight: bold;text-align:left;'>";
			$out .= "<a href='".generer_url_ecrire('articles',"id_article=".$row2['id_article'])."'>";
			$out .= http_img_pack("article-24.gif", "", "width='24' height='24' border='0'");
			$out .= entites_html($row2['titre'])."</a></div>";
		}
	}
	echo $out ."&nbsp;</div>" ;

	echo affiche_evenements_agenda($flag_editable);

	if (($edit||$neweven)&&($flag_editable))	{ //---------------Edition RDV ------------------------------
		$ndate = _request('ndate');
		echo Agenda_formulaire_edition_evenement($id_evenement,$neweven,$ndate);
	}
	else
		if ((isset($id_evenement))&&(isset($visu_evenements[$id_evenement]))){ //---------------Visualisation RDV ------------------------------
			echo visu_evenement_agenda($id_evenement,$flag_editable);
		}
	fin_page();

}

function http_calendrier_ics_message($annee, $mois, $jour, $large)
{
	return "";
}

function http_calendrier_aide_mess()
{
	return "";
}

function http_calendrier_semainesh($annee, $mois, $jour, $echelle, $partie_cal, $script, $ancre, $evt)
{
	global $spip_ecran;
	if (!isset($spip_ecran)) $spip_ecran = 'large';

	$init = date("w",mktime(1,1,1,$mois,$jour,$annee));
	$init = $jour+1-($init ? $init : 7);
	$sd = '';

	if (is_array($evt))
	  {
		  list($sansduree, $evenements, $premier_jour, $dernier_jour) = $evt;
		  if ($sansduree)
		    foreach($sansduree as $d => $r) 
		      $evenements[$d] = !$evenements[$d] ? $r : array_merge($evenements[$d], $r);
	    $finurl = "&amp;echelle=$echelle&amp;partie_cal=$partie_cal$ancre";
	    $evt =
	      http_calendrier_semaine_noms($annee, $mois, $init, $script, $finurl) .
	      http_calendrier_mois_sept($annee, $mois, $init, $init+ 6, $evenements, $script);
	  } else $evt = "<tr><td>$evt</td></tr>";

	return 
	  "\n<table class='calendrier-table-$spip_ecran' cellspacing='0' cellpadding='0'>" .
	  http_calendrier_semaine_navigation($annee, $mois, $init, $echelle, $partie_cal, $script, $ancre) .
	  $evt .
	  "</table>" .
	  (_DIR_RESTREINT ? "" : http_calendrier_aide_mess());
}