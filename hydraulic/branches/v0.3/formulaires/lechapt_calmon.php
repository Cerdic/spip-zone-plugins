<?php
/*
 * formulaires/lechapt_calmon.php
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

// Cette fonction renvoit tous les indices des champs présents dans le formulaire.
function mes_champs_coeff_materiau() {
    $mes_champs_coeff = array('L','M','N','Q','D','J','Lg');
    return $mes_champs_coeff;
}

// Cette fonction renvoit seulement les paramètres fixes, ainsi que leur code pour le dictionnaires des langues
function mes_champs_sans_coeff_materiau(){
    $mes_champs_sans_coeff = array('Q','D','J','Lg');

    return $mes_champs_sans_coeff;
}


/*
 * Tableau des données pour chaque type de tuyau. Ces valeurs sont associées
 * aux numéros des options du select (voir page lechapt_calmon.php)
 */
function mes_saisies_materiau() {
    $type_materiaux = array(

                '1'          => array(
                                       'L' =>1.863,
                                       'M' =>2,
                                       'N' =>5.33
                ),

                '2'          => array(
                                       'L' =>1.601,
                                       'M' =>1.975,
                                       'N' =>5.25
                ),

                '3'          => array(
                                       'L' =>1.40,
                                       'M' =>1.96,
                                       'N' =>5.19
                ),

                '4'          => array(
                                       'L' =>1.16,
                                       'M' =>1.93,
                                       'N' =>5.11
                ),

                '5'          => array(
                                       'L' =>1.1,
                                       'M' =>1.89,
                                       'N' =>5.01
                ),

                '6'          => array(
                                       'L' =>1.049,
                                       'M' =>1.86,
                                       'N' =>4.93
                ),

                '7'          => array(
                                       'L' =>1.01,
                                       'M' =>1.84,
                                       'N' =>4.88
                ),

                '8'          => array(
                                       'L' =>0.916,
                                       'M' =>1.78,
                                       'N' =>4.78
                ),

                '9'          => array(
                                       'L' =>0.971,
                                       'M' =>1.81,
                                       'N' =>4.81
                ),
    );

  return $type_materiaux;

}

