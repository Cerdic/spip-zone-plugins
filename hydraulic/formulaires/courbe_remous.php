<?php
function formulaires_courbe_remous_charger_dist() {
   $valeurs = array(
      'rLarg'=>2.5,
      'rFruit'=>0.56,
      'rYaval'=>0.6,
      'rYamont'=>0.15,
      'rKs'=>50,
      'rQ'=>2,
      'rLong'=>50,
      'rIf'=>0.005,
      'rDx'=>5,
      'rPrec'=>0.001);

   return $valeurs;
}

function formulaires_courbe_remous_verifier_dist(){
   $erreurs = array();
   $datas = array();
   // verifier que les champs obligatoires sont bien là :
   foreach(array('rLarg','rYaval','rYamont','rKs','rQ','rLong','rIf','rDx','rPrec') as $obligatoire) {
      if (!_request($obligatoire)) {
         $erreurs[$obligatoire] = 'Ce champ est obligatoire';}
      else {
         $datas[$obligatoire] = _request($obligatoire);
      }
   }

   foreach($datas as $champ=>$data)
      if ($data < 0) $erreurs[$champ] = 'La valeur doit être strictement positive';

   if (count($erreurs)) $erreurs['message_erreur'] = 'Votre saisie contient des erreurs !';
   return $erreurs;
}

