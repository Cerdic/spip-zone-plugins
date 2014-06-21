<?php
/**
 *      @file formulaires/calcul_ouvrages.php
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

function numLoi(){

    $numLoi = array(
        1 => 'or_cemagref88',
        //2 => 's_cemagref88',
        3 => 's_denoye',
        4 => 's_noye',
        5 => 'v_denoye',
        6 => 'v_noye',
        7 => 'cunge',
        8 => 'or_cemagref02',
        9 => 's_cemagref02'
    );

    return $numLoi;
}

function champsLib(){

    $libVar = array(
        'L'  => array('param_L', 2),
        'W'  => array('param_W', 0.5),
        'C'  => array('param_C', 0.4),
        'LF' => array('param_LF', 2),
        'CR' => array('param_CR', 0.4),
        'CT' => array('param_CT', 0.5),
        'F'  => array('param_F', 0.56)
    );

    return $libVar;
}

function mes_saisies_surverse(){
    $num_equation = array(1, 2, 3, 4, 7);
    $numLoi = numLoi();
    $surverse = array(
        'H'    => array('param_H', 1),
        'CS'    => array('param_C', 0.4)
    );

    foreach($num_equation as $valeur){
        $surverse['loi_debit'][] = array($valeur, $numLoi[$valeur]);
    }

    return $surverse;
}

function mes_saisies_caract_fixe(){
    $caract_fixe = array(
        array(
            'caract_globale',
             array(
                    'Q'    =>array('param_Q', 2),
                    'ZM'   =>array('param_ZM', 1.5),
                    'ZV'   =>array('param_ZV', 1),
                  )
        ),

        array(
            'param_calcul',
             array(
                    'rPrec' =>array('precision', 0.001)
                  )
        )
    );

    return $caract_fixe;
}

function mes_saisies_ouvrages(){
    $numLoi = numLoi();
    $champsLib = champsLib();
    $mes_saisies = array(
        'vRect' => array(
            'vanne_rect',
            array( 'L', 'W', 'C'),
            array(1, 2, 5, 6, 7),
            true
        ),

        'vCirc' => array(
            'vanne_circ',
            array( 'L', 'W', 'C'),
            array(1, 2, 5, 6, 7),
            true
        ),

        'sRect' => array(
            'seuil_rect',
            array( 'L', 'C'),
            array(1, 2, 3, 4, 7),
            false
        ),

        'sTrap' => array(
            'seuil_trap',
            array( 'LF', 'CR', 'CT', 'F'),
            array(8,9),
            false
        ),

        'vTrap' => array(
            'vanne_trap',
            array( 'LF', 'CR', 'CT', 'F'),
            array(8,9),
            true
        ),
    );

    $mes_saisies_ouv = array();

    foreach($mes_saisies as $cleF=>$valeurTab){
        $mes_saisies_ouv[$cleF][0] = $valeurTab[0];
        foreach($valeurTab[1] as $valeur){
            $mes_saisies_ouv[$cleF][1][$valeur]= $champsLib[$valeur];
        }
        foreach($valeurTab[2] as $valeur){
            $mes_saisies_ouv[$cleF][2][]= array($valeur, $numLoi[$valeur]);
        }
        $mes_saisies_ouv[$cleF][3] = $valeurTab[3];
    }
    return $mes_saisies_ouv;

}

function champs_obligatoires($bCalc = false){
    /*
     * Ce tableau contient la liste de tous les champs du formulaire.
     * La suite de cette fonction se chargera de supprimer les valeurs non obligatoires.
     */
    spip_log('champs_obligatoires','hydraulic');
    $nTypeOuv = _request('OuvrageType');
    $mes_saisies = mes_saisies_ouvrages();
    $tChOblig = array('OuvrageType','OuvrageLoi');
    $tChCalc = array_keys($mes_saisies[$nTypeOuv][1]); // Champs dépendants du type d'ouvrage
    if(_request('SurverseEnabled')) {
        // Si la surverse est sélectionnée, on ajoute les champs concernant la surverse
        array_push($tChCalc,'H','CS');
        $tChOblig[] = 'SurverseLoi';
    }
    // On ajoute les caractéristiques fixes quelque soit le type d'ouvrage
    $mes_saisies = mes_saisies_caract_fixe();
    $tChCalc = array_merge($tChCalc,array_keys($mes_saisies[0][1]));
    $tChCalc = array_merge($tChCalc,array_keys($mes_saisies[1][1]));
    $tChOblig[] = 'rPrec';
    $tChOblig = array_merge($tChOblig,$tChCalc);

    $choix_champs = array();
    foreach($tChCalc as $cle){
        $choix_champs[$cle] = _request('choix_champs_'.$cle);
    }

    foreach($choix_champs as $cle=>$valeur){
        spip_log('Choix champ '.$cle.'=>'.$valeur,'hydraulic');
        // Si le choix du select est de calculer une valeur...
        if(substr($valeur, 0,3) != 'val'){
            foreach($tChOblig as $cle1=>$valeur1){
                if($cle == $valeur1){
                    // ... alors on peut supprimer de notre tableau le champs calculé (il n'est pas obligatoire car grisé)
                    //unset($tChOblig[$cle1]);
                    // Renumérotation des clés du tableau
                    $tChOblig = array_values($tChOblig);
                }
            }
        }
        // Si le choix du select est de faire varier une valeur alors on ajoute les 3 champs nécessaires
        if(substr($valeur, 0, 3) == 'var'){
            $tChOblig[] = 'val_min_'.$cle;
            $tChOblig[] = 'val_max_'.$cle;
            $tChOblig[] = 'pas_var_'.$cle;
        }
    }
    spip_log($tChOblig,'hydraulic');
    spip_log($tChCalc,'hydraulic');
    if($bCalc) {
        return $tChCalc;
    }
    else {
        return $tChOblig;
    }
}