function champs_obligatoires($bCalc = false){
    /*
     * Ce tableau contient la liste de tous les champs du formulaire.
     * La suite de cette fonction se chargera de supprimer les valeurs non obligatoires.
     */
    $tChOblig = mes_champs_coeff_materiau();
    $tChCalc = mes_champs_sans_coeff_materiau();

    $choix_champs = array();
    foreach($tChCalc as $valeur){
        $choix_champs[$valeur] = _request('choix_champs_'.$valeur);
    }

    foreach($choix_champs as $cle=>$valeur){
        // Si le choix du select est de calculer une valeur...
        if(substr($valeur, 0,3) != 'val'){
            foreach($tChOblig as $cle1=>$valeur1){
                if($cle == $valeur1){
                    // ... alors on peut supprimer de notre tableau le champs calculé (il n'est pas obligatoire car grisé)
                    unset($tChOblig[$cle1]);
                    // Permet de tasser le tableau
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
    $tChOblig[] = 'rPrec';

    if($bCalc) {
        return $tChCalc;
    }
    else {
        return $tChOblig;
    }
}

function formulaires_lechapt_calmon_charger_dist() {
    $valeurs = array(
        'mes_saisies_materiaux' => mes_saisies_materiau(),
        'tableau_caract' => mes_champs_sans_coeff_materiau(),
        'typeMateriau' => 1,
        'rPrec' => 0.001,
        'L' => 1.863,
        'M' => 2,
        'N' => 5.33,
        'Q' => 3,
        'D' => 1.2,
        'J' => 0.634482025,
        'Lg'=> 100
    );

    $mes_champs = mes_champs_sans_coeff_materiau();
    // On parcourt tous le tableau des indices, et on initialise les valeurs des boutons radios, et des champs de variation
    foreach($mes_champs as $cle){
        if($cle == 'Q'){
            $valeurs['choix_champs_'.$cle] = 'calcul_val_'.$cle;
        }
        else{
            $valeurs['choix_champs_'.$cle] = 'val_fixe_'.$cle;
        }

        $valeurs['val_min_'.$cle] = 1;
        $valeurs['val_max_'.$cle] = 2;
        $valeurs['pas_var_'.$cle] = 0.1;
    }

    return $valeurs;
}

function formulaires_lechapt_calmon_verifier_dist(){
    $erreurs = array();
    $datas = array();
    $tChOblig= champs_obligatoires();
    // Vérifier que les champs obligatoires sont bien là :
    foreach($tChOblig as $obligatoire) {
        if (_request($obligatoire) == NULL) {
            $erreurs[$obligatoire] = _T('hydraulic:champ_obligatoire');
        }
        // Les coefficients des matériaux doivent être strictement positifs
        else if(($obligatoire == 'L' || $obligatoire == 'M' || $obligatoire == 'N') && _request($obligatoire) == 0){
            $erreurs[$obligatoire] = _T('hydraulic:valeur_positive');
        }
        else {
            $datas[$obligatoire] = _request($obligatoire);
        }
    }

    // Gestion des valeurs négatives
    foreach($datas as $champ=>$data) {
        if ($data < 0) $erreurs[$champ] = _T('hydraulic:valeur_positive_nulle');
    }

    if (count($erreurs)) {
        $erreurs['message_erreur'] = _T('hydraulic:saisie_erreur');
    }

    return $erreurs;
}

function formulaires_lechapt_calmon_traiter_dist(){
     /***************************************************************************
    *                        Calcul de Lechapt et calmon
    ****************************************************************************/
    include_spip('hyd_inc/cache');
    include_spip('hyd_inc/log.class');
    include_spip('hyd_inc/charge_datas');
    $datas = charge_datas();
    //spip_log($datas,'hydraulic');
    // On transforme les champs du tableau en variables
    foreach($datas as $cle=>&$valeur){
        ${$cle} = &$valeur;
    }

    $bNoCache = true; // true pour débugage
    if(!$bNoCache && is_file(HYD_CACHE_DIRECTORY.$CacheFileName)) {
        // On récupère toutes les données dans un cache déjà créé
        list($tAbs,$tRes) = ReadCacheFile($CacheFileName);
    }
    else {
        /*
         * Selon la variable à calculer, on gère les valeurs = à 0  et les valeurs infinies
         * et on fait le valcul correspondant.
         */
        $tDiv0 = array('Q'=>'Lg', 'D'=>'J', 'J'=>'D', 'Lg'=>'Q');
        $Div0 = $tDiv0[$ValCal];

        if(${$Div0} == 0 && _request("choix_champs_$Div0") != "varier_val_$Div0"){
            $tRes[] = 0;
        }
        else{
            $tRes = array(); // Tableau des résultats (ordonnées)
            $tAbs = array(); // Tableau des abscisses
            for($i = $min; $i <= $max; $i+= $pas){
                $tAbs[] = $i;
                if($i == 0 && _request("choix_champs_$Div0") == "varier_val_$Div0"){
                    $tRes[] = INF;
                }
                else{
                    switch($ValCal){
                        case 'Q':
                            $tRes[] = pow(((($J*pow($D, $N))/$L)*(1000/$Lg)), 1/$M);
                            break;
                        case 'D':
                            $tRes[] = pow(((($L*pow($Q, $M))/$J)*($Lg/1000)), 1/$N);
                            break;
                        case 'J':
                            $tRes[] = (($L*pow($Q, $M))/pow($D, $N))*($Lg/1000);
                            break;
                        case 'Lg':
                            $tRes[] = (($J*pow($D, $N))/($L*pow($Q,$M)))*1000;
                    }
                }
            }
        }

        //Enregistrement des données dans fichier cache
        WriteCacheFile($CacheFileName,array($tabs,$tRes));
    }
    /***************************************************************************
    *                   Affichage du tableau de données
    ****************************************************************************/
    spip_log($datas,'hydraulic');
    include_spip('hyd_inc/affiche_resultats');
    $res['message_ok'] = AfficheResultats($datas, $tAbs, $tRes);
    return $res;
}
?>
