<?php
include_spip('hyd_inc/form_section.abstract.class');

class form_calcul_section extends form_section {

    // Définition de la variable à calculer par défaut
    protected $sVarCal = '';

    // Définition du nombre de colonnes du formulaire
    protected $nb_col = 4;

    // Définition des variables calculables et de leur code de langue
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

    function __construct() {
        $this->saisies['c_hyd'] = array(
            'caract_hydraulique',
            array(
                'rQ'       =>array('debit', 1.2, 'op'),
                'rY'       =>array('tirant_eau', 0.8, 'op')
            ),
            'var'
        );
        $this->saisies['param_calcul'] = array(
            'param_calcul',
            array(
                'rPrec' => array('precision',0.001,'fop')
            ),
            'fix'
        );
        $this->saisies['val_a_cal'] = array(
            'donnee_calc',
            array(
                'val_a_cal' => array('choix_donnee_calc','form_calcul_section_valacal','s')
            ),
            'fix'
        );
        parent::__construct();
    }


    public function charger() {
        $valeurs = parent::charger();
        $valeurs = array_merge($valeurs,
            array(
                'choix_section' => 'FT',
                'val_a_cal'  => 'Hs',
                'choix_champs_select' => $this->champs_select_calc
            )
        );
        return $valeurs;
    }

    protected function calculer() {
        $this->creer_section_param();

        // On transforme les champs du tableau des données du formulaire en variables
        extract($this->data, EXTR_OVERWRITE|EXTR_REFS);

        if(isset($ValVar) && $ValVar != ''){
            // Pointage de la variable qui varie sur le bon attribut
            if($ValVar == 'rY' or in_array($ValVar, $this->get_champs_section($choix_section))){
                $this->oSn->{$ValVar} = &$i;
            }
            else{
                $this->oP->{$ValVar} = &$i;
            }
            // Définition de la variable à calculer
            $tVarCal = array($val_a_cal);
            $this->data['ValCal'] = $val_a_cal;
        }
        else {
            switch($choix_section) {
                case 'FR':
                    $tVarCal = array('Hs', 'Hsc', 'B', 'P', 'S', 'R', 'V', 'Fr', 'Yc', 'Yn', 'Yf', 'Yt', 'Yco', 'J', 'I-J', 'Imp', 'Tau0');
                    break;
                default:
                    // Le calcul de la hauteur conjuguée n'est pas OK pour les sections autres que rectangulaire
                    $tVarCal = array('Hs', 'Hsc', 'B', 'P', 'S', 'R', 'V', 'Fr', 'Yc', 'Yn', 'Yf', 'Yt', 'J', 'I-J', 'Imp', 'Tau0');
            }
        }

        $tRes = array(); // Tableau des résultats (ordonnées)
        $tAbs = array(); // Tableau des abscisses
        for($i = $min; $i <= $max; $i+= $pas){
            $this->oSn->Reset(true);
            $tAbs[] = $i;
            foreach($tVarCal as $sCalc){
                $rY = $this->oSn->rY;
                if(!in_array($sCalc,array('Yn', 'Yc', 'Hsc'))){
                    $tRes[] = $this->oSn->Calc($sCalc);
                }
                else{
                    $tRes[] = $this->oSn->CalcGeo($sCalc);
                }
                $this->oSn->rY = $rY;
            }
        }
        return array('abs'=>$tAbs,'res'=>$tRes,'tVarCal'=>$tVarCal);
    }


    /** ************************************************************************
     * Affichage des tableaux et graphiques des résultats des calculs
     * @return Chaîne de caractère avec le code HTML à afficher
     ***************************************************************************/
    protected function afficher_result() {
        if(isset($this->data['ValVar']) && $this->data['ValVar'] != ''){
            // Une donnée varie : affichage du graphique et des tableaux de base
            return parent::afficher_result();
        }
        else{
            // Toutes les données sont fixes affichage du tableau et du schéma de la section
            $tRes = $this->result['res'];
            // Tableaux des résultats des calculs
            $tC =array();
            $c = 0;
            foreach($this->result['tVarCal'] as $champ){
                $tC[$c][] = _T('hydraulic:'.$this->champs_select_calc[$champ]);
                $tC[$c][] = format_nombre($tRes[$c], $this->data['iPrec']);
                $c++;
            }
            $echo = '<div style="display:inline-block;">';
            $echo .= $this->get_result_table($tC);
            $echo .= '</div>';

            // Schéma de la section
            $lib_datas = array();
            $par = 0;
            foreach($this->result['tVarCal'] as $champ){
                if(substr($this->champs_select_calc[$champ], 0, 6) == 'tirant' || $champ == 'Hs' || $champ == 'Hsc'){
                    $lib_datas[$champ] = $tRes[$par];
                }
                $par++;
            }

            $lib_datas['rYB'] = $this->oP->rYB;
            include_spip('hyd_inc/dessinSection.class');
            $dessinSection = new dessinSection(250, 400, 100, $this->oSn, $lib_datas);
            $echo.= $dessinSection->GetDessinSection();
            return $echo;
        }
    }

    protected function get_champs_libelles() {
        $lib = parent::get_champs_libelles();
        foreach($this->champs_select_calc as $cle=>$champ) {
            $lib[$cle] = _T('hydraulic:'.$champ);
        }
        return $lib;
    }
}
?>
