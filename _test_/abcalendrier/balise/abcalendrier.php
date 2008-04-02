<?php
/*
calcola i parametri:
#ENV{prec_mois}
#ENV{prec_annee}
#ENV*{moise_annee_curr}
#ENV{suiv_mois}
#ENV{suiv_annee}
#ENV*{abcal_table}
#ENV*{event_table}
l'asterisco serve a inserire l'html
*/
function balise_ABCALENDRIER($p) {
   return calculer_balise_dynamique($p,ABCALENDRIER,array('prec_mois','prec_annee','moise_annee_curr','suiv_mois','suiv_annee','abcal_table','event_table'));

}

function balise_ABCALENDRIER_dyn() {

   $calendrier_mois=$_GET['calendrier_mois'];
   $calendrier_annee=$_GET['calendrier_annee'];
   $months = array('', _T('spip:date_mois_1'), _T('spip:date_mois_2'), _T('spip:date_mois_3'), _T('spip:date_mois_4'), _T('spip:date_mois_5'), _T('spip:date_mois_6'), _T('spip:date_mois_7'), _T('spip:date_mois_8'), _T('spip:date_mois_9'), _T('spip:date_mois_10'), _T('spip:date_mois_11'), _T('spip:date_mois_12'));
   $days = array(substr(_T('spip:date_jour_1_abbr'),0,2), substr(_T('spip:date_jour_2_abbr'),0,2), substr(_T('spip:date_jour_3_abbr'),0,2), substr(_T('spip:date_jour_4_abbr'),0,2), substr(_T('spip:date_jour_5_abbr'),0,2), substr(_T('spip:date_jour_6_abbr'),0,2), substr(_T('spip:date_jour_7_abbr'),0,2));
   /*???*/
   if(isset($GLOBALS['var_nav_month'])) {
   $cal_day = mkdate($GLOBALS['var_nav_month'], 1, $GLOBALS['var_nav_year']);
   } else {
   $cal_day = time();
   }

   $D = intval(date('d', $cal_day));
   if (isset($calendrier_mois)) {
   $M = $calendrier_mois;
   } else {$M = intval(date('m', $cal_day));}
   if (isset($calendrier_annee)) {
   $Y = $calendrier_annee;
   } else {$Y = intval(date('Y', $cal_day));}
   //echo "$Y $M $D";

   $events = array();
   $events = crea_eventi($Y,$M,$D);

   //calcolo la stringa del mese del calendario
   $mes = $months [$M];

   //calcolo mese e anno precedente e successivo
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
   /*---------------------------*/
   //calcolo la tabella del mese
   $my_cal_tab .='<table width="100%" cellpadding="0" cellspacing="0" align="center" id="abcal_table">';
   $my_cal_tab .='<tr>';

   // giorni della settimana
   for($i = 1; $i < 8; $i++) {
      $my_cal_tab .= '<th>'.$days[$i%7].'</th>';
   }
   $TempD = 1;
   if(date('w', mkdate($M, 1, $Y)) != 1) {
      $my_cal_tab .= '</tr><tr>';
      $tmp = '';
      while(date('w', mkdate($M, $TempD, $Y)) != 1) {
         $TempD--;
         $case = '<td>';
         $case .= date('j', mkdate($M, $TempD, $Y));
         $date = date('Ymd', mkdate($M, $TempD, $Y));

         $case .= '</td>';
         $tmp = $case.$tmp;
      }
      $my_cal_tab .= $tmp;
   }
   $TempD = 1;
   while((date('m', mkdate($M, $TempD, $Y)) == $M) || (date('w', mkdate($M, $TempD, $Y)) != 1)) {
      if(date('w', mkdate($M, $TempD, $Y)) == 1) {
         $my_cal_tab .= '</tr><tr>';
      }
      $my_cal_tab .= '<td '.(date('Ymd', mkdate($M, $TempD, $Y)) == date('Ymd') ? ' class="today"' : '').'>';
      $date = date('Ymd', mkdate($M, $TempD, $Y));
      if (isset($events[$date])) {
         if(1==count($events[$date]))
         {
            $my_cal_tab .= '<a href="'.$events[$date][0]['link'].'" title="'.$events[$date][0]['title'].'" >'. date('j', mkdate($M, $TempD, $Y)) .'</a>';
         }
         else{
            $my_cal_tab .= '<ul class="multievent" ><li><a href="">'. date('j', mkdate($M, $TempD, $Y)) ."</a>\n<ul>\n";
               foreach($events[$date] as $myevent){
                  $my_cal_tab .= '<li><a href="'.$myevent['link'].'" title="'.$myevent['title'].'" >'.$myevent['title']."</a></li>\n";
               }
            $my_cal_tab .= '</ul></li></ul>';
         }
      }
      else {
      $my_cal_tab .= date('j', mkdate($M, $TempD, $Y));
      }
      $my_cal_tab .= '</td>';
      $TempD++;
   }
   $my_cal_tab .="\n</tr>\n</table>\n";
   //fine tabella giorni mese";

   if (count($events)>0)
   {
   ksort($events);
  // $my_balise .= '<table width="100%"  border="0" cellspacing="0" cellpadding="2">';
  // $my_balise .="\n<tr>\n<td>\n";
   $my_event_tab .='<table id="events" border="0" cellpadding="0" cellspacing="0">';
        $test_boucle=0;
        if($M<10) $Mi = '0'.$M;
        else $Mi = $M;
        // list_starting_day
        if ( (intval(date('m', time()))==$M) AND (intval(date('Y', time()))==$Y) )$s_d=$D;
        else $s_d=1;
        for ($i=$s_d;$i<32;$i++)
        {
         if($i<10) $i='0'.$i;
         $datai=$Y.$Mi.$i;
         if(isset($events[$datai]))
         {
            foreach($events[$datai] as $myevent){
                $my_event_tab .= "<tr>
                    <td class=\"date\">$i-$M: </td>
                    <td class=\"eventtitle\"><a href=\"{$myevent[link]}\" >{$myevent[title]}</a>
                    </td>
                    </tr>";
                }
                $test_boucle++;
            }

        }
          IF ($test_boucle==0) {
          $my_event_tab .= "<tr>
                <td width='100%' align='center' valign='top'>"._T('abcalendrier:aucun_evenement')."</td>
                </tr>";
          }
        $my_event_tab .="\n</table>\n";
   }
   else
   {
      $my_event_tab .= '
      <table width="100%"  border="0" cellspacing="0" cellpadding="1">
        <tr>
          <td align="center" valign="top" style="font-size:80%;">
          '._T('abcalendrier:aucun_evenement').'</td>
        </tr>
      </table>';
   }
   return array('formulaires/abcalendrier', 0,
   array(
      'prec_mois'=>$calendrier_mois_moins,
      'prec_annee'=>$calendrier_annee_moins,
      'moise_annee_curr'=>$mes.'&nbsp;'.$Y,
      'suiv_mois'=>$calendrier_mois_plus,
      'suiv_annee'=>$calendrier_annee_plus,
      'abcal_table'=>$my_cal_tab,
      'event_table'=>$my_event_tab
    ));

}



