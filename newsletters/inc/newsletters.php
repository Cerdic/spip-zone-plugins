<?php
/**
 * Plugin Newsletters
 * (c) 2012 Cedric Morin
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Extraire les infos de ce fond
 * Les parametres sont passes dans le squelette de newsletter sous la forme :
 * par <!-- param=valeur -->
 *
 * Parametres utilises : titre
 *
 * @param $fond
 * @return array
 */
function newsletters_fond_extraire_infos($fond){
	$infos = array();

	if ($f = trouver_fond($fond,"newsletters")){
		lire_fichier($f,$contenu);
		preg_match_all('/(<!-- ([a-z0-9_]\w+)(\*)?=)(.*?)-->/sim',$contenu,$matches,PREG_SET_ORDER);
		if ($matches){
			foreach ($matches as $m){
				newsletters_fond_post_params($infos,$m);
			}
		}
	}
	return $infos;
}

/**
 * callback pour interpreter les parametres objets d'une newsletter
 * (tire du plugin cfg)
 *
 * Parametres :
 * - $regs[2] = 'parametre'
 * - $regs[3] = '*' ou ''
 * - $regs[4] = 'valeur'
 *
 * Lorsque des parametres sont passes dans le formulaire
 * par <!-- param=valeur --><br>
 * stocker $this->param['parametre']=valeur
 *
 * Si <!-- param*=valeur --><br>
 * Stocker $this->param['parametre'][]=valeur
 *
 * @param array $param
 * @param array $regs
 */
function newsletters_fond_post_params(&$param,$regs) {

	// $regs[3] peut valoir '*' pour signaler un tableau
	$regs[4] = trim($regs[4]);

	if (empty($regs[3])) {
		$param[$regs[2]] = $regs[4];
	} elseif (is_array($this->param[$regs[2]])) {
		$param[$regs[2]][] = $regs[4];
	}
}