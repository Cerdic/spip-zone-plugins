<?php
// Instanciation du journal de calcul


/*******************************************************************************
 * Gestion des formulaires des calculettes pour l'hydraulique
 *******************************************************************************/
abstract class formulaire {

	const DBG = false; /// Debuggage de la classe et des classes filles
	const DBG_CHARGER = false; /// Debuggage de la methode charger

	/***************************************************************************
	 * Structure du formulaire qui contient un tableau avec les regroupements de champs (fieldset).
	 * Dans un fieldset, on a :
	 *  - 0 : identifiant utilisé sur l'élément id (qui sera suivi de "_fs')
	 *  - 1 : tableau contenant la description des champs, pour chaque champ, on a une clé correspondant au code du champ (name) et un tableau avec :
	 *      - 0 : code de langue du libellé du champ
	 *      - 1 : valeur par défaut du champ ou chaîne commençant par "form_" donnant le nom du squelette à charger dans le répertoire "hyd_inc" du plugin
	 *      - 3 : Codes de contrôle qui signifient :
	 *          - o : Champ obligatoire (une valeur différente de "" est exigée)
	 *          - p : Valeur strictement positive exigée
	 *          - n : Valeur nulle acceptée (à associer systématiquement avec p)
	 *          - s : chaîne de caractère acceptée (Une valeur numérique est exigée sinon)
	 *  - 2 : Type de fieldset :
	 *      - fix : Valeur fixe uniquement (pas de boutons radios)
	 *      - var : Valeur fixe ou valeur variable
	 *      - cal : var + valeur à calculer
	 ***************************************************************************/
	protected $saisies;
	protected $champs_fvc; ///< Liste des codes de champs du formulaire avec bouton radio
	protected $champs; ///< Liste des codes de champs du formulaire
	protected $data; ///< Données du formulaire
	protected $sVarCal=''; ///< Nom du champ à calculer par défaut
	public $VarCal; ///< Pointeur vers la variable de section qui sera calculée
	protected $nb_col; ///< Nombre de colonnes du tableau du formulaire (2,4 ou 5)
	/** Résultats du calcul - tableau associatif contenant :
	 *  - 'abs' => Vecteur de la donnée qui varie (abscisse)
	 *  - 'res' => Vecteur du résultat du calcul (ordonnée)
	 *  - 'flag' => Vecteur du flags d'écoulement pour les ouvrages (facultatif)
	 *  -  Plus d'autres qui peuvent être définies et utilisées par la méthode 'afficher' des classes filles */
	protected $result;
	protected $oLog; ///< Journal de calcul
	private $bNoCache = true; ///< Utilisation du cache pour ne pas refaire les calculs (true pour débugage)

	/// Nombre de pas de variation par défaut
	protected $nbPas = 15;
	protected $bFVC = true; ///< Formulaire FVC (boutons fixe, varie, calcul)

	/***************************************************************************
	 * Calcul des résultats
	 * @return un tableau au format de $this->result
	 ***************************************************************************/
	abstract protected function calculer();

	/***************************************************************************
	 * Constructeur de la classe : initialisation de la liste des champs du formulaire
	 ***************************************************************************/
	public function __construct() {
		$this->champs_fvc = $this->get_champs_fvc();
		$this->champs = $this->get_champs();
		if(self::DBG) spip_log($this->saisies,'hydraulic',_LOG_DEBUG);
		if(self::DBG) spip_log($this->champs_fvc,'hydraulic',_LOG_DEBUG);
		include_spip('hyd_inc/log.class');
		$this->oLog = new cLog();
	}

	/***************************************************************************
	 * Initialisation de la liste des champs des variables du calcul qui peuvent
	 * être fixe, variant ou calculés (fvc)
	 ***************************************************************************/
	private function get_champs_fvc() {
		$champs = array();
		foreach($this->saisies as $fs) {
			foreach($fs[1] as $cle=>$val) {
				if($fs[2]!='fix') {
					// Le champ peut être "à varier", il nécessite les vérifications des champs de variation
					$champs[] = $cle;
				}
			}
		}
		return $champs;
	}

