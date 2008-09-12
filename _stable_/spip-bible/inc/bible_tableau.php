<?php

//ce fichier doit contenir plusieurs tableaux 


function bible_tableau($i,$lang=''){
	$nombre_chapitres = array(50,40,27,36,34,24,21,4,31,24,22,25,29,36,10,13,14,16,10,16,15,42,150,31,51,8,1,51,66,52,5,6,48,13,14,3,9,1,4,7,3,3,3,2,14,14,28,16,24,21,28,16,16,13,6,6,4,4,5,3,6,4,3,1,13,5,5,3,5,1,1,1,22);
	
	$deutero = array(17,18,19,20,21,28,32);
	$lire_la_bible = array(
		'1Ch' =>'1Chroniques',		'1R' =>'1Rois',		'1S' =>'1Samuel',		'2Ch' =>'2Chroniques',		'2R' =>'2Rois',		'2S' =>'2Samuel',		'Ab' =>'Abdias',		'Ag' =>'Aggee',		'Am' =>'Amos',		'Ct' =>'Cantiquedescantiques',		'Dn' =>'Daniel',		'Dt' =>'Deuteronome',		'Qo' =>'Ecclesiaste',		'Is' =>'Esaie',		'Ez' =>'Ezechiel',		'Esd' =>'Esdras',		'Est' =>'Esther',		'Ex' =>'Exode',		'Ez' =>'Ezechiel',		'Gn' =>'Genese',		'Ha' =>'Habacuc',		'Jr' =>'Jeremie',		'Jl' =>'Joel',		'Jb' =>'Job',		'Jon' =>'Jonas',		'Jos' =>'Josue',		'Jg' =>'Juges',		'Lm' =>'Lamentations',		'So' =>'Sophonie',		'Lv' =>'Levitique',		'Ml' =>'Malachie',		'Mi' =>'Michee',		'Na' =>'Nahoum',		'Ne' =>'Nehemie',		'Nb' =>'Nombres',		'Os' =>'Osee',		'Pr' =>'Proverbes',		'Ps' =>'Psaumes',		'Rt' =>'Ruth',		'1M' =>'1Maccabees',		'2M' =>'2Maccabees',		'Ba' =>'Baruch',		'Sg' =>'Sagesse',		'Si' =>'Siracide',		'Tb' =>'Tobit',		'Jdt' =>'Judith',		'1Co' =>'1Corinthiens',		'1Jn' =>'1Jean',		'1P' =>'1Pierre',		'1Th' =>'1Thessaloniciens',		'1Tm' =>'1Timothee',		'2Co' =>'2Corinthiens',		'2Jn' =>'2Jean',		'2P' =>'2Pierre',		'2Th' =>'2Thessaloniciens',		'2Tm' =>'2Timothee',		'3Jn' =>'3Jean',		'Ac' =>'Actes',		'Ap' =>'Apocalypse',		'Col' =>'Colossiens',		'Ep' =>'Ephesiens',		'Ga' =>'Galates',		'He' =>'Hebreux',		'Jc' =>'Jacques',		'Jn' =>'Jean',		'Jude' =>'Jude',		'Lc' =>'Luc',		'Mc' =>'Marc',		'Mt' =>'Matthieu',		'Ph' =>'Philippiens',		'Phm' =>'Philémon',		'Rm' =>'Romains',		'Tt' =>'Tite');
	$gateway_to_unboud = array
		(1=>'01O',		2=>'02O',		3=>'03O',		4=>'04O',		5=>'05O',		6=>'06O',		7=>'07O',		8=>'08O',		9=>'09O',		10=>'10O',		11=>'11O',		12=>'12O',		13=>'13O',		14=>'14O',		15=>'15O',		16=>'16O',		19=>'17O',		22=>'18O',		23=>'19O',		24=>'20O',		25=>'21O',		26=>'22O',		29=>'23O',		30=>'24O',		31=>'25O',		33=>'26O',		34=>'27O',		35=>'28O',		36=>'29O',		37=>'30O',		38=>'31O',		39=>'32O',		40=>'33O',		41=>'34O',		42=>'35O',		43=>'36O',		44=>'37O',		45=>'38O',		46=>'39O',		47=>'40N',		48=>'41N',		49=>'42N',		50=>'43N',		51=>'44N',		52=>'45N',		53=>'46N',		54=>'47N',		55=>'48N',		56=>'49N',		57=>'50N',		58=>'51N',		59=>'52N',		60=>'53N',		61=>'54N',		62=>'55N',		63=>'56N',		64=>'57N',		65=>'58N',		66=>'59N',		67=>'60N',		68=>'61N',		69=>'62N',		70=>'63N',		71=>'64N',		72=>'65N',		73=>'66N',		17=>'67A',		18=>'68A',		27=>'70A',		28=>'71A',		32=>'72A',		20=>'77A',		21=>'78A');

	$tableau_langue_original = array(
		'grc'=>'ltr',
		'hbo'=>'rtl',
		'la'=>'ltr',
		
		);
	
	$tableau_traduction = array(
	'bty'		=>array(
		'gateway'=>12,
		'traduction'=> 'Biblia Tysiaclecia',
		'lang'=>'pl',
		'nt'=> true,
		'at'=> true,
		'deutero'=> true
	),
	'pev'		=>array(
			'gateway'=>34,
			'traduction'=> 'La Parola è Vita',
			'lang'=>'it',
		'nt'=> true
		
			
				
					),
	'lnd'		=>array(
			'gateway'=>55,
			'traduction'=> 'La Nuova Diodati',
			'lang'=>'it',
		'nt'=> true,
		'at'=> true	
					),
	'cei'		=>array(
			'gateway'=>3,
			'traduction'=> 'Conferenza Episcopale Italiana',
			'lang'=>'it',
		'nt'=> true,
		'at'=> true,
		'deutero'=> true	
					),
	
	'rvantigua'		=>array(
			'gateway'=>61,
			'traduction'=> 'Reina-Valera Antigua',
			'lang'=>'es',
		'nt'=> true,
		'at'=> true	
					),
	'rv95'		=>array(
			'gateway'=>61,
			'traduction'=> 'Reina-Valera 1995',
			'lang'=>'es',
		'nt'=> true,
		'at'=> true	
					),

	'rv60'		=>array(
			'gateway'=>60,
			'traduction'=> 'Reina-Valera 1960',
			'lang'=>'es',
		'nt'=> true,
		'at'=> true	
					),
	'nvi'		=>array(
			'gateway'=>42,
			'traduction'=> 'Nueva Versión Internacional',
			'lang'=>'es',
		'nt'=> true,
		'at'=> true	
					),
	'americas'		=>array(
			'gateway'=>59,
			'traduction'=> 'La Biblia De Las Américas',
			'lang'=>'es',
		'nt'=> true,
		'at'=> true	
					),
	'bls'		=>array(
			'gateway'=>57,
			'traduction'=> 'Biblia en lenguaje sencillo',
			'lang'=>'es',
		'nt'=> true
		
					),
	'cast'		=>array(
			'gateway'=>41,
			'traduction'=> 'Castilian',
			'lang'=>'es',
		'nt'=> true
		
					),
	'dhh'		=>array(
			'gateway'=>58,
			'traduction'=> 'Dios Habla Hoy',
			'lang'=>'es',
		'nt'=> true,
		'at'=> true,
		'deutero'=> true	
					),
	
	'tob'		=>array(
			'lire'=>'TOB',
			'traduction'=> 'Traduction Œcuménique de la Bible',
			'lang'=>'fr',
		'nt'=> true,
		'at'=> true,
		'deutero'=> true	
					),
	'bfc'		=>array(
			'lire'=>'BFC',
			'traduction'=> 'Bible en Français Courant',
			'lang'=>'fr',
		'nt'=> true,
		'at'=> true,
		'deutero'=> true	
					),
	'nbs'		=>array(
			'lire'=>'NBS',
			'traduction'=> 'Nouvelle Bible Segond',
			'lang'=>'fr',
		'nt'=> true,
		'at'=> true	
					),
	'pdv'		=>array(
			'lire'=>'PDV',
			'traduction'=> 'La Bible Parole de Vie',
			'lang'=>'fr',
		'nt'=> true,
		'at'=> true,
		'deutero'=> true	
					),
	'colombe'		=>array(
			'lire'=>'Colombe',
			'traduction'=> 'Bible de la Colombe',
			'lang'=>'fr',
		'nt'=> true,
		'at'=> true	
					),
	'nachricht'		=> array(
		
		'wissen'=>'gute-nachricht-bibel',
		'traduction'=> 'Gute Nachricht Bibel ',
		'lang'		=>'de',
		'nt'=> true,
		'at'=> true,
		'deutero'=> true
			),
	'menge'		=> array(
		
		'wissen'=>'Menge Bibel',
		'traduction'=> 'Menge Bibel',
		'lang'		=>'de',
		'nt'=> true,
		'at'=> true,
		'deutero'=> true
			),
	'luther1984'		=> array(
		
		'wissen'=>'luther-bibel-1984',
		'traduction'=> 'Bibel von Luther (1984)',
		'lang'		=>'de',
		'nt'=> true,
		'at'=> true,
		'deutero'=> true
		
			),
	'luther1912'		=> array(
		
		'unbound'=>'german_luther_1912_ucs2',
		'traduction'=> 'Bibel von Luther (1912)',
		'lang'		=>'de',
		'nt'=> true,
		'at'=> true
			),
	'luther1545'		=> array(
		
		'unbound'=>'german_luther_1545_ucs2',
		'traduction'=> 'Bibel von Luther (1545)',
		'lang'		=>'de',
		'nt'=> true,
		'at'=> true,
		'historique'=>'La fameuse Bible que Luther publia en langue vulgaire en 1545.'
			),
	'lxx'		=> array(
		'wissen' 	=> 'septuaginta-lxx',
		'gateway'	=> false,
		'traduction'=> 'Septante',
		'lang'		=>'grc',
		'at'=> true,
		'deutero'=> true
			),
	'vulg'		=> array(
		'wissen' 	=> 'biblia-sacra-vulgata',
		'gateway'	=> false,
		'traduction'=> 'Biblia Sacra Vulgata',
		'lang'		=>'la',
		'nt'=> true,
		'at'=> true,
		'deutero'=> true
			),
		
	'bhs'		=> array(
		'wissen' 	=> 'biblia-hebraica-stuttgartensia-bhs',
		'gateway'	=> false,
		'traduction'=> 'Biblia Hebra&iuml;ca Stuttgartensia',
		'lang'		=>'hbo',
		
		'at'=> true
				),
	'na'		=> array(
		'traduction'=> 'Nestl&eacute;-Aland',
		'gateway'	=> false,
		'lang'		=>'grc',
		'wissen'	=>'novum-testamentum-graece-na-27',
		'nt'=> true)
		,
	'jerusalem' => array(
		'traduction'=> 'Bible de J&eacute;rusalem',
		'gateway'	=> false,
		'lang'		=> 'fr',
		'nt'=> true,
		'at'=> true,
		'deutero'=> true,
		'historique'=>'[Voir ici->http://fr.wikipedia.org/wiki/Bible_de_Jérusalem]'),
	'lsg' => array(
		'traduction'=> 'Louis Segond 1910',
		'gateway'	=> 2,
		'lang'		=> 'fr',
		'nt'=> true,
		'at'=> true),
	'bds' => array(
		'traduction'=> 'Bible du Semeur',
		'gateway'	=> 32,
		'lang'		=> 'fr',
		'nt'=> true,
		'at'=> true),
	'kj21' => array(
		'traduction'=> 'The 21st Century King James',
		'gateway'	=> 48,
		'lang'		=> 'en',
		'nt'=> true,
		'at'=> true),
	'darb_en' => array(
		'traduction'=> 'Darby',
		'gateway'	=> 16,
		'lang'		=> 'en',
		'nt'=> true,
		'at'=> true),
	'kj'		=> array(
		'traduction'=>'King Jame',
		'gateway'	=>9,
		'lang'		=>'en',
		'nt'=> true,
		'at'=> true
	)
);

$livre_gateways = array(
		'it'=>array(
			'Gen'=>1,			'Es'=>2,			'Lv'=>3,			'Nm'=>4,			'Dt'=>5,			'Gs'=>6,			'Gdc'=>7,			'Rt'=>8,			'1Sam'=>9,			'2Sam'=>10,			'1Re'=>11,			'2Re'=>12,			'1Cr'=>13,			'2Cr'=>14,			'Esd'=>15,			'Ne'=>16,			'Tb'=>17,			'Gdt'=>18,			'Est'=>19,			'1Mac'=>20,			'2Mac'=>21,			'Gb'=>22,			'Sal'=>23,			'Pr'=>24,			'Qo'=>25,			'Ct'=>26,			'Sap'=>27,			'Sir'=>28,			'Is'=>29,			'Ger'=>30,			'Lam'=>31,			'Bar'=>32,			'Ez'=>33,			'Dn'=>34,			'Os'=>35,			'Gl'=>36,			'Am'=>37,			'Abd'=>38,			'Gn'=>39,			'Mi'=>40,			'Na'=>41,			'Ab'=>42,			'Sof'=>43,			'Ag'=>44,			'Zc'=>45,			'Ml'=>46,			'Mt'=>47,			'Mc'=>48,			'Lc'=>49,			'Gv'=>50,			'At'=>51,			'Rm'=>52,			'1Cor'=>53,			'2Cor'=>54,			'Gal'=>55,			'Ef'=>56,			'Fil'=>57,			'Col'=>58,			'1Ts'=>59,			'2Ts'=>60,			'1Tm'=>61,			'2Tm'=>62,			'Tt'=>63,			'Fm'=>64,			'Eb'=>65,			'Gc'=>66,			'1Pt'=>67,			'2Pt'=>68,			'1Gv'=>69,			'2Gv'=>70,			'3Gv'=>71,			'Gd'=>72,			'Ap'=>73
		
		),
		'pl'=>array('Rdz'=>1,'Wj'=>2,'Kpł'=>3,'Lb'=>4,'Pwt'=>5,'Joz'=>6,'Sdz'=>7,'Rt'=>8,'1Sm'=>9,'2Sm'=>10,'1Krl'=>11,'2Krl'=>12,'1Krn'=>13,'2Krn'=>14,'Ezd'=>15,'Ne'=>16,'Tb'=>17,'Jdt'=>18,'Est'=>19,'1Mch'=>20,'2Mch'=>21,'Hi'=>22,'Ps'=>23,'Prz'=>24,'Koh'=>25,'Pnp'=>26,'Mdr'=>27,'Syr'=>28,'Iz'=>29,'Jr'=>30,'Lm'=>31,'Ba'=>32,'Ez'=>33,'Dn'=>34,'Oz'=>35,'Jl'=>36,'Am'=>37,'Ab'=>38,'Jon'=>39,'Mi'=>40,'Na'=>41,'Ha'=>42,'So'=>43,'Ag'=>44,'Za'=>45,'Ml'=>46,'Mt'=>47,'Mk'=>48,'Łk'=>49,'J'=>50,'Dz'=>51,'Rz'=>52,'1Kor'=>53,'2Kor'=>54,'Ga'=>55,'Ef'=>56,'Flp'=>57,'Kol'=>58,'1Tes'=>59,'2Tes'=>60,'1Tm'=>61,'2Tm'=>62,'Tt'=>63,'Flm'=>64,'Hbr'=>65,'Jk'=>66,'1P'=>67,'2P'=>68,'1J'=>69,'2J'=>70,'3J'=>71,'Jud'=>72,'Ap'=>73),
		
		'es'=>array('Gn'=>1,'Ex'=>2,'Lv'=>3,'Nm'=>4,'Dt'=>5,'Jos'=>6,'Jue'=>7,'Rut'=>8,'1Sm'=>9,'2Sm'=>10,'1Re'=>11,'2Re'=>12,'1Cr'=>13,'2Cr'=>14,'Esd'=>15,'Neh'=>16,'Tob'=>17,'Jdt'=>18,'Est'=>19,'1Mac'=>20,'2Mac'=>21,'Job'=>22,'Sal'=>23,'Prov'=>24,'Ecl'=>25,'Cant'=>26,'Sab'=>27,'Eclo'=>28,'Is'=>29,'Jr'=>30,'Lam'=>31,'Bar'=>32,'Ez'=>33,'Dn'=>34,'Os'=>35,'Jl'=>36,'Am'=>37,'Abd'=>38,'Jon'=>39,'Miq'=>40,'Nah'=>41,'Hab'=>42,'Sof'=>43,'Ag'=>44,'Zac'=>45,'Mal'=>46,'Mt'=>47,'Mc'=>48,'Lc'=>49,'Jn'=>50,'Hch'=>51,'Rm'=>52,'1Cor'=>53,'2Cor'=>54,'Gal'=>55,'Ef'=>56,'Flp'=>57,'Col'=>58,'1Tes'=>59,'2Tes'=>60,'1Tim'=>61,'2Tim'=>62,'Tit'=>63,'Flm'=>64,'Heb'=>65,'Sant'=>66,'1Pe'=>67,'2Pe'=>68,'1Jn'=>69,'2Jn'=>70,'3Jn'=>71,'Jds'=>72,'Ap'=>73),
		
		'de'=>array(
			'Gen'=> 1,			'Ex'=> 2,			'Lev'=> 3,			'Num'=> 4,			'Dtn'=> 5,			'Jos'=> 6,			'Ri'=> 7,			'Rut'=> 8,			'1Sam'=> 9,			'2Sam'=> 10,			'1Kön'=> 11,			'2Kön'=> 12,			'1Chr'=> 13,			'2Chr'=> 14,			'Esra'=> 15,			'Neh'=> 16,			'Tob'=> 17,			'Jdt'=> 18,			'Est'=> 19,			'1Makk'=> 20,			'2Makk'=> 21,			'Ijob'=> 22,			'Ps'=> 23,			'Spr'=> 24,			'Koh'=> 25,			'Hld'=> 26,			'Weish'=> 27,			'Sir'=> 28,			'Jes'=> 29,			'Jer'=> 30,			'Klgl'=> 31,			'Bar'=> 32,			'Ez'=> 33,			'Dan'=> 34,			'Hos'=> 35,			'Joël'=> 36,			'Am'=> 37,			'Obd'=> 38,			'Jona'=> 39,			'Mi'=> 40,			'Nah'=> 41,			'Hab'=> 42,			'Zef'=> 43,			'Hag'=> 44,			'Sach'=> 45,			'Mal'=> 46,			'Mt'=> 47,			'Mk'=> 48,			'Lk'=> 49,			'Joh'=> 50,			'Apg'=> 51,			'Röm'=> 52,			'1Kor'=> 53,			'2Kor'=> 54,			'Gal'=> 55,			'Eph'=> 56,			'Phil'=> 57,			'Kol'=> 58,			'1Thess'=> 59,			'2Thess'=> 60,			'1Tim'=> 61,			'2Tim'=> 62,			'Tit'=> 63,			'Phlm'=> 64,			'Hebr'=> 65,			'Jak'=> 66,			'1Petr'=> 67,			'2Petr'=> 68,			'1Joh'=> 69,			'2Joh'=> 70,			'3Joh'=> 71,			'Jud'=> 72,			'Offb'=> 73)
,

		
		'en'=>array(			'Gn'=>1,			'1Ch'=>13,			'1Co'=>53,			'1Jn'=>69,			'1M'=>20,			'1P'=>67,			'1K'=>11,			'1S'=>9,			'1Th'=>59,			'1Tm'=>61,			'2Ch'=>14,			'2Co'=>54,			'2Jn'=>70,			'2M'=>21,			'2 P'=>68,			'2K'=>12,			'2S'=>10,			'2th'=>60,			'2Tm'=>62,			'3Jn'=>71,			'Hab'=>42,			'Ob'=>38,			'Hg'=>44,			'Am'=>37,			'Rv'=>73,			'Ac'=>51,			'Ba'=>32,			'Sg'=>26,			'Col'=>58,			'Dn'=>34,			'Dt'=>5,			'Heb'=>65,			'Qo'=>25,			'Ep'=>56,			'ezr'=>15,			'Est'=>19,			'Ex'=>2,			'Ezk'=>33,			'Phm'=>64,			'Ph'=>57,			'Ga'=>55,			'Jr'=>30,			'Jm'=>66,			'Jb'=>22,			'Jl'=>36,			'Jon'=>39,			'Jn'=>50,			'Jude'=>72,			'Jdt'=>18,			'Is'=>29,			'Jg'=>7,			'Jos'=>6,			'Lm'=>31,			'Lk'=>49,			'Lv'=>3,			'Ml'=>46,			'Mk'=>48,			'Mt'=>47,			'Mi'=>40,			'Na'=>41,			'Ne'=>16,			'Nb'=>4,			'Hos'=>35,			'Pr'=>24,			'Rm'=>52,			'Rt'=>8,			'Ps'=>23,			'Ws'=>27,			'Si'=>28,			'Zp'=>43,			'Tt'=>63,			'Tb'=>17,			'Zc'=>45),
		'fr'=>array(			'1Ch'=>13,			'1Co'=>53,			'1Jn'=>69,			'1M'=>20,			'1P'=>67,			'1R'=>11,			'1S'=>9,			'1Th'=>59,			'1Tm'=>61,			'2Ch'=>14,			'2Co'=>54,			'2Jn'=>70,			'2M'=>21,			'2P'=>68,			'2R'=>12,			'2S'=>10,			'2Th'=>60,			'2Tm'=>62,			'3Jn'=>71,			'Ab'=>38,			'Ac'=>51,			'Ag'=>44,			'Am'=>37,			'Ap'=>73,			'Ba'=>32,			'Col'=>58,			'Ct'=>26,			'Dn'=>34,			'Dt'=>5,			'Ep'=>56,			'Esd'=>15,			'Est'=>19,			'Ex'=>2,			'Ez'=>33,			'Ga'=>55,			'Gn'=>1,			'Ha'=>42,			'He'=>65,			'Is'=>29,			'Jb'=>22,			'Jc'=>66,			'Jdt'=>18,			'Jg'=>7,			'Jl'=>36,			'Jn'=>50,			'Jon'=>39,			'Jos'=>6,			'Jr'=>30,			'Jude'=>72,			'Lc'=>49,			'Lm'=>31,			'Lv'=>3,			'Mc'=>48,			'Mi'=>40,			'Ml'=>46,			'Mt'=>47,			'Na'=>41,			'Nb'=>4,			'Ne'=>16,			'Os'=>35,			'Ph'=>57,			'Phm'=>64,			'Pr'=>24,			'Ps'=>23,			'Qo'=>25,			'Rm'=>52,			'Rt'=>8,			'Sg'=>27,			'Si'=>28,			'So'=>43,			'Tb'=>17,			'Tt'=>63,			'Za'=>45));



$tableau_livres = array(
	'pl'=>array('Rdz'=>'Księga Rodzaju',				'Wj'=>'Księga Wyjścia',				'Kpł'=>'Księga Kapłańska',				'Lb'=>'Księga Liczb',				'Pwt'=>'Księga Powtórzonego Prawa',				'Joz'=>'Księga Jozuego',				'Sdz'=>'Księga Sędziów',				'Rt'=>'Księga Rut',				'1Sm'=>'1 Księga Samuela',				'2Sm'=>'2 Księga Samuela',				'1Krl'=>'1 Księga Królewska',				'2Krl'=>'2 Księga Królewska',				'1Krn'=>'1 Księga Kronik',				'2Krn'=>'2 Księga Kronik',				'Ezd'=>'Księga Ezdrasza',				'Ne'=>'Księga Nehemiasza',				'Tb'=>'Księga Tobiasza',				'Jdt'=>'Księga Judyty',				'Est'=>'Księga Estery',				'1Mch'=>'1 Księga Machabejska',				'2Mch'=>'2 Księga Machabejska',				'Hi'=>'Księga Hioba',				'Ps'=>'Księga Psalmów',				'Prz'=>'Księga Przysłów',				'Koh'=>'Księga Koheleta',				'Pnp'=>'Pieśń nad pieśniami',				'Mdr'=>'Księga Mądrości',				'Syr'=>'Mądrość Syracha',				'Iz'=>'Księga Izajasza',				'Jr'=>'Księga Jeremiasza',				'Lm'=>'Lamentacje Jeremiasza',				'Ba'=>'Księga Barucha',				'Ez'=>'Księga Ezechiela',				'Dn'=>'Księga Daniela',				'Oz'=>'Księga Ozeasza',				'Jl'=>'Księga Joela',				'Am'=>'Księga Amosa',				'Ab'=>'Księga Abdiasza',				'Jon'=>'Księga Jonasza',				'Mi'=>'Księga Micheasza',				'Na'=>'Księga Nahuma',				'Ha'=>'Księga Habakuka',				'So'=>'Księga Sofoniasza',				'Ag'=>'Księga Aggeusza',				'Za'=>'Księga Zachariasza',				'Ml'=>'Ksiêga Malachiasza',				'Mt'=>'Ewangelia Mateusza',				'Mk'=>'Ewangelia Marka',				'Łk'=>'Ewangelia Łukasza',				'J'=>'Ewangelia Jana',				'Dz'=>'Dzieje Apostolskie',				'Rz'=>'List do Rzymian',				'1Kor'=>'1 List do Koryntian',				'2Kor'=>'2 List do Koryntian',				'Ga'=>'List do Galatów',				'Ef'=>'List do Efezjan',				'Flp'=>'List do Filipian',				'Kol'=>'List do Kolosan',				'1Tes'=>'1 List do Tesaloniczan',				'2Tes'=>'2 List do Tesaloniczan',				'1Tm'=>'1 List do Tymoteusza',				'2Tm'=>'2 List do Tymoteusza',				'Tt'=>'List do Tytusa',				'Flm'=>'List do Filemona',				'Hbr'=>'List do Hebrajczyków',				'Jk'=>'List Jakuba',				'1P'=>'1 List Piotra',				'2P'=>'2 List Piotra',				'1J'=>'1 List Jana',				'2J'=>'2 List Jana',				'3J'=>'3 List Jana',				'Jud'=>'List Judy',				'Ap'=>'Apokalipsa Świętego Jana'),
	
	'it'=>array(
		'Gen'=>'Genesi',		'Es'=>'Esodo',		'Lv'=>'Levitico',		'Nm'=>'Numeri',		'Dt'=>'Deuteronomio',		'Gs'=>'Giosué',		'Gdc'=>'Giudici',		'Rt'=>'Rut',		'1Sam'=>'1 Samuele',		'2Sam'=>'2 Samuele',		'1Re'=>'1 Re',		'2Re'=>'2 Re',		'1Cr'=>'1 Cronache',		'2Cr'=>'2 Cronache',		'Esd'=>'Esdra',		'Ne'=>'Neemia',		'Tb'=>'Tobi',		'Gdt'=>'Giuditta',		'Est'=>'Ester',		'1Mac'=>'1 Maccabei',		'2Mac'=>'2 Maccabei',		'Gb'=>'Giobbe',		'Sal'=>'Salmi',		'Pr'=>'Proverbi',		'Qo'=>'Ecclesiaste',		'Ct'=>'Cantico dei Cantici',		'Sap'=>'Sapienza',		'Sir'=>'Siracide',		'Is'=>'Isaia',		'Ger'=>'Geremia',		'Lam'=>'Lamentazioni',		'Bar'=>'Baruc',		'Ez'=>'Ezechiele',		'Dn'=>'Daniele',		'Os'=>'Osea',		'Gl'=>'Gioele',		'Am'=>'Amos',		'Abd'=>'Abdia',		'Gn'=>'Giona',		'Mi'=>'Michea',		'Na'=>'Nahum',		'Ab'=>'Abacuc',		'Sof'=>'Sofonia',		'Ag'=>'Aggeo',		'Zc'=>'Zaccaria',		'Ml'=>'Malachia',		'Mt'=>'Matteo',		'Mc'=>'Marco',		'Lc'=>'Luca',		'Gv'=>'Giovanni',		'At'=>'Atti',		'Rm'=>'Romani',		'1Cor'=>'1 Corinzi',		'2Cor'=>'2 Corinzi',		'Gal'=>'Galati',		'Ef'=>'Efesini',		'Fil'=>'Filippesi',		'Col'=>'Colossesi',		'1Ts'=>'1 Tessalonicesi',		'2Ts'=>'2 Tessalonicesi',		'1Tm'=>'1 Timoteo',		'2Tm'=>'2 Timoteo',		'Tt'=>'Tito',		'Fm'=>'Filemone',		'Eb'=>'Ebrei',		'Gc'=>'Giacomo',		'1Pt'=>'1 Pietro',		'2Pt'=>'2 Pietro',		'1Gv'=>'1 Giovanni',		'2Gv'=>'2 Giovanni',		'3Gv'=>'3 Giovanni',		'Gd'=>'Giuda',		'Ap'=>'Apocalisse'
	
	),
	
	'es'=>array('Gn'=>'Génesis','Ex'=>'Éxodo','Lv'=>'Levítico','Nm'=>'Números','Dt'=>'Deuteronomio','Jos'=>'Josué','Jue'=>'Jueces','Rut'=>'Rut','1Sm'=>'1 Samuel','2Sm'=>'2 Samuel','1Re'=>'1 Reyes','2Re'=>'2 Reyes','1Cr'=>'1 Crónicas','2Cr'=>'2 Crónicas','Esd'=>'Esdras','Neh'=>'Nehemías','Tob'=>'Tobit','Jdt'=>'Judit','Est'=>'Ester','1Mac'=>'1 Macabeos','2Mac'=>'2 Macabeos','Job'=>'Job','Sal'=>'Salmos','Prov'=>'Proverbios','Ecl'=>'Eclesiastés','Cant'=>'Cantares','Sab'=>'Sabiduría','Eclo'=>'Eclesiástico','Is'=>'Isaías','Jr'=>'Jeremías','Lam'=>'Lamentaciones','Bar'=>'Baruc','Ez'=>'Ezequiel','Dn'=>'Daniel','Os'=>'Oseas','Jl'=>'Joel','Am'=>'Amós','Abd'=>'Abdías','Jon'=>'Jonás','Miq'=>'Miqueas','Nah'=>'Nahúm','Hab'=>'Habacuc','Sof'=>'Sofonías','Ag'=>'Hageo','Zac'=>'Zacarías','Mal'=>'Malaquías','Mt'=>'Mateo','Mc'=>'Marcos','Lc'=>'Lucas','Jn'=>'Juan','Hch'=>'Hechos','Rm'=>'Romanos','1Cor'=>'1 Corintios','2Cor'=>'2 Corintios','Gal'=>'Gálatas','Ef'=>'Efesios','Flp'=>'Filipenses','Col'=>'Colosenses','1Tes'=>'1 Tesalonicenses','2Tes'=>'2 Tesalonicenses','1Tim'=>'1 Timoteo','2Tim'=>'2 Timoteo','Tit'=>'Tito','Flm'=>'Filemón','Heb'=>'Hebreos','Sant'=>'Santiago','1Pe'=>'1 Pedro','2Pe'=>'2 Pedro','1Jn'=>'1 Juan','2Jn'=>'2 Juan','3Jn'=>'3 Juan','Jds'=>'Judas','Ap'=>'Apocalipsis'),
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
	'Ha'=>'Habacuc',
	'Ap'=>'Apocalypse',
	'Rm'=>'Romains'
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
	'de'=>',',
	'es'=>',',
	'it'=>',',
	'pl'=>',');

	
	switch ($i){
		case 'traduction':
			return $tableau_traduction;
		case 'separateur':
			return $tableau_separateur;
		case 'gateway':
			return $livre_gateways;
		case 'livres':
			return $tableau_livres;
		case 'original':
			return $tableau_langue_original;
		case 'unbound':
			return $gateway_to_unboud;
		case 'lire_la_bible':
			return $lire_la_bible;
		case 'nombres_chapitre':
			return $nombre_chapitres;
		case 'deutero':
			return $deutero;
		case 'petit_livre':
			$petit_livre=array(38,64,70,71,72);
			$petit_livre_2 = array();
			$tableau_inverse = array_flip($livre_gateways[$lang]);
			$j = 0;
			foreach ($petit_livre as $i){
					$petit_livre_2[$j] = strtolower($tableau_inverse[$i]);
					$j++;
					
			
			}
			
			return $petit_livre_2;
	}
};

?>