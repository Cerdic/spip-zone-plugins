<?php

$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(dirname(__FILE__)))));
define('_DIR_PLUGIN_WIDGET_CALENDAR',(_DIR_PLUGINS.end($p)));

function WCalendar_point_entree($suffixe){
	return "<div id='container$suffixe' style='position:absolute;display:none;z-index:5000;'></div>
	";
}

function WCalendar_body_prive($flux){
	global $WCalendar_independants,$WCalendar_lies;
	if (count($WCalendar_independants)+count($WCalendar_lies)){
		$flux.= "<script type='text/javascript'>";
		$flux .= "window.onload = wc_init;";
		$flux .= "</script>";
		foreach($WCalendar_independants as $infos)
			$flux .= WCalendar_point_entree($infos['suffixe']);
		foreach($WCalendar_lies as $infos){
			$flux .= WCalendar_point_entree($infos['suffixe1']);
			$flux .= WCalendar_point_entree($infos['suffixe2']);
		}
	}
	return $flux;
}

function WCalendar_statique_jsinit($t, $s){
	$vars = "
			cal$s = undefined;";
	$js_a = "";
	$js_b = "
			var selected$s = '';
			this.content$s = document.getElementById('selected_date$s');
			if (this.content$s){
				selected$s=this.content$s.innerHTML;
			}
			if (cal$s != undefined) delete cal$s;
			$('#container$s').html('');
			cal$s = new SPIP.widget.Calendar2up_INT_multi('cal$s','container$s','',selected$s);
			cal$s.title = '$t';
			cal$s.sync();
			cal$s.render();
			";
	$js_c = "
		function getSelectedDate$s() {
			var res = document.getElementById('selected_date$s');
			res.innerHTML = cal$s.getSelectedDates();
		}
	";
	return array($vars,$js_a,$js_b,$js_c);
}