	/***************************************************************************
	 * Initialisation de la liste complète des champs du formulaire
	 ***************************************************************************/
	private function get_champs() {
		$champs = array();
		foreach($this->saisies as $fs) {
			foreach($fs[1] as $cle=>$val) {
				$champs[] = $cle;
			}
		}
		return $champs;
	}


	/***************************************************************************
	 * Ce tableau contient la liste de tous les champs du formulaire en fonction
	 * des choix faits sur les variables à varier et à calculer
	 ***************************************************************************/
	private function champs_obligatoires($bCalc = false){
		$tChOblig = $this->champs;
		$tChCalc = $this->champs_fvc;

		if($bCalc) {
			return $tChCalc;
		}

		$choix_champs = array();
		foreach($tChCalc as $valeur){
			$choix_champs[$valeur] = _request('choix_champs_'.$valeur);
		}

		foreach($choix_champs as $cle=>$valeur){
			// Si le choix du select est de calculer une valeur...
			if($valeur != 'fix'){
				foreach($tChOblig as $cle1=>$valeur1){
					if($cle == $valeur1){
						// Permet de tasser le tableau
						$tChOblig = array_values($tChOblig);
					}
				}
			}
			// Si le choix du select est de faire varier une valeur alors on ajoute les 3 champs nécessaires
			if($valeur == 'var'){
				$tChOblig[] = 'val_min_'.$cle;
				$tChOblig[] = 'val_max_'.$cle;
				$tChOblig[] = 'pas_var_'.$cle;
			}
		}
		return $tChOblig;
	}


	/***************************************************************************
	 * Méthode à appeler par la procédure charger du formulaire CVT
	 * @note Etendre la méthode en faisant un $valeurs = parent::charger()
	 * pour ajouter des variables supplémentaires dans l'environnement du
	 * formulaire.
	 * @param bFix true = Formulaire sans choix fvc
	 ***************************************************************************/
	public function charger() {
		$valeurs['saisies'] = $this->saisies;
		$valeurs['nb_col'] = $this->nb_col;
		$valeurs['sVarCal'] = $this->sVarCal;
		$valeurs['champs_fvc'] = $this->champs_fvc;

		// Initialisation de la valeur des champs pour le formulaire
		foreach($this->saisies as $fs) {
			foreach($fs[1] as $cle=>$val) {
				$request = _request($cle);
				if(self::DBG_CHARGER) spip_log("cle=$cle request=$request",'hydraulic',_LOG_DEBUG);
				if($request != '') {
					// La valeur du champ est dans l'adresse de la page
					$valeurs[$cle] = $request;
				} else {
					// Valeur par défaut
					$valeurs[$cle] = $val[1];
				}
			}
		}

		// On parcourt tous le tableau des indices, et on initialise les valeurs des boutons radios, et des champs de variation
		if($this->bFVC) {
			$sVarCal = $this->sVarCal;
			foreach($this->champs_fvc as $cle){
				$valeurs['choix_champs_'.$cle] = 'fix';
				$valeurs['val_min_'.$cle] = $valeurs[$cle]/2;
				$valeurs['val_max_'.$cle] = $valeurs[$cle]*2;
				$valeurs['pas_var_'.$cle] = 1.5*$valeurs[$cle]/$this->nbPas;
				if(_request('choix_champs_'.$cle)=='cal') {
					$sVarCal = $cle;
				}
			}
			$valeurs['choix_champs_'.$sVarCal] = 'cal';
		}

		return $valeurs;
	}


