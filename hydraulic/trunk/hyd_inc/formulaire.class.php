<?php
abstract class formulaire {

	/**
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
	 */
	protected $saisies;
	public $champs_fvc; ///< Liste des codes de champs du formulaire avec bouton radio
	public $champs; ///< Liste des codes de champs du formulaire
	public $data; ///< Données du formulaire
	protected $sVarCal=''; ///< Champ à calculer par défaut
	protected $nb_col; ///< Nombre de colonnes du tableau du formulaire (2,4 ou 5)

	abstract protected function get_environnement();

	public function __construct() {
		$this->champs_fvc = $this->get_champs_fvc();
		$this->champs = $this->get_champs();
		spip_log($this->saisies,'hydraulic',_LOG_DEBUG);
		spip_log($this->champs,'hydraulic');
		spip_log($this->champs_fvc,'hydraulic',_LOG_DEBUG);
	}

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

	private function get_champs() {
		$champs = array();
		foreach($this->saisies as $fs) {
			foreach($fs[1] as $cle=>$val) {
				spip_log($cle,'hydraulic');
				$champs[] = $cle;
			}
		}
		return $champs;
	}

	public function champs_obligatoires($bCalc = false){
		/*
		 * Ce tableau contient la liste de tous les champs du formulaire.
		 * La suite de cette fonction se chargera de supprimer les valeurs non obligatoires.
		 */
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
						// ... alors on peut supprimer de notre tableau le champs calculé (il n'est pas obligatoire car grisé)
						unset($tChOblig[$cle1]);
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


	public function charger() {
		$valeurs = $this->get_environnement();
		$valeurs['saisies'] = $this->saisies;
		$valeurs['nb_col'] = $this->nb_col;
		$valeurs['sVarCal'] = $this->sVarCal;
		$valeurs['champs_fvc'] = $this->champs_fvc;

		// Initialisation de la valeur des champs pour le formulaire
		foreach($this->saisies as $fs) {
			foreach($fs[1] as $cle=>$val) {
				$valeurs[$cle] = $val[1];
			}
		}

		// On parcourt tous le tableau des indices, et on initialise les valeurs des boutons radios, et des champs de variation
		$sVarCal = $this->sVarCal;
		foreach($this->champs_fvc as $cle){
			$valeurs['choix_champs_'.$cle] = 'fix';
			$valeurs['val_min_'.$cle] = 1;
			$valeurs['val_max_'.$cle] = 2;
			$valeurs['pas_var_'.$cle] = 0.1;
			if(_request('choix_champs_'.$cle)=='cal') {
				$sVarCal = $cle;
			}
		}
		$valeurs['choix_champs_'.$sVarCal] = 'cal';

		return $valeurs;
	}


	/**
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
	 */
	function charge_data($bLibelles = true) {
		global $spip_lang;

		$tChOblig = $this->champs_obligatoires();
		$tChCalc = $this->champs_obligatoires(true);
		spip_log($tChOblig,'hydraulic');
		spip_log($tChCalc,'hydraulic');
		$choix_radio = array();
		$tLib = array();
		$data=array();
		$data['iPrec']=(int)-log10(_request('rPrec'));

		//On récupère les données
		foreach($tChOblig as $champ) {
			if (_request($champ)){
				$data[$champ] = _request($champ);
			} else {
				$data[$champ] = 999.;
			}
			$data[$champ] = str_replace(',','.',$data[$champ]); // Bug #574
		}
		//spip_log($data,'hydraulic');
		// On ajoute la langue en cours pour différencier le fichier de cache par langue
		$data['sLang'] = $spip_lang;

		// Nom du fichier en cache pour calcul déjà fait
		$data['CacheFileName']=md5(serialize($data));

		// On récupère les différents choix effectué sur les boutons radios ainsi que les libelles de tous les paramètres
		foreach($tChCalc as $cle){
			$choix_radio[$cle] = _request('choix_champs_'.$cle);
			if($bLibelles) {$data['tLib'][$cle] = _T('hydraulic:param_'.$cle);}
		}

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
		spip_log($data,'hydraulic',_LOG_DEBUG);
		return $data;
	}


	/**
	 * Vérification des données transmises et génération des messages d'erreur pour le formulaire
	 * @note Il faut l'utiliser après un appel à charge_data
	 */
	public function verifier() {
		$tCtrl = array();
		$tData = array();

		foreach($this->saisies as $fs) {
			foreach($fs[1] as $cle=>$val) {
				$tData[$cle] = (isset($this->data[$cle]))?$this->data[$cle]:999;
				$tCtrl[$cle] = $val[2];
			}
		}

		$erreurs = array();
		foreach($tCtrl as $Cle=>$Ctrl) {
			$tData[$Cle] = trim(str_replace(',','.',$tData[$Cle]));
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

		return $erreurs;
	}
}
?>