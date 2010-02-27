<?php
/*
 * Plugin TestBuilder
 * (c) 2010 Cedric MORIN Yterium
 * Distribue sous licence GPL
 *
 */


/**
 * Definir des jeu de valeurs test par type d'argument
 * @param string $type
 * @return array
 */
function inc_tb_essais_type_dist($type){
	$jeu = array();
	switch ($type){
		case 'bool':
			$jeu = array(true,false);
			break;
		case 'string':
			$jeu = array(
				'',
				'0',
				'Un texte avec des <a href="http://spip.net">liens</a> [Article 1->art1] [spip->http://www.spip.net] http://www.spip.net',
				'Un texte avec des entit&eacute;s &amp;&lt;&gt;&quot;',
				'Un texte sans entites &<>"\'',
				'{{{Des raccourcis}}} {italique} {{gras}} <code>du code</code>',
				'Un modele <modeleinexistant|lien=[->http://www.spip.net]>',
			);
			break;
		case 'email':
			$jeu = array(
				'jean',
				'jean@mapetiteentreprise.org',
				'jean.dujardin@mapetiteentreprise.org',
				'jean-dujardin@mapetiteentreprise.org',
				'jean@dujardin.name',
			);
			break;
		case 'date':
			$jeu = array(
				"2001-00-00 12:33:44",
				"2001-03-00 09:12:57",
				"2001-02-29 14:12:33",
				"0000-00-00",
				"0001-01-01",
				"1970-01-01"
			);
			$t = inc_tb_essais_type_dist('time');
			foreach($t as $d)
				$jeu[] = date('Y-m-d H:i:s',$d);
			foreach($t as $d)
				$jeu[] = date('Y-m-d',$d);
			foreach($t as $d)
				$jeu[] = date('Y/m/d',$d);
			foreach($t as $d)
				$jeu[] = date('d/m/Y',$d);
			break;
		case 'time':
			$jeu = array_map('strtotime',array(
				"2001-07-05 18:25:24",
				"2001-01-01 00:00:00",
				"2001-12-31 23:59:59",
				"2001-02-29 14:12:33",
				"2004-02-29 14:12:33",
				"2012-03-20 12:00:00",
				"2012-03-21 12:00:00",
				"2012-03-22 12:00:00",
				"2012-06-20 12:00:00",
				"2012-06-21 12:00:00",
				"2012-06-22 12:00:00",
				"2012-09-20 12:00:00",
				"2012-09-21 12:00:00",
				"2012-09-22 12:00:00",
				"2012-12-20 12:00:00",
				"2012-12-21 12:00:00",
				"2012-12-22 12:00:00")
			);
			break;
		case 'int':
			$jeu = array(
				0,
				-1,
				1,
				2,
				3,
				4,
				5,
				6,
				7,
				10,
				20,
				30,
				50,
				100,
				1000,
				10000
			);
			break;
		case 'int8':
			$jeu = array(
				0,
				7,
				15,
				63,
				127,
				191,
				255,
			);
			break;
		case 'float01':
			$jeu = array(
				0.0,
				0.25,
				0.5,
				0.75,
				1.0,
			);
			break;
		case 'array':
			$jeu = array(
				array(),
				inc_tb_essais_type_dist('string'),
				inc_tb_essais_type_dist('int'),
				inc_tb_essais_type_dist('bool'),
			);
			$jeu[] = $jeu; // et un array d'array
			break;
		case 'image':
			$jeu = array(
				'http://www.spip.net/squelettes/img/spip.png',
				'prive/images/logo_spip.jpg',
				'prive/images/logo-spip.gif',
				'prive/aide_body.css',
				'prive/images/searching.gif',
			);
			break;
	}
	return $jeu;
}

?>