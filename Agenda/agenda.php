<?php

function Agenda_ajouter_onglets($flux) {
  if($flux['args']=='calendrier')
  {
		$flux['data']['evenements']= new Bouton(
																 '../'._DIR_PLUGIN_AGENDA_EVENEMENTS.'/img_pack/agenda-24.png', 'Evenements',
																generer_url_ecrire("calendrier","type=semaine"));
	
		$flux['data']['editorial']= new Bouton(
															 'cal-rv.png', 'Activité Editoriale',
																 generer_url_ecrire("calendrier","mode=editorial&type=semaine"));
  }
	return $flux;
}
function Agenda_header_prive($flux) {
	$exec = _request('exec');
	// les CSS
	if ($exec == 'calendrier'){
		$flux .= '<link rel="stylesheet" href="' ._DIR_PLUGIN_AGENDA_EVENEMENTS . '/calendrier.css" type="text/css" />'. "\n";
		$flux .= '<link rel="stylesheet" href="' ._DIR_PLUGIN_AGENDA_EVENEMENTS . '/agenda.css" type="text/css" />'. "\n";
	}
	if ($exec == 'articles'){
		$flux .= '<link rel="stylesheet" href="' ._DIR_PLUGIN_AGENDA_EVENEMENTS . '/agenda_articles.css" type="text/css" />'. "\n";
	}
	// le JS
	$flux .= "<script src = '"._DIR_PLUGIN_AGENDA_EVENEMENTS."/img_pack/YAHOO.js' ></script>\n";
	$flux .= "<script src = '"._DIR_PLUGIN_AGENDA_EVENEMENTS."/img_pack/dom.js' ></script>\n";
	$flux .= "<script src = '"._DIR_PLUGIN_AGENDA_EVENEMENTS."/img_pack/event.js' ></script>\n";
	$flux .= "<script src = '"._DIR_PLUGIN_AGENDA_EVENEMENTS."/img_pack/calendar.js' ></script>\n";
	$flux .= '<link rel="stylesheet" href="' ._DIR_PLUGIN_AGENDA_EVENEMENTS . '/img_pack/calendar.css" type="text/css" />'. "\n";

	// les noms de mois et de jour selon les fichiers de langue :
	$months_long = "";
	$months_short = "";
	for ($j=1;$j<=12;$j++){
		$nom = ucfirst(html_entity_decode(_T("spip:date_mois_$j")));
		$months_long .= ",'".$nom."'";
		$months_short .= ",'".substr($nom,0,3)."'";
	}
	$months_long = substr($months_long,1);
	$months_short = substr($months_short,1);

	$days_long = "";
	$days_medium = "";
	$days_short = "";
	$days_1char = "";
	for ($j=1;$j<=7;$j++){
		$nom = ucfirst(html_entity_decode(_T("spip:date_jour_$j")));
		$days_long .= ",'".$nom."'";
		$days_medium .= ",'".substr($nom,0,3)."'";
		$days_short .= ",'".substr($nom,0,2)."'";
		$days_1char .= ",'".substr($nom,0,1)."'";
	}
	$days_long = htmlentities(substr($days_long,1));
	$days_medium = htmlentities(substr($days_medium,1));
	$days_short = htmlentities(substr($days_short,1));
	$days_1char = htmlentities(substr($days_1char,1));
	$start_weekday = 1;
	$img_arrow_left = _DIR_PLUGIN_AGENDA_EVENEMENTS."/img_pack/callt.gif";
	$img_arrow_right = _DIR_PLUGIN_AGENDA_EVENEMENTS."/img_pack/calrt.gif";
	$img_close = _DIR_PLUGIN_AGENDA_EVENEMENTS."/img_pack/calx.gif";

	
	$flux .= <<<initcalendriers
<script language="javascript">
		YAHOO.widget.Calendar2up_INT_Cal = function(id, containerId, monthyear, selected) {
			if (arguments.length > 0)
			{
				this.init(id, containerId, monthyear, selected);
			}
		}
		
		YAHOO.widget.Calendar2up_INT_Cal.prototype = new YAHOO.widget.Calendar2up_Cal();
		
		YAHOO.widget.Calendar2up_INT_Cal.prototype.customConfig = function() {
			this.Config.Locale.MONTHS_SHORT = [$months_short];
			this.Config.Locale.MONTHS_LONG = [$months_long];
			this.Config.Locale.WEEKDAYS_1CHAR = [$days_1char];
			this.Config.Locale.WEEKDAYS_SHORT = [$days_short];
			this.Config.Locale.WEEKDAYS_MEDIUM = [$days_medium];
			this.Config.Locale.WEEKDAYS_LONG = [$days_long];
		
			this.Config.Options.START_WEEKDAY = $start_weekday;
			this.Config.Options.NAV_ARROW_LEFT = '$img_arrow_left';
			this.Config.Options.NAV_ARROW_RIGHT = '$img_arrow_right';
			this.Config.Options.IMG_CLOSE = '$img_close';
			
		}
		
		YAHOO.widget.Calendar2up_INT = function(id, containerId, monthyear, selected) {
			if (arguments.length > 0)
			{	
				this.buildWrapper(containerId);
				this.init(2, id, containerId, monthyear, selected);
			}
		}
		
		YAHOO.widget.Calendar2up_INT.prototype = new YAHOO.widget.Calendar2up();
		
		YAHOO.widget.Calendar2up_INT.prototype.constructChild = function(id,containerId,monthyear,selected) {
			var cal = new YAHOO.widget.Calendar2up_INT_Cal(id,containerId,monthyear,selected);
			return cal;
		};
		
		/*************************************/

		var cal1;
		var cal2;

		function init() {
			this.today = new Date();

			var thisMonth = this.today.getMonth();
			var thisDay = this.today.getDate();
			var thisYear = this.today.getFullYear();

			this.link1 = document.getElementById('dateLink_debut');
			this.link2 = document.getElementById('dateLink_fin');

			this.selYear1 = document.getElementById('evenement_annee_debut');
			this.selMonth1 = document.getElementById('evenement_mois_debut');
			this.selDay1 = document.getElementById('evenement_jour_debut');

			thisYear1 = this.selYear1.selectedIndex+parseInt(this.selYear1.options[0].value);
			thisMonth1 = this.selMonth1.selectedIndex;
			thisDay1 = this.selDay1.selectedIndex +1;

			this.selYear2 = document.getElementById('evenement_annee_fin');
			this.selMonth2 = document.getElementById('evenement_mois_fin');
			this.selDay2 = document.getElementById('evenement_jour_fin');

			thisYear2 = this.selYear2.selectedIndex+parseInt(this.selYear1.options[0].value);
			thisMonth2 = this.selMonth2.selectedIndex;
			thisDay2 = this.selDay2.selectedIndex +1;

			cal1 = new YAHOO.widget.Calendar2up_INT("cal1","container_debut",(thisMonth1+1)+"/"+thisYear1,(thisMonth1+1)+"/"+thisDay1+"/"+thisYear1);
			cal1.title = "Date de debut";
			cal1.setChildFunction("onSelect",setDate_debut);
			cal1.render();

			cal2 = new YAHOO.widget.Calendar2up_INT("cal2","container_fin",(thisMonth2+1)+"/"+thisYear2,(thisMonth2+1)+"/"+thisDay2+"/"+thisYear2);
			cal2.title = "Date de fin";
			cal2.setChildFunction("onSelect",setDate_fin);
			cal2.render();
		}

		function showCalendar_debut() {
			cal2.hide();
			cal1.outerContainer.style.top = (link1.offsetTop+link1.offsetParent.offsetTop+link1.height-1) + "px";
			cal1.outerContainer.style.left = (link1.offsetLeft+link1.offsetParent.offsetLeft) + "px";
			cal1.outerContainer.style.display='block';
		}

		function showCalendar_fin() {
			cal1.hide();
			cal2.outerContainer.style.top = (link2.offsetTop+link2.offsetParent.offsetTop+link2.height-1) + "px";
			cal2.outerContainer.style.left = (link2.offsetLeft+link2.offsetParent.offsetLeft) + "px";
			cal2.outerContainer.style.display='block';
		}

		function setDate_debut() {
			var date1 = cal1.getSelectedDates()[0];
			selYear1.selectedIndex=date1.getFullYear()-parseInt(selYear1.options[0].value);
			selMonth1.selectedIndex=date1.getMonth();
			selDay1.selectedIndex=date1.getDate()-1;
			cal1.hide();
		}

		function setDate_fin() {
			var date2 = cal2.getSelectedDates()[0];
			selYear2.selectedIndex=date2.getFullYear()-parseInt(selYear2.options[0].value);
			selMonth2.selectedIndex=date2.getMonth();
			selDay2.selectedIndex=date2.getDate()-1;
			cal2.hide();
		}

		function changeDate_debut() {
			var month = this.selMonth1.selectedIndex;
			var day = this.selDay1.selectedIndex + 1;
			var year = this.selYear1.selectedIndex+parseInt(selYear1.options[0].value);
			cal1.select((month+1) + "/" + day + "/" + year);
			cal1.setMonth(month);
			cal1.setYear(year);
			cal1.render();
		}

		function changeDate_fin() {
			var month = this.selMonth2.selectedIndex;
			var day = this.selDay2.selectedIndex + 1;
			var year = this.selYear2.selectedIndex+parseInt(selYear2.options[0].value);

			cal2.select((month+1) + "/" + day + "/" + year);
			cal2.setMonth(month);
			cal2.setYear(year);
			cal2.render();
		}
	</script>
initcalendriers;

	return $flux;
}

