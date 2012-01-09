<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

// indiquer qu'on peut s'authentifier via une auth PMB
$GLOBALS['liste_des_authentifications']['pmb'] = 'pmb';

$GLOBALS['pmb_statut_nouvel_auteur'] = '6forum';

// ne pas faire planter #URL_x dans une boucle (pmb:n)
$GLOBALS['exception_des_connect'][] = 'pmb';


/**
 *  
 * Indiquer les inclusions en ajax parallele
 *  
**/
/*
// indiquer qu'on peut mettre des inclusions a chargement ajax
define('_Z_AJAX_PARALLEL_LOAD_OK', true);

// il n'y a pas de solution en SPIP 3 encore geniale pour faire un {ajaxload} sur
// une inclusion, mais on peut dire que tout ce qui se trouve dans le
// repertoire 'ajaxload' sera charge en parallele.
define('_Z_AJAX_PARALLEL_LOAD', 'ajaxload'); // separes par des virgules

// pour que la definition de _Z_AJAX_PARALLEL_LOAD fonctionne
// il faut que le bloc inclu soit un bloc Z
if (!isset($GLOBALS['z_blocs'])) {
	$GLOBALS['z_blocs'] = array('contenu');
}

if (!in_array('ajaxload', $GLOBALS['z_blocs'])) {
	$GLOBALS['z_blocs'][] = 'ajaxload';
}

*/
?>
