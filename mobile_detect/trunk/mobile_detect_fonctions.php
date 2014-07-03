<?php
/**
 * Fonctions utiles au plugin SPIP Mobile Detect
 *
 * @plugin     SPIP Mobile Detect
 * @copyright  2014
 * @author     Teddy Payet
 * @licence    GNU/GPL
 * @package    SPIP\Mobile_detect\Fonctions
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('lib/Mobile_Detect/Mobile_Detect');

/**
 * Récupérer le numéro de version de la librairie
 *
 * @return string
 *         Numéro de version sous la forme x.y.z
 */
function support_script_version ()
{
    $detect = new Mobile_Detect();

    return $detect->getScriptVersion();
}



/**
 * Lister les fonctions de la classe `Mobile_Detect`
 *
 * @return array
 */
function support_lister_methodes ()
{
    $methodes = get_class_methods(new Mobile_Detect);

    return $methodes;
}

/**
 * Lister les propriétés de la classe `Mobile_Detect`
 *
 * @return array
 */
function support_lister_proprietes ()
{
    $detect = new Mobile_Detect();

    return $detect->getProperties();
}

/**
 * Lister les téléphones reconnus par la librairie
 *
 * @return array
 */
function support_lister_telephones ()
{
    $detect = new Mobile_Detect();

    return $detect->getPhoneDevices();
}

/**
 * Lister les tablettes reconnues par la librairie
 *
 * @return array
 */
function support_lister_tablettes ()
{
    $detect = new Mobile_Detect();

    return $detect->getTabletDevices();
}



/**
 * Détermine si le support est une smartphone ou pas
 *
 * @return bool
 */
function support_est_telephone ()
{
    $detect = new Mobile_Detect();

    return $detect->isMobile();
}

/**
 * Détermine si le support est une tablette ou pas
 *
 * @return bool
 */
function support_est_tablette ()
{
    $detect = new Mobile_Detect();

    return $detect->isTablet();
}

/**
 * Détermine si le support est un ordinateur ou pas
 *
 * @return bool
 */
function support_est_ordinateur ()
{
    $detect = new Mobile_Detect();

    $ordinateur = true;

    if ($detect->isMobile() || $detect->isTablet()) {
        $ordinateur = false;
    }

    return $ordinateur;
}

/**
 * Vérifie si le paramètre existe dans le User-Agent
 *
 * @param  null|string $nom
 *         Nom à rechercher
 * @return null|bool
 */
function support_est ($nom = null)
{
    $detect = new Mobile_Detect();

    return $detect->is($nom);
}

/**
 * Retourne le numéro de version de l'élément demandé
 *
 * @param  string $nom
 * @return string|float
 *         Numéro de version du paramètre que nous recherchons.
 */
function support_version($nom = null)
{
    $detect = new Mobile_Detect();

    return $detect->version($nom);
}

?>