if ($test_mini_agenda_deja_present!=1) {
function mkdate($month, $day, $year)
{
   return mktime(0, 0, 0, $month, $day, $year);
}

}

function crea_eventi($Y,$M,$D)
{
  include_spip('inc/filtres');
  include_spip('urls/standard');//senza questo non trova generer_url_article() quando la pagina è in cache
  /*
  include_spip('inc/urls');
  include_spip('urls/propres');
  urls\html.php
  urls\page.php
  */
  $my_q="SELECT articles.id_article, articles.date_redac, articles.titre, articles.lang
   FROM spip_mots_articles AS `L1`, spip_mots AS `L2`, spip_articles AS `articles`
   WHERE (L2.titre = 'mini-calendrier')
       AND (YEAR(articles.date_redac) = '$Y')
       AND (MONTH(articles.date_redac) = '$M')
       AND (articles.statut = 'publie')
       AND (articles.date < NOW())
       AND L1.id_mot=L2.id_mot
       AND articles.id_article=L1.id_article
   GROUP BY articles.id_article ORDER BY articles.date_redac";
       // REQUETE
       $result = spip_query($my_q);
       while($article=mysql_fetch_assoc($result))
       {
         $date = ereg_replace("^([0-9]{4})-([0-9]{2})-([0-9]{2}).*$", "\\1\\2\\3", $article['date_redac']);
         if ($date > date("Ymd", mkdate($M, $D - 31, $Y)) && $date < date("Ymd", mkdate($M, $D + 31, $Y))) {
            if (!isset($events[$date])) {
               $events[$date] = array();
            }
            $events[$date][] = array('link' => generer_url_article($article['id_article'], 'prop'), 'title' => supprimer_numero($article['titre']), 'logo' => "");
         }
        }

  $my_q="SELECT breves.id_breve, breves.evento, breves.titre, breves.lang
   FROM spip_mots_breves AS `L1`, spip_mots AS `L2`, spip_breves AS `breves`
   WHERE (L2.titre = 'mini-calendrier')
       AND (YEAR(breves.evento) = '$Y')
       AND (MONTH(breves.evento) = '$M')
       AND (breves.statut = 'publie')
       AND (breves.date_heure < NOW())
       AND L1.id_mot=L2.id_mot
       AND breves.id_breve=L1.id_breve
   GROUP BY breves.id_breve ORDER BY breves.evento";
          $result = spip_query($my_q);
          while($article=mysql_fetch_assoc($result))
          {
            $date = ereg_replace("^([0-9]{4})-([0-9]{2})-([0-9]{2}).*$", "\\1\\2\\3", $article['evento']);
            if ($date > date("Ymd", mkdate($M, $D - 31, $Y)) && $date < date("Ymd", mkdate($M, $D + 31, $Y))) {
               if (!isset($events[$date])) {
                  $events[$date] = array();
               }
               $events[$date][] = array('link' => generer_url_breve($article['id_breve'], 'prop'), 'title' => supprimer_numero($article['titre']), 'logo' => "");
            }
           }
return $events;
}
?>