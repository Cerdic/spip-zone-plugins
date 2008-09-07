<?php

//ce fichier doit contenir plusieurs tableaux 
function bible_tableau($i){
	$tableau_traduction = array(
	'na'		=> array(
		'traduction'=> 'Nestl&eacute;-Aland',
		'gateway'	=> false,
		'lang'		=>'grc',
		'wissen'	=>'novum-testamentum-graece-na-27'),
	'jerusalem' => array(
		'traduction'=> 'Bible de J&eacute;rusalem',
		'gateway'	=> false,
		'lang'		=> 'fr'),
	'lsg' => array(
		'traduction'=> 'Louis Segond 1910',
		'gateway'	=> 2,
		'lang'		=> 'fr'),
	'bds' => array(
		'traduction'=> 'Bible du Semeur',
		'gateway'	=> 32,
		'lang'		=> 'fr'),
	'kj21' => array(
		'traduction'=> 'The 21st Century King James',
		'gateway'	=> 48,
		'lang'		=> 'en'),
	'darb_en' => array(
		'traduction'=> 'Darby',
		'gateway'	=> 16,
		'lang'		=> 'en'),
	'kj'		=> array(
		'traduction'=>'King Jame',
		'gateway'	=>9,
		'lang'		=>'en'
	)
);

$livre_gateways = array(
		'de'=>array(
			'Gen'=> 1,			'Ex'=> 2,			'Lev'=> 3,			'Num'=> 4,			'Dtn'=> 5,			'Jos'=> 6,			'Ri'=> 7,			'Rut'=> 8,			'1Sam'=> 9,			'2Sam'=> 10,			'1Kön'=> 11,			'2Kön'=> 12,			'1Chr'=> 13,			'2Chr'=> 14,			'Esra'=> 15,			'Neh'=> 16,			'Tob'=> 17,			'Jdt'=> 18,			'Est'=> 19,			'1Makk'=> 20,			'2Makk'=> 21,			'Ijob'=> 22,			'Ps'=> 23,			'Spr'=> 24,			'Koh'=> 25,			'Hld'=> 26,			'Weish'=> 27,			'Sir'=> 28,			'Jes'=> 29,			'Jer'=> 30,			'Klgl'=> 31,			'Bar'=> 32,			'Ez'=> 33,			'Dan'=> 34,			'Hos'=> 35,			'Joël'=> 36,			'Am'=> 37,			'Obd'=> 38,			'Jona'=> 39,			'Mi'=> 40,			'Nah'=> 41,			'Hab'=> 42,			'Zef'=> 43,			'Hag'=> 44,			'Sach'=> 45,			'Mal'=> 46,			'Mt'=> 47,			'Mk'=> 48,			'Lk'=> 49,			'Joh'=> 50,			'Apg'=> 51,			'Röm'=> 52,			'1Kor'=> 53,			'2Kor'=> 54,			'Gal'=> 55,			'Eph'=> 56,			'Phil'=> 57,			'Kol'=> 58,			'1Thess'=> 59,			'2Thess'=> 60,			'1Tim'=> 61,			'2Tim'=> 62,			'Tit'=> 63,			'Phlm'=> 64,			'Hebr'=> 65,			'Jak'=> 66,			'1Petr'=> 67,			'2Petr'=> 68,			'1Joh'=> 69,			'2Joh'=> 70,			'3Joh'=> 71,			'Jud'=> 72,			'Offb'=> 73)
,

		
		'en'=>array(			'Gn'=>1,			'1Ch'=>13,			'1Co'=>53,			'1Jn'=>69,			'1M'=>20,			'1P'=>67,			'1K'=>11,			'1S'=>9,			'1Th'=>59,			'1Tm'=>61,			'2Ch'=>14,			'2Co'=>54,			'2Jn'=>70,			'2M'=>21,			'2 P'=>68,			'2K'=>12,			'2S'=>10,			'2th'=>60,			'2Tm'=>62,			'3Jn'=>71,			'Hab'=>42,			'Ob'=>38,			'Hg'=>44,			'Am'=>37,			'Rv'=>73,			'Ac'=>51,			'Ba'=>32,			'Sg'=>26,			'Col'=>58,			'Dn'=>34,			'Dt'=>5,			'Heb'=>65,			'Qo'=>25,			'Ep'=>56,			'ezr'=>15,			'Est'=>19,			'Ex'=>2,			'Ezk'=>33,			'Phm'=>64,			'Ph'=>57,			'Ga'=>55,			'Jr'=>30,			'Jm'=>66,			'Jb'=>22,			'Jl'=>36,			'Jon'=>39,			'Jn'=>50,			'Jude'=>72,			'Jdt'=>18,			'Is'=>29,			'Jg'=>7,			'Jos'=>6,			'Lm'=>31,			'Lk'=>49,			'Lv'=>3,			'Ml'=>46,			'Mk'=>48,			'Mt'=>47,			'Mi'=>40,			'Na'=>41,			'Ne'=>16,			'Nb'=>4,			'Hos'=>35,			'Pr'=>24,			'Rm'=>52,			'Rt'=>8,			'Ps'=>23,			'Ws'=>27,			'Si'=>28,			'Zp'=>43,			'Tt'=>63,			'Tb'=>17,			'Zc'=>45),
		'fr'=>array(			'1Ch'=>13,			'1Co'=>53,			'1Jn'=>69,			'1M'=>20,			'1P'=>67,			'1R'=>11,			'1S'=>9,			'1Th'=>59,			'1Tm'=>61,			'2Ch'=>14,			'2Co'=>54,			'2Jn'=>70,			'2M'=>21,			'2P'=>68,			'2R'=>12,			'2S'=>10,			'2Th'=>60,			'2Tm'=>62,			'3Jn'=>71,			'Ab'=>38,			'Ac'=>51,			'Ag'=>44,			'Am'=>37,			'Ap'=>73,			'Ba'=>32,			'Col'=>58,			'Ct'=>26,			'Dn'=>34,			'Dt'=>5,			'Ep'=>56,			'Esd'=>15,			'Est'=>19,			'Ex'=>2,			'Ez'=>33,			'Ga'=>55,			'Gn'=>1,			'Ha'=>42,			'He'=>65,			'Is'=>29,			'Jb'=>22,			'Jc'=>66,			'Jdt'=>18,			'Jg'=>7,			'Jl'=>36,			'Jn'=>50,			'Jon'=>39,			'Jos'=>6,			'Jr'=>30,			'Jude'=>72,			'Lc'=>49,			'Lm'=>31,			'Lv'=>3,			'Mc'=>48,			'Mi'=>40,			'Ml'=>46,			'Mt'=>47,			'Na'=>41,			'Nb'=>4,			'Ne'=>16,			'Os'=>35,			'Ph'=>57,			'Phm'=>64,			'Pr'=>24,			'Ps'=>23,			'Qo'=>25,			'Rm'=>52,			'Rt'=>8,			'Sg'=>27,			'Si'=>28,			'So'=>43,			'Tb'=>17,			'Tt'=>63,			'Za'=>45));



$tableau_livres = array(
	'de'=>array(
		'Gen'=>'Genesis',		'Ex'=>'Exodus',		'Lev'=>'Levitikus',		'Num'=>'Numeri',		'Dtn'=>'Deuteronomium',		'Jos'=>'Josua',		'Ri'=>'Richter',		'Rut'=>'Rut',		'1Sam'=>'1Samuel',		'2Sam'=>'2Samuel',		'1Kön'=>'1Könige',		'2Kön'=>'2Könige',		'1Chr'=>'1Chronik',		'2Chr'=>'2Chronik',		'Esra'=>'Esra',		'Neh'=>'Nehemia',		'Tob'=>'Tobit/Tobias',		'Jdt'=>'Judit',		'Est'=>'Ester',		'1Makk'=>'1Makkabäer',		'2Makk'=>'2Makkabäer',		'Ijob'=>'Ijob/Hiob',		'Ps'=>'Psalter',		'Spr'=>'Sprichwörter/Sprüche',		'Koh'=>'Kohelet/Prediger',		'Hld'=>'Hoheslied',		'Weish'=>'Weisheit',		'Sir'=>'Sirach',		'Jes'=>'Jesaja',		'Jer'=>'Jeremia',		'Klgl'=>'Klagelieder',		'Bar'=>'Baruch',		'Ez'=>'Ezechiel/Hesekiel',		'Dan'=>'Daniel',		'Hos'=>'Hosea',		'Joël'=>'Joel',		'Am'=>'Amos',		'Obd'=>'Obadja',		'Jona'=>'Jona',		'Mi'=>'Micha',		'Nah'=>'Nahum',		'Hab'=>'Habakuk',		'Zef'=>'Zefanja',		'Hag'=>'Haggai',		'Sach'=>'Sacharja',		'Mal'=>'Maleachi',		'Mt'=>'Matthäus',		'Mk'=>'Markus',		'Lk'=>'Lukas',		'Joh'=>'Johannes',		'Apg'=>'Apostelgeschichte',		'Röm'=>'Römer',		'1Kor'=>'1Korinther',		'2Kor'=>'2Korinther',		'Gal'=>'Galater',		'Eph'=>'Epheser',		'Phil'=>'Philipper',		'Kol'=>'Kolosser',		'1Thess'=>'1Thessalonicher',		'2Thess'=>'2Thessalonicher',		'1Tim'=>'1Timotheus',		'2Tim'=>'2Timotheus',		'Tit'=>'Titus',		'Phlm'=>'Philemon',		'Hebr'=>'Hebräer',		'Jak'=>'Jakobus',		'1Petr'=>'1Petrus',		'2Petr'=>'2Petrus',		'1Joh'=>'1Johannes',		'2Joh'=>'2Johannes',		'3Joh'=>'3Johannes',		'Jud'=>'Judas',		'Offb'=>'Offenbarung'),
	
	'fr'=> array('Gn'=>'Gen&egrave;se',
	'Ex'=>'Exode',
	'Lv'=>'L&eacute;vitique',
	'Nb'=>'Nombres',
	'Dt'=>'Deut&eacute;ronome',
	'Jos'=>'Josu&eacute;',
	'Jg'=>'Juges',
	'1S'=>'1 Samuel',
	'2S'=>'2 Samuel',
	'1R'=>'1 Rois',
	'2R'=>'2 Rois',
	'1Ch'=>'1 Chroniques',
	'2Ch'=>'2 Chroniques',
	'Esd'=>'Esdras',
	'Ne'=>'N&eacute;h&eacute;mie',
	'1M'=>'1 Maccab&eacute;es',
	'2M'=>'2 Maccab&eacute;es',
	'Is'=>'Isa&iuml;e',
	'Es'=>'Esa&iuml;e',
	'Jr'=>'J&eacute;r&eacute;mie',
	'Ez'=>'Ez&eacute;quiel',
	'Os'=>'Os&eacute;e',
	'Jl'=>'Jo&euml;l',
	'Am'=>'Amos',
	'Ab'=>'Abdias',
	'Jon'=>'Jonas',
	'Mi'=>'Mich&eacute;e',
	'Na'=>'Nahum',
	'So'=>'Sophonie',
	'Ag'=>'Agg&eacute;e',
	'Za'=>'Zacharie',
	'Ml'=>'Malachie',
	'Dn'=>'Daniel',
	'Jb'=>'Job',
	'Pr'=>'Proverbes',
	'Qo'=>'Qoh&eacute;leth (Eccl&eacute;siaste)',
	'Ct'=>'Cantiques des cantiques',
	'Rt'=>'Ruth',
	'Lm'=>'Lamentations',
	'Est'=>'Esther',
	'Tb'=>'Tobie',
	'Jdt'=>'Judith',
	'Ba'=>'Baruch',
	'Sg'=>'Sagesse',
	'Si'=>'Siracide (Eccl&eacute;siastique)',
	'Ps'=>'Psaumes',
	'Mt'=>'Matthieu',
	'Lc'=>'Luc',
	'Jn'=>'Jean',
	'Mc'=>'Marc',
	'Ac'=>'Acte des Ap&ocirc;tres',
	'Ro'=>'Romains',
	'1Co'=>'1 Corinthiens',
	'2Co'=>'2 Corinthiens',
	'Ga'=>'Galates',
	'Ep'=>'Eph&eacute;siens',
	'Ph'=>'Philippiens',
	'Col'=>'Colossiens',
	'1Th'=>'1 Thessaloniciens',
	'2Th'=>'2 Thessaloniciens',
	'1Tm'=>'1 Timoth&eacute;e',
	'2Tm'=>'2 Timoth&eacute;e',
	'Tt'=>'Tite',
	'Phm'=>'Phil&eacute;mon',
	'He'=>'H&eacute;breux',
	'Jc'=>'Jacques',
	'1P'=>'1 Pierre',
	'2P'=>'2 Pierre',
	'1Jn'=>'1 Jean',
	'2Jn'=>'2 Jean',
	'3Jn'=>'3 Jean',
	'Jude' => 'Jude',
	'Ap'=>'Apocalypse'
	 ),
	'en' =>array(
		'Gn'=>'Genesis',
		'1Ch'=>'1 Chronicles',
		'1Co'=>'1 Corinthians',
		'1Jn'=>'1 John',
		'1M'=>'1 Maccabees',
		'1 P'=>'1 Peter',
		'1K'=>'1 Kings',
		'1S'=>'1 Samuel',
		'1th' =>'2 Thessalonians',
		'1Tm' =>'1 Timothy',
		'1Ch'=>'2 Chronicles',
		'2Co'=>'2 Corinthians',
		'2Jn'=>'2 John',
		'2Mc' =>'2 Maccabbes',
		'2 P'=> '2 Peter',
		'2K' =>'2 Kings',
		'2S' =>'2 Samuel',
		'2th'=>'2 Thessalonians',
		'2Tm' =>'2 Timothy',
		'3Jn'  =>'3 John',
		'Hab' =>'Habakkuk',
		'Ob' =>'Obadiah',
		'Hg'=>'Haggai',
		'Am'=>'Amos',
		'Rv'=>'Apocalypse (Revelation)',
		'Ac'=>'Acts of the Apostles',
		'Ba'=>'Baruch',
		'Sg'=>'Song of Songs',
		'Col'=>'Colossians',
		'Dn'=>'Daniel',
		'Dt'=>'Deuteronom',
		'Heb'=>'Hebrews',
		'Qo'=>'Qohelet (Ecclesiastes)',
		'Ep'=>'Ephesians',
		'ezr'=>'Ezra',
		'Est'=>'Esther',
		'Ex'=>'Exodus',
		'Ezk'=>'Ezekiel',
		'Phm'=>'Philemon',
		'Ph'=>'Philippians',
		'Ga'=>'Galates',
		'Jr'=>'Jeremiah',
		'Jm'=>'James',
		'Jb'=>'Job',
		'Jl'=>'Joel',
		'Jon'=>'Jonas',
		'Jn'=>'John',
		'Jude'=>'Jude',
		'Jdt'=>'Judith',
		'Is'=>'Isaiah',
		'Jg'=>'Judges',
		'Jos'=>'Joshua',
		'Lm'=>'Lamentations',
		'Lk'=>'Luk',
		'Lv'=>'Levitic',
		'Ml'=>'Malachi',
		'Mk'=>'Mark',
		'Mt'=>'Matthieu',
		'Mi'=>'Micah',
		'Na'=>'Nahum',
		'Ne'=>'Neemiah',
		'Nb'=>'Numbers',
		'Hos'=>'Hosea',
		'Pr'=>'Proverbs',
		'Rm'=>'Romans',
		'Rt'=>'Ruth',
		'Ps'=>'Psalms',
		'Ws'=>'Wisdom',
		'Si'=>'Sirach',
		'Zp'=>'Zephaniah',
		'Tt'=>'Titus',
		'Tb'=>'Tobit',
		'Zc'=>'Zechariah'
	)	 
	 
);

$tableau_separateur = array(
	'fr'=>',',
	'en'=>':',
	'de'=>',');

	
	switch ($i){
		case 'traduction':
			return $tableau_traduction;
		case 'separateur':
			return $tableau_separateur;
		case 'gateway':
			return $livre_gateways;
		case 'livres':
			return $tableau_livres;
	}
};

?>