<?php
/**
 * Fonction d'upgrade/installation du plugin foundation-4-spip
 *
 * @plugin     foundation-4-spip
 * @copyright  2013
 * @author     Phenix
 * @licence    GNU/GPL
 * @package    SPIP\Foundation\Autorisations
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
* Plugin iView pour SPIP
* (c) 2012 Phenix
* Licence GNU/GPL
*/

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
* Fonction d'installation du plugin et de mise à jour.
* Vous pouvez :
* - créer la structure SQL,
* - insérer du pre-contenu,
* - installer des valeurs de configuration,
* - mettre à jour la structure SQL
**/
function iview_upgrade($nom_meta_base_version, $version_cible) {
    // Configuration de base de foundation (désactivé par défaut).
    $config_default = array(
        'foundation_version' => 0
    );

    ecrire_meta('foundation', serialize($config_default));
}



?>