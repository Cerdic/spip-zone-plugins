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

function mes_saisies_normale_critique(){
	$fieldset_champs_nc = array(
	
				'FT'          => array(
									   'Définition de la section trapézoïdale',
									   array(
											 'rLarg'  =>array('largeur_fond',2.5),
											 'rFruit' =>array('fruit', 0.56, false)
											)
				),
				
				'FR'          => array(
									   'Définition de la section rectangulaire',
									   array(
											 'rLarg'  =>array('largeur_fond',2.5),
											)
				),
					
				'FC'          => array(
									   'Définition de la section circulaire',
									   array(
											 'circ1'  =>array('champ_circulaire1',3),
											 'circ2'  =>array('champ_circulaire2', 0.6)
											)
				),
				
				'FP'          => array(
									   'Définition de la section puissance',
									   array(
											 'puiss1' =>array('champs_puissance1',10),
											 'puiss2' =>array('champs_puissance2', 0.7)
											)
				),
				
				'Caract' => array(				
									   'Caractéristiques',					
									   array(
											 'rug'	    =>array('rugosite_nc',50),
											 'pente'    =>array('pente_nc', 50),
											 'cote_eau' =>array('cote_eau_nc', 0.005),
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
    
    foreach($tSaisie as $IdFS=>$FieldSet) {
		if((substr($IdFS,0,1) != 'F') || ($IdFS == $sTypeSection)){
			foreach($FieldSet[1] as $Cle=>$Champ) {
				if((!isset($Champ[2])) || (isset($Champ[2]) && $Champ[2])) {
					$tChOblig[] = $IdFS.'_'.$Cle.'_nc';
				}
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
		'mes_saisies' => $tSaisie_nc,
		'choix_champs_rug'      => 'varier_val_rug',
		'choix_champs_pente'    => 'val_fixe_pente',
		'choix_champs_cote_eau' => 'val_fixe_cote_eau',
		'choix_champs_debit'    => 'val_fixe_debit',
	);
    
    foreach($tSaisie_nc as $CleFD=>$FieldSet) {
		foreach($FieldSet[1] as $Cle=>$Champ) {
			$valeurs[$CleFD.'_'.$Cle.'_nc'] = $Champ[1];
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
        if (!_request($obligatoire)) {
			$erreurs[$obligatoire] = _T('hydraulic:champ_obligatoire');
        }
        else {
            $datas[$obligatoire] = _request($obligatoire);
        }
    }

	// Gestion des valeurs négatives
    foreach($datas as $champ=>$data) {
        if ($data < 0) $erreurs[$champ] = _T('hydraulic:valeur_positive');
    }
    
    if (count($erreurs)) {
        $erreurs['message_erreur'] = _T('hydraulic:saisie_erreur');
    }

    return $erreurs;
}

function formulaires_normale_critique_traiter_dist(){

	 /***************************************************************************
    *                        Calcul de Lechapt et calmon
    ****************************************************************************/

	/***************************************************************************
    *                   Affichage du tableau de données
    ****************************************************************************/

    /***************************************************************************
    *                        Affichage du graphique
    ****************************************************************************/

}
?>

