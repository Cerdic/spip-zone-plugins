<?php
/*
 * formulaire/courbe_remous.php
 *
 *
 *
 * Copyright 2012 David Dorchies <dorch@dorch.fr>
 *
 *
 *
 * This program is free software; you can redistribute it and/or modify
 *
 * it under the terms of the GNU General Public License as published by
 *
 * the Free Software Foundation; either version 2 of the License, or
 *
 * (at your option) any later version.
 *
 *
 *
 * This program is distributed in the hope that it will be useful,
 *
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 *
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *
 * GNU General Public License for more details.
 *
 *
 *
 * You should have received a copy of the GNU General Public License
 *
 * along with this program; if not, write to the Free Software
 *
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 *
 * MA 02110-1301, USA.
 *
 */

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
            $erreurs[$obligatoire] = _T('hydraulic:champ_obligatoire');}
        else {
            $datas[$obligatoire] = _request($obligatoire);
        }
    }

    foreach($datas as $champ=>$data) {
        if ($data < 0) $erreurs[$champ] = _T('hydraulic:valeur_positive');
    }

    if (count($erreurs)) {
        $erreurs['message_erreur'] = _T('hydraulic:saisie_erreur');
    }
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
    include_spip('hyd_inc/graph.class');

    $datas = array();
    $echo = '';
    // On récupère les données
    foreach(array('rLarg','rFruit','rYaval','rYamont','rKs','rQ','rLong','rIf','rDx','rPrec') as $champ) {
        if (_request($champ)) $datas[$champ] = _request($champ);
        $datas[$champ] = str_replace(',','.',$datas[$champ]); // Bug #574
    }

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


    /***************************************************************************
    *                        Calcul de la ligne d'eau
    ****************************************************************************/
    if(is_file(HYD_CACHE_DIRECTORY.$CacheFileName)) {
        // On récupère toutes les données dans un cache déjà créé
        list($tr,$sLog) = ReadCacheFile($CacheFileName);
    }
    else {
        // On calcule les données pour créer un cache et afficher le résultat
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
    //~ spip_log($tr,'hydraulic'); // Debug


    /***************************************************************************
    *                        Affichage du graphique
    ****************************************************************************/
    $oGraph = new cGraph();
    // Ligne d'eau fluviale
    if(isset($tr['Y1'])) {
        $oGraph->AddSerie(
            'ligne_eau_fluviale',
            $tr['X1'],
            $tr['Y1'],
            '#00a3cd',
            'lineWidth:3, showMarker:true, markerOptions:{style:\'filledCircle\', size:8}');
    }
    // Ligne d'eau torrentielle
    if(isset($tr['Y2'])) {
        $oGraph->AddSerie(
            'ligne_eau_torrentielle',
            $tr['X2'],
            $tr['Y2'],
            '#77a3cd',
            'lineWidth:3, showMarker:true, markerOptions:{style:\'filledCircle\', size:8}');
    }
    // Cote du fond
    $oGraph->AddSerie(
        'fond',
        $trX,
        0,  // La cote du fond sera calculée à partir de la pente fournie dans GetGraph
        '#753f00',
        'lineWidth:1, fill:true');
    // Hauteur critique
    $oGraph->AddSerie(
        'h_critique',
        $trX,
        $oSection->rHautCritique,  // La cote du fond sera calculée à partir de la pente fournie dans GetGraph
        '#ff0000',
        'lineWidth:1');
    // Hauteur normale
    $oGraph->AddSerie(
        'h_normale',
        $trX,
        $oSection->rHautNormale,  // La cote du fond sera calculée à partir de la pente fournie dans GetGraph
        '#a4c537',
        'lineWidth:1');

    // Décalage des données par rapport au fond
    $oGraph->Decal(0, $rIf, $rLong);

    // Récupération du graphique
    $echo .= $oGraph->GetGraph('courbe_remous',400,600);

    $echo .= $sLog;

    /***************************************************************************
    *                   Affichage du tableau de données
    ****************************************************************************/
    $echo.='<table class="spip">
        <thead>
            <tr class="row_first">
                <th scope="col" colspan="1" rowspan="2">'._T('hydraulic:abscisse').' (m)</th>
                <th scope="col" colspan="2" rowspan="1">'._T('hydraulic:ligne_eau_fluviale').'</th>
                <th scope="col" colspan="2" rowspan="1">'._T('hydraulic:ligne_eau_torrentielle').'</th>
            </tr>
            <tr class="row_first">
                <th scope="col">'._T('hydraulic:tirant_eau').'</th>
                <th scope="col">Froude</th>
                <th scope="col">'._T('hydraulic:tirant_eau').'</th>
                <th scope="col">Froude</th>
            </tr>
                <th></th>
        </thead>
        <tbody>';
    $i=0;
    foreach($trX as $rX) {
        $i+=1;
        $echo.='<tr class="';
        $echo.=($i%2==0)?'row_even':'row_odd';
        $echo.='"><td>'.format_nombre($rX,$iPrec).'</td>';
        if(isset($tr['X1']) && !(($cle = array_search($rX,$tr['X1'])) === false)) {
            $echo .= '<td>'.format_nombre($tr['Y1'][$cle],$iPrec).'</td>';
            $echo .= '<td>'.format_nombre($oSection->Calc('Fr', $tr['Y1'][$cle]),$iPrec).'</td>';
        }
        else {
            $echo .= '<td></td><td></td>';
        }
        if(isset($tr['X2']) && !(($cle = array_search($rX,$tr['X2'])) === false)) {
            $echo .= '<td>'.format_nombre($tr['Y2'][$cle],$iPrec).'</td>';
            $echo .= '<td>'.format_nombre($oSection->Calc('Fr', $tr['Y2'][$cle]),$iPrec).'</td>';
        }
        else {
            $echo .= '<td></td><td></td>';
        }
        $echo .= '</tr>';
    }
    $echo.='</tbody>
        </table>';

    $res['message_ok'] = $echo;

    return $res;
}
?>
