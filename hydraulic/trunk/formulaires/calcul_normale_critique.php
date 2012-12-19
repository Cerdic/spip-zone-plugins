<?php
/**
 *      @file formulaires/calcul_normale_critique.php
 *      Formulaire CVT pour les calculs des paramètres hydrauliques d'une section
 */

/*      Copyright 2012 Médéric Dulondel
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


/*
 * Découpe le champ $champ en plusieurs morceaux séparés par '_'
 * Ce découpage s'effectue à partir du $deb underscore et est de longueur $lg
 * Par exemple :
 * $test =  'petit_test_de_decoupage'
 * id_decoupe($test, 1, 2) renvoit 'test_de'
 */
function id_decoupe($champ,$deb, $lg){
    $decoup = explode('_', $champ);
    $val = '';
    $selectElmt = array_slice($decoup, $deb, $lg);
    $val = join("_", $selectElmt);

    return $val;
}

include_spip('hyd_inc/section');

/*
 * Contient tous les champs relatifs au formulaire.
 * Les champs communs sont localisés ici: hyd_inc/section.php
 */
function mes_saisies_normale_critique(){

    $fieldset_champs_nc = caract_communes();

    $fieldset_champs_nc['Cr'] = array(
                                           'caract_globale',
                                           array(
                                                 'rKs'      =>array('rugosite_nc',50),
                                                 'rIf'      =>array('pente_fond', 0.001),
                                                 'rQ'       =>array('debit', 1.2),
                                                 'rYB'      =>array('h_berge', 1),
                                                 'rY'       =>array('tirant_eau', 1)
                                                )
                                         );


  return $fieldset_champs_nc;
}

function champs_select_calculer(){
    $champs_select_calc = array(
        'Hs'   => 'charge_spe',
        'Hsc'  => 'charge_critique',
        'B'    => 'larg_miroir',
        'P'    => 'perim_mouille',
        'S'    => 'surf_mouille',
        'R'    => 'rayon_hyd',
        'V'    => 'vit_moy',
        'Fr'   => 'froud',
        'Yc'   => 'tirant_eau_crit',
        'Yn'   => 'tirant_eau_norm',
        'Yf'   => 'tirant_eau_fluv',
        'Yt'   => 'tirant_eau_torr',
        'Yco'  => 'tirant_eau_conj',
        'J'    => 'perte_charge',
        'I-J'  => 'var_lin',
        'Imp'  => 'impulsion',
        'Tau0' => 'force_tract'
    );

    return $champs_select_calc;
}

function champs_obligatoires_nc() {

    $tSaisie = mes_saisies_normale_critique();
    $tChOblig = array();
    $sTypeSection = _request('ncTypeSection');
    // Cette variable va contenir le nom de la variable qui varie s'il y en a une.
    $ValVar = '';

    foreach($tSaisie as $IdFS=>$FieldSet) {
        // Si ce n'est pas une section ou alors celle selectionnée...
        if((substr($IdFS,0,1) != 'F') || ($IdFS == $sTypeSection)){
            foreach($FieldSet[1] as $Cle=>$Champ) {
                //... alors on enresgistre les champs
                if(substr(_request('choix_champs_'.$IdFS.'_'.$Cle), 0, 3) == 'var'){
                    $ValVar = $IdFS.'_'.$Cle;
                }
                if((!isset($Champ[2])) || (isset($Champ[2]) && $Champ[2])) {
                    $tChOblig[] = $IdFS.'_'.$Cle;
                }
            }
        }
    }

    if($ValVar != ''){
        foreach($tChOblig as $cle=>$valeur){
            if($valeur == $ValVar){
                unset($tChOblig[$cle]);
                $tChOblig = array_values($tChOblig);
                $tChOblig [] = 'val_min_'.$valeur;
                $tChOblig [] = 'val_max_'.$valeur;
                $tChOblig [] = 'pas_var_'.$valeur;
            }
        }
    }

   //On ajoute rPrec_nc car il ne fait pas partie des saisies de sections.
    $tChOblig[] = 'Param_calc_rPrec';

    return $tChOblig;
}