	/***************************************************************************
	 * Charge les données d'un formulaire avec choix des variables fixées, qui varient et à calculer
	 * @param $bLibelles Remplit la clé tlib avec les libellés traduits des variables
	 * @return un tableau avec les clés suivantes:
	 *      - Couples clés/valeur des champs du formulaire
	 *      - iPrec : nombre de décimales pour la précision des calculs
	 *      - tLib: tableau avec couples clés/valeurs des libellés traduits des champs du formulaire
	 *      - sLang : la langue en cours
	 *      - CacheFileName : Le nom du fichier de cache
	 *      - min, max, pas : resp. le min, le max et le pas de variation de la variable qui varie
	 *      - i : pointeur vers la variable qui varie
	 *      - ValCal : Nom de la variable à calculer
	 *      - ValVar : Nom de la variable qui varie
	 * @author David Dorchies
	 * @date Juillet 2012
	 ***************************************************************************/
	public function charge_data() {
		global $spip_lang;

		$tChOblig = $this->champs_obligatoires();
		$tChCalc = $this->champs_obligatoires(true);
		if(self::DBG) spip_log($tChCalc,'hydraulic',_LOG_DEBUG);
		$choix_radio = array();
		$tLib = array();
		$data=array();
		$data['iPrec']=(int)-log10(_request('rPrec'));

		//On récupère les données
		foreach($tChOblig as $champ) {
			if(self::DBG) spip_log( $champ.'=>'._request($champ),'hydraulic',_LOG_DEBUG);
			if (_request($champ)!==false){
				$data[$champ] = _request($champ);
			} else {
				$data[$champ] = 999.;
			}
			$data[$champ] = str_replace(',','.',$data[$champ]); // Bug #574
		}
		// On ajoute la langue en cours pour différencier le fichier de cache par langue
		$data['sLang'] = $spip_lang;

		// On récupère les différents choix effectué sur les boutons radios ainsi que les libelles de tous les paramètres
		foreach($tChCalc as $cle){
			$choix_radio[$cle] = _request('choix_champs_'.$cle);
		}
		if(self::DBG) spip_log($choix_radio,'hydraulic',_LOG_DEBUG);

		$data['min'] = 0;
		$data['max'] = 0;
		$data['pas'] = 1;
		$data['i'] = 999.;

		foreach($choix_radio as $sVar=>$valeur){
			// Si il y a une valeur a calculer
			if($valeur == 'cal'){
				$data['ValCal'] = $sVar; // Stockage du nom de la variable à calculer
			}
			// Sinon si une valeur varie
			else if($valeur == 'var'){
				// alors on récupère sa valeur maximum, minimum et son pas de variation
				$data['min'] = _request('val_min_'.$sVar);
				$data['max'] = _request('val_max_'.$sVar);
				$data['pas'] = _request('pas_var_'.$sVar);
				// On fait pointer la variable qui varie sur l'indice de parcours du tableau i
				$data['ValVar'] = $sVar; // Stockage du nom de la variable qui varie
				$data[$sVar] = &$data['i']; // Pointeur pour relier le compteur de boucle à la variable
			}
		}
		// Pour afficher correctement la valeur maximum avec les pb d'arrondi des réels
		$data['max'] += $data['pas']/2;

		$this->data = $data;
		if(self::DBG) spip_log($data,'hydraulic',_LOG_DEBUG);
	}


