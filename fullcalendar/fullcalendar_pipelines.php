<?php

/**********
 * PUBLIC *
 **********/

function fullcalendar_insert_head_css($flux_ = '', $prive = false){
	static $done = false;
	if($done) return $flux_;
	$done = true;
	$flux .= "<link rel='stylesheet' type='text/css' href='".find_in_path('css/cupertino/theme.css')."' />
";
	$flux .= "<link rel='stylesheet' type='text/css' href='".find_in_path('css/fullcalendar.css')."' />
";
	$flux .= "<link rel='stylesheet' type='text/css' media='print' href='".find_in_path('css/fullcalendar.print.css')."' />
";
	return $flux_ . $flux;
}

function fullcalendar_insert_head($flux_){
	$flux .= "<script type='text/javascript' src='".find_in_path('js/fullcalendar.js')."'></script>
";
	$flux .= "<script type='text/javascript' src='".find_in_path('js/gcal.js')."'></script>
";
	return $flux_ . fullcalendar_insert_head_css() . $flux;
}

/*********
 * PRIVE *
 *********/

function fullcalendar_header_prive($flux_){
	$flux  = "<!-- FULLCALENDAR HEADER PRIVE START -->
";
	$flux .= "<script type='text/javascript' src='".url_absolue(find_in_path('lib/jquery-ui-1.8.9/ui/jquery-ui.js'))."'></script>
";
	$flux .= "<script type='text/javascript' src='".url_absolue(find_in_path('lib/jquery-ui-1.8.9/ui/jquery.ui.core.js'))."'></script>
";
	$flux .= "<script type='text/javascript' src='".url_absolue(find_in_path('lib/jquery-ui-1.8.9/ui/jquery.ui.datepicker.js'))."'></script>
";
	$flux .= "<script type='text/javascript' src='".url_absolue(find_in_path('lib/jquery-ui-1.8.9/ui/jquery.effects.scale.js'))."'></script>
";
	$flux .= "<script type='text/javascript' src='".url_absolue(find_in_path('js/jquery.ui.timepicker.js'))."'></script>
";
	$flux .= "<script type='text/javascript' src='".url_absolue(find_in_path('js/fullcalendar.js'))."'></script>
";
	$flux .= "<script type='text/javascript' src='".url_absolue(find_in_path('js/gcal.js'))."'></script>
";
	$flux .= "<link rel='stylesheet' type='text/css' href='".url_absolue(find_in_path('css/jquery-ui.css'))."' />
";
	$flux .= "<link rel='stylesheet' type='text/css' href='".url_absolue(find_in_path('css/jquery-ui-timepicker.css'))."' />
";
	$flux .= "<link rel='stylesheet' type='text/css' href='".find_in_path('css/cupertino/theme.css')."' />
";
	$flux .= "<link rel='stylesheet' type='text/css' href='".url_absolue(find_in_path('css/fullcalendar.css'))."' />
";
	$flux .= "<!-- FULLCALENDAR HEADER PRIVE FIN -->
";
	return $flux_ . $flux;
}

function fullcalendar_affiche_milieu($flux) {
#	$exec = $flux["args"]["exec"];
#	if ($exec == "naviguer") {
#		if($flux['args']['id_rubrique']){
#			$ret = "<div id='pave_selection'>";
#			$ret .= recuperer_fond("prive/contenu/fullcalendar_rubriques", array('id_auteur'=>$flux['args']['id_rubrique']));
#			$ret .= "</div>";
#			$flux["data"] .= $ret;
#		}
#	}
	return $flux;
}

function fullcalendar_affiche_gauche($flux){
#	include_spip('inc/presentation');
#	if ($flux['args']['exec'] == 'articles'){
#		$flux['data'] .=
#		debut_cadre_relief('',true,'', _T('fullcalendar:fullcalendar')) . 
#		recuperer_fond('prive/contenu/fullcalendar_articles', array('id_auteur'=>$flux['args']['id_article'])) .
#		fin_cadre_relief(true);
#	}
	return $flux;
}


/*************
 * JQUERY UI *
 *************/

function fullcalendar_jqueryui_forcer($scripts){
	$scripts[] = "jquery.ui.core";
	$scripts[] = "jquery.ui.all";
	#$scripts[] = "jquery.ui.timepicker.js";
	$scripts[] = "jquery.ui.datepicker";
	$scripts[] = "jquery.effects.scale";
	$scripts[] = "jquery.ui.dialog";
	$scripts[] = "jquery.ui.tabs";
	return $scripts;
}

?>
