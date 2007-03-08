<?php


if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/texte');
include_spip('inc/presentation');
include_spip('inc/agenda_filtres');
include_spip('inc/agenda_gestion');

function inc_voir_agenda($flag_editable){
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

	$visu_evenements=array();

	if ((!$annee)||(!$mois)||(!$jour)){
		if (!$id_evenement){ // pas d'id_evenement--> date du jour
			$stamp=time();
		}
		else { // date de l'evenement
			$res = spip_query("SELECT date_debut FROM spip_evenements WHERE id_evenement="._q($id_evenement));
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
	$res = spip_query("SELECT * 
							FROM spip_evenements AS evenements
						 WHERE ((evenements.date_debut>='$datestart' AND evenements.date_debut<='$datefin') 
						 		OR (evenements.date_fin>='$datestart' AND evenements.date_fin<='$datefin')
						 		OR (evenements.date_debut<'$datestart' AND evenements.date_fin>'$datefin'))
						 ORDER BY evenements.date_debut;");
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
		$args = explode('?',parametre_url($url,'exec',''));
		// $url sous forme d'array pour appeler ajax_action_auteur
		$url = array('action'=>'voir_evenement','id'=>"0-voir",'script'=>'calendrier','args'=>end($args));
		
		$titre = typo($row['titre']);
		$descriptif = typo($row['descriptif']);
		$lieu = typo($row['lieu']);
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
	// creneaux pour ajout uniquement si ajouter_id_article present
	if (($type!='mois')&&($partie_cal!='sansheure')&&($partie_cal!=NULL))
	{
		if ($echelle<=120)
			$freq_creneaux=30*60;
		else
			$freq_creneaux=60*60;
	
		$today=date('Y-m-d');
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
				$args = explode('?',parametre_url($url,'exec',''));
				// $url sous forme d'array pour appeler ajax_action_auteur
				$url = array('action'=>'voir_evenement','id'=>"0-editer",'script'=>'calendrier','args'=>end($args),'fct_ajax'=>'wc_init');
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
if (!function_exists('http_calendrier_ics_message')) {
	function http_calendrier_ics_message($annee, $mois, $jour, $large)
	{
		return "";
	}
}

if (!function_exists('http_calendrier_aide_mess')) {
	function http_calendrier_aide_mess()
	{
		return "";
	}
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
?>