	/***************************************************************************
	 * Méthode à appeler par la procédure traiter du formulaire CVT
	 * Vérification des données transmises et génération des messages d'erreur pour le formulaire
	 ***************************************************************************/
	public function verifier() {
		// Chargement des données du formulaire
		$this->charge_data();
		$tCtrl = array();
		$tData = array();
		$sValCal = (isset($this->data['ValCal']))?$this->data['ValCal']:false;

		foreach($this->saisies as $fs) {
			foreach($fs[1] as $cle=>$val) {
				if($cle != $sValCal) {
					if(isset($this->data[$cle])) {
						$tData[$cle] = $this->data[$cle];
					}
					$tCtrl[$cle] = $val[2];
				}
			}
		}
		// Vérifications des données
		$erreurs = array();
		foreach($tCtrl as $Cle=>$Ctrl) {
			if(isset($tData[$Cle])) {
				$tData[$Cle] = trim(str_replace(',','.',$tData[$Cle]));
			}
			if(strpos($Ctrl,'o')!==false & (!isset($tData[$Cle]) | $tData[$Cle]=="")) {
				// Champ obligatoire
				$erreurs[$Cle] = _T('hydraulic:erreur_obligatoire');
			} elseif(strpos($Ctrl,'s')===false & !preg_match('#^[-+]?[0-9]*\.?[0-9]+([eE][-+]?[0-9]+)?$#', $tData[$Cle]) & $tData[$Cle]!="") {
				// Valeurs numériques obligatoire
				$erreurs[$Cle] = _T('hydraulic:erreur_non_numerique');
            } else {
				// Conversion des champs en valeur réelle
				$tData[$Cle] =  floatval($tData[$Cle]);
				if(strpos($Ctrl,'p')!==false & strpos($Ctrl,'n')!==false & $tData[$Cle] < 0) {
					// Contrôles des valeurs qui doivent être positives ou nulles
					$erreurs[$Cle] = _T('hydraulic:erreur_val_positive_nulle');
				} elseif(strpos($Ctrl,'p')!==false & strpos($Ctrl,'n')===false & $tData[$Cle] <= 0) {
					// Contrôles des valeurs qui doivent être strictement positives
					$erreurs[$Cle] = _T('hydraulic:erreur_val_positive');
				}
			}
		}

		// On compte s'il y a des erreurs. Si oui, alors on affiche un message
		if (count($erreurs)) {
			$erreurs['message_erreur'] = _T('hydraulic:saisie_erreur');
		}
		if(self::DBG) spip_log($erreurs,'hydraulic',_LOG_DEBUG);
		return $erreurs;
	}


	/***************************************************************************
	 * Méthode à appeler par la procédure traiter du formulaire CVT
	 ***************************************************************************/
	public function traiter() {
		spip_log('toto','hydraulic',_LOG_DEBUG);
		include_spip('hyd_inc/cache');
		if(self::DBG) spip_log('toto','hydraulic',_LOG_DEBUG);
		// Calcul des résultats
		$CacheFileName = md5(serialize($this->data)); // Nom du fichier en cache pour calcul déjà fait
		if(!$this->bNoCache && is_file(HYD_CACHE_DIRECTORY.$CacheFileName)) {
			// On récupère toutes les données dans un cache déjà créé
			$this->result = ReadCacheFile($CacheFileName);
		} else {
			// On effectue les calculs
			$this->result = $this->calculer();
		}
		if(self::DBG) spip_log($this->result,'hydraulic',_LOG_DEBUG);

		// Affichage des résultats
		return array('message_ok'=>$this->afficher_result());
	}


	/***************************************************************************
	 * Récupération des libellés des champs des variables de calcul (fvc)
	 ***************************************************************************/
	protected function get_champs_libelles() {
		$lib = array();
		foreach($this->saisies as $fs) {
			foreach($fs[1] as $cle=>$val) {
				if($fs[2]!='fix') {
					$lib[$cle] = _T('hydraulic:'.$val[0]);
				}
			}
		}
		return $lib;
	}


