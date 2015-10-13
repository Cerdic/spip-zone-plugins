<?php
/**
 * Fonction d'API du plugin Html2Spip
 *
 * @plugin     Html2Spip
 * @copyright  2015
 * @author     bystrano
 * @licence    GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/*
 * Reconversion HTML vers typo SPIP
 *
 * Code piqué là https://github.com/VertigeASBL/migration/blob/master/migration_fonctions.php#L21
 *
 * @param String $html : le code html
 * @return String : le code converti en raccourcis SPIP
 */
function inc_html2spip_dist ($html) {

    include_spip('lib/html2spip/misc_tools');
    require_once(find_in_path('lib/html2spip/HTMLEngine.class'));
    require_once(find_in_path('lib/html2spip/HTML2SPIPEngine.class'));

    $parser = new HTML2SPIPEngine($GLOBALS['db_ok']['link'], _DIR_IMG);
    $parser->loggingEnable();
    $identity_tags = 'script;embed;param;object';
    $parser->addIdentityTags(explode(';', $identity_tags));
    $output = $parser->translate($html);
    return trim($output['default']);
}