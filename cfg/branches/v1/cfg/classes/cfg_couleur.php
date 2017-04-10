<?php
/**
 * Plugin générique de configuration pour SPIP
 *
 * @license    GNU/GPL
 * @package    plugins
 * @subpackage cfg
 * @category   outils
 * @copyright  (c) toggg, marcimat 2007-2008
 * @link       https://contrib.spip.net/
 * @version    $Id$
 */

if (!defined("_ECRIRE_INC_VERSION")) return;


 /**
 * apres que le parseur a trouve les champs (mais avant l'action 'charger' des parametres)
 * ajouter automatiquement le parametre 'selecteur_couleur'
 * (ajoute les js du plugin Palette et la librairie farbtastic d'une façon mutualisable entre plugins)
 * 
 * @param mixed $nom # inutilisé
 * @param Object $cfg
 * @return Object
 */
function cfg_charger_cfg_couleur($nom, &$cfg){

	$cfg->param['selecteur_couleur'] = 1;
	$cfg->ajouter_extension_parametre('selecteur_couleur');
	    
	return $cfg;
}


?>