function formulaires_courbe_remous_traiter_dist(){
   global $spip_lang;
/*
$fdbg = fopen('debug.log','w');
*/
   include_spip('hyd_inc/section.class');

   include_spip('hyd_inc/cache');

   include_spip('hyd_inc/log.class');
   include_spip('hyd_inc/calcul');
   $datas = array();
   $echo = '';
   // On récupère les données
   foreach(array('rLarg','rFruit','rYaval','rYamont','rKs','rQ','rLong','rIf','rDx','rPrec') as $champ)
      if (_request($champ)) $datas[$champ] = _request($champ);

   // On ajoute la langue en cours pour différencier le fichier de cache par langue
   $datas['sLang'] = $spip_lang;

   // Nom du fichier en cache pour calcul déjà fait
   $CacheFileName=md5(serialize($datas));

   // Initialisation de la classe chargée d'afficher le journal de calcul
   $oLog = new cLog();

   //Transformation des variables contenues dans $datas
   foreach($datas as $champ=>$data) {
      ${$champ}=$data;
   }

   // Initialisation du format d'affichage des réels
   $iPrec=(int)-log10($rPrec);

   // Contrôle du nombre de pas d'espace maximum
   $iPasMax = 1000;
   if($rLong / $rDx > $iPasMax) {
      $rDx = $rLong / $iPasMax;
      $oLog->Add(_T('hydraulic:pas_nombre').' > '.$iPasMax.' => '._T('hydraulic:pas_ajustement').$rDx.' m');
   }

   // Enregistrement des paramètres dans les classes qui vont bien
   $oParam= new cParam($rYaval,$rKs,$rQ,$rLong,$rIf,$rDx,$rPrec);
   $oSection=new cSnTrapeze($oParam,$rLarg,$rFruit);

   if(is_file(HYD_CACHE_DIRECTORY.$CacheFileName)) {
      list($tr,$sLog) = ReadCacheFile($CacheFileName);
   }
   else {
      $oLog->Add(_T('hydraulic:h_critique').' = '.format_nombre($oSection->rHautCritique,$iPrec).' m');
      $oLog->Add(_T('hydraulic:h_normale').' = '.format_nombre($oSection->rHautNormale,$iPrec).' m');

      // Calcul depuis l'aval
      if($oSection->rHautCritique <= $rYaval) {
         $oLog->Add(_T('hydraulic:calcul_fluvial'));
         list($tr['X1'],$tr['Y1']) = calcul_courbe_remous($oParam,$oSection,$oLog,$iPrec);
      }
      else {
         $oLog->Add(_T('hydraulic:pas_calcul_depuis_aval'));
      }

      // Calcul depuis l'amont
      if($oSection->rHautCritique >= $rYamont) {
         $oLog->Add(_T('hydraulic:calcul_torrentiel'));
         $oParam->rYCL = $rYamont; // Condition limite amont
         $oParam->rDx = -$oParam->rDx; // un pas négatif force le calcul à partir de l'amont
         list($tr['X2'],$tr['Y2']) = calcul_courbe_remous($oParam,$oSection,$oLog,$iPrec);
      }
      else {
         $oLog->Add(_T('hydraulic:pas_calcul_depuis_amont'));
      }

      //Production du journal de calcul
      $sLog = $oLog->Result();
      //Enregistrement des données dans fichier cache
      WriteCacheFile($CacheFileName,array($tr,$sLog));
   }
   //Construction d'un tableau des indices x combinant les abscisses des 2 lignes d'eau
   $trX = array();
   if(isset($tr['X1'])) $trX = array_merge($trX, $tr['X1']);
   if(isset($tr['X2'])) $trX = array_merge($trX, $tr['X2']);
   $trX = array_unique($trX, SORT_NUMERIC);
   sort($trX, SORT_NUMERIC);
   spip_log($tr,'hydraulic');

   //Affichage du résultat

   $echo .= '<div id="jqplot_courbe_remous" style="height:400px;width:600px; "></div>';
   $echo .= '<script language="javascript" type="text/javascript">';
   if(isset($tr['Y1'])) $echo .= 'var y1Points=[];';
   if(isset($tr['Y2'])) $echo .= 'var y2Points=[];';
   $echo .= 'var fPoints=[];
            var cPoints=[];
            var nPoints=[];';
   $rYmax = 0; // TODO : pour une échelle des ordonnées intelligente
   foreach($trX as $rX) {
      $rCoteFond = $rIf*($rLong-$rX);
      $echo .= 'fPoints.push(['.$rX.', '.$rCoteFond.']);
               cPoints.push(['.$rX.', '.($rCoteFond+$oSection->rHautCritique).']);
               nPoints.push(['.$rX.', '.($rCoteFond+$oSection->rHautNormale).']);';
   }
   if(isset($tr['X1']))
      foreach($tr['X1'] as $cle=>$rX)
         $echo .= ' y1Points.push(['.$rX.', '.($tr['Y1'][$cle]+$rIf*($rLong-$rX)).']);';
   if(isset($tr['X2']))
      foreach($tr['X2'] as $cle=>$rX)
         $echo .= ' y2Points.push(['.$rX.', '.($tr['Y2'][$cle]+$rIf*($rLong-$rX)).']);';
   $echo .= 'chart=$.jqplot(\'jqplot_courbe_remous\',  [';
   if(isset($tr['X1'])) $echo .= 'y1Points,';
   if(isset($tr['X2'])) $echo .= 'y2Points,';
   $echo .= 'fPoints,cPoints,nPoints], {
               seriesDefaults: {showMarker:false},
               seriesColors: [';
   if(isset($tr['X1'])) $echo .= '"#00a3cd",';
   if(isset($tr['X2'])) $echo .= '"#77a3cd",';
   $echo .= '"#753f00", "#ff0000", "#a4c537"],
               series:[';
   if(isset($tr['X1'])) $echo .= '{label:\''.addslashes(_T('hydraulic:ligne_eau_fluviale')).'\', lineWidth:3, showMarker:true, markerOptions:{style:\'filledCircle\', size:8}},';
   if(isset($tr['X2'])) $echo .= '{label:\''.addslashes(_T('hydraulic:ligne_eau_torrentielle')).'\', lineWidth:3, showMarker:true, markerOptions:{style:\'filledCircle\', size:8}},';
   $echo .= '     {label:\''.addslashes(_T('hydraulic:fond')).'\', lineWidth:1, fill:true},
                  {label:\''.addslashes(_T('hydraulic:h_critique')).'\', lineWidth:1},
                  {label:\''.addslashes(_T('hydraulic:h_normale')).'\', lineWidth:1}
               ],
               legend: {show: true, location:\'ne\', fontSize:\'1em\'},
               cursor: {
                    show:true,
                    showVerticalLine: true,
                    showHorizontalLine: true,
                    showCursorLegend: true,
                    showTooltip: false,
                    zoom: true,
                    dblClickReset: false,
                    intersectionThreshold: 6
               },
               axes:{
                  xaxis:{min:0, max:'.$rLong.'},
                  yaxis:{min:0,
                     max:null,
                     tickOptions:{formatString:\'%.3f\'}
                  }
               }
            });
      </script>';
   $echo .= $sLog;

   $echo.='<table class="spip"><thead><tr class="row_first"><th scope="col">'._T('hydraulic:abscisse').' (m)</th>';
   if(isset($tr['Y1'])) $echo .= '<th scope="col">'._T('hydraulic:ligne_eau_fluviale').'</th><th scope="col">Froude</th>';
   if(isset($tr['Y2'])) $echo .= '<th scope="col">'._T('hydraulic:ligne_eau_torrentielle').'</th><th scope="col">Froude</th>';
   $echo.='</thead>
      <tbody>
      ';
   $i=0;
   foreach($trX as $rX) {
      $i+=1;
      $echo.='<tr class="';
      $echo.=($i%2==0)?'row_even':'row_odd';
      $echo.='"><td>'.format_nombre($rX,$iPrec).'</td>';
      if(isset($tr['X1']) && !(($cle = array_search($rX,$tr['X1'])) === false)) {
         $echo .= '<td>'.format_nombre($tr['Y1'][$cle],$iPrec).'</td>';
         $echo .= '<td>'.format_nombre($oSection->ReCalcFr($oParam, $tr['Y1'][$cle]),$iPrec).'</td>';
      }
      else {
         $echo .= '<td></td><td></td>';
      }
/*
      fwrite($fdbg,"cle1=$cle\n");
*/
      if(isset($tr['X2']) && !(($cle = array_search($rX,$tr['X2'])) === false)) {
         $echo .= '<td>'.format_nombre($tr['Y2'][$cle],$iPrec).'</td>';
         $echo .= '<td>'.format_nombre($oSection->ReCalcFr($oParam, $tr['Y2'][$cle]),$iPrec).'</td>';
      }
      else {
         $echo .= '<td></td><td></td>';
      }
/*
      fwrite($fdbg,"cle2=$cle\n");
*/
      $echo .= '</tr>';
   }
   $echo.='</tbody>
      </table>';

   $res['message_ok'] = $echo;


   return $res;
}
?>