function formulaires_calcul_ouvrages_charger_dist() {
    // On charge les saisies et les champs qui nécessitent un accès par les fonctions
    $choixOuv = mes_saisies_ouvrages();
    $champsLib = champsLib();
    $numLoi = numLoi();
    $champs_surverse = mes_saisies_surverse();
    $caract_fixes = mes_saisies_caract_fixe();
    $valeurs = array(
        'OuvrageType' => 'vRect',
        'OuvrageLoi'=> 1,
        'SurverseLoi' => 1,
        'SurverseEnabled' => _request('SurverseEnabled'),
        'mes_saisies'  => $choixOuv,
        'OuvrageCaract' => $champsLib,
        'numLoi'       => $numLoi,
        'surverse'     => $champs_surverse,
        'caract_fixes' => $caract_fixes
    );

    foreach($choixOuv as $cleF=>$valeurF){
        $cpt = 0;
        foreach($valeurF[1] as $cle=>$valeur){
            if($cpt == 0){
                $valeurs['choix_champs_'.$cle] = 'calcul_val_'.$cle;
                $cpt++;
            }
            else{
                $valeurs['choix_champs_'.$cle] = 'val_fixe_'.$cle;
            }
            $valeurs[$cle] = $valeur[1];
            $valeurs['val_min_'.$cle] = 1;
            $valeurs['val_max_'.$cle] = 2;
            $valeurs['pas_var_'.$cle] = 0.1;
        }
    }

    foreach($champs_surverse as $cle=>$valeur){
        if($cle !== 'loi_debit'){
            $valeurs[$cle] = $valeur[1];
            $valeurs['choix_champs_'.$cle] = 'val_fixe_'.$cle;
            $valeurs['val_min_'.$cle] = 1;
            $valeurs['val_max_'.$cle] = 2;
            $valeurs['pas_var_'.$cle] = 0.1;
        }
    }

    foreach($caract_fixes as $cleF=>$valeurF){
        foreach($valeurF[1] as $cle=>$valeur){
            $valeurs[$cle] = $valeur[1];
            $valeurs['choix_champs_'.$cle] = 'val_fixe_'.$cle;
            $valeurs['val_min_'.$cle] = 1;
            $valeurs['val_max_'.$cle] = 2;
            $valeurs['pas_var_'.$cle] = 0.1;
        }
    }
    return $valeurs;
}


function formulaires_calcul_ouvrages_verifier_dist() {
    $erreurs = array();
    return $erreurs;
}


function formulaires_calcul_ouvrages_traiter_dist() {
    include_spip('hyd_inc/cache');
    include_spip('hyd_inc/charge_datas');
    $datas = charge_datas();

    // Initialisation de la classe chargée d'afficher le journal de calcul
    include_spip('hyd_inc/log.class');
    $oLog = new cLog();
    include_spip('hyd_inc/ouvrage.class');
    $Ouv = new cOuvrage($oLog,$datas);
    $tsDatas = array('i','min','max','pas');
    foreach($tsDatas as $sData) {
        ${$sData} = $datas[$sData];
    }
    $tFlags = array();
    $tAbs = array();
    for($i = $min; $i <= $max; $i+= $pas){
        if(isset($datas['ValVar'])) {
            $Ouv->Set($datas['ValVar'],$i);
        }
        if(!isset($tRes)) {
            $rInit = $datas[$datas['ValCal']];
            $tRes = array();
        }
        else {
            // Solution initiale = dernière solution trouvée
            $rInit = end($tRes);
        }
        $tAbs[] = $i;
        list($tRes[],$tFlag[]) = $Ouv->Calc($datas['ValCal'],max(1,$rInit));
    }

    $tLibFlag = array(
        -1  => 'erreur_calcul',
        0   => 'débit_nul',
        1   => 'libre_den',
        2   => 'libre_noy',
        3   => 'charge_den',
        4   => 'charge_noy_part',
        5   => 'charge_noy_tot',
        11  => 'surverse_den',
        12  => 'surverse_noy');
    $tsFlag = array();
    foreach($tFlag as $Flag) {
        $tsFlag[] = $tLibFlag[$Flag];
    }
    include_spip('hyd_inc/affiche_resultats');
    unset($datas['tLib']['rPrec']);
    $res['message_ok'] = AfficheResultats($datas, $tAbs, $tRes, $tsFlag);
    return $res;
}
?>

