<?php
/*
 * Plugin CFG pour SPIP
 * (c) toggg, marcimat 2009, distribue sous licence GNU/GPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

// surcharger la regexp pour ajouter l'analyse des classes CSS
define('_CFG_REGEXP_CHAMP', '#<(?:(select|textarea)|input type="(text|password|checkbox|radio|hidden|file)") name="(\w+)(\[\])?"(?: class="([^"]*)")?[^>]*?>#ims');

// charger la classe CFG d'origine pour l'etendre
include_spip('inc/cfg_formulaire_dist');

class cfg_formulaire extends cfg_formulaire_dist {

// provient-on d'un formulaire de type CVT (charger/verifier/traiter) dans formulaires/ ?
	var $depuis_cvt = false;
// y a t-il des extensions (classes css 'type_{nom}' ou 'cfg_{nom}' sur champs) a traiter ?
	var $extensions = array();
	
	function cfg_formulaire($nom, $cfg_id = '', $opt = array()){
		$this->cfg_formulaire_dist($nom, $cfg_id, $opt);
		$this->param = array_merge(array(
			'head' => '', // partie du fond cfg a inserer dans le head par le pipeline header_prive
			'inline' => '', // code qui sera insere apres le contenu du fond (peut servir pour inserer du js)
			
			'liens' => array(), // liens optionnels sur des sous-config <!-- liens*=xxx -->
			'liens_multi' => array(), // liens optionnels sur des sous-config pour des fonds utilisant un champ multiple  <!-- liens_multi*=xxx -->
			'onglet' => 'oui', // cfg doit-il afficher un lien vers le fond sous forme d'onglet dans la page ?exec=cfg
			'presentation' => 'auto', // cfg doit-il encadrer le formulaire tout seul ?
		), $this->param);
	}

	// il s'agit de recuperer le contenu du fichier
	// on cherche aussi dans fonds/ comme avant
	function trouver_formulaire(){
		// si on appelle expressement un 'cfg/xx.html' pour
		// simplement obtenir les <!-- param=valeur -->
		if (false!==strpos($this->vue,'/')) {
			$fichier = find_in_path($nom = $this->vue .'.html');
			$this->param['interpreter'] = 'non'; // ne pas interpreter par defaut du coup.
		} else {
			// sinon recherche de formulaire normal
			if (!$fichier = find_in_path($nom = 'fonds/cfg_' . $this->vue .'.html')){
				if ($fichier = find_in_path($nom = 'formulaires/' . $this->vue .'.html'))
					$this->depuis_cvt = true;
			}
		}
		return array($fichier, $nom);
	}

	// securiser les actions des formulaires CFG non CVT.
	function securiser() {
		if (!$this->depuis_cvt) {
			$securiser_action = charger_fonction('securiser_action', 'inc');
			$securiser_action();
		}
	}
	
	// ajoute une extension (classe cfg_xx ou type_xx) 
	// ce qui dit a cfg d'executer des fonctions particulieres
	// si elles existent : ex: cfg_traiter_cfg_xx()
	// lors de l'appel de 'actionner_extensions($faire)'
	function ajouter_extension($ext, $nom){
		if (!is_array($this->extensions[$ext])) $this->extensions[$ext] = array();
		$this->extensions[$ext][] = $nom;	
	}
	

	
	// ajoute une extension sur un parametre
	// seulement si un fichier sur ce parametre existe
	function ajouter_extension_parametre($param){
		if (in_array($param, $this->extensions_parametres))
			return true;
		
		if (find_in_path('cfg/params/'.$param.'.php')){
			$this->extensions_parametres[] = $param;
			return true;
		}
		return false;
	}
	
	
	// SURCHARGE
	//
	// teste et charge les points d'entrees de CFG a travers certaines actions
	// 1 : fonctions generales cfg_{nom}_{action}
	// 
	// utilisations DEPRECIEES :
	// 2 : actions sur les types de champs particuliers
	//     notifies par 'type_XX' ou 'cfg_YY' sur les classes css
	//     s'ils existent dans /cfg/classes/ par des fonctions
	//     cfg_{action}_{classe}
	// 3 : actions en fonctions des parametres du formulaire
	//     s'ils existent dans /cfg/params/ par des fonctions
	//     cfg_{action}_{parametre}
	//
	// les actions possibles sont :
	// - pre_charger, charger, 
	// - pre_verifier, verifier, 
	// - pre_traiter, post_traiter
	//
	function actionner_extensions($action){
		// 1 - general : on transmet l'instance de cfg_formulaire
		if (function_exists($f = 'cfg_' . $this->vue . '_' . $action)) {
			$res = $f($this);
			// pour verifier, qui peut retourner un array comme cvt
			// il faut envoyer le resultat dans la fonction d'ajout des erreurs
			if ($action == 'verifier') {
				$this->ajouter_erreurs($res);
			}
		} 
		// 2 - type de champ : on transmet le nom du champ et l'instance de cfg_formulaire
		if ($this->extensions) {
			foreach ($this->extensions as $type => $champs){
				// si un fichier de ce type existe, on lance la fonction 
				// demandee pour chaque champs possedant la classe css en question
				if (include_spip('cfg/classes/'.$type)) {
					foreach ($champs as $champ){
						if (function_exists($f = 'cfg_' . $action . '_' . $type)){ // absence possible normale
							$f($champ, $this);
						}
					}
				}	
			}
		}
		// 3 - parametre : on transmet la valeur du parametre et l'instance de cfg_formulaire
		if ($this->extensions_parametres){
			foreach ($this->extensions_parametres as $param){
				if (include_spip('cfg/params/'.$param)) {
					if (function_exists($f = 'cfg_' . $action . '_param_' . $param)){ // absence possible normale
						// artillerie lourde on passe
						// la valeur et la classe
						$f($this->param[$param], $this);						
					}
				}
			}
		}
	}
	


	//
	// Recherche et stockage
	// des parametres #REM passes a CFG
	// (DEPRECIE)
	//
	function recuperer_parametres_rem(){
		// cas de #REM (deprecie)
		preg_replace_callback('/(\[\(#REM\) ([a-z0-9_]\w+)(\*)?=)(.*?)\]/sim',
					array(&$this, 'post_params'), $this->controldata);
	}
	
	
	// SURCHARGE
	// comme le core de CFG, mais en plus : (DEPRECIE)
	// - recuperer les vieux types de parametres #REM
	// - recuperer les vieux noms de depots (classic, extrapack...)
	// - permettre que chaque parametre puisse servir d'extension
	function recuperer_parametres(){

		// pour compatibilite, recuperer l'ancien code #REM
		$this->recuperer_parametres_rem();	
		
		$this->recuperer_fond();
		$this->fond_compile = preg_replace_callback(_CFG_REGEXP_PARAMETRE,
							array(&$this, 'post_params'), $this->fond_compile);


		// pour compatibilite avec les anciennes versions (<1.4.1)
		if (isset($this->param['storage'])) 
			$this->param['depot'] = $this->param['storage'];
		
		if ($this->param['depot'] == 'classic')
			$this->param['depot'] = 'meta';
			
		if ($this->param['depot'] == 'extrapack'){
			$this->param['depot'] = 'tablepack';
			$this->param['colonne'] = 'extra';
			$this->param['table'] = 'spip_auteurs';
		}
		
		// definir les parametres qui sont a traiter comme des extensions
		// il faut que le parametre ne soit pas vide et qu'un fichier 
		// /cfg/params/{param}.php existe
		$this->extensions_parametres = array();
		foreach ($this->param as $nom=>$val){
			if ($val) $this->ajouter_extension_parametre($nom);		
		}
	}	

	
	// SURCHARGE
	// En plus de ce que fait le core, 
	// on analyse aussi les classes CSS (DEPRECIE)
	// comme etant des parametres ou extensions possibles
	// 
	function recuperer_noms_champs(){	
		if (!$this->vue) return;

		// recherche d'au moins un champ de formulaire pour savoir si la vue est valide
		$this->recuperer_fond();
		if (!preg_match_all(_CFG_REGEXP_CHAMP, $this->fond_compile, $matches, PREG_SET_ORDER)) {
			return _T('cfg:pas_de_champs_dans', array('nom' => $this->vue));
		}

		foreach ($matches as $regs) {
			$name = $regs[3];
			if (substr($name, 0, 5) == '_cfg_') continue;

			// select, textarea ou input
			$this->champs[$name] = array('balise' => $regs[1]);
			
			// input type
		    if ($regs[2]) {
				$this->champs[$name]['type'] = $regs[2];
				$this->champs[$name]['balise'] = 'input';
			}
		    // champs tableau[]
			if ($regs[4]) $this->champs[$name]['tableau'] = true;


			//
			// Extensions et validations des champs
			// via les classes css
			//
			// attention : ordre important : <balise (type="xx")? name="xx" class="xx" />
			//
			if ($regs[5]) {
				$tcss = explode(' ',trim($regs[5]));
				foreach($tcss as $css){
					// classes css type_xx
					if (substr($css,0,5)=='type_') {
						$this->ajouter_extension($css, $name);
					// classes css cfg_xx
					} elseif (substr($css,0,4)=='cfg_') {
						$this->champs[$name]['cfg'] = substr($css,4); // juste 'id' si classe = cfg_id
						$this->ajouter_extension($css, $name);
					}
				}
			}
			
			// cas particulier automatiques : 
			// * input type file => type de verification : fichier
			if (($regs[2] == 'file') AND (!$this->champs[$name]['cfg'])){
				$this->champs[$name]['cfg'] = 'fichier';
				$this->ajouter_extension('cfg_fichier', $name);	
			}
			
	    }

	    return '';
	}	 
}

?>
