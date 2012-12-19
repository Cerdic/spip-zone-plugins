<?php
/**
 *      @file formulaires/courbe_remous.php
 *      Fonctions du formulaire CVT pour les courbes de remous
 */

/*      Copyright 2009-2012 Dorch <dorch@dorch.fr>, Médéric Dulondel
 *
 *      This program is free software; you can redistribute it and/or modify
 *      it under the terms of the GNU General Public License as published by
 *      the Free Software Foundation; either version 2 of the License, or
 *      (at your option) any later version.
 *
 *      This program is distributed in the hope that it will be useful,
 *      but WITHOUT ANY WARRANTY; without even the implied warranty of
 *      MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *      GNU General Public License for more details.
 *
 *      You should have received a copy of the GNU General Public License
 *      along with this program; if not, write to the Free Software
 *      Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 *      MA 02110-1301, USA.
 */

include_spip('hyd_inc/section');

/* Tableau des champs à afficher dans le formulaire.
 * On travaille avec les libelles non traduits pour pouvoir gérer
 * le multilinguisme.
 */
function mes_saisies_section() {

    // On récupère les champs communs à tous les formulaires à savoir les champs de section.
    $fieldset_champs = caract_communes();

    $fieldset_champs['Caract_bief'] = array(
                                           'caract_bief',
                                           array(
                                                 'rKs'    =>array('coef_strickler',50),
                                                 'rLong'  =>array('longueur_bief', 50),
                                                 'rIf'    =>array('pente_fond', 0.005),
                                                 'rYBerge'=>array('h_berge',1)
                                                )
                                       );

    $fieldset_champs['Cond_lim']    = array(
                                           'condition_limite',
                                           array(
                                                 'rQ'     =>array('debit_amont', 2),
                                                 'rYaval' =>array('h_aval_imposee', 0.6),
                                                 'rYamont'=>array('h_amont_imposee', 0.15)
                                                )
                                       );

    $fieldset_champs['Param_calc']  = array(
                                           'param_calcul',
                                           array(
                                                 'rDx'    =>array('pas_discret', 5),
                                                 'rPrec'  =>array('precision_calc', 0.001)
                                                )
                                       );

  return $fieldset_champs;

}

// Définition des champs obligatoires pour le formulaire.
function champs_obligatoires() {

    $tSaisie = mes_saisies_section();
    $sTypeSection = _request('crTypeSection');
    $tChOblig = array();

    foreach($tSaisie as $IdFS=>$FieldSet) {
        // Si ce n'est pas une section ou la section définie...
        if((substr($IdFS,0,1) != 'F') || ($IdFS == $sTypeSection)){
            // ... alors on parcourt notre deuxième tableau en ajoutant les champs nécessaires.
            foreach($FieldSet[1] as $Cle=>$Champ) {
                if((!isset($Champ[2])) || (isset($Champ[2]) && $Champ[2])) {
                    $tChOblig[] = $IdFS.'_'.$Cle;
                }
            }
        }
    }
    return $tChOblig;
}

function formulaires_courbe_remous_charger_dist() {
    // On charge les saisies et les champs qui nécessitent un accès par les fonctions
    $tSaisie_section = mes_saisies_section();
    $valeurs = array(
        'crTypeSection' => 'FT',
        'mes_saisies' => $tSaisie_section
    );

	// On charge tous les champs avec leur valeur
    foreach($tSaisie_section as $CleFD=>$FieldSet) {
        foreach($FieldSet[1] as $Cle=>$Champ) {
			$valeurs[$CleFD.'_'.$Cle] = $Champ[1];
        }
    }

    return $valeurs;
}

function formulaires_courbe_remous_verifier_dist(){
    $erreurs = array();
    $datas = array();
    $tChOblig= champs_obligatoires();

    // On vérifie que les champs obligatoires sont bien là :
    foreach($tChOblig as $obligatoire) {
        if (!_request($obligatoire)) {
            $erreurs[$obligatoire] = _T('hydraulic:champ_obligatoire');}
        else {
            $datas[$obligatoire] = _request($obligatoire);
        }
    }

	// Gestion des valeurs négatives
    foreach($datas as $champ=>$data) {
        if ($data < 0) $erreurs[$champ] = _T('hydraulic:valeur_positive');
    }

	// On compte s'il y a des erreurs. Si oui, alors on affiche un message
    if (count($erreurs)) {
        $erreurs['message_erreur'] = _T('hydraulic:saisie_erreur');
    }

    return $erreurs;
}

