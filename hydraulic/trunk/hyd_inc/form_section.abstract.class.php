<?php
/**
 *      @file inc_hyd/section.php
 *      Listes des champs concernant les sections paramétrées
 */

/*      Copyright 2012, 2015 David Dorchies <dorch@dorch.fr>, Médéric Dulondel
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
include_spip('hyd_inc/formulaire.abstract.class');

abstract class form_section extends formulaire {

	protected $oSn; ///< Objet section
	protected $oP; ///< Objet paramètres de section

	/**
	 * Définition des variables calculables et de leur code de langue
	 */
	public $champs_select_calc = array(
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


	/*
	* Caractéristiques communes aux calculs sur les sections :
	* - Caractéristiques des différents types de section
	* - Caractéristiques du bief
	* @param $bCourbe Pour ajouter la longueur du bief dans la liste des champs (calcul courbe de remous)
	*/
	public function __construct($bCourbe=false) {
		/* Tableau niveau 1 : Composantes pour chaque variable, la clé est le nom de
		* la variable dans le formulaire, et la valeur contient un tableau avec le
		* code de langue, la valeur par défaut et les tests de de vérification à
		* effectuer sur le champ (o : obligatoire, p : positif, n : nul accepté, s : chaîne de caractère acceptée)
		*/
		$saisies_section = array(
			'type_section' => array(
				'type_section',
				array(
					'choix_section' => array('choix_section','form_section_type','s')
				),
				'fix'
			),
			'FT' => array(
				'def_section_trap',
				array(
					'FT_rLargeurFond'  =>array('largeur_fond',2.5,'opn'),
					'FT_rFruit'        =>array('fruit', 0.56,'opn')
				),
				'var'
			),

			'FR' => array(
				'def_section_rect',
				array(
					'FR_rLargeurFond'  =>array('largeur_fond',2.5,'op'),
				),
				'var'
			),

			'FC' => array(
				'def_section_circ',
				array(
					'FC_rD'  =>array('diametre',2,'op')
				),
				'var'
			),

			'FP' => array(
				'def_section_parab',
				array(
					'FP_rk' =>array('coef',0.5,'op'),
					'FP_rLargeurBerge' =>array('largeur_berge',4,'op')
				),
				'var'
			)
		);

		$saisies_bief['c_bief'] = array(
			'caract_bief',
			array('rKs'=>array('coef_strickler',40,'op')),
		'var');
		if($bCourbe) {
			// Pour la courbe de remous, on a besoin de la longueur du bief en plus
			$saisies_bief['c_bief'][1]['rLong'] = array('longueur_bief',100,'op');
		}
		$saisies_bief['c_bief'][1]['rIf'] = array('pente_fond',0.001,'o');
		$saisies_bief['c_bief'][1]['rYB'] = array('h_berge',1,'opn');
		$this->saisies = array_merge($saisies_section, $saisies_bief, $this->saisies);
		parent::__construct();
	}

	public function get_champs_section($choix_section) {
		$ChampsSection = array();
		foreach($this->saisies as $fs_cle=>$fs) {
			if(substr($fs_cle,0,2)==$choix_section) {
				$ChampsSection = array_merge($ChampsSection,array_keys($fs[1]));
			}
		}
		foreach($ChampsSection as &$Champ) {
			$Champ = substr($Champ,3);
		}
		spip_log($ChampsSection,'hydraulic',_LOG_DEBUG);
		return $ChampsSection;
	}



	/** ************************************************************************
	* Récupération des libellés des champs des variables de calcul (fvc)
	***************************************************************************/
	protected function get_champs_libelles() {
		$lib = array();
		foreach($this->saisies as $fs) {
			foreach($fs[1] as $cle=>$val) {
				if($fs[2]!='fix') {
					if(substr($cle,0,1)!='F') {
						$lib[$cle] = _T('hydraulic:'.$val[0]);
					} else {
						$lib[substr($cle,3)] = _T('hydraulic:'.$val[0]);
					}
				}
			}
		}
		return $lib;
	}


	protected function creer_section_param() {
		include_spip('hyd_inc/section.class');

		foreach(array_keys($this->data) as $k) {
			if(substr($k,0,1)=='F') {
				if(substr($k,0,3)==$this->data['choix_section'].'_') {
					// On supprime le préfixe pour les champs dépendants du type de section
					$this->data[substr($k,3)] = $this->data[$k];
				}
				// On élimine les champs avec le préfixe "type de section"
				unset($this->data[$k]);
			}
		}
		// On supprime aussi le préfixe sur ValCal et ValVar
		foreach(array('ValCal','ValVar') as $k) {
			if(isset($this->data[$k]) && substr($this->data[$k],0,1)=='F') {
				$this->data[$k] = substr($this->data[$k],3);
			}
		}

		// On transforme les champs du tableau des données du formulaire en variables
		extract($this->data, EXTR_OVERWRITE|EXTR_REFS);

		// Instanciation des objets pour le calcul
		$this->oP = new cParam($rKs, $rQ, $rIf, $rPrec, $rYB);
		switch($choix_section) {
			case 'FT':
			include_spip('hyd_inc/sectionTrapez.class');
			$this->oSn=new cSnTrapez($this->oLog,$this->oP,$rLargeurFond,$rFruit);
			break;

			case 'FR':
			include_spip('hyd_inc/sectionRectang.class');
			$this->oSn=new cSnRectang($this->oLog,$this->oP,$rLargeurFond);
			break;

			case 'FC':
			include_spip('hyd_inc/sectionCirc.class');
			$this->oSn=new cSnCirc($this->oLog,$this->oP,$rD);
			break;

			case 'FP':
			include_spip('hyd_inc/sectionPuiss.class');
			$this->oSn=new cSnPuiss($this->oLog,$this->oP,$rk,$rLargeurBerge);
			break;

			default:
			include_spip('hyd_inc/sectionTrapez.class');
			$this->oSn=new cSnTrapez($this->oLog,$this->oP,$rLargeurFond,$rFruit);
		}
		if(isset($rY)) {
			$this->oSn->rY = $rY;
		}
		spip_log($this->oSn,'hydraulic',_LOG_DEBUG);
		if(self::DBG) spip_log($this->oP,'hydraulic',_LOG_DEBUG);
	}
}
?>