	/***************************************************************************
	 * Affichage des tableaux et graphiques des résultats des calculs
	 * @return Chaîne de caractère avec le code HTML à afficher
	 ***************************************************************************/
	protected function afficher_result() {
		// Initialisation des données nécessaires
		$data = &$this->data; // Données du formulaire
		$tAbs = &$this->result['abs']; // Valeur de la variable qui varie
		$tRes = &$this->result['res']; // Résultats du calcul
		if(isset($this->result['flag'])) {
			$tFlag = &$this->result['flag']; // Type d'écoulement pour les vannes
		} else {
			$tFlag = false;
		}
		$tLib = $this->get_champs_libelles(); // Libellé traduit des champs fvc

		$echo = $this->oLog->Result(); // Insertion du journal de calcul

		if(!isset($data['ValVar'])) {
			$data['ValVar']='';
		}
		// Affichage des paramètres fixes
		$tCnt = array();
		foreach($data as $k=>$v) {
			if(in_array($k,$this->champs_fvc) && !in_array($k,array($data['ValCal'],$data['ValVar']))) {
				$tCnt[]=array($tLib[$k],format_nombre($data[$k], $data['iPrec']));
			}
		}
		// Si il n'y a pas de valeur à varier on ajoute le résultat et le flag de calcul s'il existe
		if(!$data['ValVar']) {
			$tCnt[]=array('<b>'.$tLib[$data['ValCal']].'</b>','<b>'.format_nombre($tRes[0], $data['iPrec']).'</b>');
			if($tFlag) {
				if(self::DBG) spip_log($tFlag,'hydraulic.'._LOG_DEBUG);
				$tCnt[]= array(_T('hydraulic:type_ecoulement'),_T('hydraulic:flag_'.$tFlag[0]));
			}
		}
		$tableau_fixe = $this->get_result_table($tCnt,array(_T('hydraulic:param_fixes'),_T('hydraulic:valeurs')));

		// Affichage d'un tableau pour un paramètre qui varie
		if($data['ValVar']) {
			$tCnt=array();
			foreach($tAbs as $k=>$Abs){
				$tCnt[] = array(format_nombre($Abs, $data['iPrec']),format_nombre($tRes[$k], $data['iPrec']));
			}
			$tEnt = array($tLib[$data['ValVar']],$tLib[$data['ValCal']]);
			$tableau_variable = $this->get_result_table($tCnt,$tEnt);

			// Si la première valeur est infinie alors ...
			if(is_infinite($tRes[0])){
				// ... on supprime cette valeur
				unset($tRes[0]);
				// ... on tasse le tableau des résultats
				$tRes = array_values($tRes);
				// ... on supprime l'abscisse correspond
				unset($tAbs[0]);
				// ... on tasse le tableau des abscisses
				$tAbs = array_values($tAbs);
			}

			// Affichage du graphique

			$echo .= $this->getGraph($tLib[$data['ValVar']], $tLib[$data['ValCal']], $tAbs, $tRes);
		}
		$echo .= '<div class="hyd_table">'.$tableau_fixe.'</div>';
		if(isset($tableau_variable)) {
			$echo .= '<div class="hyd_table">'.$tableau_variable.'</div>';
		}
		return $echo;
	}


	/**
	 * Affichage du graphique de la variable calculée
	 * @param $sLibVar Libellé de la variable variée
	 * @param $sLibCal Libellé de la variable calculée
	 * @param $tAbs Vecteur des abscisses
	 * @param $tRes Vecteur des valeurs calculées
	 */
	protected function getGraph($sLibVar, $LibCal, $tAbs, $tRes) {
		include_spip('hyd_inc/graph.class');
		$oGraph = new cGraph('',$sLibVar,'');
		if(isset($tRes)) {
			$oGraph->AddSerie(
				$LibCal,
				$tAbs,
				$tRes,
				'#00a3cd',
				'lineWidth:3, showMarker:true, markerOptions:{style:\'filledCircle\', size:8}');
		}
		// Récupération du graphique
		return $oGraph->GetGraph('graphique',400,600)."\n";
	}


	/***************************************************************************
	 * Renvoie un tableau formaté à partir d'un array à deux dimensions
	 * @param $tContent Tableau à 2 dimensions contenant les cellules [ligne][colonne]
	 * @param $tEntetes Tableau contenant les entêtes de colonne
	 ***************************************************************************/
	protected function get_result_table($tContent,$tEntetes=false) {
		// On génère les entêtes du tableau de résulats
		$echo='<table class="spip">';

			if($tEntetes) {
				$echo.='<thead>
					<tr class="row_first">';
						foreach($tEntetes as $s){
							$echo.= '<th scope="col" rowspan="2">'.$s.'</th>';
						}
					$echo.= '</tr>
				</thead>';
			}
			$echo.='<tbody>';
				$i=0;
				foreach($tContent as $Ligne){
					$i++;
					$echo.= '<tr class="align_right ';
						$echo.=($i%2==0)?'row_even':'row_odd';
						$echo.='">';
						foreach($Ligne as $Cellule){
							$echo.= '<td>'.$Cellule.'</td>';
						}
					$echo.= '</tr>';
				}
			$echo.= '</tbody>
		</table>';
		return $echo;
	}
}
?>