function formulaires_courbe_remous_traiter_dist(){
    global $spip_lang;
    include_spip('hyd_inc/section.class');
    include_spip('hyd_inc/cache');
    include_spip('hyd_inc/log.class');
    include_spip('hyd_inc/courbe_remous');
    include_spip('hyd_inc/graph.class');

    $datas = array();
    $echo = '';
    $tSaisie = mes_saisies_section();
    $tChUtil = array();
    $crTypeSection = _request('crTypeSection');

	// On récupère tous les champs utiles, à savoir les champs fixes, et les champs appartenant à la section choisie
    foreach($tSaisie as $IdFS=>$FieldSet) {
        if((substr($IdFS,0,1) != 'F') || ($IdFS == $crTypeSection)){
            foreach($FieldSet[1] as $Cle=>$Champ) {
				$tChUtil[] = $IdFS.'_'.$Cle;
            }
        }
    }

    //On récupère tous les champs utiles dans le tableau datas
    foreach($tChUtil as $champ) {
        if (_request($champ)){
            $datas[$champ] = _request($champ);
        }

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

    // Contrôle du nombre de pas d'espace maximum
    $iPasMax = 1000;
    if($Caract_bief_rLong / $Param_calc_rDx > $iPasMax) {
        $Param_calc_rDx = $Caract_bief_rLong / $iPasMax;
        $oLog->Add(_T('hydraulic:pas_nombre').' > '.$iPasMax.' => '._T('hydraulic:pas_ajustement').$Param_calc_rDx.' m');
    }
    //spip_log(array($Cond_lim_rYaval,$Caract_bief_rKs,$Cond_lim_rQ,$Caract_bief_rLong,$Caract_bief_rIf,$Param_calc_rDx,$Param_calc_rPrec),'hydraulic');

    // Enregistrement des paramètres dans les classes qui vont bien
    $oParam= new cParam($Caract_bief_rKs,$Cond_lim_rQ,$Caract_bief_rIf,$Param_calc_rPrec,$Caract_bief_rYBerge,$Cond_lim_rYaval,$Param_calc_rDx,$Caract_bief_rLong);

	// Création d'un objet de type Section selon la section choisie.
    switch($crTypeSection) {
        case 'FT':
            include_spip('hyd_inc/sectionTrapez.class');
            $oSection=new cSnTrapez($oLog,$oParam,$FT_rLargeurFond,$FT_rFruit);
            break;

        case 'FR':
            include_spip('hyd_inc/sectionRectang.class');
            $oSection=new cSnRectang($oLog,$oParam,$FR_rLargeurBerge);
            break;

        case 'FC':
            include_spip('hyd_inc/sectionCirc.class');
            $oSection=new cSnCirc($oLog,$oParam,$FC_rD);
            break;

        case 'FP':
            include_spip('hyd_inc/sectionPuiss.class');
            $oSection=new cSnPuiss($oLog,$oParam,$FP_rCoef,$FP_rLargeurBerge);
            break;

        default:
            include_spip('hyd_inc/sectionTrapez.class');
            $oSection=new cSnTrapeze($oLog,$oParam,$FT_rLargeurFond,$FT_rFruit);
    }

    /***************************************************************************
    *                        Calcul de la ligne d'eau
    ****************************************************************************/
    $bNoCache = true; // true pour débugage
    if(!$bNoCache && is_file(HYD_CACHE_DIRECTORY.$CacheFileName)) {
        // On récupère toutes les données dans un cache déjà créé
        list($tr,$sLog,$oSection->rHautCritique,$oSection->rHautNormale) = ReadCacheFile($CacheFileName);
    }
    else {
        // On calcule les données pour créer un cache et afficher le résultat
        $oLog->Add(_T('hydraulic:largeur_berge').' = '.format_nombre($oSection->rLargeurBerge,$oParam->iPrec).' m');
        $oLog->Add(_T('hydraulic:h_critique').' = '.format_nombre($oSection->CalcGeo('Yc'),$oParam->iPrec).' m');
        $oLog->Add(_T('hydraulic:h_normale').' = '.format_nombre($oSection->CalcGeo('Yn'),$oParam->iPrec).' m');

        // Calcul depuis l'aval
        if($oSection->rHautCritique <= $Cond_lim_rYaval) {
            $oLog->Add(_T('hydraulic:calcul_fluvial'));
            list($tr['X1'],$tr['Y1']) = calcul_courbe_remous($oParam,$oSection,$oLog,$oParam->iPrec);
        }
        else {
            $oLog->Add(_T('hydraulic:pas_calcul_depuis_aval'));
        }

        // Calcul depuis l'amont
        if($oSection->rHautCritique >= $Cond_lim_rYamont) {
            $oLog->Add(_T('hydraulic:calcul_torrentiel'));
            $oParam->rYCL = $Cond_lim_rYamont; // Condition limite amont
            $oParam->rDx = -$oParam->rDx; // un pas négatif force le calcul à partir de l'amont
            list($tr['X2'],$tr['Y2']) = calcul_courbe_remous($oParam,$oSection,$oLog,$oParam->iPrec);
        }
        else {
            $oLog->Add(_T('hydraulic:pas_calcul_depuis_amont'));
        }

        //Production du journal de calcul
        $sLog = $oLog->Result();
        //Enregistrement des données dans fichier cache
        WriteCacheFile($CacheFileName,array($tr,$sLog,$oSection->rHautCritique,$oSection->rHautNormale));
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
    // Cote des berges
    $oGraph->AddSerie(
        'berge',
        $trX,
        $oParam->rYB,  // La cote des berges sera calculée à partir de la pente fournie dans GetGraph
        '#C58f50',
        'lineWidth:1');
    // Cote du fond
    $oGraph->AddSerie(
        'fond',
        $trX,
        0,  // La cote du fond sera calculée à partir de la pente fournie dans GetGraph
        '#753f00',
        'lineWidth:1, fill:true');
   // Ligne d'eau fluviale
    if(isset($tr['Y1'])) {
        $oGraph->AddSerie(
            'ligne_eau_fluviale',
            $tr['X1'],
            $tr['Y1'],
            '#0093bd',
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
    // Hauteur critique
    $oGraph->AddSerie(
        'h_critique',
        $trX,
        $oSection->rHautCritique,  // La cote du fond sera calculée à partir de la pente fournie dans GetGraph
        '#ff0000',
        'lineWidth:2');
    // Hauteur normale
    $oGraph->AddSerie(
        'h_normale',
        $trX,
        $oSection->rHautNormale,  // La cote du fond sera calculée à partir de la pente fournie dans GetGraph
        '#a4c537',
        'lineWidth:2');

    // Décalage des données par rapport au fond
    $oGraph->Decal(0, $Caract_bief_rIf, $Caract_bief_rLong);

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
        $echo.='<tr class="align_right ';
        $echo.=($i%2==0)?'row_even':'row_odd';
        $echo.='"><td>'.format_nombre($rX,$oParam->iPrec).'</td>';
        if(isset($tr['X1']) && !(($cle = array_search($rX,$tr['X1'])) === false)) {
			// On formalise les résultats, avec le nombre de chiffres aprés la virgule adéquat
            $echo .= '<td>'.format_nombre($tr['Y1'][$cle],$oParam->iPrec).'</td>';
            $echo .= '<td>'.format_nombre($oSection->Calc('Fr', $tr['Y1'][$cle]),$oParam->iPrec).'</td>';
        }
        else {
            $echo .= '<td></td><td></td>';
        }
        if(isset($tr['X2']) && !(($cle = array_search($rX,$tr['X2'])) === false)) {
            $echo .= '<td>'.format_nombre($tr['Y2'][$cle],$oParam->iPrec).'</td>';
            $echo .= '<td>'.format_nombre($oSection->Calc('Fr', $tr['Y2'][$cle]),$oParam->iPrec).'</td>';
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