function formulaires_calcul_normale_critique_charger_dist() {
    // On charge les saisies et les champs qui nécessitent un accès par les fonctions
    $tSaisie_nc = mes_saisies_normale_critique();
    $champs_select = champs_select_calculer();

    $valeurs = array(
        'ncTypeSection' => 'FT',
        'mes_saisies'   => $tSaisie_nc,
        'val_a_cal_nc'  => 'Hs',
        'Param_calc_rPrec' => 0.001,
        'choix_champs_select' => $champs_select
    );

    foreach($tSaisie_nc as $CleFD=>$FieldSet) {
        foreach($FieldSet[1] as $Cle=>$Champ) {
            $valeurs[$CleFD.'_'.$Cle] = $Champ[1];
            $valeurs['choix_champs_'.$CleFD.'_'.$Cle] = 'val_fixe_'.$CleFD.'_'.$Cle;
            if($Cle == 'rIf'){
                $valeurs['val_min_'.$CleFD.'_'.$Cle] = 0.001;
                $valeurs['val_max_'.$CleFD.'_'.$Cle] = 0.005;
                $valeurs['pas_var_'.$CleFD.'_'.$Cle] = 0.001;
            }
            else {
                $valeurs['val_min_'.$CleFD.'_'.$Cle] = 1;
                $valeurs['val_max_'.$CleFD.'_'.$Cle] = 2;
                $valeurs['pas_var_'.$CleFD.'_'.$Cle] = 0.1;
            }
        }
    }

    return $valeurs;
}

function formulaires_calcul_normale_critique_verifier_dist(){
    $erreurs = array();
    $datas = array();
    $tChOblig= champs_obligatoires_nc();
    // Vérifier que les champs obligatoires sont bien là :
    foreach($tChOblig as $obligatoire) {
        if (_request($obligatoire) == NULL) {
            $erreurs[$obligatoire] = _T('hydraulic:champ_obligatoire');
        }
        else {
            $datas[$obligatoire] = _request($obligatoire);
        }
    }

    // Gestion des valeurs négatives
    foreach($datas as $champ=>$data){
        if($data < 0 && !strstr($champ, 'Cr_rIf')) $erreurs[$champ] = _T('hydraulic:valeur_positive_nulle');
    }

    if (count($erreurs)) {
        $erreurs['message_erreur'] = _T('hydraulic:saisie_erreur');
    }

    return $erreurs;
}