function Agenda_rendu_boite($titre,$descriptif,$lieu,$type='ics'){
	$texte = "<span class='calendrier-verdana10'><span  style='font-weight: bold;'>";
	$texte .= wordwrap($sum=typo($titre),15)."</span>";
	$texte .= "<span class='survol'>";
	$texte .= "<strong>$sum</strong><br />";
	$texte .= $lieu ? "$lieu<br />":"";
	$texte .= propre($descriptif);
	$texte .= "</span>";
	if ($type=='ics'){	
		$texte .= (strlen($lieu.$descriptif)?"<hr/>":"").$lieu.(strlen($lieu)?"<br/>":"");
		$texte .= $descriptif;
	}
	$texte .= "</span>";

	return $texte;
}
function Agenda_rendu_evenement($flux) {
	global $couleur_claire;
	$evenement = $flux['args']['evenement'];

	
	$url = $evenement['URL']; 
	/*$desc = propre($evenement['DESCRIPTION']);
	$lieu = $evenement['LOCATION'];
	$sum = typo($evenement['SUMMARY']);

	$texte = "<span class='calendrier-verdana10'><span  style='font-weight: bold;'>";
	$texte .= wordwrap($sum,15)."</span>";
	$texte .= "<span class='survol'>";
	$texte .= "<strong>$sum</strong><br />";
	$texte .= $lieu ? "$lieu<br />":"";
	$texte .= $desc;
	$texte .= "</span>";
	$texte .= "</span>";*/
	$texte = Agenda_rendu_boite($evenement['SUMMARY'],$evenement['DESCRIPTION'],$evenement['LOCATION'],$flux['args']['type']);
	$texte = http_href(quote_amp($url), $texte, '', '', '', '');
	
	$flux['data'] = $texte;
	return $flux;
}

/* public static */
/*function Agenda_ajouterBoutons($boutons_admin) {
	// si on est admin
	//if ($GLOBALS['connect_statut'] == "0minirezo" && $GLOBALS["connect_toutes_rubriques"]) {

	  // on voit le bouton dans la barre "naviguer"
		$boutons_admin['accueil']->sousmenu["agenda"]= new Bouton(
		"../"._DIR_PLUGIN_PUBLIMAP."/habillage/publimap-24.png",  // icone
		_L("Publimap") //titre
		);
	}
	return $boutons_admin;
}*/


?>