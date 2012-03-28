<?php
/*
 * formulaires/calcul_normale_critique.php
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

function id_decoupe($champs,$nb){
	$decoup = explode('_', $champs, 3);
	return $decoup[$nb];
}	
	
function mes_saisies_normale_critique(){
	$fieldset_champs_nc = array(
	
				'FT'          => array(
									   'def_section_trap',
									   array(
											 'rLarg'  =>array('largeur_fond',2.5),
											 'rFruit' =>array('fruit', 0.56)
											 )
				),
				
				'FR'          => array(
									   'def_section_rect',
									   array(
											 'rLarg'  =>array('largeur_fond',2.5)
											)
				),
					
				'FC'          => array(
									   'def_section_circ',
									   array(
											 'circ1'  =>array('champ_circulaire1',3),
											 'circ2'  =>array('champ_circulaire2', 0.6)
											)
				),
				
				'FP'          => array(
									   'def_section_puis',
									   array(
											 'puiss1' =>array('champs_puissance1',10),
											 'puiss2' =>array('champs_puissance2', 0.7)
											)
				),
				
				'Caract' => array(				
									   'caract_globale',					
									   array(
											 'rug'	    =>array('rugosite_nc',50),
											 'pente'    =>array('pente_nc', 50),
											 'coteEau' =>array('cote_eau_nc', 0.005),
											 'debit'    =>array('debit_nc', 1.2)
											)
				)
	);
		
  return $fieldset_champs_nc;
}

function champs_obligatoires_nc() {
	
	$tSaisie = mes_saisies_normale_critique();
    $tChOblig = array();
    $sTypeSection = _request('ncTypeSection');
    $ValVar = '';
    $tChOblig[] = 'prec_nc';
    foreach($tSaisie as $IdFS=>$FieldSet) {
		if((substr($IdFS,0,1) != 'F') || ($IdFS == $sTypeSection)){
			foreach($FieldSet[1] as $Cle=>$Champ) {
				if(substr(_request('choix_champs_'.$Cle), 0, 3) == 'var'){
					$ValVar = $IdFS.'_'.$Cle.'_nc';
				}
				if((!isset($Champ[2])) || (isset($Champ[2]) && $Champ[2])) {
					$tChOblig[] = $IdFS.'_'.$Cle.'_nc';
				}
			}
		}
	}
	
	if($ValVar != ''){
		foreach($tChOblig as $cle=>$valeur){
			if($valeur == $ValVar){
				unset($tChOblig[$cle]);
				$tChOblig = array_values($tChOblig);
				$tChOblig [] = 'val_min_'.id_decoupe($valeur, 1);
				$tChOblig [] = 'val_max_'.id_decoupe($valeur, 1);
				$tChOblig [] = 'pas_var_'.id_decoupe($valeur, 1);
			}
		}
	}
	
	return $tChOblig;
}

function formulaires_calcul_normale_critique_charger_dist() { 
	// On charge les saisies et les champs qui nécessitent un accès par les fonctions
	$tSaisie_nc = mes_saisies_normale_critique();
	
	$valeurs = array(
		'ncTypeSection' => 'FT',
		'mes_saisies'   => $tSaisie_nc,
		'val_a_cal_nc'  => 1,
		'prec_nc'       => 0.001
	);
    
    foreach($tSaisie_nc as $CleFD=>$FieldSet) {
		foreach($FieldSet[1] as $Cle=>$Champ) {
			$valeurs[$CleFD.'_'.$Cle.'_nc'] = $Champ[1];
			if($CleFD == 'Caract'){
				$valeurs['choix_champs_'.$Cle] = 'val_fixe_'.$Cle;		
				$valeurs['val_min_'.$Cle] = 1;
				$valeurs['val_max_'.$Cle] = 2;
				$valeurs['pas_var_'.$Cle] = 0.1;
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
        else if(_request($obligatoire) == 0){
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


function formulaires_normale_critique_traiter_dist(){

	 /***************************************************************************
    *                        Calcul normale critique
    ***************************************************************************/

	
	/***************************************************************************
    *                   Affichage du tableau de données
    ****************************************************************************/
	
    /***************************************************************************
    *                        Affichage du graphique
    ****************************************************************************/

	
}
?>

