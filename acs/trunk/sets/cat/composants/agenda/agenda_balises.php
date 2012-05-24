<?php
/**
 * Balise #BLOC_AGENDA{annee,mois}
 * Affiche un bloc agenda
 * Sur les pages rubrique et auteur, les évènements sont filtrés.
 */
function balise_BLOC_AGENDA($p) {
  $annee = interprete_argument_balise(1,$p);
  $mois = interprete_argument_balise(2,$p);
  $rubrique = interprete_argument_balise(3,$p);
  $auteur = interprete_argument_balise(4,$p);
	$p->code = 'calculer_balise_agenda('.$annee.', '.$mois.', '.$rubrique.', '.$auteur.')';
	$p->statut = 'php';
  $p->interdire_scripts = false;
  return $p;
}

function calculer_balise_agenda($annee, $mois, $rubrique, $auteur) {
  include_once _DIR_RESTREINT_ABS."base/abstract_sql.php";
  
  if (!is_callable(sql_select)) {
    function sql_select($fields, $table, $cond) {
      return spip_abstract_select($fields, $table, $cond);
    }
  }
  if (!is_callable(sql_fetch)) {
    function sql_fetch($r) {
      return spip_abstract_fetch($r);
    }
  }
  $calendrier_mois = $mois;
  $calendrier_annee = $annee;
  $lang = $GLOBALS['lang'];
  $oldlang = $GLOBALS['spip_lang'];
  $GLOBALS['spip_lang'] = $lang;
  $months = array('', _T('acs:agenda_janvier'), _T('acs:agenda_fevrier'), _T('acs:agenda_mars'),
  	 _T('acs:agenda_avril'), _T('acs:agenda_mai'), _T('acs:agenda_juin'), _T('acs:agenda_juillet'),
  	 _T('acs:agenda_aout'), _T('acs:agenda_septembre'), _T('acs:agenda_octobre'),
  	 _T('acs:agenda_novembre'), _T('acs:agenda_decembre'));
  $days = array(_T('acs:agenda_di'), _T('acs:agenda_lu'), _T('acs:agenda_ma'), _T('acs:agenda_me'), _T('acs:agenda_je'), _T('acs:agenda_ve'), _T('acs:agenda_sa'));
  $GLOBALS['spip_lang'] = $oldlang;
  if ($test_mini_agenda_deja_present!=1 &&!function_exists('mkdate')) {
  	function mkdate($month, $day, $year) {
  		return mktime(0, 0, 0, $month, $day, $year);
  	}
  	function preparation_URL($texte_URL,$mois_URL,$annee_URL) {
      $position = StrPos($texte_URL,"calendrier_mois");
      $texte_remplacement = "calendrier_mois=".$mois_URL."&amp;calendrier_annee=".$annee_URL;
      if ($position!==FALSE)
          {
          $texte_URL = substr_replace ($texte_URL,$texte_remplacement,$position);}
          else  { $presence = StrPos($texte_URL,"?");
                  if ($presence === false)
                    {$texte_URL = $texte_URL."?".$texte_remplacement;}
                  else
                    {$texte_URL = $texte_URL."&amp;".$texte_remplacement;}
                }
      return $texte_URL.'&amp;lang='.$GLOBALS['lang'];
  	}
  	$test_mini_agenda_deja_present = 1;
  }

  $cal_day = time();  
  $D = intval(date('d', $cal_day));
  if (isset($calendrier_mois) && $calendrier_mois) {
  	$M = $calendrier_mois;
  } else {$M = intval(date('m', $cal_day));}
  if (isset($calendrier_annee) && $calendrier_annee) {
  	$Y = $calendrier_annee;
  } else {$Y = intval(date('Y', $cal_day));}
  
  if ($M==1){
      $calendrier_mois_moins=12;
      $calendrier_annee_moins=$Y-1;}
  else {
      $calendrier_mois_moins=$M-1;
      $calendrier_annee_moins=$Y;}
  if ($M==12){
      $calendrier_mois_plus=1;
      $calendrier_annee_plus=$Y+1;}
  else {
      $calendrier_mois_plus=$M+1;
      $calendrier_annee_plus=$Y;}
  $datedebut = date("Ymd", mkdate($M, 1, $Y));
  $datefin = date("Ymd", mkdate($calendrier_mois_plus, 1, $calendrier_annee_plus));

  $prefix = $GLOBALS['table_prefix'].'_';

  //Filtrer suivant la rubrique
  if ($rubrique != '')
  	$sql_rubrique = ' AND (id_rubrique="'.$rubrique.'")';
  else
  	$sql_rubrique = '';

  // Filtrer articles modifiés
  if (isset($GLOBALS['meta']['acsAgendaBulleVoirArticlesModifies']) && ($GLOBALS['meta']['acsAgendaBulleVoirArticlesModifies'] == 'oui'))
  	$sql_modif = " OR (date_modif BETWEEN '$datedebut' AND '$datefin')";
  
  //Filtrer suivant l'auteur
  if ($auteur != '')
  	$r = sql_select(array($prefix.'articles.id_article','date','date_modif','titre','chapo'), $prefix.'articles LEFT JOIN '.$prefix.'auteurs_articles USING(id_article)', $prefix."articles.statut='publie' AND ((date BETWEEN '$datedebut' AND '$datefin')$sql_modif) AND (id_auteur='$auteur')");
  else
  	$r = sql_select(array('id_article','date','date_modif','titre','chapo'), $prefix.'articles', $prefix."articles.statut='publie' AND ((date BETWEEN '$datedebut' AND '$datefin')$sql_modif)$sql_rubrique");

  $events = array();
  while($article = sql_fetch($r)) {
  	if (substr($article['chapo'],0,1) == '=') continue; // Masque les redirections d'articles SPIP
  	$heure = ereg_replace("^.*([0-9]{2}):([0-9]{2}):([0-9]{2})$", "\\1h\\2", $article['date']);
  	$date = ereg_replace("^([0-9]{4})-([0-9]{2})-([0-9]{2}).*$", "\\1\\2\\3", $article['date']);
  	if (!isset($events[$date])) 
  		$events[$date] = array();
  	if (($date > $datedebut) && ($date < $datefin)) {
  		$events[$date][$heure][ ] = array(
  			'class' => 'publie',
  			'type' => 'article',
  			'id' => $article['id_article'],
  			'title' => $article['titre']);
  	}	elseif(isset($sql_modif)) {
  		$heure = ereg_replace("^.*([0-9]{2}):([0-9]{2}):([0-9]{2})$", "\\1h\\2", $article['date_modif']);
  		$date = ereg_replace("^([0-9]{4})-([0-9]{2})-([0-9]{2}).*$", "\\1\\2\\3", $article['date_modif']);
  		$events[$date][$heure][ ] = array(
  			'class' => 'modifie',
  			'type' => 'article',
  			'id' => $article['id_article'],
  			'title' => $article['titre']);
  	}
  }
  
  // Breves, si actives dans SPIP
  if ($GLOBALS['meta']['activer_breves'] == 'oui') {
  	$r = sql_select(array('id_breve','date_heure','titre'), 'spip_breves', "statut='publie' AND (date_heure BETWEEN '".$datedebut."' AND '".$datefin."')$sql_rubrique");
  	while($breve = sql_fetch($r)) {
  		$heure = ereg_replace("^.*([0-9]{2}):([0-9]{2}):([0-9]{2})$", "\\1h\\2", $breve['date_heure']);
  		$date = ereg_replace("^([0-9]{4})-([0-9]{2})-([0-9]{2}).*$", "\\1\\2\\3", $breve['date_heure']);
  		if (!isset($events[$date])) 
  			$events[$date] = array();
  		$events[$date][$heure][ ] = array(
  			'class' => 'breve',
  			'type' => 'breve',
  			'id' => $breve['id_breve'],
  			'title' => $breve['titre']);
  	}
  }

	$self = (isset($_GET['selfurl']) ? $_GET['selfurl'] : $_SERVER['QUERYSTRING']);
	
  $r =  '<table width="100%" cellpadding="1" cellspacing="0" align="center"><tr><th colspan="7" valign="middle" class="title bsize">
    <img id="jourchelp" src="'.acs_chemin("agenda/aide.gif").'" class="chelp unjour" alt="?" />
		<a id="agenda_prev" href="'.preparation_URL($self,$calendrier_mois_moins,$calendrier_annee_moins).'" title="Mois pr&eacute;c&eacute;dent" class="ajax" rel="nofollow"><img src="'.acs_chemin("agenda/fleche-left.png").'" alt="&lt;&lt;" /></a>&nbsp;&nbsp;<a href="spip.php?page=agenda&amp;annee='.$Y.'&amp;mois='.$M.'&amp;type=mois&amp;jour=01" class="bloc-title" rel="nofollow">'.$months[$M].' '.$Y.'</a>&nbsp;&nbsp;<a id="agenda_next" href="'.preparation_URL($self,$calendrier_mois_plus,$calendrier_annee_plus).'" title="Mois suivant" class="ajax" rel="nofollow"><img src="'.acs_chemin("agenda/fleche-right.png").'" alt="&gt;&gt;" /></a>
    <div id="bullechelp" class="bulle">
      <div class="publie"><a href="#"><small>01h11</small>: '._T("acs:agenda_publie").'</a></div>';
  if ($GLOBALS['meta']['acsAgendaBulleVoirArticlesModifies'] == "oui")
		$r .= '<div class="modifie"><a href="#"><small>02h22</small>: '._T("acs:agenda_modifie").'</a></div>';
	if ($GLOBALS['meta']['activer_breves'] == "oui")
		$r .= '<div class="breve"><a href="#"><small>03h33</small>: '._T("breves").'</a></div>';
	$r .= '</div></th></tr><tr>';
	for($i = 1; $i < 8; $i++) {
		$r .= '<th width="14%" class="calendar_head_mini nsize">'.$days[$i%7].'</th>';
	}
	$TempD = 1;
	if(date('w', mkdate($M, 1, $Y)) != 1) {
		$r .=  '</tr><tr>';
		$tmp = '';
		while(date('w', mkdate($M, $TempD, $Y)) != 1) {
			$TempD--;
			$case = '<td width="14%" valign="top" class="calendar_not_this_month nsize">';
			$case .= date('j', mkdate($M, $TempD, $Y));
			$date = date('Ymd', mkdate($M, $TempD, $Y));

			$case .= '</td>';
			$tmp = $case.$tmp;
		}
		$r .=  $tmp;
	}
	$TempD = 1;
	while((date('m', mkdate($M, $TempD, $Y)) == $M) || (date('w', mkdate($M, $TempD, $Y)) != 1)) {
		if(date('w', mkdate($M, $TempD, $Y)) == 1) {
			$r .=  '</tr><tr>';
		}
		$r .=  '<td width="6%" valign="top" class="nsize calendar_'.(date('m', mkdate($M, $TempD, $Y)) != $M ? 'not_' : '').'this_'.(date('Ymd', mkdate($M, $TempD, $Y)) == date('Ymd') ? 'day' : 'month').'">';
		$date = date('Ymd', mkdate($M, $TempD, $Y));
		if (isset($events[$date])) {
				$evt = '';
				$eventsjour = $events[$date];
				ksort($eventsjour);
				foreach($eventsjour as $heure=>$eventsheure) {
					foreach($eventsheure as $event) {
						$evt .= '<div class="'.$event['class'].'"><a href="spip.php?'.$event['type'].''.$event['id'].'&amp;calendrier_mois='.$M.'&amp;calendrier_annee='.$Y.'" rel="nofollow"><small>'.$heure.'</small>: '.$event['title']."</a></div>\r";
					}
				}
				$r .= '<a id="jour'.$date.'" href="?page=agenda&amp;annee='.$Y.'&amp;mois='.$M.'&amp;jour='.$TempD.'&amp;type=jour&amp;echelle=80" class="unjour" rel="nofollow">'. date('j', mkdate($M, $TempD, $Y)) .'</a><div id="bulle'.$date.'" class="bulle">'.$evt.'</div>';
		}
		else {
			$r .=  '<a>'.date('j', mkdate($M, $TempD, $Y)).'</a>';
		}
		$r .=  '</td>';
		$TempD++;
	}
	$r .= "</tr></table>";
	return $r;
}
?>