function formulaires_calcul_normale_critique_traiter_dist(){
    global $spip_lang;
    include_spip('hyd_inc/cache');
    include_spip('hyd_inc/log.class');
    include_spip('hyd_inc/graph.class');
    include_spip('hyd_inc/section.class');
    include_spip('hyd_inc/dessinSection.class');

    $datas = array();
    $echo = '';
    $tChUtil = champs_obligatoires_nc();
    $ncTypeSection = _request('ncTypeSection');
    $tVarCal = array();
    $VarVar = '';
    $result = array();
    $champs_select_nc = champs_select_calculer();

    foreach($tChUtil as $champ) {
        if (_request($champ)){
            $datas[$champ] = _request($champ);
        }

        if(id_decoupe($champ, 0, 2) == 'pas_var'){
            $VarVar = id_decoupe($champ, 2, 2);
        }
        $datas[$champ] = str_replace(',','.',$datas[$champ]); // Bug #574*/
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

    /***************************************************************************
    *                        Calcul normale critique
    ***************************************************************************/
    $oParam= new cParam($Cr_rKs, $Cr_rQ, $Cr_rIf, $Param_calc_rPrec, $Cr_rYB);
    switch($ncTypeSection) {
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
			$oSection=new cSnPuiss($oLog,$oParam,$FP_rCoef,$FP_rLargBerge);
			break;

		default:
			include_spip('hyd_inc/sectionTrapez.class');
			$oSection=new cSnTrapez($oLog,$oParam,$FT_rLargeurfond,$FT_rFruit);

    }
    $oSection->rY = $Cr_rY;

    $min = 0;
    $max = 0;
    $pas = 1;
    $i = 0;

    $tChampsSect = mes_saisies_normale_critique();
    $champsSection = array();

    foreach($tChampsSect as $IdFS=>$FieldSet){
        if(substr($IdFS, 0, 1) == 'F'){
            foreach($FieldSet[1] as $Cle=>$Champ) {
                $champsSection[] = $IdFS.'_'.$Cle;
            }
        }
    }

    if($VarVar != ''){
        $tVarCal[] = _request('val_a_cal_nc');
        $min = _request('val_min_'.$VarVar);
        $max = _request('val_max_'.$VarVar);
        $pas = _request('pas_var_'.$VarVar);
        $valACalculer = id_decoupe($VarVar, 1, 1);
        if($valACalculer == 'rY' or in_array($VarVar, $champsSection)){
            $oSection->{$valACalculer} = &$i;
        }
        else{
            $oParam->{$valACalculer} = &$i;
        }
    }
    else {
		switch($ncTypeSection) {
			case 'FR':
				$tVarCal = array('Hs', 'Hsc', 'B', 'P', 'S', 'R', 'V', 'Fr', 'Yc', 'Yn', 'Yf', 'Yt', 'Yco', 'J', 'I-J', 'Imp', 'Tau0');
				break;
			default:
				// Le calcul de la hauteur conjuguée n'est pas OK pour les sections autres que rectangulaire
				$tVarCal = array('Hs', 'Hsc', 'B', 'P', 'S', 'R', 'V', 'Fr', 'Yc', 'Yn', 'Yf', 'Yt', 'J', 'I-J', 'Imp', 'Tau0');
		}
    }

    $max += $pas/2;

    $bNoCache = true; // true pour débugage
    if(!$bNoCache && is_file(HYD_CACHE_DIRECTORY.$CacheFileName)) {
        // On récupère toutes les données dans un cache déjà créé
        $result = ReadCacheFile($CacheFileName);
    }
    else{
        for($i = $min; $i <= $max; $i+= $pas){
            $oSection->Reset(true);
            foreach($tVarCal as $sCalc){
                $rY = $oSection->rY;
                if(!in_array($sCalc,array('Yn', 'Yc', 'Hsc'))){
                    $result[] = $oSection->Calc($sCalc);
                }
                else{
                    $result[] = $oSection->CalcGeo($sCalc);
                }
                $oSection->rY = $rY;
            }
        }
        //Enregistrement des données dans fichier cache
        WriteCacheFile($CacheFileName,$result);
    }
    /***************************************************************************
    *                             Une valeur varie
    ****************************************************************************/
    if($VarVar != ''){

    /***************************************************************************
    *                   Affichage du tableau de données
    ****************************************************************************/

        $tabClass = array();
        foreach($tChampsSect as $cleFD=>$champsFD){
            foreach($champsFD[1] as $cle=>$valeur){
                if(substr($cleFD, 0, 1) != 'F' || $cleFD == _request('ncTypeSection')){
                    if(substr(_request('choix_champs_'.$cleFD.'_'.$cle), 0, 3) == 'var'){
                        $tabClass['var'] = $valeur[0];
                    }
                    else if(substr(_request('choix_champs_'.$cleFD.'_'.$cle), 0, 3) != 'var' && _request($cleFD.'_'.$cle)){
                        $tabClass['val'.$i] = $valeur[0];
                        $i++;
                    }
                }
            }
        }

        $var_a_cal = '';
        foreach($champs_select_nc as $cle=>$valeur){
            if($cle == _request('val_a_cal_nc')){
                $var_a_cal = _T('hydraulic:'.$valeur);
            }
        }

        $echo.='<table class="spip">
                <thead>
                    <tr class="row_first">';

                    foreach($tabClass as $cle=>$valeur){
                        if(substr($cle, 0, 3) == 'val'){
                            $echo.= '<th scope="col" rowspan="2" style="text-align:center;">'._T('hydraulic:'.$tabClass[$cle]).'</th>';
                        }
                    }

        $echo.= '       <th style="text-align:center;" scope="col" rowspan="2">('._T('hydraulic:abscisse').')<br/>'._T('hydraulic:'.$tabClass['var']).'</th>
                        <th style="text-align:center;" scope="col" rowspan="2">('._T('hydraulic:ordonnee').')<br/>'.$var_a_cal.'</th>
                    </tr>
                </thead>
                <tbody>';

        $i=0;
        $tabAbs = array();

        $ValeurVarie = $min;

        foreach($result as $indice){
            $i++;
            $echo.= '<tr class="align_right ';
            $echo.=($i%2==0)?'row_even':'row_odd';
            $echo.='">';

                    foreach($datas as $cle=>$valeur){
                        if((substr($cle, 0, 1) == 'F' || substr($cle, 0, 2) == 'Cr') && $valeur != 0){
                            $echo.= '<td>';
                            $echo.= format_nombre($valeur, $oSection->oP->iPrec).'</td>';
                        }
                    }

            $echo.= '<td>'.format_nombre($ValeurVarie, $oSection->oP->iPrec).'</td><td>'.format_nombre($indice, $oSection->oP->iPrec).'</td>';
            $echo.= '</tr>';
            $tabAbs[] = $ValeurVarie;
            $ValeurVarie+= $pas;
        }

        $echo.= '</tbody>
            </table>';

     /***************************************************************************
    *                        Affichage du graphique
    ****************************************************************************/
        if(is_infinite($result[0])){
            unset($result[0]);
            $result = array_values($result);
            unset($tabAbs[0]);
            $tabAbs = array_values($tabAbs);
        }

        $oGraph = new cGraph();
        // Ligne Courbe normale critique
        if(isset($result)) {
            $oGraph->AddSerie(
                $var_a_cal,
                $tabAbs,
                $result,
                '#00a3cd',
                'lineWidth:3, showMarker:true, markerOptions:{style:\'filledCircle\', size:8}');
        }
        // Récupération du graphique
        $echo .= $oGraph->GetGraph('ligne_normale_critique',400,600);
        $echo .= _T('hydraulic:'.$tabClass['var']);

    }

    /***************************************************************************
    *                   Aucune valeur ne varie
    ****************************************************************************/
    else{

    /***************************************************************************
    *                   Affichage du tableau de données
    ****************************************************************************/
        $lib_data = array();
        $test = champs_select_calculer();
        $par = 0;

        foreach($tVarCal as $champ){
            $lib_data[$champ][] = $result[$par];
            $lib_data[$champ][] = $test[$champ];
            $par++;
        }

        $idCla = 0;

        $echo.='<table class="spip" id="tableau_nc" style="display: inline-block;">
                <tbody>';

        foreach($lib_data as $cal=>$datas){
            $idCla++;
            $echo.= '<tr class="align_right ';
            $echo.=($idCla%2==0)?'row_even':'row_odd';
            $echo.='">';
            $echo.= '<td class="varACal">';
            $echo.= $cal.': '._T('hydraulic:'.$datas[1]).'</td>';
            $echo.= '<td>'.format_nombre($datas[0], $oSection->oP->iPrec).'</td>';
            $echo.= '</tr>';

        }
        $echo.= '</tbody>
            </table>';


     /***************************************************************************
    *                        Affichage du graphique
    ****************************************************************************/

        $lib_datas = array();
        $par = 0;

        foreach($tVarCal as $champ){
            if(substr($test[$champ], 0, 6) == 'tirant' || $champ == 'Hs' || $champ == 'Hsc'){
                $lib_datas[$champ] = $result[$par];
            }
            $par++;
        }

        $lib_datas['rYB'] = $oSection->oP->rYB;
        $dessinSection = new dessinSection(250, 200, $oSection, $lib_datas);
        $echo.= $dessinSection->GetDessinSection();
    }

    $res['message_ok'] = $echo;
    return $res;
}
?>

