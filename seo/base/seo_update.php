<?php
/**
* BouncingOrange SPIP SEO plugin
*
* @category   SEO
* @package    SPIP_SEO
* @author     Pierre ROUSSET (p.rousset@gmail.com)
* @copyright  Copyright (c) 2009 BouncingOrange (http://www.bouncingorange.com)
* @license    http://opensource.org/licenses/gpl-2.0.php  General Public License (GPL 2.0)
*/

if (! defined('_ECRIRE_INC_VERSION') ) return;

$GLOBALS['seo_base_version'] = '1.0';

/**
 * Installation and upgrade hook.
 */
function seo_install($install) {
    switch ($install) {
        case 'test':
            return seo_test();
            break;
        case 'install':
            return seo_update();
            break;
        case 'uninstall':
            return seo_uninstall();
            break;
    }
}

function seo_test() {
    global $seo_base_version, $meta;
    return (isset($meta['seo_base_version']) &&  ($meta['seo_base_version'] >= $seo_base_version) );
}

function seo_update() {
    global $meta;
    $code = $GLOBALS['seo_base_version'];
    $curr = '1.0'; // default value if never installed before
    if ( isset($meta['seo_base_version']) ) {
        $curr = $meta['seo_base_version'];
        if ($curr >= $code) {
            return;
        }
    }

    # Install or update the database
    include_spip('base/abstract_sql');

	// Create the database for new version (never installed before)
    if ('1.0' == $curr) {

        sql_create('seo_meta_tags',
            array(
                'id_object'   => "INT(11) NOT NULL",
                'type_object' => "VARCHAR(10) NOT NULL",
                'meta_name'    => "VARCHAR(20) NOT NULL",
                'meta_content'   => "TEXT NOT NULL",
				),
            array(
                'PRIMARY KEY' => 'id_object, type_object, meta_name'
            ),
            TRUE
        );

        $curr = '1.0';
    }
	
    ecrire_meta('seo_base_version', $curr);
    ecrire_metas();
    return;
}

function seo_uninstall() {
    include_spip('base/abstract_sql');

    # Delete tables
    sql_drop_table("seo_meta_tags");
	
    # Delete settings
    effacer_meta('seo'); 
    effacer_meta('seo_base_version');
    ecrire_metas();
	
    return;
}
