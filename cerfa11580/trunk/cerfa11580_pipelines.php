<?php
if (!defined('_ECRIRE_INC_VERSION'))
	return;

/**
 * Interface avec le plugin "Associaspip"
**/
function cerfa11580_associaspip($liste) {
	return $liste['cerfa11580'] = array(
		1 => array('cerfa11580:configuration', 'cerfa11580.gif', array('configurer_cerfa11580'), array('editer_profil', 'association') ), // 1: profil_asso
    );
}

?>