function WCalendar_jsinit($t, $s){
	$vars = "
			var cal$s;";
	$js_a = "
			if (cal$s)
				cal$s.hide();";
	$js_b = "
			this.link$s = document.getElementById('dateLink$s');
			if (this.link$s){
				this.selYear$s = document.getElementById('annee$s');
				this.selMonth$s = document.getElementById('mois$s');
				this.selDay$s = document.getElementById('jour$s');
				thisYear$s = this.selYear$s.selectedIndex+parseInt(this.selYear$s.options[0].value);
				thisMonth$s = this.selMonth$s.selectedIndex;
				thisDay$s = this.selDay$s.selectedIndex +1;
				cal$s = new SPIP.widget.Calendar2up_INT('cal$s','container$s',(thisMonth$s+1)+'/'+thisYear$s,(thisMonth$s+1)+'/'+thisDay$s+'/'+thisYear$s);
				cal$s.title = '$t';
				cal$s.setChildFunction('onSelect',setDate$s);
				cal$s.render();
			}
			";
	$js_c = "
		function showCalendar$s() {
			wc_hideall();
			cal$s.outerContainer.style.top = (link$s.height-1+findPosY(link$s)) + 'px';
			cal$s.outerContainer.style.left = (findPosX(link$s)) + 'px';
			cal$s.outerContainer.style.display='block';
		}
		";
	return array($vars,$js_a,$js_b,$js_c);
}
function WCalendar_js_verifie_lies($sd,$sf){
	return "
		function verifie_date$sd$sf(modif){
			var Date$sd = new Date;
			Date$sd.setFullYear(this.selYear$sd.selectedIndex+parseInt(selYear$sd.options[0].value));
			Date$sd.setMonth(this.selMonth$sd.selectedIndex);
			Date$sd.setDate( this.selDay$sd.selectedIndex + 1);
			var Date$sf = new Date;
			Date$sf.setFullYear(this.selYear$sf.selectedIndex+parseInt(selYear$sf.options[0].value));
			Date$sf.setMonth(this.selMonth$sf.selectedIndex);
			Date$sf.setDate( this.selDay$sf.selectedIndex + 1);
			if (Date$sf<Date$sd){
				if (modif==1){
					selYear$sf.selectedIndex=selYear$sd.selectedIndex;
					selMonth$sf.selectedIndex=selMonth$sd.selectedIndex;
					selDay$sf.selectedIndex=selDay$sd.selectedIndex;
					cal$sf.select((Date$sd.getMonth()+1) + '/' + (Date$sd.getDate()) + '/' + (Date$sd.getFullYear()));
					cal$sf.setMonth(Date$sd.getMonth());
					cal$sf.setYear(Date$sd.getFullYear());
					cal$sf.render();
				}
				else{
					selYear$sd.selectedIndex=selYear$sf.selectedIndex;
					selMonth$sd.selectedIndex=selMonth$sf.selectedIndex;
					selDay$sd.selectedIndex=selDay$sf.selectedIndex;
					cal$sd.select((Date$sf.getMonth()+1) + '/' + (Date$sf.getDate()) + '/' + (Date$sf.getFullYear()));
					cal$sd.setMonth(Date$sf.getMonth());
					cal$sd.setYear(Date$sf.getFullYear());
					cal$sd.render();
				}
			}
		}
		";
}
function Wcalendar_js_set_change_date($s,$sdverif=NULL,$sfverif=NULL,$modif=""){
	return "
		function setDate$s() {
			var date$s = cal$s.getSelectedDates()[0];
			selYear$s.selectedIndex=date$s.getFullYear()-parseInt(selYear$s.options[0].value);
			selMonth$s.selectedIndex=date$s.getMonth();
			selDay$s.selectedIndex=date$s.getDate()-1;
			cal$s.hide();
			" .
			($sdverif!=NULL?"verifie_date$sdverif$sfverif($modif);":"") ."
		}

		function changeDate$s() {
			var month = this.selMonth$s.selectedIndex;
			var day = this.selDay$s.selectedIndex + 1;
			var year = this.selYear$s.selectedIndex+parseInt(selYear$s.options[0].value);
			cal$s.reset();
			cal$s.clear();
			cal$s.select((month+1) + '/' + day + '/' + year);
			cal$s.setMonth(month);
			cal$s.setYear(year);
			cal$s.render();
			" .
			($sdverif!=NULL?"verifie_date$sdverif$sfverif($modif);":"") ."
		}
		";
}
function WCalendar_header_prive($flux) {
	global $WCalendar_independants,$WCalendar_lies,$WCalendar_statiques;
	global $spip_lang_right;
	if (count($WCalendar_independants)+count($WCalendar_lies)){
		// Remplace les entités litérales
		$trans_tbl = get_html_translation_table (HTML_ENTITIES);
		$trans_tbl = array_flip ($trans_tbl);
		
		// les includes JS statiques
		$flux .= "<script src = '"._DIR_PLUGIN_WIDGET_CALENDAR."/img_pack/SPIP.js' ></script>\n";
		$flux .= "<script src = '"._DIR_PLUGIN_WIDGET_CALENDAR."/img_pack/dom.js' ></script>\n";
		$flux .= "<script src = '"._DIR_PLUGIN_WIDGET_CALENDAR."/img_pack/event.js' ></script>\n";
		$flux .= "<script src = '"._DIR_PLUGIN_WIDGET_CALENDAR."/img_pack/calendar.js' ></script>\n";
		$flux .= "<script src = '"._DIR_PLUGIN_WIDGET_CALENDAR."/img_pack/calendar_custom.js' ></script>\n";
		$flux .= '<link rel="stylesheet" href="' ._DIR_PLUGIN_WIDGET_CALENDAR . '/img_pack/calendar.css" type="text/css" />'. "\n";

		// les noms de mois et de jour selon les fichiers de langue :
		$months_long = "";
		$months_short = "";
		for ($j=1;$j<=12;$j++){
			$nom = entites_html(ucfirst(strtr(_T("spip:date_mois_$j"),$trans_tbl)));
			$months_long .= ",'".unicode2charset(charset2unicode($nom,''))."'";
			$months_short .= ",'".unicode2charset(charset2unicode(preg_replace("/^((&#x?[0-9]{2,5};|.){0,3}).*$/i","\\1",$nom),''))."'";
		}
		$months_long = substr($months_long,1);
		$months_short = substr($months_short,1);

		$days_long = "";
		$days_medium = "";
		$days_short = "";
		$days_1char = "";
		for ($j=1;$j<=7;$j++){
			$nom = entites_html(ucfirst(strtr(_T("spip:date_jour_$j"),$trans_tbl)));
			$days_long .= ",'".$nom."'";
			$days_medium .= ",'".preg_replace("/^((&#x?[0-9]{2,5};|.){0,3}).*$/i","\\1",$nom)."'";
			$days_short .= ",'".preg_replace("/^((&#x?[0-9]{2,5};|.){0,2}).*$/i","\\1",$nom)."'";
			$days_1char .= ",'".preg_replace("/^((&#x?[0-9]{2,5};|.){0,1}).*$/i","\\1",$nom)."'";
		}
		$days_long = substr($days_long,1);
		$days_medium = substr($days_medium,1);
		$days_short = substr($days_short,1);
		$days_1char = substr($days_1char,1);
		$start_weekday = 1;
		$img_arrow_left = _DIR_PLUGIN_WIDGET_CALENDAR."/img_pack/callt.gif";
		$img_arrow_right = _DIR_PLUGIN_WIDGET_CALENDAR."/img_pack/calrt.gif";
		$img_close = _DIR_PLUGIN_WIDGET_CALENDAR."/img_pack/calx.gif";


	// le JS dynamique d'init
	$flux .= "<script language='javascript'>";

	// partie fonction de la langue
	$js = "";

	$js .= "	
		SPIP.widget.Calendar2up_INT_Cal.prototype.customConfig = function() {
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
			this.Config.Options.IMG_CLOSE_CLASS = 'close-icon-$spip_lang_right';
		}
		";

	$js .= "	
		SPIP.widget.Calendar2up_INT_Cal_multi.prototype.customConfig = function() {
			this.Config.Locale.MONTHS_SHORT = [$months_short];
			this.Config.Locale.MONTHS_LONG = [$months_long];
			this.Config.Locale.WEEKDAYS_1CHAR = [$days_1char];
			this.Config.Locale.WEEKDAYS_SHORT = [$days_short];
			this.Config.Locale.WEEKDAYS_MEDIUM = [$days_medium];
			this.Config.Locale.WEEKDAYS_LONG = [$days_long];
		
			this.Config.Options.START_WEEKDAY = $start_weekday;
			this.Config.Options.NAV_ARROW_LEFT = '$img_arrow_left';
			this.Config.Options.NAV_ARROW_RIGHT = '$img_arrow_right';
			this.Config.Options.MULTI_SELECT = true;
		}
		";
	
		// construire les variables et fonctions de mise a jour

		$vars = "";
		$js_a= "";	
		$js_b= "";	
		$js_c= "";	
		$liste_suffixes = array();
		foreach ($WCalendar_statiques as  $infos){
			$s = $infos['suffixe'];
			$t = $infos['titre'];
			list($v,$a,$b,$c) = WCalendar_statique_jsinit($t, $s);
			$vars .= $v; $js_a .= $a; $js_b .= $b;$js_c .= $c;
		}
		foreach ($WCalendar_independants as  $infos){
			$s = $infos['suffixe'];
			$t = $infos['titre'];
			list($v,$a,$b,$c) = WCalendar_jsinit($t, $s);
			$vars .= $v; $js_a .= $a; $js_b .= $b;$js_c .= $c;
		}
		foreach ($WCalendar_lies as  $infos){
			$s = $infos['suffixe1'];
			$t = $infos['titre1'];
			list($v,$a,$b,$c) = WCalendar_jsinit($t, $s);
			$vars .= $v; $js_a .= $a; $js_b .= $b;$js_c .= $c;

			$s = $infos['suffixe2'];
			$t = $infos['titre2'];
			list($v,$a,$b,$c) = WCalendar_jsinit($t, $s);
			$vars .= $v; $js_a .= $a; $js_b .= $b;$js_c .= $c;
		}
		global $init_functions;
		$js .= "
		$vars
		function wc_hideall(){
			$js_a
		}
		function wc_init() {
			$init_functions
			$js_b
		}
		
		$js_c
		";
		foreach ($WCalendar_independants as  $infos){
			$s = $infos['suffixe'];
			$js .= Wcalendar_js_set_change_date($s);
		}

		foreach ($WCalendar_lies as  $infos){
			$sd = $infos['suffixe1'];
			$sf = $infos['suffixe2'];
			$js .= WCalendar_js_verifie_lies($sd,$sf);
			$js .= Wcalendar_js_set_change_date($sd,$sd,$sf,1);
			$js .= Wcalendar_js_set_change_date($sf,$sd,$sf,2);
		}
		
		$js .= "
	</script>";
		$flux .= $js;
	}
	return $flux;
}

?>