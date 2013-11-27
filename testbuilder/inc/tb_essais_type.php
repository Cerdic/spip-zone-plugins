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
				'Un texte avec des entit&amp;eacute;s echap&amp;eacute; &amp;amp;&amp;lt;&amp;gt;&amp;quot;',
				'Un texte avec des entit&#233;s num&#233;riques &#38;&#60;&#62;&quot;',
				'Un texte avec des entit&amp;#233;s num&amp;#233;riques echap&amp;#233;es &amp;#38;&amp;#60;&amp;#62;&amp;quot;',
				'Un texte sans entites &<>"\'',
				'{{{Des raccourcis}}} {italique} {{gras}} <code>du code</code>',
				'Un modele <modeleinexistant|lien=[->http://www.spip.net]>',
				"Un texte avec des retour
a la ligne et meme des

paragraphes",
			);
			break;
		case 'iso-string':
			$jeu = array(
				'',
				'0',
				'Un texte avec des <a href="http://spip.net">liens avec des accents ISO aàâä eéèêë iîï oô uùü</a> [Article 1 avec des accents ISO aàâä eéèêë iîï oô uùü->art1] [spip avec des accents ISO aàâä eéèêë iîï oô uùü->http://www.spip.net] http://www.spip.net',
				'Un texte avec des entit&eacute;s &amp;&lt;&gt;&quot; et avec des accents ISO aàâä eéèêë iîï oô uùü',
				'Un texte avec des entit&amp;eacute;s echap&amp;eacute; &amp;amp;&amp;lt;&amp;gt;&amp;quot; et avec des accents ISO aàâä eéèêë iîï oô uùü',
				'Un texte avec des entit&#233;s num&#233;riques &#38;&#60;&#62;&quot; et avec des accents ISO aàâä eéèêë iîï oô uùü',
				'Un texte avec des entit&amp;#233;s num&amp;#233;riques echap&amp;#233;es &amp;#38;&amp;#60;&amp;#62;&amp;quot; et avec des accents ISO aàâä eéèêë iîï oô uùü',
				'Un texte sans entites &<>"\' et avec des accents ISO aàâä eéèêë iîï oô uùü',
				'{{{Des raccourcis avec des accents ISO aàâä eéèêë iîï oô uùü}}} {italique avec des accents ISO aàâä eéèêë iîï oô uùü} {{gras avec des accents ISO aàâä eéèêë iîï oô uùü}} <code>du code avec des accents ISO aàâä eéèêë iîï oô uùü</code>',
				'Un modele avec des accents ISO aàâä eéèêë iîï oô uùü <modeleinexistant|lien=[avec des accents ISO aàâä eéèêë iîï oô uùü->http://www.spip.net]>',
				"Un texte avec des retour
a la ligne et meme des

paragraphes avec des accents ISO aàâä eéèêë iîï oô uùü",
			);
			break;
		case 'utf8-string':
			$jeu = array(
				'',
				'0',
				'Un texte avec des <a href="http://spip.net">liens avec des accents UTF-8 aÃ Ã¢Ã¤ eÃ©Ã¨ÃªÃ« iÃ®Ã¯ oÃ´ uÃ¹Ã¼</a> [Article 1 avec des accents UTF-8 aÃ Ã¢Ã¤ eÃ©Ã¨ÃªÃ« iÃ®Ã¯ oÃ´ uÃ¹Ã¼->art1] [spip avec des accents UTF-8 aÃ Ã¢Ã¤ eÃ©Ã¨ÃªÃ« iÃ®Ã¯ oÃ´ uÃ¹Ã¼->http://www.spip.net] http://www.spip.net',
				'Un texte avec des entit&eacute;s &amp;&lt;&gt;&quot; et avec des accents UTF-8 aÃ Ã¢Ã¤ eÃ©Ã¨ÃªÃ« iÃ®Ã¯ oÃ´ uÃ¹Ã¼',
				'Un texte avec des entit&amp;eacute;s echap&amp;eacute; &amp;amp;&amp;lt;&amp;gt;&amp;quot; et avec des accents UTF-8 aÃ Ã¢Ã¤ eÃ©Ã¨ÃªÃ« iÃ®Ã¯ oÃ´ uÃ¹Ã¼',
				'Un texte avec des entit&#233;s num&#233;riques &#38;&#60;&#62;&quot; et avec des accents UTF-8 aÃ Ã¢Ã¤ eÃ©Ã¨ÃªÃ« iÃ®Ã¯ oÃ´ uÃ¹Ã¼',
				'Un texte avec des entit&amp;#233;s num&amp;#233;riques echap&amp;#233;es &amp;#38;&amp;#60;&amp;#62;&amp;quot; et avec des accents UTF-8 aÃ Ã¢Ã¤ eÃ©Ã¨ÃªÃ« iÃ®Ã¯ oÃ´ uÃ¹Ã¼',
				'Un texte sans entites &<>"\' et avec des accents UTF-8 aÃ Ã¢Ã¤ eÃ©Ã¨ÃªÃ« iÃ®Ã¯ oÃ´ uÃ¹Ã¼',
				'{{{Des raccourcis avec des accents UTF-8 aÃ Ã¢Ã¤ eÃ©Ã¨ÃªÃ« iÃ®Ã¯ oÃ´ uÃ¹Ã¼}}} {italique avec des accents UTF-8 aÃ Ã¢Ã¤ eÃ©Ã¨ÃªÃ« iÃ®Ã¯ oÃ´ uÃ¹Ã¼} {{gras avec des accents UTF-8 aÃ Ã¢Ã¤ eÃ©Ã¨ÃªÃ« iÃ®Ã¯ oÃ´ uÃ¹Ã¼}} <code>du code avec des accents UTF-8 aÃ Ã¢Ã¤ eÃ©Ã¨ÃªÃ« iÃ®Ã¯ oÃ´ uÃ¹Ã¼</code>',
				'Un modele avec des accents UTF-8 aÃ Ã¢Ã¤ eÃ©Ã¨ÃªÃ« iÃ®Ã¯ oÃ´ uÃ¹Ã¼ <modeleinexistant|lien=[avec des accents UTF-8 aÃ Ã¢Ã¤ eÃ©Ã¨ÃªÃ« iÃ®Ã¯ oÃ´ uÃ¹Ã¼->http://www.spip.net]>',
				"Un texte avec des retour
a la ligne et meme des

paragraphes avec des accents UTF-8 aÃ Ã¢Ã¤ eÃ©Ã¨ÃªÃ« iÃ®Ã¯ oÃ´ uÃ¹Ã¼",
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
		case 'mimetype':
			$jeu = array(
				// Images reconnues par PHP
				'jpg'=>'image/jpeg',
				'png'=>'image/png',
				'gif'=>'image/gif',

				// Autres images (peuvent utiliser le tag <img>)
				'bmp'=>'image/x-ms-bmp', // pas enregistre par IANA, variante: image/bmp
				'tif'=>'image/tiff',

				// Multimedia (peuvent utiliser le tag <embed>)
				'aiff'=>'audio/x-aiff',
				'asf'=>'video/x-ms-asf',
				'avi'=>'video/x-msvideo',
				'anx'=>'application/annodex',
				'axa'=>'audio/annodex',
				'axv'=>'video/annodex',
				'dv'=> 'video/x-dv',
				'flac' => 'audio/x-flac',
				'flv' => 'video/x-flv',
				'm4a' => 'audio/mp4a-latm',
				'm4b' => 'audio/mp4a-latm',
				'm4p' => 'audio/mp4a-latm',
				'm4u' => 'video/vnd.mpegurl',
				'm4v' => 'video/x-m4v',
				'mid'=>'audio/midi',
				'mka' => 'audio/mka',
				'mkv' => 'video/mkv',
				'mng'=>'video/x-mng',
				'mov'=>'video/quicktime',
				'mp3'=>'audio/mpeg',
				'mp4' => 'application/mp4',
				'mpg'=>'video/mpeg',
				'oga' => 'audio/ogg',
				'ogg' => 'audio/ogg ',
				'ogv' => 'video/ogg ',
				'ogx' => 'application/ogg ',
				'qt' =>'video/quicktime',
				'ra' =>'audio/x-pn-realaudio',
				'ram'=>'audio/x-pn-realaudio',
				'rm' =>'audio/x-pn-realaudio',
				'spx' => 'audio/ogg',
				'svg'=>'image/svg+xml',
				'swf'=>'application/x-shockwave-flash',
				'wav'=>'audio/x-wav',
				'wmv'=>'video/x-ms-wmv',
				'3gp'=>'video/3gpp',

				// Documents varies
				'ai' =>'application/illustrator',
				'abw' =>'application/abiword',
				'bin' => 'application/octet-stream', # le tout-venant
				'blend' => 'application/x-blender',
				'bz2'=>'application/x-bzip2',
				'c'  =>'text/x-csrc',
				'css'=>'text/css',
				'csv'=>'text/csv',
				'deb'=>'application/x-debian-package',
				'doc'=>'application/msword',
				'djvu'=>'image/vnd.djvu',
				'dvi'=>'application/x-dvi',
				'eps'=>'application/postscript',
				'gz' =>'application/x-gzip',
				'h'  =>'text/x-chdr',
				'html'=>'text/html',
				'kml'=>'application/vnd.google-earth.kml+xml',
				'kmz'=>'application/vnd.google-earth.kmz',
				'pas'=>'text/x-pascal',
				'pdf'=>'application/pdf',
				'pgn' =>'application/x-chess-pgn',
				'ppt'=>'application/vnd.ms-powerpoint',
				'ps' =>'application/postscript',
				'psd'=>'image/x-photoshop', // pas enregistre par IANA
				'rpm'=>'application/x-redhat-package-manager',
				'rtf'=>'application/rtf',
				'sdd'=>'application/vnd.stardivision.impress',
				'sdw'=>'application/vnd.stardivision.writer',
				'sit'=>'application/x-stuffit',
				'sxc'=>'application/vnd.sun.xml.calc',
				'sxi'=>'application/vnd.sun.xml.impress',
				'sxw'=>'application/vnd.sun.xml.writer',
				'tex'=>'text/x-tex',
				'tgz'=>'application/x-gtar',
				'torrent' => 'application/x-bittorrent',
				'ttf'=>'application/x-font-ttf',
				'txt'=>'text/plain',
				'xcf'=>'application/x-xcf',
				'xls'=>'application/vnd.ms-excel',
				'xspf'=>'application/xspf+xml',
				'xml'=>'application/xml',
				'zip'=>'application/zip',

				// Open Document format
				'odt' => 'application/vnd.oasis.opendocument.text',
				'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
				'odp' => 'application/vnd.oasis.opendocument.presentation',
				'odg' => 'application/vnd.oasis.opendocument.graphics',
				'odc' => 'application/vnd.oasis.opendocument.chart',
				'odf' => 'application/vnd.oasis.opendocument.formula',
				'odb' => 'application/vnd.oasis.opendocument.database',
				'odi' => 'application/vnd.oasis.opendocument.image',
				'odm' => 'application/vnd.oasis.opendocument.text-master',
				'ott' => 'application/vnd.oasis.opendocument.text-template',
				'ots' => 'application/vnd.oasis.opendocument.spreadsheet-template',
				'otp' => 'application/vnd.oasis.opendocument.presentation-template',
				'otg' => 'application/vnd.oasis.opendocument.graphics-template',


				'cls'=>'text/x-tex',
				'sty'=>'text/x-tex',

				// Open XML File Formats
				'docm' => 'application/vnd.ms-word.document.macroEnabled.12',
				'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
				'dotm' => 'application/vnd.ms-word.template.macroEnabled.12',
				'dotx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.template',

				'potm' => 'application/vnd.ms-powerpoint.template.macroEnabled.12',
				'potx' => 'application/vnd.openxmlformats-officedocument.presentationml.template',
				'ppam' => 'application/vnd.ms-powerpoint.addin.macroEnabled.12',
				'ppsm' => 'application/vnd.ms-powerpoint.slideshow.macroEnabled.12',
				'ppsx' => 'application/vnd.openxmlformats-officedocument.presentationml.slideshow',
				'pptm' => 'application/vnd.ms-powerpoint.presentation.macroEnabled.12',
				'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',

				'xlam' => 'application/vnd.ms-excel.addin.macroEnabled.12',
				'xlsb' => 'application/vnd.ms-excel.sheet.binary.macroEnabled.12',
				'xlsm' => 'application/vnd.ms-excel.sheet.macroEnabled.12',
				'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
				'xltm' => 'application/vnd.ms-excel.template.macroEnabled.12',
				'xltx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.template'
			);
			return $jeu;
			break;
		case 'version':
			$jeu = array(
				"2",
				"2.0",
				"2.0.0",
				"2.0.0dev",
				"2.0.0alpha",
				"2.0.0beta",
				"2.0.0rc",
				"2.0.0#",
				"2.0.0pl",
				"2.0.1"
			);
			return $jeu;
		case 'operateur':
			$jeu = array(
				"<",
				">",
				"=",
				"<=",
				">="
			);
			return $jeu;
			break;
	}
	return $jeu;
}

?>