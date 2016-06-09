<?php
/**
 * Plugin daterubriques
 *
 * @plugin     daterubriques
 * @copyright  2011-2016
 * @author     Touti, Yffic
 * @licence    GPL 3
 * @package    SPIP\daterubriques\base
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

function daterubriques_declarer_champs_extras($champs = array()){

	$champs['spip_rubriques']['date_utile'] = array(
		'saisie' => 'date', //Type du champs (voir plugin Saisies)
		'options' => array(
			'nom' => 'date_utile',
			'label' => _T('daterubriques:date_label'),
			'sql' => "datetime NOT NULL DEFAULT '".date("Y-m-d 00:00:00")."'", // declaration sql
			'defaut' => '',
		),
		'verifier' => array(
			'type' => 'date',
			'options' => array(
				'normaliser' => 'datetime',
			)
		)
	);

	return $champs;
}
