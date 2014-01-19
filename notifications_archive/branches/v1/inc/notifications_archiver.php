<?php
/**
 * Fonctions utiles au plugin Archive notifications
 *
 * @plugin     Archive notifications
 * @copyright  2014
 * @author     Rainer
 * @licence    GNU/GPL
 * @package    SPIP\Notifications_archive\Inc
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

function inc_notifications_archiver_dist($data=array()){
    $data = pipeline('notifications_archive',$data);

    return  $data;
}

?>