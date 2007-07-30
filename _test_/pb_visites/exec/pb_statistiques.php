<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2007                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');
include_spip('inc/statistiques');

function pb_nom_pays($code) {
	$result = spip_query("SELECT nom_pays FROM spip_pb_geoip WHERE code_pays='$code' LIMIT 0,1");
	if ($row = spip_fetch_array($result)) {
		return $row["nom_pays"];
	}
}


function nom_pays_francais($code) {
	$nom_code_pays["A1"] = "Proxy anonyme";
	$nom_code_pays["AF"] = "AFGHANISTAN" ;
	$nom_code_pays["ZA"] = "AFRIQUE DU SUD" ;
	$nom_code_pays["AL"] = "ALBANIE" ;
	$nom_code_pays["DZ"] = "ALGÉRIE" ;
	$nom_code_pays["DE"] = "ALLEMAGNE" ;
	$nom_code_pays["AD"] = "ANDORRE" ;
	$nom_code_pays["AO"] = "ANGOLA" ;
	$nom_code_pays["AI"] = "ANGUILLA" ;
	$nom_code_pays["AQ"] = "ANTARCTIQUE" ;
	$nom_code_pays["AG"] = "ANTIGUA-ET-BARBUDA" ;
	$nom_code_pays["AN"] = "ANTILLES NÉERLANDAISES" ;
	$nom_code_pays["SA"] = "ARABIE SAOUDITE" ;
	$nom_code_pays["AR"] = "ARGENTINE" ;
	$nom_code_pays["AM"] = "ARMÉNIE" ;
	$nom_code_pays["AW"] = "ARUBA" ;
	$nom_code_pays["AU"] = "AUSTRALIE" ;
	$nom_code_pays["AT"] = "AUTRICHE" ;
	$nom_code_pays["AZ"] = "AZERBAÏDJAN" ;
	$nom_code_pays["BS"] = "BAHAMAS" ;
	$nom_code_pays["BH"] = "BAHREÏN" ;
	$nom_code_pays["BD"] = "BANGLADESH" ;
	$nom_code_pays["BB"] = "BARBADE" ;
	$nom_code_pays["BY"] = "BÉLARUS" ;
	$nom_code_pays["BE"] = "BELGIQUE" ;
	$nom_code_pays["BZ"] = "BELIZE" ;
	$nom_code_pays["BJ"] = "BÉNIN" ;
	$nom_code_pays["BM"] = "BERMUDES" ;
	$nom_code_pays["BT"] = "BHOUTAN" ;
	$nom_code_pays["BO"] = "BOLIVIE" ;
	$nom_code_pays["BA"] = "BOSNIE-HERZÉGOVINE" ;
	$nom_code_pays["BW"] = "BOTSWANA" ;
	$nom_code_pays["BV"] = "BOUVET, ÎLE" ;
	$nom_code_pays["BR"] = "BRÉSIL" ;
	$nom_code_pays["BN"] = "BRUNÉI DARUSSALAM" ;
	$nom_code_pays["BG"] = "BULGARIE" ;
	$nom_code_pays["BF"] = "BURKINA FASO" ;
	$nom_code_pays["BI"] = "BURUNDI" ;
	$nom_code_pays["KY"] = "CAÏMANES, ÎLES" ;
	$nom_code_pays["KH"] = "CAMBODGE" ;
	$nom_code_pays["CM"] = "CAMEROUN" ;
	$nom_code_pays["CA"] = "CANADA" ;
	$nom_code_pays["CV"] = "CAP-VERT" ;
	$nom_code_pays["CF"] = "CENTRAFRICAINE, RÉPUBLIQUE" ;
	$nom_code_pays["CL"] = "CHILI" ;
	$nom_code_pays["CN"] = "CHINE" ;
	$nom_code_pays["CX"] = "CHRISTMAS, ÎLE" ;
	$nom_code_pays["CY"] = "CHYPRE" ;
	$nom_code_pays["CC"] = "COCOS (KEELING), ÎLES" ;
	$nom_code_pays["CO"] = "COLOMBIE" ;
	$nom_code_pays["KM"] = "COMORES" ;
	$nom_code_pays["CG"] = "CONGO" ;
	$nom_code_pays["CD"] = "CONGO, LA RÉPUBLIQUE DÉMOCRATIQUE DU" ;
	$nom_code_pays["CK"] = "COOK, ÎLES" ;
	$nom_code_pays["KR"] = "CORÉE, RÉPUBLIQUE DE" ;
	$nom_code_pays["KP"] = "CORÉE, RÉPUBLIQUE POPULAIRE DÉMOCRATIQUE DE" ;
	$nom_code_pays["CR"] = "COSTA RICA" ;
	$nom_code_pays["CI"] = "CÔTE D'IVOIRE" ;
	$nom_code_pays["HR"] = "CROATIE" ;
	$nom_code_pays["CU"] = "CUBA" ;
	$nom_code_pays["DK"] = "DANEMARK" ;
	$nom_code_pays["DJ"] = "DJIBOUTI" ;
	$nom_code_pays["DO"] = "DOMINICAINE, RÉPUBLIQUE" ;
	$nom_code_pays["DM"] = "DOMINIQUE" ;
	$nom_code_pays["EG"] = "ÉGYPTE" ;
	$nom_code_pays["SV"] = "EL SALVADOR" ;
	$nom_code_pays["AE"] = "ÉMIRATS ARABES UNIS" ;
	$nom_code_pays["EC"] = "ÉQUATEUR" ;
	$nom_code_pays["ER"] = "ÉRYTHRÉE" ;
	$nom_code_pays["ES"] = "ESPAGNE" ;
	$nom_code_pays["EE"] = "ESTONIE" ;
	$nom_code_pays["US"] = "ÉTATS-UNIS" ;
	$nom_code_pays["ET"] = "ÉTHIOPIE" ;
	$nom_code_pays["FK"] = "FALKLAND, ÎLES (MALVINAS)" ;
	$nom_code_pays["FO"] = "FÉROÉ, ÎLES" ;
	$nom_code_pays["FJ"] = "FIDJI" ;
	$nom_code_pays["FI"] = "FINLANDE" ;
	$nom_code_pays["FR"] = "FRANCE" ;
	$nom_code_pays["GA"] = "GABON" ;
	$nom_code_pays["GM"] = "GAMBIE" ;
	$nom_code_pays["GE"] = "GÉORGIE" ;
	$nom_code_pays["GS"] = "GÉORGIE DU SUD ET LES ÎLES SANDWICH DU SUD" ;
	$nom_code_pays["GH"] = "GHANA" ;
	$nom_code_pays["GI"] = "GIBRALTAR" ;
	$nom_code_pays["GR"] = "GRÈCE" ;
	$nom_code_pays["GD"] = "GRENADE" ;
	$nom_code_pays["GL"] = "GROENLAND" ;
	$nom_code_pays["GP"] = "GUADELOUPE" ;
	$nom_code_pays["GU"] = "GUAM" ;
	$nom_code_pays["GT"] = "GUATEMALA" ;
	$nom_code_pays["GN"] = "GUINÉE" ;
	$nom_code_pays["GW"] = "GUINÉE-BISSAU" ;
	$nom_code_pays["GQ"] = "GUINÉE ÉQUATORIALE" ;
	$nom_code_pays["GY"] = "GUYANA" ;
	$nom_code_pays["GF"] = "GUYANE FRANÇAISE" ;
	$nom_code_pays["HT"] = "HAÏTI" ;
	$nom_code_pays["HM"] = "HEARD, ÎLE ET MCDONALD, ÎLES" ;
	$nom_code_pays["HN"] = "HONDURAS" ;
	$nom_code_pays["HK"] = "HONG-KONG" ;
	$nom_code_pays["HU"] = "HONGRIE" ;
	$nom_code_pays["UM"] = "ÎLES MINEURES ÉLOIGNÉES DES ÉTATS-UNIS" ;
	$nom_code_pays["VG"] = "ÎLES VIERGES BRITANNIQUES" ;
	$nom_code_pays["VI"] = "ÎLES VIERGES DES ÉTATS-UNIS" ;
	$nom_code_pays["IN"] = "INDE" ;
	$nom_code_pays["ID"] = "INDONÉSIE" ;
	$nom_code_pays["IR"] = "IRAN, RÉPUBLIQUE ISLAMIQUE D'" ;
	$nom_code_pays["IQ"] = "IRAQ" ;
	$nom_code_pays["IE"] = "IRLANDE" ;
	$nom_code_pays["IS"] = "ISLANDE" ;
	$nom_code_pays["IL"] = "ISRAËL" ;
	$nom_code_pays["IT"] = "ITALIE" ;
	$nom_code_pays["JM"] = "JAMAÏQUE" ;
	$nom_code_pays["JP"] = "JAPON" ;
	$nom_code_pays["JO"] = "JORDANIE" ;
	$nom_code_pays["KZ"] = "KAZAKHSTAN" ;
	$nom_code_pays["KE"] = "KENYA" ;
	$nom_code_pays["KG"] = "KIRGHIZISTAN" ;
	$nom_code_pays["KI"] = "KIRIBATI" ;
	$nom_code_pays["KW"] = "KOWEÏT" ;
	$nom_code_pays["LA"] = "LAO, RÉPUBLIQUE DÉMOCRATIQUE POPULAIRE" ;
	$nom_code_pays["LS"] = "LESOTHO" ;
	$nom_code_pays["LV"] = "LETTONIE" ;
	$nom_code_pays["LB"] = "LIBAN" ;
	$nom_code_pays["LR"] = "LIBÉRIA" ;
	$nom_code_pays["LY"] = "LIBYENNE, JAMAHIRIYA ARABE" ;
	$nom_code_pays["LI"] = "LIECHTENSTEIN" ;
	$nom_code_pays["LT"] = "LITUANIE" ;
	$nom_code_pays["LU"] = "LUXEMBOURG" ;
	$nom_code_pays["MO"] = "MACAO" ;
	$nom_code_pays["MK"] = "MACÉDOINE, L'EX-RÉPUBLIQUE YOUGOSLAVE DE" ;
	$nom_code_pays["MG"] = "MADAGASCAR" ;
	$nom_code_pays["MY"] = "MALAISIE" ;
	$nom_code_pays["MW"] = "MALAWI" ;
	$nom_code_pays["MV"] = "MALDIVES" ;
	$nom_code_pays["ML"] = "MALI" ;
	$nom_code_pays["MT"] = "MALTE" ;
	$nom_code_pays["MP"] = "MARIANNES DU NORD, ÎLES" ;
	$nom_code_pays["MA"] = "MAROC" ;
	$nom_code_pays["MH"] = "MARSHALL, ÎLES" ;
	$nom_code_pays["MQ"] = "MARTINIQUE" ;
	$nom_code_pays["MU"] = "MAURICE" ;
	$nom_code_pays["MR"] = "MAURITANIE" ;
	$nom_code_pays["YT"] = "MAYOTTE" ;
	$nom_code_pays["MX"] = "MEXIQUE" ;
	$nom_code_pays["FM"] = "MICRONÉSIE, ÉTATS FÉDÉRÉS DE" ;
	$nom_code_pays["MD"] = "MOLDOVA, RÉPUBLIQUE DE" ;
	$nom_code_pays["MC"] = "MONACO" ;
	$nom_code_pays["MN"] = "MONGOLIE" ;
	$nom_code_pays["MS"] = "MONTSERRAT" ;
	$nom_code_pays["MZ"] = "MOZAMBIQUE" ;
	$nom_code_pays["MM"] = "MYANMAR" ;
	$nom_code_pays["NA"] = "NAMIBIE" ;
	$nom_code_pays["NR"] = "NAURU" ;
	$nom_code_pays["NP"] = "NÉPAL" ;
	$nom_code_pays["NI"] = "NICARAGUA" ;
	$nom_code_pays["NE"] = "NIGER" ;
	$nom_code_pays["NG"] = "NIGÉRIA" ;
	$nom_code_pays["NU"] = "NIUÉ" ;
	$nom_code_pays["NF"] = "NORFOLK, ÎLE" ;
	$nom_code_pays["NO"] = "NORVÈGE" ;
	$nom_code_pays["NC"] = "NOUVELLE-CALÉDONIE" ;
	$nom_code_pays["NZ"] = "NOUVELLE-ZÉLANDE" ;
	$nom_code_pays["IO"] = "OCÉAN INDIEN, TERRITOIRE BRITANNIQUE DE L'" ;
	$nom_code_pays["OM"] = "OMAN" ;
	$nom_code_pays["UG"] = "OUGANDA" ;
	$nom_code_pays["UZ"] = "OUZBÉKISTAN" ;
	$nom_code_pays["PK"] = "PAKISTAN" ;
	$nom_code_pays["PW"] = "PALAOS" ;
	$nom_code_pays["PS"] = "PALESTINIEN OCCUPÉ, TERRITOIRE" ;
	$nom_code_pays["PA"] = "PANAMA" ;
	$nom_code_pays["PG"] = "PAPOUASIE-NOUVELLE-GUINÉE" ;
	$nom_code_pays["PY"] = "PARAGUAY" ;
	$nom_code_pays["NL"] = "PAYS-BAS" ;
	$nom_code_pays["PE"] = "PÉROU" ;
	$nom_code_pays["PH"] = "PHILIPPINES" ;
	$nom_code_pays["PN"] = "PITCAIRN" ;
	$nom_code_pays["PL"] = "POLOGNE" ;
	$nom_code_pays["PF"] = "POLYNÉSIE FRANÇAISE" ;
	$nom_code_pays["PR"] = "PORTO RICO" ;
	$nom_code_pays["PT"] = "PORTUGAL" ;
	$nom_code_pays["QA"] = "QATAR" ;
	$nom_code_pays["RE"] = "RÉUNION" ;
	$nom_code_pays["RO"] = "ROUMANIE" ;
	$nom_code_pays["GB"] = "ROYAUME-UNI" ;
	$nom_code_pays["RU"] = "RUSSIE, FÉDÉRATION DE" ;
	$nom_code_pays["RW"] = "RWANDA" ;
	$nom_code_pays["EH"] = "SAHARA OCCIDENTAL" ;
	$nom_code_pays["SH"] = "SAINTE-HÉLÈNE" ;
	$nom_code_pays["LC"] = "SAINTE-LUCIE" ;
	$nom_code_pays["KN"] = "SAINT-KITTS-ET-NEVIS" ;
	$nom_code_pays["SM"] = "SAINT-MARIN" ;
	$nom_code_pays["PM"] = "SAINT-PIERRE-ET-MIQUELON" ;
	$nom_code_pays["VA"] = "SAINT-SIÈGE (ÉTAT DE LA CITÉ DU VATICAN)" ;
	$nom_code_pays["VC"] = "SAINT-VINCENT-ET-LES GRENADINES" ;
	$nom_code_pays["SB"] = "SALOMON, ÎLES" ;
	$nom_code_pays["WS"] = "SAMOA" ;
	$nom_code_pays["AS"] = "SAMOA AMÉRICAINES" ;
	$nom_code_pays["ST"] = "SAO TOMÉ-ET-PRINCIPE" ;
	$nom_code_pays["SN"] = "SÉNÉGAL" ;
	$nom_code_pays["SC"] = "SEYCHELLES" ;
	$nom_code_pays["SL"] = "SIERRA LEONE" ;
	$nom_code_pays["SG"] = "SINGAPOUR" ;
	$nom_code_pays["SK"] = "SLOVAQUIE" ;
	$nom_code_pays["SI"] = "SLOVÉNIE" ;
	$nom_code_pays["SO"] = "SOMALIE" ;
	$nom_code_pays["SD"] = "SOUDAN" ;
	$nom_code_pays["LK"] = "SRI LANKA" ;
	$nom_code_pays["SE"] = "SUÈDE" ;
	$nom_code_pays["CH"] = "SUISSE" ;
	$nom_code_pays["SR"] = "SURINAME" ;
	$nom_code_pays["SJ"] = "SVALBARD ET ÎLE JAN MAYEN" ;
	$nom_code_pays["SZ"] = "SWAZILAND" ;
	$nom_code_pays["SY"] = "SYRIENNE, RÉPUBLIQUE ARABE" ;
	$nom_code_pays["TJ"] = "TADJIKISTAN" ;
	$nom_code_pays["TW"] = "TAÏWAN, PROVINCE DE CHINE" ;
	$nom_code_pays["TZ"] = "TANZANIE, RÉPUBLIQUE-UNIE DE" ;
	$nom_code_pays["TD"] = "TCHAD" ;
	$nom_code_pays["CZ"] = "TCHÈQUE, RÉPUBLIQUE" ;
	$nom_code_pays["TF"] = "TERRES AUSTRALES FRANÇAISES" ;
	$nom_code_pays["TH"] = "THAÏLANDE" ;
	$nom_code_pays["TL"] = "TIMOR-LESTE" ;
	$nom_code_pays["TG"] = "TOGO" ;
	$nom_code_pays["TK"] = "TOKELAU" ;
	$nom_code_pays["TO"] = "TONGA" ;
	$nom_code_pays["TT"] = "TRINITÉ-ET-TOBAGO" ;
	$nom_code_pays["TN"] = "TUNISIE" ;
	$nom_code_pays["TM"] = "TURKMÉNISTAN" ;
	$nom_code_pays["TC"] = "TURKS ET CAÏQUES, ÎLES" ;
	$nom_code_pays["TR"] = "TURQUIE" ;
	$nom_code_pays["TV"] = "TUVALU" ;
	$nom_code_pays["UA"] = "UKRAINE" ;
	$nom_code_pays["UY"] = "URUGUAY" ;
	$nom_code_pays["VU"] = "VANUATU" ;
	$nom_code_pays["VE"] = "VENEZUELA" ;
	$nom_code_pays["VN"] = "VIET NAM" ;
	$nom_code_pays["WF"] = "WALLIS ET FUTUNA" ;
	$nom_code_pays["YE"] = "YÉMEN" ;
	$nom_code_pays["YU"] = "YOUGOSLAVIE" ;
	$nom_code_pays["ZM"] = "ZAMBIE" ;
	$nom_code_pays["ZW"] = "ZIMBABWE" ;
	return $nom_code_pays["$code"];
}

function pb_population($code) {
	$population["AF"] = "27145000";
	$population["AL"] = "3190000";
	$population["DZ"] = "33858000";
	$population["AS"] = "67000";
	$population["AD"] = "81200";
	$population["AO"] = "17024000";
	$population["AI"] = "13000";
	$population["AG"] = "85000";
	$population["AR"] = "39531000";
	$population["AM"] = "3002000";
	$population["AW"] = "104000";
	$population["AU"] = "21018897";
	$population["AT"] = "8361000";
	$population["AZ"] = "8467000";
	$population["BS"] = "331000";
	$population["BH"] = "753000";
	$population["BD"] = "158665000";
	$population["BB"] = "294000";
	$population["BY"] = "9689000";
	$population["BE"] = "10457000";
	$population["BZ"] = "288000";
	$population["BJ"] = "9033000";
	$population["BM"] = "65000";
	$population["BT"] = "658000";
	$population["BO"] = "9525000";
	$population["BA"] = "3935000";
	$population["BW"] = "1882000";
	$population["BR"] = "186736000";
	$population["VG"] = "23000";
	$population["BN"] = "390000";
	$population["BG"] = "7639000";
	$population["BF"] = "14784000";
	$population["BI"] = "8508000";
	$population["KH"] = "14444000";
	$population["CM"] = "18549000";
	$population["CA"] = "32975700";
	$population["CV"] = "530000";
	$population["KY"] = "47000";
	$population["CF"] = "4343000";
	$population["TD"] = "10781000";
	$population["CL"] = "16598074";
	$population["CN"] = "1319498000";
	$population["CO"] = "43964602";
	$population["KM"] = "682000";
	$population["CD"] = "62636000";
	$population["CG"] = "3768000";
	$population["CR"] = "4468000";
	$population["CI"] = "19262000";
	$population["HR"] = "4555000";
	$population["CU"] = "11268000";
	$population["CY"] = "855000";
	$population["CZ"] = "10306709";
	$population["DK"] = "5550000";
	$population["DJ"] = "833000";
	$population["DM"] = "67000";
	$population["DO"] = "9760000";
	$population["TL"] = "1155000";
	$population["EC"] = "13341000";
	$population["EG"] = "75498000";
	$population["SV"] = "6857000";
	$population["GQ"] = "507000";
	$population["ER"] = "4851000";
	$population["EE"] = "1342409";
	$population["ET"] = "77127000";
	$population["FK"] = "3000";
	$population["FO"] = "48455";
	$population["FJ"] = "839000";
	$population["FI"] = "5310000";
	$population["FR"] = "64102140";
	$population["GF"] = "202000";
	$population["PF"] = "259800";
	$population["GA"] = "1331000";
	$population["GM"] = "1709000";
	$population["GE"] = "4395000[10] ";
	$population["DE"] = "82314900";
	$population["GH"] = "23478000";
	$population["GI"] = "29000";
	$population["GR"] = "11147000";
	$population["GL"] = "58000";
	$population["GD"] = "106000";
	$population["GP"] = "405000";
	$population["GU"] = "173000";
	$population["GT"] = "13354000";
	$population["GG"] = "65573";
	$population["GN"] = "9370000";
	$population["GW"] = "1695000";
	$population["GY"] = "738000";
	$population["HT"] = "9598000";
	$population["HN"] = "7106000";
	$population["HK"] = "7206000";
	$population["HU"] = "10030000";
	$population["IS"] = "309699";
	$population["IN"] = "1169016000[3] ";
	$population["ID"] = "231627000";
	$population["IR"] = "71208000";
	$population["IQ"] = "28993000";
	$population["IE"] = "4234925";
	$population["IM"] = "79000";
	$population["IL"] = "7161000[7] ";
	$population["IT"] = "59131287";
	$population["JM"] = "2714000";
	$population["JP"] = "127750000";
	$population["JE"] = "88200";
	$population["JO"] = "5924000";
	$population["KZ"] = "15422000";
	$population["KE"] = "37538000";
	$population["KI"] = "95000";
	$population["KP"] = "23790000";
	$population["KR"] = "48224000";
	$population["KW"] = "2851000";
	$population["KG"] = "5317000";
	$population["LA"] = "5859000";
	$population["LV"] = "2277000";
	$population["LB"] = "4099000";
	$population["LS"] = "2008000";
	$population["LR"] = "3750000";
	$population["LY"] = "6160000";
	$population["LI"] = "35000";
	$population["LT"] = "3390000";
	$population["LU"] = "467000";
	$population["MO"] = "481000";
	$population["MK"] = "2038000";
	$population["MG"] = "19683000";
	$population["MW"] = "13925000";
	$population["MY"] = "27199388";
	$population["MV"] = "306000";
	$population["ML"] = "12337000";
	$population["MT"] = "407000";
	$population["MH"] = "59000";
	$population["MQ"] = "399000";
	$population["MR"] = "3124000";
	$population["MU"] = "1262000";
	$population["YT"] = "182000";
	$population["MX"] = "103263388";
	$population["MD"] = "3794000";
	$population["MC"] = "33000";
	$population["MN"] = "2629000";
	$population["ME"] = "598000";
	$population["MS"] = "5900";
	$population["MA"] = "31224000";
	$population["MZ"] = "21397000";
	$population["-"] = "48798000";
	$population["NA"] = "2074000";
	$population["NR"] = "10000";
	$population["NP"] = "28196000";
	$population["NL"] = "16390000";
	$population["AN"] = "192000";
	$population["NC"] = "240390";
	$population["NZ"] = "4230000";
	$population["NI"] = "5603000";
	$population["NE"] = "14226000";
	$population["NG"] = "148093000";
	$population["NU"] = "1600";
	$population["MP"] = "84000";
	$population["NO"] = "4770000";
	$population["OM"] = "2595000";
	$population["PK"] = "160757000";
	$population["PW"] = "20000";
	$population["PA"] = "3343000";
	$population["PG"] = "6331000";
	$population["PY"] = "6127000";
	$population["PE"] = "27903000";
	$population["PH"] = "88706300";
	$population["PN"] = "50000";
	$population["PL"] = "38125479";
	$population["PT"] = "10623000";
	$population["PR"] = "3991000";
	$population["QA"] = "841000";
	$population["RE"] = "784000";
	$population["RO"] = "21438000";
	$population["RU"] = "142499000";
	$population["RW"] = "9725000";
	$population["SH"] = "6600";
	$population["KN"] = "50000";
	$population["LC"] = "165000";
	$population["PM"] = "6125";
	$population["VC"] = "120000";
	$population["WS"] = "187000";
	$population["SM"] = "31000";
	$population["SA"] = "24735000";
	$population["SN"] = "12379000";
	$population["RS"] = "9858000";
	$population["SC"] = "87000";
	$population["SL"] = "5866000";
	$population["SG"] = "4436000";
	$population["SK"] = "5390000";
	$population["SI"] = "2030000";
	$population["SB"] = "496000";
	$population["SO"] = "8699000";
	$population["ZA"] = "48577000";
	$population["ES"] = "45116894";
	$population["LK"] = "19299000";
	$population["SD"] = "38560000";
	$population["SR"] = "458000";
	$population["SZ"] = "1141000";
	$population["SE"] = "9150000";
	$population["CH"] = "7484000";
	$population["SY"] = "19929000";
	$population["TW"] = "2200000";
	$population["TJ"] = "6736000";
	$population["TZ"] = "40454000";
	$population["TH"] = "62828706";
	$population["TG"] = "6585000";
	$population["TK"] = "1400";
	$population["TO"] = "100000";
	$population["TT"] = "1333000";
	$population["TN"] = "10327000";
	$population["TR"] = "74877000";
	$population["TM"] = "4965000";
	$population["TC"] = "26000";
	$population["TV"] = "11000";
	$population["UG"] = "30884000";
	$population["UA"] = "46205000";
	$population["AE"] = "4380000";
	$population["GB"] = "60209500";
	$population["US"] = "302425000";
	$population["UY"] = "3340000";
	$population["UZ"] = "27372000";
	$population["VU"] = "226000";
	$population["VE"] = "27657000";
	$population["VN"] = "87375000";
	$population["WF"] = "15000";
	$population["YE"] = "22389000";
	$population["ZM"] = "11922000";
	$population["ZW"] = "13349000";
	
	$pop = $population["$code"];
	if ($pop < 1) $pop = "1000000";
	
	return $pop;
}

function pb_corriger_code_fips($code) {

	$code_fips["AF"] = "AF";
	$code_fips["AL"] = "AL";
	$code_fips["DZ"] = "AG";
	$code_fips["AS"] = "AQ";
	$code_fips["AD"] = "AN";
	$code_fips["AO"] = "AO";
	$code_fips["AI"] = "AV";
	$code_fips["AQ"] = "AY";
	$code_fips["AG"] = "AC";
	$code_fips["AR"] = "AR";
	$code_fips["AM"] = "AM";
	$code_fips["AW"] = "AA";
	$code_fips["-"] = "AT";
	$code_fips["AU"] = "AS";
	$code_fips["AT"] = "AU";
	$code_fips["AZ"] = "AJ";
	$code_fips["BS"] = "BF";
	$code_fips["BH"] = "BA";
	$code_fips["-"] = "FQ";
	$code_fips["BD"] = "BG";
	$code_fips["BB"] = "BB";
	$code_fips["-"] = "BS";
	$code_fips["BY"] = "BO";
	$code_fips["BE"] = "BE";
	$code_fips["BZ"] = "BH";
	$code_fips["BJ"] = "BN";
	$code_fips["BM"] = "BD";
	$code_fips["BT"] = "BT";
	$code_fips["BO"] = "BL";
	$code_fips["BA"] = "BK";
	$code_fips["BW"] = "BC";
	$code_fips["BV"] = "BV";
	$code_fips["BR"] = "BR";
	$code_fips["IO"] = "IO";
	$code_fips["VG"] = "VI";
	$code_fips["BN"] = "BX";
	$code_fips["BG"] = "BU";
	$code_fips["BF"] = "UV";
	$code_fips["MM"] = "BM";
	$code_fips["BI"] = "BY";
	$code_fips["KH"] = "CB";
	$code_fips["CM"] = "CM";
	$code_fips["CA"] = "CA";
	$code_fips["CV"] = "CV";
	$code_fips["KY"] = "CJ";
	$code_fips["CF"] = "CT";
	$code_fips["TD"] = "CD";
	$code_fips["CL"] = "CI";
	$code_fips["CN"] = "CH";
	$code_fips["CX"] = "KT";
	$code_fips["-"] = "IP";
	$code_fips["CC"] = "CK";
	$code_fips["CO"] = "CO";
	$code_fips["KM"] = "CN";
	$code_fips["CD"] = "CG";
	$code_fips["CG"] = "CF";
	$code_fips["CK"] = "CW";
	$code_fips["-"] = "CR";
	$code_fips["CR"] = "CS";
	$code_fips["CI"] = "IV";
	$code_fips["HR"] = "HR";
	$code_fips["CU"] = "CU";
	$code_fips["CY"] = "CY";
	$code_fips["CZ"] = "EZ";
	$code_fips["DK"] = "DA";
	$code_fips["DJ"] = "DJ";
	$code_fips["DM"] = "DO";
	$code_fips["DO"] = "DR";
	$code_fips["TL"] = "TT";
	$code_fips["EC"] = "EC";
	$code_fips["EG"] = "EG";
	$code_fips["SV"] = "ES";
	$code_fips["GQ"] = "EK";
	$code_fips["ER"] = "ER";
	$code_fips["EE"] = "EN";
	$code_fips["ET"] = "ET";
	$code_fips["-"] = "EU";
	$code_fips["FK"] = "FK";
	$code_fips["FO"] = "FO";
	$code_fips["FJ"] = "FJ";
	$code_fips["FI"] = "FI";
	$code_fips["FR"] = "FR";
	$code_fips["FX"] = "-";
	$code_fips["GF"] = "FG";
	$code_fips["PF"] = "FP";
	$code_fips["TF"] = "FS";
	$code_fips["GA"] = "GB";
	$code_fips["GM"] = "GA";
	$code_fips["PS"] = "GZ";
	$code_fips["GE"] = "GG";
	$code_fips["DE"] = "GM";
	$code_fips["GH"] = "GH";
	$code_fips["GI"] = "GI";
	$code_fips["-"] = "GO";
	$code_fips["GR"] = "GR";
	$code_fips["GL"] = "GL";
	$code_fips["GD"] = "GJ";
	$code_fips["GP"] = "GP";
	$code_fips["GU"] = "GQ";
	$code_fips["GT"] = "GT";
	$code_fips["GG"] = "GK";
	$code_fips["GN"] = "GV";
	$code_fips["GW"] = "PU";
	$code_fips["GY"] = "GY";
	$code_fips["HT"] = "HA";
	$code_fips["HM"] = "HM";
	$code_fips["VA"] = "VT";
	$code_fips["HN"] = "HO";
	$code_fips["HK"] = "HK";
	$code_fips["-"] = "HQ";
	$code_fips["HU"] = "HU";
	$code_fips["IS"] = "IC";
	$code_fips["IN"] = "IN";
	$code_fips["ID"] = "ID";
	$code_fips["IR"] = "IR";
	$code_fips["IQ"] = "IZ";
	$code_fips["IE"] = "EI";
	$code_fips["IM"] = "IM";
	$code_fips["IL"] = "IS";
	$code_fips["IT"] = "IT";
	$code_fips["JM"] = "JM";
	$code_fips["-"] = "JN";
	$code_fips["JP"] = "JA";
	$code_fips["-"] = "DQ";
	$code_fips["JE"] = "JE";
	$code_fips["-"] = "JQ";
	$code_fips["JO"] = "JO";
	$code_fips["-"] = "JU";
	$code_fips["KZ"] = "KZ";
	$code_fips["KE"] = "KE";
	$code_fips["-"] = "KQ";
	$code_fips["KI"] = "KR";
	$code_fips["KP"] = "KN";
	$code_fips["KR"] = "KS";
	$code_fips["KW"] = "KU";
	$code_fips["KG"] = "KG";
	$code_fips["LA"] = "LA";
	$code_fips["LV"] = "LG";
	$code_fips["LB"] = "LE";
	$code_fips["LS"] = "LT";
	$code_fips["LR"] = "LI";
	$code_fips["LY"] = "LY";
	$code_fips["LI"] = "LS";
	$code_fips["LT"] = "LH";
	$code_fips["LU"] = "LU";
	$code_fips["MO"] = "MC";
	$code_fips["MK"] = "MK";
	$code_fips["MG"] = "MA";
	$code_fips["MW"] = "MI";
	$code_fips["MY"] = "MY";
	$code_fips["MV"] = "MV";
	$code_fips["ML"] = "ML";
	$code_fips["MT"] = "MT";
	$code_fips["MH"] = "RM";
	$code_fips["MQ"] = "MB";
	$code_fips["MR"] = "MR";
	$code_fips["MU"] = "MP";
	$code_fips["YT"] = "MF";
	$code_fips["MX"] = "MX";
	$code_fips["FM"] = "FM";
	$code_fips["-"] = "MQ";
	$code_fips["MD"] = "MD";
	$code_fips["MC"] = "MN";
	$code_fips["MN"] = "MG";
	$code_fips["ME"] = "MJ";
	$code_fips["MS"] = "MH";
	$code_fips["MA"] = "MO";
	$code_fips["MZ"] = "MZ";
	$code_fips["-"] = "-";
	$code_fips["NA"] = "WA";
	$code_fips["NR"] = "NR";
	$code_fips["-"] = "BQ";
	$code_fips["NP"] = "NP";
	$code_fips["NL"] = "NL";
	$code_fips["AN"] = "NT";
	$code_fips["NC"] = "NC";
	$code_fips["NZ"] = "NZ";
	$code_fips["NI"] = "NU";
	$code_fips["NE"] = "NG";
	$code_fips["NG"] = "NI";
	$code_fips["NU"] = "NE";
	$code_fips["NF"] = "NF";
	$code_fips["MP"] = "CQ";
	$code_fips["NO"] = "NO";
	$code_fips["OM"] = "MU";
	$code_fips["PK"] = "PK";
	$code_fips["PW"] = "PS";
	$code_fips["-"] = "LQ";
	$code_fips["PA"] = "PM";
	$code_fips["PG"] = "PP";
	$code_fips["-"] = "PF";
	$code_fips["PY"] = "PA";
	$code_fips["PE"] = "PE";
	$code_fips["PH"] = "RP";
	$code_fips["PN"] = "PC";
	$code_fips["PL"] = "PL";
	$code_fips["PT"] = "PO";
	$code_fips["PR"] = "RQ";
	$code_fips["QA"] = "QA";
	$code_fips["RE"] = "RE";
	$code_fips["RO"] = "RO";
	$code_fips["RU"] = "RS";
	$code_fips["RW"] = "RW";
	$code_fips["SH"] = "SH";
	$code_fips["KN"] = "SC";
	$code_fips["LC"] = "ST";
	$code_fips["PM"] = "SB";
	$code_fips["VC"] = "VC";
	$code_fips["WS"] = "WS";
	$code_fips["SM"] = "SM";
	$code_fips["ST"] = "TP";
	$code_fips["SA"] = "SA";
	$code_fips["SN"] = "SG";
	$code_fips["RS"] = "RB";
	$code_fips["SC"] = "SE";
	$code_fips["SL"] = "SL";
	$code_fips["SG"] = "SN";
	$code_fips["SK"] = "LO";
	$code_fips["SI"] = "SI";
	$code_fips["SB"] = "BP";
	$code_fips["SO"] = "SO";
	$code_fips["ZA"] = "SF";
	$code_fips["GS"] = "SX";
	$code_fips["ES"] = "SP";
	$code_fips["-"] = "PG";
	$code_fips["LK"] = "CE";
	$code_fips["SD"] = "SU";
	$code_fips["SR"] = "NS";
	$code_fips["SJ"] = "SV";
	$code_fips["SZ"] = "WZ";
	$code_fips["SE"] = "SW";
	$code_fips["CH"] = "SZ";
	$code_fips["SY"] = "SY";
	$code_fips["TW"] = "TW";
	$code_fips["TJ"] = "TI";
	$code_fips["TZ"] = "TZ";
	$code_fips["TH"] = "TH";
	$code_fips["TG"] = "TO";
	$code_fips["TK"] = "TL";
	$code_fips["TO"] = "TN";
	$code_fips["TT"] = "TD";
	$code_fips["-"] = "TE";
	$code_fips["TN"] = "TS";
	$code_fips["TR"] = "TU";
	$code_fips["TM"] = "TX";
	$code_fips["TC"] = "TK";
	$code_fips["TV"] = "TV";
	$code_fips["UG"] = "UG";
	$code_fips["UA"] = "UP";
	$code_fips["AE"] = "AE";
	$code_fips["GB"] = "UK";
	$code_fips["US"] = "US";
	$code_fips["UM"] = "-";
	$code_fips["UY"] = "UY";
	$code_fips["UZ"] = "UZ";
	$code_fips["VU"] = "NH";
	$code_fips["VE"] = "VE";
	$code_fips["VN"] = "VM";
	$code_fips["VI"] = "VQ";
	$code_fips["-"] = "-";
	$code_fips["-"] = "-";
	$code_fips["-"] = "WQ";
	$code_fips["WF"] = "WF";
	$code_fips["PS"] = "WE";
	$code_fips["EH"] = "WI";
	$code_fips["-"] = "-";
	$code_fips["-"] = "-";
	$code_fips["YE"] = "YM";
	$code_fips["-"] = "-";
	$code_fips["ZM"] = "ZA";
	$code_fips["ZW"] = "ZI";

	return $code_fips["$code"];
}

// Donne la hauteur du graphe en fonction de la valeur maximale
// Doit etre un entier "rond", pas trop eloigne du max, et dont
// les graduations (divisions par huit) soient jolies :
// on prend donc le plus proche au-dessus de x de la forme 12,16,20,40,60,80,100
// http://doc.spip.org/@maxgraph
function maxgraph($max) {
	switch (strlen($max)) {
	case 0:
		$maxgraph = 1;
		break;
	case 1:
		$maxgraph = 16;
		break;
	case 2:
		$maxgraph = (floor($max / 8) + 1) * 8;
		break;
	case 3:
		$maxgraph = (floor($max / 80) + 1) * 80;
		break;
	default:
		$maxgraph = (floor($max / (2 * pow(10, strlen($max)-2))) + 1) * 2 * pow(10, strlen($max)-2);
	}
	return $maxgraph;
}

// http://doc.spip.org/@http_img_rien
function http_img_rien($width, $height, $class='', $title='') {
	return http_img_pack('rien.gif', $title, 
		"width='$width' height='$height'" 
		. (!$class ? '' : (" class='$class'"))
		. (!$title ? '' : (" title=\"$title\"")));
}

// pondre les stats sous forme d'un fichier csv tres basique
// http://doc.spip.org/@statistiques_csv
function statistiques_csv($id) {

	$filename = 'stats_'.($id ? 'article'.$id : 'total').'.csv';
	header('Content-Type: text/csv');
	header('Content-Disposition: attachment; filename='.$filename);
	
	if ($id)
		$s = spip_query("SELECT date, visites FROM spip_visites_articles WHERE id_article=$id ORDER BY date");
	else
		$s = spip_query("SELECT date, visites FROM spip_visites ORDER BY date");
	while ($t = spip_fetch_array($s)) {
		echo $t['date'].";".$t['visites']."\n";
	}
}

// http://doc.spip.org/@exec_statistiques_visites_dist
function exec_pb_statistiques()
{
	global $connect_statut, $spip_lang_left, $couleur_claire;


	$id_article = intval(_request('id_article'));
	$aff_jours = intval(_request('aff_jours'));
	$origine = _request('origine');

	if (!autoriser('voirstats', $id_article ? 'article':'', $id_article)) {
	  include_spip('minipres');
	  echo minipres();
	  exit;
	}

	if (!$aff_jours) $aff_jours = 105;
	// nombre de referers a afficher
	$limit = intval(_request('limit'));
	if ($limit == 0) $limit = 100;

	if (_request('format') == 'csv')
		return statistiques_csv($id_article);

	$GLOBALS['accepte_svg'] = flag_svg();

	$titre = $pourarticle = "";
	$class = " class='arial1 spip_x-small'";
	$style = 'color: #999999';

	if ($id_article){
		$result = spip_query("SELECT titre, visites, popularite FROM spip_articles WHERE statut='publie' AND id_article=$id_article");

		if ($row = spip_fetch_array($result)) {
			$titre = typo($row['titre']);
			$total_absolu = $row['visites'];
			$val_popularite = round($row['popularite']);
		}
	} else {
		$result = spip_query("SELECT SUM(visites) AS total_absolu FROM spip_visites");


		if ($row = spip_fetch_array($result)) {
			$total_absolu = $row['total_absolu'];
		}
	}

	if ($titre) $pourarticle = " "._T('info_pour')." &laquo; $titre &raquo;";

	if ($origine) {
	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page(_T('titre_page_statistiques_referers'), "statistiques_visites", "statistiques");
	echo "<br /><br />";
	gros_titre(_T('titre_liens_entrants'));
	echo barre_onglets("statistiques", "referers");

	debut_gauche();
	debut_boite_info();
	echo "<p style='text-align: left' style='font-size:small;' class='verdana1'>"._T('info_gauche_statistiques_referers')."</p>";
	fin_boite_info();
	
	debut_droite();

}
else {
	if ($_GET["afficher_pays"]) {
		$le_pays = $_GET["afficher_pays"];
		$pourarticle = " pour : " . nom_pays_francais($le_pays);
	}
	
	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page(_T('titre_page_statistiques_visites').$pourarticle, "statistiques_visites", "statistiques");
	echo "<br /><br />";
	gros_titre(_T('titre_evolution_visite').$pourarticle."<html>".aide("confstat")."</html>");
//	barre_onglets("statistiques", "evolution");
	if ($titre) gros_titre($titre);

	debut_gauche();



			$result = spip_query("SELECT * FROM spip_pb_visites_pays WHERE date > DATE_SUB(NOW(), INTERVAL 1 WEEK) AND pays != ''");
			while ($row = spip_fetch_array($result)) {
				$pays = $row["pays"];
				$nom_pays["$pays"] = pb_nom_pays($pays);
				if ($nom_pays["$pays"] == "") $nom_pays["$pays"] = $pays;
				$total["$pays"] = $total["$pays"] + $row["visites"];	
			}



			if (count($nom_pays) > 0) {
				arsort($total);
				
				echo "<ul class='verdana1'>";
				
					if ($le_pays) echo "<li><b><a href='?exec=pb_statistiques'>Tous les pays</a></b></li>";
					else echo "<li><b>Tous les pays</b></li>";
				
				foreach($total AS $pays => $value) {
					if ($le_pays == $pays) echo "<li>".nom_pays_francais($pays)."</li>";
					else echo "<li>$pays - <a href='?exec=pb_statistiques&amp;afficher_pays=$pays'>".nom_pays_francais($pays)."</a></li>";
				}
				echo "</ul>";
					
				
			}
			



	debut_droite();
 }

include_spip("inc/plugin");
$liste = liste_plugin_actifs();
if ($liste["PB_CHARTS"]) $pb_charts = true;

//echo propre("<chart type=\"line\">\n|Hello|3|4|5|\n|120|130|140|150|\n</chart>");


	if ($GLOBALS["spip_version"] < 1.93) { 
	
	echo "<style>\n"
		."table.bottom td {vertical-align:bottom}\n"
		."table.bottom img {display:block}\n"
		.".trait_haut {background:#762996;}\n"
		.".trait_bas {background:black;}\n"
		.".trait_moyen {background:#666;}\n"
		.".couleur_dimanche {background:#762996;}\n"
		.".couleur_jour { background:#c190d6;}\n"
		.".couleur_janvier {background:#762996;}\n"
		.".couleur_mois { background:#c190d6;}\n"
		.".couleur_prevision { background:#eee;}\n"
		.".couleur_realise {background:#999;}\n"
		.".couleur_cumul {background:#666;}\n"
		.".couleur_nombre {background:#eee;}\n"
		.".couleur_langue {background:#762996;}\n"
		.".tr_liste {font-size: 10px; font-family: verdana, arial, sans;}\n"
		."</style>\n";
	}



function afficher_duree($duree) {
	$minutes = floor($duree);
	$secondes = round(($duree - $minutes) * 60);
	
	return "$minutes min $secondes s " ;
}


$GLOBALS["largeur_charts"] = 480;

// Carte du monde


	$result_date = spip_query("SELECT * FROM spip_pb_visites ORDER BY date DESC LIMIT 1,1");
	if ($row_date = spip_fetch_array($result_date)) {
		$date_pays = $row_date["date"];
		
	} else {
		$result_date = spip_query("SELECT * FROM spip_pb_visites ORDER BY date DESC LIMIT 0,1");
		if ($row_date = spip_fetch_array($result_date)) {
			$date_pays = $row_date["date"];
		
		}
	}



	if ($date_pays) {
		
			$stats = "
				<state id='range'>
					<data>0 - 5</data>
					<color>b99bc5</color>
				</state>
				<state id='range'>
					<data>6 - 10</data>
					<color>aba5ca</color>
				</state>
				<state id='range'>
					<data>11 - 15</data>
					<color>98b6ca</color>
				</state>
				<state id='range'>
					<data>16 - 20</data>
					<color>89c4ca</color>
				</state>
				<state id='range'>
					<data>21 - 30</data>
					<color>80d2ca</color>
				</state>
				<state id='range'>
					<data>31 - 40</data>
					<color>95df94</color>
				</state>
				<state id='range'>
					<data>41 - 50</data>
					<color>c0e73c</color>
				</state>
				<state id='range'>
					<data>51 - 60</data>
					<color>dae612</color>
				</state>
				<state id='range'>
					<data>61 - 70</data>
					<color>e9e405</color>
				</state>
				<state id='range'>
					<data>71 - 80</data>
					<color>f3cb00</color>
				</state>
				<state id='range'>
					<data>81 - 90</data>
					<color>f47400</color>
				</state>
				<state id='range'>
					<data>91 - 100</data>
					<color>f40c00</color>
				</state>
<state id='default_color'>
		<color>ffffff</color>
</state>
<state id='background_color'>
		<color>f1eef6</color>
</state>
<state id='outline_color'>
		<color>8c7297</color>
</state>
";
			$visites_utiles = array();
			$nom_pays = array();
			$pages_vues = array();
			$pages_vues_utiles = array();
			
			$result = spip_query("SELECT * FROM spip_pb_visites_pays WHERE date > DATE_SUB(NOW(), INTERVAL 1 WEEK) AND pays != ''");
			while ($row = spip_fetch_array($result)) {
				$pays = $row["pays"];
				$nom_pays["$pays"] = pb_nom_pays($pays);
				$lesvisites["$pays"] = $lesvisites["$pays"] + $row["visites"];	
				$visites_pop["$pays"] = $visites_pop["$pays"] + (1000000000 * $row["visites"] / pb_population($pays));
				$visites_utiles["$pays"] += $row["visites_utiles"];
				$pages_vues["$pays"] += $row["pages_vues"];
				$pages_vues_utiles["$pays"] += $row["pages_vues_utiles"];
				
				
//				echo "<li>$pays / ". $visites_pop["$pays"];
			}



			if (count($nom_pays) > 0) {
				arsort($visites_pop);
			}
			
			foreach($visites_pop AS $pays=>$visites) {
				
				$count ++;
				if ($count == 1) $max = $visites_pop["$pays"];
				$visites = $lesvisites["$pays"];
				$code = pb_corriger_code_fips($pays);
				$pourcent = round($visites_pop["$pays"] / $max * 100);
				$nom = nom_pays_francais($pays);
				
//				 echo "<li style='font-size: 10px; font-family: verdana;'>$code / $nom : $visites ($pourcent %)</li>";
				
				$stats .= "<state id='$code'>\n\t<name>$nom</name>\n\t<data>$pourcent</data>\n\t<hover>$visites visites (rang $count)</hover>\n</state>\n";				
				
			}
			$stats = "<countrydata>$stats</countrydata>";
			
			
			// Hum, probleme: le fichier XML des statistiques est accessible en ligne...
			$codes = lire_meta("nom_site").lire_meta("email_webmaster").lire_meta("formats_graphiques").lire_meta("image_process").lire_meta("low_sec").lire_meta("max_taille_vignettes");
			$fichier_xml = sous_repertoire(_DIR_VAR, 'cache-pb_visites')."geo".md5($codes).".xml";	
			
			ecrire_fichier($fichier_xml, $stats);
						
			$movie = _DIR_PLUGINS."pb_visites/world-swf/world.swf";
			
			if (file_exists($movie)) {
				debut_cadre_relief("",false, "", "Origine des visites (sur 7 jours)");
	
				
				echo "<div style='text-align:center;'><object classid='clsid:d27cdb6e-ae6d-11cf-96b8-444553540000' codebase='http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,0,0' width='480' height='300' id='zoom_map' align='top'>";
				echo "<param name='movie' value='$movie?data_file=$fichier_xml' />";
				echo "<param name='quality' value='high' />";
				echo "<param name='bgcolor' value='#FFFFFF' />";
				echo "<embed src='$movie?data_file=$fichier_xml' quality='high' bgcolor='#FFFFFF'  width='480' height='300' name='Clickable World Map' align='top' type='application/x-shockwave-flash' pluginspage='http://www.macromedia.com/go/getflashplayer'></embed>";
				echo "</object></div>";
				
				
				echo "<div style='padding-top: 5px; text-align: right; font-size: 10px;'>";
				echo "<div>Base adresses IP vers pays © LGPL <a href='http://www.maxmind.com'>MaxMind</a></div>";
				echo "<div>Carte Flash interactive © <a href='http://backspace.com/mapapp/'>DIY Map</a></div>";
				echo "<div><b>N.B.</b> Les codes de couleur et le rang correspondent au nombre de visites<br /> ramen&eacute; &agrave; la population de chaque pays.</div>";
				echo "</div>";
				fin_cadre_relief();
			} else {
				debut_cadre_relief();
				echo "Pour pouvoir afficher la carte des origines des visites&nbsp;:";
				echo "<ul>";
				echo "<li>rendez-vous <a href='http://www.backspace.com/mapapp/'>sur le site DIY Map</a>;</li>";
				echo "<li>t&eacute;l&eacute;chargez le fichier &laquo;<a href='http://backspace.com/mapapp/world/world.zip'>World Map</a>&raquo;</li>";
				echo "<li>d&eacute;compactez ce fichier et rep&eacute;rez le document Flash &laquo;word.swf&raquo;;";
				echo "<li>installez (par FTP) ce fichier dans le dossier &laquo;/plugins/pb_visites/world-swf&raquo;;</li>";
				echo "<li>cette carte n'est pas libre; pensez &agrave; respecter la licence d'utilisation.</li>";
				echo "</ul>";
				fin_cadre_relief();
			}
		}


// RESUME


	$delais[] = -1;
	$delais[] = 0;
	$delais[] = 7;
	$delais[] = 14;
	$delais[] = 30;
	$delais[] = round(365/4);
	$delais[] = round(365/2);
	$delais[] = 365;
	
	echo "<table width='100%' cellpadding='2' cellspacing='0' border='0' style='font-size: 10px; border: 1px solid black;'>";
	echo "<tr class='tr_liste' style='font-weight: bold; background-color: white;'><td></td><td>Visites</td><td>Visites utiles</td><td>Pages vues</td><td>PV/V</td><td>Dur&eacute;e par visite</td></tr>";
	
	foreach($delais AS $del) {
		if ($le_pays) $result = spip_query("SELECT * FROM spip_pb_visites_pays WHERE pays='$le_pays' ORDER BY date DESC LIMIT ".($del+1).",1");
		else $result = spip_query("SELECT * FROM spip_pb_visites ORDER BY date DESC LIMIT ".($del+1).",1");
		if ($row = spip_fetch_array($result)) {
			$visites = $row["visites"];
			$visites_utiles = $row["visites_utiles"];
			$pages_vues = $row["pages_vues"];
			$pages_vues_utiles = $row["pages_vues_utiles"];
			$duree = $row["duree"];
			if ($visites_utiles > 0) $duree_visite = ($duree/$visites_utiles) / 60;
			else $duree_visite = "";
			
			if ($visites > 0) {
				$pv_visite = round(($pages_vues / $visites) * 10) / 10;
			}
			
			if ($del == -1) {
				echo "<tr class='tr_liste'><td>J</td><td>$visites</td><td>$visites_utiles</td><td>$pages_vues</td><td>$pv_visite</td><td>".afficher_duree($duree_visite)."</td></tr>";
			
			}
			else if ($del == 0) {
				echo "<tr class='tr_liste' style='font-weight: bold;'><td>J-1</td><td>$visites</td><td>$visites_utiles</td><td>$pages_vues</td><td>$pv_visite</td><td>".afficher_duree($duree_visite)."</td></tr>";
				
				$visites_abs = $visites;
				$visites_utiles_abs = $visites_utiles;
				$pages_vues_abs = $pages_vues;
				$pages_vues_utiles_abs = $pages_vues_utiles;
				$duree_visite_abs = $duree_visite;
				
			} else {
				echo "<tr class='tr_liste'><td>J-".($del+1)."</td>";
				
				if ($visites > 0) {
					$visites_rel = round(100 * ($visites_abs - $visites) / $visites);
					if ($visites_rel > 0) $visites_rel = "<span style='color: green;'>(+$visites_rel%)</span>";
					else $visites_rel = "<span style='color: red;'>($visites_rel%)</span>";
				}
				if ($visites_utiles > 0) {
					$visites_utiles_rel = round(100 * ($visites_utiles_abs - $visites_utiles) / $visites_utiles);
					if ($visites_utiles_rel > 0) $visites_utiles_rel = "<span style='color: green;'>(+$visites_utiles_rel%)</span>";
					else $visites_utiles_rel = "<span style='color: red;'>(+$visites_utiles_rel%)</span>";
				}
				if ($pages_vues > 0) {
					$pages_vues_rel = round(100 * ($pages_vues_abs - $pages_vues) / $pages_vues);
					if ($pages_vues_rel > 0) $pages_vues_rel = "<span style='color: green;'>(+$pages_vues_rel%)</span>";
					else $pages_vues_rel = "<span style='color: red;'>($pages_vues_rel%)</span>";
				}
				if ($duree_visite > 0) {
					$duree_visite_rel = round(100 * ($duree_visite_abs - $duree_visite) / $duree_visite);
					if ($duree_visite_rel > 0) $duree_visite_rel = "<span style='color: green;'>(+$duree_visite_rel%)</span>";
					else $duree_visite_rel = "<span style='color: red;'>($duree_visite_rel%)</span>";
				}
				
				echo "<td>$visites $visites_rel</td><td>$visites_utiles $visites_utiles_rel</td><td>$pages_vues $pages_vues_rel</td><td>$pv_visite</td><td>".afficher_duree($duree_visite)." $duree_visite_rel</td></tr>";			
			}
			
			
			
			
			
		}

	}
	echo "</table>";





////// COMPTER VISITES

 if (!$origine) {

		if ($le_pays) {
			$table = "spip_pb_visites_pays";
			$where = "pays='$le_pays'";		
		} else {
			$table = "spip_pb_visites";
			$table_ref = "spip_referers";
			$where = "0=0";
		}
	$result = spip_query("SELECT UNIX_TIMESTAMP(date) AS date_unix FROM $table WHERE $where ORDER BY date LIMIT 1");

	while ($row = spip_fetch_array($result)) {
		$date_premier = $row['date_unix'];
	}

	$result=spip_query("SELECT UNIX_TIMESTAMP(date) AS date_unix, visites, visites_utiles FROM $table WHERE $where AND date > DATE_SUB(NOW(),INTERVAL $aff_jours DAY) ORDER BY date");

	$date_debut = '';
	$log = array();
	while ($row = spip_fetch_array($result)) {
		$date = $row['date_unix'];
		if (!$date_debut) $date_debut = $date;
		$log[$date] = $row['visites'];
		$log_utiles[$date] = $row['visites_utiles'];
	}


	// S'il y a au moins cinq minutes de stats :-)
	if (count($log)>0) {
		// les visites du jour
		$date_today = max(array_keys($log));
		$visites_today = $log[$date_today];
		// sauf s'il n'y en a pas :
		if (time()-$date_today>3600*24) {
			$date_today = time();
			$visites_today=0;
		}
		
		// le nombre maximum
		$max = max($log);
		$nb_jours = floor(($date_today-$date_debut)/(3600*24));

		$maxgraph = maxgraph($max);
		$rapport = 200 / $maxgraph;

		if (count($log) < 420) $largeur = floor(450 / ($nb_jours+1));
		if ($largeur < 1) {
			$largeur = 1;
			$agreg = ceil(count($log) / 420);	
		} else {
			$agreg = 1;
		}
		if ($largeur > 50) $largeur = 50;

		debut_cadre_relief("statistiques-24.gif",false, "", "&Eacute;volution des visites et visites utiles (au moins 2 pages vues)");
		

		$largeur_abs = 420 / $aff_jours;
		
		if ($largeur_abs > 1) {
			$inc = ceil($largeur_abs / 5);
			$aff_jours_plus = 420 / ($largeur_abs - $inc);
			$aff_jours_moins = 420 / ($largeur_abs + $inc);
		}
		
		if ($largeur_abs == 1) {
			$aff_jours_plus = 840;
			$aff_jours_moins = 210;
		}
		
		if ($largeur_abs < 1) {
			$aff_jours_plus = 420 * ((1/$largeur_abs) + 1);
			$aff_jours_moins = 420 * ((1/$largeur_abs) - 1);
		}
		
		$pour_article = $id_article ? "&id_article=$id_article" : '';
		
		if ($date_premier < $date_debut)
		  echo http_href(generer_url_ecrire("pb_statistiques","aff_jours=$aff_jours_plus$pour_article"),
				 http_img_pack('loupe-moins.gif',
					       _T('info_zoom'). '-', 
					       "style='border: 0px; vertical-align: middle;'"),
				 "&nbsp;");
		if ( (($date_today - $date_debut) / (24*3600)) > 30)
		  echo http_href(generer_url_ecrire("pb_statistiques","aff_jours=$aff_jours_moins$pour_article"), 
				 http_img_pack('loupe-plus.gif',
					       _T('info_zoom'). '+', 
					       "style='border: 0px; vertical-align: middle;'"),
				 "&nbsp;");
	
	if ($pb_charts) {
		foreach ($log as $key => $value) {
					$ce_jour = date("d", $key);
					
					if ($ce_jour == "1") {
						$afficher = nom_mois(date("Y-m-d", $key));
						if (date("m", $key) == 1) $afficher = annee(date("Y-m-d", $key));
						
					} else {
						$afficher = "";
					}
		
		
			$ligne0 .= "|$afficher";
			$ligne1 .= "|".($value - $log_utiles["$key"]);
			$ligne2 .= "|".$log_utiles["$key"];		
		
		}
		
		$ligne0 = "|$ligne0|\n";
		$ligne1 = "|Visites$ligne1|\n";
		$ligne2 = "|Visites utiles$ligne2|\n";
		
		echo propre("<chart type=\"stacked area\">\n$ligne0$ligne2$ligne1\n</chart>");
		
	} else {
	
		echo "\n<table cellpadding='0' cellspacing='0' border='0'><tr>",
		  "\n<td ".http_style_background("fond-stats.gif").">";
		echo "\n<table cellpadding='0' cellspacing='0' border='0' class='bottom'><tr>";
		
		echo "\n<td style='background-color: black'>", http_img_rien(1,200), "</td>";
		
		$test_agreg = $decal = $jour_prec = $val_prec = $total_loc =0;
	
		// Presentation graphique (rq: on n'affiche pas le jour courant)
		foreach ($log as $key => $value) {
			# quand on atteint aujourd'hui, stop
			if ($key == $date_today) break; 
	
			$test_agreg ++;
			
			if ($test_agreg == $agreg) {	
					
				$test_agreg = 0;
				
				if ($decal == 30) $decal = 0;
				$decal ++;
				$tab_moyenne[$decal] = $value;
				// Inserer des jours vides si pas d'entrees	
				if ($jour_prec > 0) {
						$ecart = floor(($key-$jour_prec)/((3600*24)*$agreg)-1);
			
						for ($i=0; $i < $ecart; $i++){
							if ($decal == 30) $decal = 0;
							$decal ++;
							$tab_moyenne[$decal] = $value;
		
							$ce_jour=date("Y-m-d", $jour_prec+(3600*24*($i+1)));
							$jour = nom_jour($ce_jour).' '.affdate_jourcourt($ce_jour);
		
							reset($tab_moyenne);
							$moyenne = 0;
							while (list(,$val_tab) = each($tab_moyenne))
								$moyenne += $val_tab;
							$moyenne = $moyenne / count($tab_moyenne);
			
							$hauteur_moyenne = round(($moyenne) * $rapport) - 1;
							echo "\n<td style='width: ${largeur}px'>";
							$difference = ($hauteur_moyenne) -1;
							$moyenne = round($moyenne,2); // Pour affichage harmonieux
							$tagtitle= attribut_html(supprimer_tags("$jour | "
							._T('info_visites')." | "
							._T('info_moyenne')." $moyenne"));
							if ($difference > 0) {	
							  echo http_img_rien($largeur,1, 'trait_moyen', $tagtitle);
							  echo http_img_rien($largeur, $hauteur_moyenne, '', $tagtitle);
							}
							echo 
								http_img_rien($largeur,1,'trait_bas', $tagtitle);
							echo "</td>";
						}
					}
		
					$ce_jour=date("Y-m-d", $key);
					$jour = nom_jour($ce_jour).' '.affdate_jourcourt($ce_jour);
		
					$total_loc = $total_loc + $value;
					reset($tab_moyenne);
		
					$moyenne = 0;
					while (list(,$val_tab) = each($tab_moyenne))
						$moyenne += $val_tab;
					$moyenne = $moyenne / count($tab_moyenne);
				
					$hauteur_moyenne = round($log_utiles[$key] * $rapport) - 1;
					$hauteur = round($value * $rapport) - 1;
					$moyenne = round($moyenne,2); // Pour affichage harmonieux
					echo "\n<td style='width: ${largeur}px'>";
		
					$tagtitle= attribut_html(supprimer_tags("$jour | "
					._T('info_visites')." ".$value));
		
					if ($hauteur > 0){
						if ($hauteur_moyenne > $hauteur) {
							$difference = ($hauteur_moyenne - $hauteur) -1;
							echo http_img_rien($largeur, 1,'trait_moyen',$tagtitle);
							echo http_img_rien($largeur, $difference, '', $tagtitle);
							echo http_img_rien($largeur,1, "trait_haut", $tagtitle);
							if (date("w",$key) == "0") // Dimanche en couleur foncee
							  echo http_img_rien($largeur, $hauteur, "couleur_dimanche", $tagtitle);
							else
							  echo http_img_rien($largeur,$hauteur, "couleur_jour", $tagtitle);
						} else if ($hauteur_moyenne < $hauteur) {
							$difference = ($hauteur - $hauteur_moyenne) -1;
							echo http_img_rien($largeur,1,"trait_haut", $tagtitle);
							if (date("w",$key) == "0") // Dimanche en couleur foncee
								$couleur =  'couleur_dimanche';
							else
								$couleur = 'couleur_jour';
							echo http_img_rien($largeur, $difference, $couleur, $tagtitle);
							echo http_img_rien($largeur,1,"trait_moyen", $tagtitle);
							echo http_img_rien($largeur, $hauteur_moyenne, $couleur, $tagtitle);
						} else {
						  echo http_img_rien($largeur, 1, "trait_haut", $tagtitle);
							if (date("w",$key) == "0") // Dimanche en couleur foncee
							  echo http_img_rien($largeur, $hauteur, "couleur_dimanche", $tagtitle);
							else
							  echo http_img_rien($largeur,$hauteur, "couleur_jour", $tagtitle);
						}
					}
					echo http_img_rien($largeur, 1, 'trait_bas', $tagtitle);
					echo "</td>\n";
				
					$jour_prec = $key;
					$val_prec = $value;
				}
				}
		
				// Dernier jour
				$hauteur = round($visites_today * $rapport)	- 1;
				$total_absolu = $total_absolu + $visites_today;
				echo "\n<td style='width: ${largeur}px'>";
				// prevision de visites jusqu'a minuit
				// basee sur la moyenne (site) ou popularite (article)
				if (! $id_article) $val_popularite = $moyenne;
				$prevision = (1 - (date("H")*60 + date("i"))/(24*60)) * $val_popularite;
				$hauteurprevision = ceil($prevision * $rapport);
				// Afficher la barre tout en haut
				if ($hauteur+$hauteurprevision>0)
					echo http_img_rien($largeur, 1, "trait_haut");
				// preparer le texte de survol (prevision)
				$tagtitle= attribut_html(supprimer_tags(_T('info_aujourdhui')." $visites_today &rarr; ".(round($prevision,0)+$visites_today)));
				// afficher la barre previsionnelle
				if ($hauteurprevision>0)
					echo http_img_rien($largeur, $hauteurprevision,'couleur_prevision', $tagtitle);
					// afficher la barre deja realisee
				if ($hauteur>0)
					echo http_img_rien($largeur, $hauteur, 'couleur_realise', $tagtitle);
				// et afficher la ligne de base
				echo http_img_rien($largeur, 1, 'trait_bas');
				echo "</td>";
	
	
				echo "\n<td style='background-color: black'>",http_img_rien(1, 1),"</td>";
				echo "</tr></table>";
				echo "</td>",
				  "\n<td ".http_style_background("fond-stats.gif")."  valign='bottom'>", http_img_rien(3, 1, 'trait_bas'),"</td>";
				echo "\n<td>", http_img_rien(5, 1),"</td>";
				echo "\n<td valign='top'><div style='font-size:small;' class='verdana1'>";
				echo "\n<table cellpadding='0' cellspacing='0' border='0'>";
				echo "\n<tr><td style='height: 15' valign='top'>";		
				echo "<span class='arial1 spip_x-small'><b>".round($maxgraph)."</b></span>";
				echo "</td></tr>";
				echo "\n<tr><td valign='middle' $class style='$style;height: 25px'>";		
				echo round(7*($maxgraph/8));
				echo "</td></tr>";
				echo "\n<tr><td style='height: 25px' valign='middle'>";		
				echo "<span class='arial1 spip_x-small'>".round(3*($maxgraph/4))."</span>";
				echo "</td></tr>";
				echo "\n<tr><td valign='middle' $class style='$style;height: 25px'>";		
				echo round(5*($maxgraph/8));
				echo "</td></tr>";
				echo "\n<tr><td style='height: 25px' valign='middle'>";		
				echo "<span class='arial1 spip_x-small'><b>".round($maxgraph/2)."</b></span>";
				echo "</td></tr>";
				echo "\n<tr><td valign='middle' $class style='$style;height: 25px'>";		
				echo round(3*($maxgraph/8));
				echo "</td></tr>";
				echo "\n<tr><td style='height: 25px' valign='middle'>";		
				echo "<span class='arial1 spip_x-small'>".round($maxgraph/4)."</span>";
				echo "</td></tr>";
				echo "\n<tr><td valign='middle' $class style='$style;height: 25px'>";		
				echo round(1*($maxgraph/8));
				echo "</td></tr>";
				echo "\n<tr><td style='height: 10px' valign='bottom'>";		
				echo "<span class='arial1 spip_x-small'><b>0</b></span>";
				echo "</td>";
				echo "</tr></table>";
				echo "</div></td>";
				echo "</tr></table>";
						
				echo "<div style='position: relative; height: 15px'>";
				$gauche_prec = -50;
				for ($jour = $date_debut; $jour <= $date_today; $jour = $jour + (24*3600)) {
					$ce_jour = date("d", $jour);
					
					if ($ce_jour == "1") {
						$afficher = nom_mois(date("Y-m-d", $jour));
						if (date("m", $jour) == 1) $afficher = "<b>".annee(date("Y-m-d", $jour))."</b>";
						
					
						$gauche = floor($jour - $date_debut) * $largeur / ((24*3600)*$agreg);
						
						if ($gauche - $gauche_prec >= 40 OR date("m", $jour) == 1) {									
							echo "<div class='arial0' style='border-$spip_lang_left: 1px solid black; padding-$spip_lang_left: 2px; padding-top: 3px; position: absolute; $spip_lang_left: ".$gauche."px; top: -1px;'>".$afficher."</div>";
							$gauche_prec = $gauche;
						}
					}
				}
				echo "</div>";
			}

		//}

		// cette ligne donne la moyenne depuis le debut
		// (desactive au profit de la moeynne "glissante")
		# $moyenne =  round($total_absolu / ((date("U")-$date_premier)/(3600*24)));

		echo "<span class='arial1 spip_x-small'>"._T('texte_pb_statistiques')."</span>";
		echo "<br /><table cellpadding='0' cellspacing='0' border='0' width='100%'><tr style='width:100%;'>";
		echo "\n<td valign='top' style='width: 33%; ' class='verdana1'>", _T('info_maximum')." ".$max, "<br />"._T('info_moyenne')." ".round($moyenne), "</td>";
		echo "\n<td valign='top' style='width: 33%; ' class='verdana1'>";
		echo '<a href="' . generer_url_ecrire("statistiques_referers","").'" title="'._T('titre_liens_entrants').'">'._T('info_aujourdhui').'</a> '.$visites_today;
		if ($val_prec > 0) echo '<br /><a href="' . generer_url_ecrire("statistiques_referers","jour=veille").'"  title="'._T('titre_liens_entrants').'">'._T('info_hier').'</a> '.$val_prec;
		if ($id_article) echo "<br />"._T('info_popularite_5').' '.$val_popularite;

		echo "</td>";
		echo "\n<td valign='top' style='width: 33%; ' class='verdana1'>";
		echo "<b>"._T('info_total')." ".$total_absolu."</b>";
		
		if ($id_article) {
			if ($classement[$id_article] > 0) {
				if ($classement[$id_article] == 1)
				      $ch = _T('info_classement_1', array('liste' => $liste));
				else
				      $ch = _T('info_classement_2', array('liste' => $liste));
				echo "<br />".$classement[$id_article].$ch;
			}
		} else {
		  echo "<span class='spip_x-small'><br />"._T('info_popularite_2')." ", ceil($GLOBALS['meta']['popularite_total']), "</span>";
		}
		echo "</td></tr></table>";	
	}		
	
	if (count($log) > 60) {
		echo "<br />";
		echo "<span class='verdana1 spip_small'><b>"._T('info_visites_par_mois')."</b></span>";

		///////// Affichage par mois
		$result=spip_query("SELECT FROM_UNIXTIME(UNIX_TIMESTAMP(date),'%Y-%m') AS date_unix, SUM(visites) AS total_visites, SUM(visites_utiles) AS total_visites_utiles  FROM $table WHERE $where AND date > DATE_SUB(NOW(),INTERVAL 2700 DAY) GROUP BY date_unix ORDER BY date");

		
		$i = 0;
		while ($row = spip_fetch_array($result)) {
			$date = $row['date_unix'];
			$visites = $row['total_visites'];
			$i++;
			$entrees["$date"] = $visites;
			$entrees_utiles["$date"] = $row["total_visites_utiles"];
		}
		// Pour la derniere date, rajouter les visites du jour sauf si premier jour du mois
		if (date("d",time()) > 1) {
			$entrees["$date"] += $visites_today;
		} else { // Premier jour du mois : le rajouter dans le tableau des date (car il n'etait pas dans le resultat de la requete SQL precedente)
			$date = date("Y-m",time());
			$entrees["$date"] = $visites_today;
		}
		
		if (count($entrees)>0){
		
			$max = max($entrees);
			$maxgraph = maxgraph($max);
			$rapport = 200/$maxgraph;

			$largeur = floor(420 / (count($entrees)));
			if ($largeur < 1) $largeur = 1;
			if ($largeur > 50) $largeur = 50;
		}


	if ($pb_charts) {
		$ligne0 = "";
		$ligne1 = "";
		$ligne2 = "";
		foreach ($entrees as $key => $value) {
	/*
					$ce_jour = date("d", $key);
					
					if ($ce_jour == "1") {
						$afficher = nom_mois(date("Y-m-d", $key));
						if (date("m", $key) == 1) $afficher = annee(date("Y-m-d", $key));
						
					} else {
						$afficher = "";
					}
	*/	
		
			$ligne0 .= "|$afficher";
			$ligne1 .= "|".($value-$entrees_utiles["$key"]);
			$ligne2 .= "|".$entrees_utiles["$key"];		
		
		}
		
		$ligne0 = "|$ligne0|\n";
		$ligne1 = "|Visites$ligne1|\n";
		$ligne2 = "|Visites utiles$ligne2|\n";
		
		echo propre("<chart type=\"Stacked 3D Column\">\n$ligne0$ligne2$ligne1\n</chart>");
		
	} else {
		
		echo "\n<table cellpadding='0' cellspacing='0' border='0'><tr>",
		  "\n<td ".http_style_background("fond-stats.gif").">";
		echo "\n<table cellpadding='0' cellspacing='0' border='0' class='bottom'><tr>";
		echo "\n<td class='trait_bas'>", http_img_rien(1, 200),"</td>";
		// Presentation graphique
		$decal = 0;
		$tab_moyenne = "";
			
		while (list($key, $value) = each($entrees)) {
			
			$mois = affdate_mois_annee($key);

			if ($decal == 30) $decal = 0;
			$decal ++;
			$tab_moyenne[$decal] = $value;
			
			$total_loc = $total_loc + $value;
			reset($tab_moyenne);
	
			$moyenne = 0;
			while (list(,$val_tab) = each($tab_moyenne))
				$moyenne += $val_tab;
			$moyenne = $moyenne / count($tab_moyenne);
			
			$hauteur_moyenne = round($entrees_utiles["$key"] * $rapport) - 1;
			$hauteur = round($value * $rapport) - 1;
			echo "\n<td style='width: ${largeur}px'>";

			$tagtitle= attribut_html(supprimer_tags("$mois | "
			._T('info_visites')." ".$value));

			if ($hauteur > 0){
				if ($hauteur_moyenne > $hauteur) {
					$difference = ($hauteur_moyenne - $hauteur) -1;
					echo http_img_rien($largeur, 1, 'trait_moyen');
					echo http_img_rien($largeur, $difference, '', $tagtitle);
					echo http_img_rien($largeur,1,"trait_haut");
					if (preg_match(",-01,",$key)){ // janvier en couleur foncee
					  echo http_img_rien($largeur,$hauteur,"couleur_janvier", $tagtitle);
					} 
					else {
					  echo http_img_rien($largeur,$hauteur,"couleur_mois", $tagtitle);
					}
				}
				else if ($hauteur_moyenne < $hauteur) {
					$difference = ($hauteur - $hauteur_moyenne) -1;
					echo http_img_rien($largeur,1,"trait_haut", $tagtitle);
					if (preg_match(",-01,",$key)){ // janvier en couleur foncee
						$couleur =  'couleur_janvier';
					} 
					else {
						$couleur = 'couleur_mois';
					}
					echo http_img_rien($largeur,$difference, $couleur, $tagtitle);
					echo http_img_rien($largeur,1,'trait_moyen',$tagtitle);
					echo http_img_rien($largeur,$hauteur_moyenne, $couleur, $tagtitle);
				}
				else {
				  echo http_img_rien($largeur,1,"trait_haut", $tagtitle);
					if (preg_match(",-01,",$key)){ // janvier en couleur foncee
					  echo http_img_rien($largeur, $hauteur, "couleur_janvier", $tagtitle);
					} 
					else {
					  echo http_img_rien($largeur,$hauteur, "couleur_mois", $tagtitle);
					}
				}
			}
			echo http_img_rien($largeur,1,'trait_bas', $tagtitle);
			echo "</td>\n";
		}
		
		echo "\n<td style='background-color: black'>", http_img_rien(1, 1),"</td>";
		echo "</tr></table>";
		echo "</td>",
		  "\n<td ".http_style_background("fond-stats.gif")." valign='bottom'>", http_img_rien(3, 1, 'trait_bas'),"</td>";
		echo "\n<td>", http_img_rien(5, 1),"</td>";
		echo "\n<td valign='top'><div style='font-size:small;' class='verdana1'>";
		echo "\n<table cellpadding='0' cellspacing='0' border='0'>";
		echo "\n<tr><td style='height: 15' valign='top'>";		
		echo "<span class='arial1 spip_x-small'><b>".round($maxgraph)."</b></span>";
		echo "</td></tr>";
		echo "\n<tr><td valign='middle' $class style='$style;height: 25px'>";		
		echo round(7*($maxgraph/8));
		echo "</td></tr>";
		echo "\n<tr><td style='height: 25px' valign='middle'>";		
		echo "<span class='arial1 spip_x-small'>".round(3*($maxgraph/4))."</span>";
		echo "</td></tr>";
		echo "\n<tr><td valign='middle' $class style='$style;height: 25px'>";		
		echo round(5*($maxgraph/8));
		echo "</td></tr>";
		echo "\n<tr><td style='height: 25px' valign='middle'>";		
		echo "<span class='arial1 spip_x-small'><b>".round($maxgraph/2)."</b></span>";
		echo "</td></tr>";
		echo "\n<tr><td valign='middle' $class style='$style;height: 25px'>";		
		echo round(3*($maxgraph/8));
		echo "</td></tr>";
		echo "\n<tr><td style='height: 25px' valign='middle'>";		
		echo "<span class='arial1 spip_x-small'>".round($maxgraph/4)."</span>";
		echo "</td></tr>";
		echo "\n<tr><td valign='middle' $class style='$style;height: 25px'>";		
		echo round(1*($maxgraph/8));
		echo "</td></tr>";
		echo "\n<tr><td style='height: 10px' valign='bottom'>";		
		echo "<span class='arial1 spip_x-small'><b>0</b></span>";
		echo "</td>";

		echo "</tr></table>";
		echo "</div></td></tr></table>";
	}
	}
	/////
		
	fin_cadre_relief();

 }










////// VISITES / VISITES UTILES

 if (!$origine) {

	$total_absolu = 0;
		if ($le_pays) {
			$table = "spip_pb_visites_pays";
			$where = "pays='$le_pays'";		
		} else {
			$table = "spip_pb_visites";
			$table_ref = "spip_referers";
			$where = "0=0";
		}
	
	$result = spip_query("SELECT UNIX_TIMESTAMP(date) AS date_unix FROM $table WHERE $where ORDER BY date LIMIT 1");

	while ($row = spip_fetch_array($result)) {
		$date_premier = $row['date_unix'];
	}

	$result=spip_query("SELECT UNIX_TIMESTAMP(date) AS date_unix, visites, visites_utiles FROM $table WHERE $where AND date > DATE_SUB(NOW(),INTERVAL $aff_jours DAY) ORDER BY date");

	$date_debut = '';
	$log = array();
	while ($row = spip_fetch_array($result)) {
		$date = $row['date_unix'];
		if (!$date_debut) $date_debut = $date;
		$log[$date] = round(100 * $row['visites_utiles'] / $row['visites']);
		
	}
	$tab_moyenne = array();


	// S'il y a au moins cinq minutes de stats :-)
	if (count($log)>0) {
		// les visites du jour
		$date_today = max(array_keys($log));
		$visites_today = $log[$date_today];
		// sauf s'il n'y en a pas :
		if (time()-$date_today>3600*24) {
			$date_today = time();
			$visites_today=0;
		}
		
		// le nombre maximum
		$max = max($log);
		$nb_jours = floor(($date_today-$date_debut)/(3600*24));

		$maxgraph = maxgraph($max);
		$maxgraph = 100;
		
		
		$rapport = 200 / $maxgraph;

		if (count($log) < 420) $largeur = floor(450 / ($nb_jours+1));
		if ($largeur < 1) {
			$largeur = 1;
			$agreg = ceil(count($log) / 420);	
		} else {
			$agreg = 1;
		}
		if ($largeur > 50) $largeur = 50;

		debut_cadre_relief("statistiques-24.gif",false, "", "Pourcentage de visites utiles (plus de 2 pages vues)");
		

		$largeur_abs = 420 / $aff_jours;
		
		if ($largeur_abs > 1) {
			$inc = ceil($largeur_abs / 5);
			$aff_jours_plus = 420 / ($largeur_abs - $inc);
			$aff_jours_moins = 420 / ($largeur_abs + $inc);
		}
		
		if ($largeur_abs == 1) {
			$aff_jours_plus = 840;
			$aff_jours_moins = 210;
		}
		
		if ($largeur_abs < 1) {
			$aff_jours_plus = 420 * ((1/$largeur_abs) + 1);
			$aff_jours_moins = 420 * ((1/$largeur_abs) - 1);
		}
		
		$pour_article = $id_article ? "&id_article=$id_article" : '';
		
		if ($date_premier < $date_debut)
		  echo http_href(generer_url_ecrire("pb_statistiques","aff_jours=$aff_jours_plus$pour_article"),
				 http_img_pack('loupe-moins.gif',
					       _T('info_zoom'). '-', 
					       "style='border: 0px; vertical-align: middle;'"),
				 "&nbsp;");
		if ( (($date_today - $date_debut) / (24*3600)) > 30)
		  echo http_href(generer_url_ecrire("pb_statistiques","aff_jours=$aff_jours_moins$pour_article"), 
				 http_img_pack('loupe-plus.gif',
					       _T('info_zoom'). '+', 
					       "style='border: 0px; vertical-align: middle;'"),
				 "&nbsp;");
	

	if ($pb_charts) {
		$ligne0 = "";
		$ligne1 = "";
		$ligne2 = "";
		
		foreach ($log as $key => $value) {
					$ce_jour = date("d", $key);
					
					if ($ce_jour == "1") {
						$afficher = nom_mois(date("Y-m-d", $key));
						if (date("m", $key) == 1) $afficher = annee(date("Y-m-d", $key));
						
					} else {
						$afficher = "";
					}
		
		
			$ligne0 .= "|$afficher";
			$ligne1 .= "|".($value);
		
		}
		
		$ligne0 = "|$ligne0|\n";
		$ligne1 = "|Pourcentage de visites utiles$ligne1|\n";
		
		echo propre("<chart type=\"stacked area\">\n$ligne0$ligne1\n</chart>");
		
	} else {
	
	
		echo "\n<table cellpadding='0' cellspacing='0' border='0'><tr>",
		  "\n<td ".http_style_background("fond-stats.gif").">";
		echo "\n<table cellpadding='0' cellspacing='0' border='0' class='bottom'><tr>";
		
		echo "\n<td style='background-color: black'>", http_img_rien(1,200), "</td>";
		
		$test_agreg = $decal = $jour_prec = $val_prec = $total_loc =0;
	
		// Presentation graphique (rq: on n'affiche pas le jour courant)
		foreach ($log as $key => $value) {
			# quand on atteint aujourd'hui, stop
			if ($key == $date_today) break; 
	
			$test_agreg ++;
			
			if ($test_agreg == $agreg) {	
					
				$test_agreg = 0;
				
				if ($decal == 30) $decal = 0;
				$decal ++;
				$tab_moyenne[$decal] = $value;
				// Inserer des jours vides si pas d'entrees	
				if ($jour_prec > 0) {
						$ecart = floor(($key-$jour_prec)/((3600*24)*$agreg)-1);
			
						for ($i=0; $i < $ecart; $i++){
							if ($decal == 30) $decal = 0;
							$decal ++;
							$tab_moyenne[$decal] = $value;
		
							$ce_jour=date("Y-m-d", $jour_prec+(3600*24*($i+1)));
							$jour = nom_jour($ce_jour).' '.affdate_jourcourt($ce_jour);
		
							reset($tab_moyenne);
							$moyenne = 0;
							while (list(,$val_tab) = each($tab_moyenne))
								$moyenne += $val_tab;
							$moyenne = $moyenne / count($tab_moyenne);
			
							$hauteur_moyenne = round(($moyenne) * $rapport) - 1;
							echo "\n<td style='width: ${largeur}px'>";
							$difference = ($hauteur_moyenne) -1;
							$moyenne = round($moyenne,2); // Pour affichage harmonieux
							$tagtitle= attribut_html(supprimer_tags("$jour | "
							._T('info_visites')." | "
							._T('info_moyenne')." $moyenne"));
							if ($difference > 0) {	
							  echo http_img_rien($largeur,1, 'trait_moyen', $tagtitle);
							  echo http_img_rien($largeur, $hauteur_moyenne, '', $tagtitle);
							}
							echo 
								http_img_rien($largeur,1,'trait_bas', $tagtitle);
							echo "</td>";
						}
					}
		
					$ce_jour=date("Y-m-d", $key);
					$jour = nom_jour($ce_jour).' '.affdate_jourcourt($ce_jour);
		
					$total_loc = $total_loc + $value;
					reset($tab_moyenne);
		
					$moyenne = 0;
					while (list(,$val_tab) = each($tab_moyenne))
						$moyenne += $val_tab;
					$moyenne = $moyenne / count($tab_moyenne);
				
					$hauteur_moyenne = round($moyenne * $rapport) - 1;
					$hauteur = round($value * $rapport) - 1;
					$moyenne = round($moyenne,2); // Pour affichage harmonieux
					echo "\n<td style='width: ${largeur}px'>";
		
					$tagtitle= attribut_html(supprimer_tags("$jour | "
					._T('info_visites')." ".$value));
		
					if ($hauteur > 0){
						if ($hauteur_moyenne > $hauteur) {
							$difference = ($hauteur_moyenne - $hauteur) -1;
							echo http_img_rien($largeur, 1,'trait_moyen',$tagtitle);
							echo http_img_rien($largeur, $difference, '', $tagtitle);
							echo http_img_rien($largeur,1, "trait_haut", $tagtitle);
							if (date("w",$key) == "0") // Dimanche en couleur foncee
							  echo http_img_rien($largeur, $hauteur, "couleur_dimanche", $tagtitle);
							else
							  echo http_img_rien($largeur,$hauteur, "couleur_jour", $tagtitle);
						} else if ($hauteur_moyenne < $hauteur) {
							$difference = ($hauteur - $hauteur_moyenne) -1;
							echo http_img_rien($largeur,1,"trait_haut", $tagtitle);
							if (date("w",$key) == "0") // Dimanche en couleur foncee
								$couleur =  'couleur_dimanche';
							else
								$couleur = 'couleur_jour';
							echo http_img_rien($largeur, $difference, $couleur, $tagtitle);
							echo http_img_rien($largeur,1,"trait_moyen", $tagtitle);
							echo http_img_rien($largeur, $hauteur_moyenne, $couleur, $tagtitle);
						} else {
						  echo http_img_rien($largeur, 1, "trait_haut", $tagtitle);
							if (date("w",$key) == "0") // Dimanche en couleur foncee
							  echo http_img_rien($largeur, $hauteur, "couleur_dimanche", $tagtitle);
							else
							  echo http_img_rien($largeur,$hauteur, "couleur_jour", $tagtitle);
						}
					}
					echo http_img_rien($largeur, 1, 'trait_bas', $tagtitle);
					echo "</td>\n";
				
					$jour_prec = $key;
					$val_prec = $value;
				}
				}
		
				// Dernier jour
				
				$hauteur = round($visites_today * $rapport)	- 1;
				$total_absolu = $total_absolu + $visites_today;
				echo "\n<td style='width: ${largeur}px'>";
				// Afficher la barre tout en haut
				$tagtitle= attribut_html(supprimer_tags(_T('info_aujourdhui')." $visites_today "));
				// afficher la barre previsionnelle
				if ($hauteur>0)
					echo http_img_rien($largeur, $hauteur, 'couleur_realise', $tagtitle);
				// et afficher la ligne de base
				echo http_img_rien($largeur, 1, 'trait_bas');
				echo "</td>";
	
	
				echo "\n<td style='background-color: black'>",http_img_rien(1, 1),"</td>";
				echo "</tr></table>";
				echo "</td>",
				  "\n<td ".http_style_background("fond-stats.gif")."  valign='bottom'>", http_img_rien(3, 1, 'trait_bas'),"</td>";
				echo "\n<td>", http_img_rien(5, 1),"</td>";
				echo "\n<td valign='top'><div style='font-size:small;' class='verdana1'>";
				echo "\n<table cellpadding='0' cellspacing='0' border='0'>";
				echo "\n<tr><td style='height: 15' valign='top'>";		
				echo "<span class='arial1 spip_x-small'><b>".round($maxgraph)."%</b></span>";
				echo "</td></tr>";
				echo "\n<tr><td valign='middle' $class style='$style;height: 25px'>";		
				echo round(7*($maxgraph/8));
				echo "%</td></tr>";
				echo "\n<tr><td style='height: 25px' valign='middle'>";		
				echo "<span class='arial1 spip_x-small'>".round(3*($maxgraph/4))."%</span>";
				echo "</td></tr>";
				echo "\n<tr><td valign='middle' $class style='$style;height: 25px'>";		
				echo round(5*($maxgraph/8));
				echo "%</td></tr>";
				echo "\n<tr><td style='height: 25px' valign='middle'>";		
				echo "<span class='arial1 spip_x-small'><b>".round($maxgraph/2)."%</b></span>";
				echo "</td></tr>";
				echo "\n<tr><td valign='middle' $class style='$style;height: 25px'>";		
				echo round(3*($maxgraph/8));
				echo "%</td></tr>";
				echo "\n<tr><td style='height: 25px' valign='middle'>";		
				echo "<span class='arial1 spip_x-small'>".round($maxgraph/4)."%</span>";
				echo "</td></tr>";
				echo "\n<tr><td valign='middle' $class style='$style;height: 25px'>";		
				echo round(1*($maxgraph/8));
				echo "%</td></tr>";
				echo "\n<tr><td style='height: 10px' valign='bottom'>";		
				echo "<span class='arial1 spip_x-small'><b>0</b></span>";
				echo "</td>";
				echo "</tr></table>";
				echo "</div></td>";
				echo "</tr></table>";
				
				echo "<div style='position: relative; height: 15px'>";
				$gauche_prec = -50;
				for ($jour = $date_debut; $jour <= $date_today; $jour = $jour + (24*3600)) {
					$ce_jour = date("d", $jour);
					
					if ($ce_jour == "1") {
						$afficher = nom_mois(date("Y-m-d", $jour));
						if (date("m", $jour) == 1) $afficher = "<b>".annee(date("Y-m-d", $jour))."</b>";
						
					
						$gauche = floor($jour - $date_debut) * $largeur / ((24*3600)*$agreg);
						
						if ($gauche - $gauche_prec >= 40 OR date("m", $jour) == 1) {									
							echo "<div class='arial0' style='border-$spip_lang_left: 1px solid black; padding-$spip_lang_left: 2px; padding-top: 3px; position: absolute; $spip_lang_left: ".$gauche."px; top: -1px;'>".$afficher."</div>";
							$gauche_prec = $gauche;
						}
					}
				}
				echo "</div>";
	
			//}
		}
		// cette ligne donne la moyenne depuis le debut
		// (desactive au profit de la moeynne "glissante")
		# $moyenne =  round($total_absolu / ((date("U")-$date_premier)/(3600*24)));

		echo "<span class='arial1 spip_x-small'>"._T('texte_pb_statistiques')."</span>";
		echo "<br /><table cellpadding='0' cellspacing='0' border='0' width='100%'><tr style='width:100%;'>";
		echo "\n<td valign='top' style='width: 33%; ' class='verdana1'>", _T('info_maximum')." ".$max, "<br />"._T('info_moyenne')." ".round($moyenne), "</td>";
		echo "\n<td valign='top' style='width: 33%; ' class='verdana1'>";
		echo '<a href="' . generer_url_ecrire("statistiques_referers","").'" title="'._T('titre_liens_entrants').'">'._T('info_aujourdhui').'</a> '.$visites_today;
		if ($val_prec > 0) echo '<br /><a href="' . generer_url_ecrire("statistiques_referers","jour=veille").'"  title="'._T('titre_liens_entrants').'">'._T('info_hier').'</a> '.$val_prec;
		if ($id_article) echo "<br />"._T('info_popularite_5').' '.$val_popularite;

		echo "</td>";
		echo "\n<td valign='top' style='width: 33%; ' class='verdana1'>";
		echo "<b>"._T('info_total')." ".$total_absolu."</b>";
		
		if ($id_article) {
			if ($classement[$id_article] > 0) {
				if ($classement[$id_article] == 1)
				      $ch = _T('info_classement_1', array('liste' => $liste));
				else
				      $ch = _T('info_classement_2', array('liste' => $liste));
				echo "<br />".$classement[$id_article].$ch;
			}
		} else {
		  echo "<span class='spip_x-small'><br />"._T('info_popularite_2')." ", ceil($GLOBALS['meta']['popularite_total']), "</span>";
		}
		echo "</td></tr></table>";	
	}		
	
	if (count($log) > 60) {
		echo "<br />";
		echo "<span class='verdana1 spip_small'><b>"._T('info_visites_par_mois')."</b></span>";

		///////// Affichage par mois
		$result=spip_query("SELECT FROM_UNIXTIME(UNIX_TIMESTAMP(date),'%Y-%m') AS date_unix, SUM(visites) AS total_visites, SUM(visites_utiles) AS total_visites_utiles  FROM $table WHERE $where AND date > DATE_SUB(NOW(),INTERVAL 2700 DAY) GROUP BY date_unix ORDER BY date");

		$entrees = array();
		$entrees_utiles = array();
		
		$i = 0;
		while ($row = spip_fetch_array($result)) {
			$date = $row['date_unix'];
			$visites = round(100 * $row["total_visites_utiles"] / $row['total_visites']);
			$i++;
			$entrees["$date"] = $visites;
		}
		// Pour la derniere date, rajouter les visites du jour sauf si premier jour du mois
		if (date("d",time()) > 1) {
			$entrees["$date"] += $visites_today;
		} else { // Premier jour du mois : le rajouter dans le tableau des date (car il n'etait pas dans le resultat de la requete SQL precedente)
			$date = date("Y-m",time());
			$entrees["$date"] = $visites_today;
		}
		
		if (count($entrees)>0){
		
			$max = max($entrees);
			$maxgraph = maxgraph($max);
			$maxgraph = 100;
			
			$rapport = 200/$maxgraph;

			$largeur = floor(420 / (count($entrees)));
			if ($largeur < 1) $largeur = 1;
			if ($largeur > 50) $largeur = 50;
		}
		
		echo "\n<table cellpadding='0' cellspacing='0' border='0'><tr>",
		  "\n<td ".http_style_background("fond-stats.gif").">";
		echo "\n<table cellpadding='0' cellspacing='0' border='0' class='bottom'><tr>";
		echo "\n<td class='trait_bas'>", http_img_rien(1, 200),"</td>";
		// Presentation graphique
		$decal = 0;
		$tab_moyenne = "";
			
		while (list($key, $value) = each($entrees)) {
			
			$mois = affdate_mois_annee($key);

			if ($decal == 30) $decal = 0;
			$decal ++;
			$tab_moyenne[$decal] = $value;
			
			$total_loc = $total_loc + $value;
			reset($tab_moyenne);
	
			$moyenne = 0;
			while (list(,$val_tab) = each($tab_moyenne))
				$moyenne += $val_tab;
			$moyenne = $moyenne / count($tab_moyenne);
			
			$hauteur_moyenne = round($moyenne * $rapport) - 1;
			$hauteur = round($value * $rapport) - 1;
			echo "\n<td style='width: ${largeur}px'>";

			$tagtitle= attribut_html(supprimer_tags("$mois | "
			._T('info_visites')." ".$value));

			if ($hauteur > 0){
				if ($hauteur_moyenne > $hauteur) {
					$difference = ($hauteur_moyenne - $hauteur) -1;
					echo http_img_rien($largeur, 1, 'trait_moyen');
					echo http_img_rien($largeur, $difference, '', $tagtitle);
					echo http_img_rien($largeur,1,"trait_haut");
					if (preg_match(",-01,",$key)){ // janvier en couleur foncee
					  echo http_img_rien($largeur,$hauteur,"couleur_janvier", $tagtitle);
					} 
					else {
					  echo http_img_rien($largeur,$hauteur,"couleur_mois", $tagtitle);
					}
				}
				else if ($hauteur_moyenne < $hauteur) {
					$difference = ($hauteur - $hauteur_moyenne) -1;
					echo http_img_rien($largeur,1,"trait_haut", $tagtitle);
					if (preg_match(",-01,",$key)){ // janvier en couleur foncee
						$couleur =  'couleur_janvier';
					} 
					else {
						$couleur = 'couleur_mois';
					}
					echo http_img_rien($largeur,$difference, $couleur, $tagtitle);
					echo http_img_rien($largeur,1,'trait_moyen',$tagtitle);
					echo http_img_rien($largeur,$hauteur_moyenne, $couleur, $tagtitle);
				}
				else {
				  echo http_img_rien($largeur,1,"trait_haut", $tagtitle);
					if (preg_match(",-01,",$key)){ // janvier en couleur foncee
					  echo http_img_rien($largeur, $hauteur, "couleur_janvier", $tagtitle);
					} 
					else {
					  echo http_img_rien($largeur,$hauteur, "couleur_mois", $tagtitle);
					}
				}
			}
			echo http_img_rien($largeur,1,'trait_bas', $tagtitle);
			echo "</td>\n";
		}
		
		echo "\n<td style='background-color: black'>", http_img_rien(1, 1),"</td>";
		echo "</tr></table>";
		echo "</td>",
		  "\n<td ".http_style_background("fond-stats.gif")." valign='bottom'>", http_img_rien(3, 1, 'trait_bas'),"</td>";
		echo "\n<td>", http_img_rien(5, 1),"</td>";
		echo "\n<td valign='top'><div style='font-size:small;' class='verdana1'>";
		echo "\n<table cellpadding='0' cellspacing='0' border='0'>";
		echo "\n<tr><td style='height: 15' valign='top'>";		
		echo "<span class='arial1 spip_x-small'><b>".round($maxgraph)."</b></span>";
		echo "</td></tr>";
		echo "\n<tr><td valign='middle' $class style='$style;height: 25px'>";		
		echo round(7*($maxgraph/8));
		echo "</td></tr>";
		echo "\n<tr><td style='height: 25px' valign='middle'>";		
		echo "<span class='arial1 spip_x-small'>".round(3*($maxgraph/4))."</span>";
		echo "</td></tr>";
		echo "\n<tr><td valign='middle' $class style='$style;height: 25px'>";		
		echo round(5*($maxgraph/8));
		echo "</td></tr>";
		echo "\n<tr><td style='height: 25px' valign='middle'>";		
		echo "<span class='arial1 spip_x-small'><b>".round($maxgraph/2)."</b></span>";
		echo "</td></tr>";
		echo "\n<tr><td valign='middle' $class style='$style;height: 25px'>";		
		echo round(3*($maxgraph/8));
		echo "</td></tr>";
		echo "\n<tr><td style='height: 25px' valign='middle'>";		
		echo "<span class='arial1 spip_x-small'>".round($maxgraph/4)."</span>";
		echo "</td></tr>";
		echo "\n<tr><td valign='middle' $class style='$style;height: 25px'>";		
		echo round(1*($maxgraph/8));
		echo "</td></tr>";
		echo "\n<tr><td style='height: 10px' valign='bottom'>";		
		echo "<span class='arial1 spip_x-small'><b>0</b></span>";
		echo "</td>";

		echo "</tr></table>";
		echo "</div></td></tr></table>";
	}
	
	/////
		
	fin_cadre_relief();

 }








////// PAGES VUES

 if (!$origine) {

		$total_absolu = 0;

		if ($le_pays) {
			$table = "spip_pb_visites_pays";
			$where = "pays='$le_pays'";		
		} else {
			$table = "spip_pb_visites";
			$table_ref = "spip_referers";
			$where = "0=0";
		}
	
	$result = spip_query("SELECT UNIX_TIMESTAMP(date) AS date_unix FROM $table WHERE $where ORDER BY date LIMIT 1");

	while ($row = spip_fetch_array($result)) {
		$date_premier = $row['date_unix'];
	}

	$result=spip_query("SELECT UNIX_TIMESTAMP(date) AS date_unix, pages_vues, pages_vues_utiles FROM $table WHERE $where AND date > DATE_SUB(NOW(),INTERVAL $aff_jours DAY) ORDER BY date");

	$date_debut = '';
	$log = array();
	while ($row = spip_fetch_array($result)) {
		$date = $row['date_unix'];
		if (!$date_debut) $date_debut = $date;
		$log[$date] = $row['pages_vues'];
		$log_utiles[$date] = $row['pages_vues_utiles'];
	}
	$tab_moyenne = array();


	// S'il y a au moins cinq minutes de stats :-)
	if (count($log)>0) {
		// les visites du jour
		$date_today = max(array_keys($log));
		$visites_today = $log[$date_today];
		// sauf s'il n'y en a pas :
		if (time()-$date_today>3600*24) {
			$date_today = time();
			$visites_today=0;
		}
		
		// le nombre maximum
		$max = max($log);
		$nb_jours = floor(($date_today-$date_debut)/(3600*24));

		$maxgraph = maxgraph($max);
		$rapport = 200 / $maxgraph;

		if (count($log) < 420) $largeur = floor(450 / ($nb_jours+1));
		if ($largeur < 1) {
			$largeur = 1;
			$agreg = ceil(count($log) / 420);	
		} else {
			$agreg = 1;
		}
		if ($largeur > 50) $largeur = 50;

		debut_cadre_relief("statistiques-24.gif",false, "", "&Eacute;volution des pages vues et pages vues utiles (au moins deux pages vues)");
		

		$largeur_abs = 420 / $aff_jours;
		
		if ($largeur_abs > 1) {
			$inc = ceil($largeur_abs / 5);
			$aff_jours_plus = 420 / ($largeur_abs - $inc);
			$aff_jours_moins = 420 / ($largeur_abs + $inc);
		}
		
		if ($largeur_abs == 1) {
			$aff_jours_plus = 840;
			$aff_jours_moins = 210;
		}
		
		if ($largeur_abs < 1) {
			$aff_jours_plus = 420 * ((1/$largeur_abs) + 1);
			$aff_jours_moins = 420 * ((1/$largeur_abs) - 1);
		}
		
		$pour_article = $id_article ? "&id_article=$id_article" : '';
		
		if ($date_premier < $date_debut)
		  echo http_href(generer_url_ecrire("pb_statistiques","aff_jours=$aff_jours_plus$pour_article"),
				 http_img_pack('loupe-moins.gif',
					       _T('info_zoom'). '-', 
					       "style='border: 0px; vertical-align: middle;'"),
				 "&nbsp;");
		if ( (($date_today - $date_debut) / (24*3600)) > 30)
		  echo http_href(generer_url_ecrire("pb_statistiques","aff_jours=$aff_jours_moins$pour_article"), 
				 http_img_pack('loupe-plus.gif',
					       _T('info_zoom'). '+', 
					       "style='border: 0px; vertical-align: middle;'"),
				 "&nbsp;");
	
	
	echo "\n<table cellpadding='0' cellspacing='0' border='0'><tr>",
	  "\n<td ".http_style_background("fond-stats.gif").">";
	echo "\n<table cellpadding='0' cellspacing='0' border='0' class='bottom'><tr>";
	
	echo "\n<td style='background-color: black'>", http_img_rien(1,200), "</td>";
	
	$test_agreg = $decal = $jour_prec = $val_prec = $total_loc =0;

	// Presentation graphique (rq: on n'affiche pas le jour courant)
	foreach ($log as $key => $value) {
		# quand on atteint aujourd'hui, stop
		if ($key == $date_today) break; 

		$test_agreg ++;
		
		if ($test_agreg == $agreg) {	
				
			$test_agreg = 0;
			
			if ($decal == 30) $decal = 0;
			$decal ++;
			$tab_moyenne[$decal] = $value;
			// Inserer des jours vides si pas d'entrees	
			if ($jour_prec > 0) {
					$ecart = floor(($key-$jour_prec)/((3600*24)*$agreg)-1);
		
					for ($i=0; $i < $ecart; $i++){
						if ($decal == 30) $decal = 0;
						$decal ++;
						$tab_moyenne[$decal] = $value;
	
						$ce_jour=date("Y-m-d", $jour_prec+(3600*24*($i+1)));
						$jour = nom_jour($ce_jour).' '.affdate_jourcourt($ce_jour);
	
						reset($tab_moyenne);
						$moyenne = 0;
						while (list(,$val_tab) = each($tab_moyenne))
							$moyenne += $val_tab;
						$moyenne = $moyenne / count($tab_moyenne);
		
						$hauteur_moyenne = round(($moyenne) * $rapport) - 1;
						echo "\n<td style='width: ${largeur}px'>";
						$difference = ($hauteur_moyenne) -1;
						$moyenne = round($moyenne,2); // Pour affichage harmonieux
						$tagtitle= attribut_html(supprimer_tags("$jour | "
						._T('info_visites')." | "
						._T('info_moyenne')." $moyenne"));
						if ($difference > 0) {	
						  echo http_img_rien($largeur,1, 'trait_moyen', $tagtitle);
						  echo http_img_rien($largeur, $hauteur_moyenne, '', $tagtitle);
						}
						echo 
						    http_img_rien($largeur,1,'trait_bas', $tagtitle);
						echo "</td>";
					}
				}
	
				$ce_jour=date("Y-m-d", $key);
				$jour = nom_jour($ce_jour).' '.affdate_jourcourt($ce_jour);
	
				$total_loc = $total_loc + $value;
				reset($tab_moyenne);
	
				$moyenne = 0;
				while (list(,$val_tab) = each($tab_moyenne))
					$moyenne += $val_tab;
				$moyenne = $moyenne / count($tab_moyenne);
			
				$hauteur_moyenne = round($log_utiles[$key] * $rapport) - 1;
				$hauteur = round($value * $rapport) - 1;
				$moyenne = round($moyenne,2); // Pour affichage harmonieux
				echo "\n<td style='width: ${largeur}px'>";
	
				$tagtitle= attribut_html(supprimer_tags("$jour | "
				._T('info_visites')." ".$value));
	
				if ($hauteur > 0){
					if ($hauteur_moyenne > $hauteur) {
						$difference = ($hauteur_moyenne - $hauteur) -1;
						echo http_img_rien($largeur, 1,'trait_moyen',$tagtitle);
						echo http_img_rien($largeur, $difference, '', $tagtitle);
						echo http_img_rien($largeur,1, "trait_haut", $tagtitle);
						if (date("w",$key) == "0") // Dimanche en couleur foncee
						  echo http_img_rien($largeur, $hauteur, "couleur_dimanche", $tagtitle);
						else
						  echo http_img_rien($largeur,$hauteur, "couleur_jour", $tagtitle);
					} else if ($hauteur_moyenne < $hauteur) {
						$difference = ($hauteur - $hauteur_moyenne) -1;
						echo http_img_rien($largeur,1,"trait_haut", $tagtitle);
						if (date("w",$key) == "0") // Dimanche en couleur foncee
							$couleur =  'couleur_dimanche';
						else
							$couleur = 'couleur_jour';
						echo http_img_rien($largeur, $difference, $couleur, $tagtitle);
						echo http_img_rien($largeur,1,"trait_moyen", $tagtitle);
						echo http_img_rien($largeur, $hauteur_moyenne, $couleur, $tagtitle);
					} else {
					  echo http_img_rien($largeur, 1, "trait_haut", $tagtitle);
						if (date("w",$key) == "0") // Dimanche en couleur foncee
						  echo http_img_rien($largeur, $hauteur, "couleur_dimanche", $tagtitle);
						else
						  echo http_img_rien($largeur,$hauteur, "couleur_jour", $tagtitle);
					}
				}
				echo http_img_rien($largeur, 1, 'trait_bas', $tagtitle);
				echo "</td>\n";
			
				$jour_prec = $key;
				$val_prec = $value;
			}
			}
	
			// Dernier jour
			$hauteur = round($visites_today * $rapport)	- 1;
			$total_absolu = $total_absolu + $visites_today;
			echo "\n<td style='width: ${largeur}px'>";
			// prevision de visites jusqu'a minuit
			// basee sur la moyenne (site) ou popularite (article)
			if (! $id_article) $val_popularite = $moyenne;
			$prevision = (1 - (date("H")*60 + date("i"))/(24*60)) * $val_popularite;
			$hauteurprevision = ceil($prevision * $rapport);
			// Afficher la barre tout en haut
			if ($hauteur+$hauteurprevision>0)
				echo http_img_rien($largeur, 1, "trait_haut");
			// preparer le texte de survol (prevision)
			$tagtitle= attribut_html(supprimer_tags(_T('info_aujourdhui')." $visites_today &rarr; ".(round($prevision,0)+$visites_today)));
			// afficher la barre previsionnelle
			if ($hauteurprevision>0)
				echo http_img_rien($largeur, $hauteurprevision,'couleur_prevision', $tagtitle);
				// afficher la barre deja realisee
			if ($hauteur>0)
				echo http_img_rien($largeur, $hauteur, 'couleur_realise', $tagtitle);
			// et afficher la ligne de base
			echo http_img_rien($largeur, 1, 'trait_bas');
			echo "</td>";


			echo "\n<td style='background-color: black'>",http_img_rien(1, 1),"</td>";
			echo "</tr></table>";
			echo "</td>",
			  "\n<td ".http_style_background("fond-stats.gif")."  valign='bottom'>", http_img_rien(3, 1, 'trait_bas'),"</td>";
			echo "\n<td>", http_img_rien(5, 1),"</td>";
			echo "\n<td valign='top'><div style='font-size:small;' class='verdana1'>";
			echo "\n<table cellpadding='0' cellspacing='0' border='0'>";
			echo "\n<tr><td style='height: 15' valign='top'>";		
			echo "<span class='arial1 spip_x-small'><b>".round($maxgraph)."</b></span>";
			echo "</td></tr>";
			echo "\n<tr><td valign='middle' $class style='$style;height: 25px'>";		
			echo round(7*($maxgraph/8));
			echo "</td></tr>";
			echo "\n<tr><td style='height: 25px' valign='middle'>";		
			echo "<span class='arial1 spip_x-small'>".round(3*($maxgraph/4))."</span>";
			echo "</td></tr>";
			echo "\n<tr><td valign='middle' $class style='$style;height: 25px'>";		
			echo round(5*($maxgraph/8));
			echo "</td></tr>";
			echo "\n<tr><td style='height: 25px' valign='middle'>";		
			echo "<span class='arial1 spip_x-small'><b>".round($maxgraph/2)."</b></span>";
			echo "</td></tr>";
			echo "\n<tr><td valign='middle' $class style='$style;height: 25px'>";		
			echo round(3*($maxgraph/8));
			echo "</td></tr>";
			echo "\n<tr><td style='height: 25px' valign='middle'>";		
			echo "<span class='arial1 spip_x-small'>".round($maxgraph/4)."</span>";
			echo "</td></tr>";
			echo "\n<tr><td valign='middle' $class style='$style;height: 25px'>";		
			echo round(1*($maxgraph/8));
			echo "</td></tr>";
			echo "\n<tr><td style='height: 10px' valign='bottom'>";		
			echo "<span class='arial1 spip_x-small'><b>0</b></span>";
			echo "</td>";
			echo "</tr></table>";
			echo "</div></td>";
			echo "</tr></table>";
			
			echo "<div style='position: relative; height: 15px'>";
			$gauche_prec = -50;
			for ($jour = $date_debut; $jour <= $date_today; $jour = $jour + (24*3600)) {
				$ce_jour = date("d", $jour);
				
				if ($ce_jour == "1") {
					$afficher = nom_mois(date("Y-m-d", $jour));
					if (date("m", $jour) == 1) $afficher = "<b>".annee(date("Y-m-d", $jour))."</b>";
					
				
					$gauche = floor($jour - $date_debut) * $largeur / ((24*3600)*$agreg);
					
					if ($gauche - $gauche_prec >= 40 OR date("m", $jour) == 1) {									
						echo "<div class='arial0' style='border-$spip_lang_left: 1px solid black; padding-$spip_lang_left: 2px; padding-top: 3px; position: absolute; $spip_lang_left: ".$gauche."px; top: -1px;'>".$afficher."</div>";
						$gauche_prec = $gauche;
					}
				}
			}
			echo "</div>";

		//}

		// cette ligne donne la moyenne depuis le debut
		// (desactive au profit de la moeynne "glissante")
		# $moyenne =  round($total_absolu / ((date("U")-$date_premier)/(3600*24)));

		echo "<span class='arial1 spip_x-small'>"._T('texte_pb_statistiques')."</span>";
		echo "<br /><table cellpadding='0' cellspacing='0' border='0' width='100%'><tr style='width:100%;'>";
		echo "\n<td valign='top' style='width: 33%; ' class='verdana1'>", _T('info_maximum')." ".$max, "<br />"._T('info_moyenne')." ".round($moyenne), "</td>";
		echo "\n<td valign='top' style='width: 33%; ' class='verdana1'>";
		echo '<a href="' . generer_url_ecrire("statistiques_referers","").'" title="'._T('titre_liens_entrants').'">'._T('info_aujourdhui').'</a> '.$visites_today;
		if ($val_prec > 0) echo '<br /><a href="' . generer_url_ecrire("statistiques_referers","jour=veille").'"  title="'._T('titre_liens_entrants').'">'._T('info_hier').'</a> '.$val_prec;
		if ($id_article) echo "<br />"._T('info_popularite_5').' '.$val_popularite;

		echo "</td>";
		echo "\n<td valign='top' style='width: 33%; ' class='verdana1'>";
		echo "<b>"._T('info_total')." ".$total_absolu."</b>";
		
		if ($id_article) {
			if ($classement[$id_article] > 0) {
				if ($classement[$id_article] == 1)
				      $ch = _T('info_classement_1', array('liste' => $liste));
				else
				      $ch = _T('info_classement_2', array('liste' => $liste));
				echo "<br />".$classement[$id_article].$ch;
			}
		} else {
		  echo "<span class='spip_x-small'><br />"._T('info_popularite_2')." ", ceil($GLOBALS['meta']['popularite_total']), "</span>";
		}
		echo "</td></tr></table>";	
	}		
	
	if (count($log) > 60) {
		echo "<br />";
		echo "<span class='verdana1 spip_small'><b>"._T('info_visites_par_mois')."</b></span>";

		///////// Affichage par mois
		$result=spip_query("SELECT FROM_UNIXTIME(UNIX_TIMESTAMP(date),'%Y-%m') AS date_unix, SUM(pages_vues) AS total_visites, SUM(pages_vues_utiles) AS total_visites_utiles  FROM $table WHERE $where AND date > DATE_SUB(NOW(),INTERVAL 2700 DAY) GROUP BY date_unix ORDER BY date");

		$entrees = array();
		$entrees_utiles = array();
		
		$i = 0;
		while ($row = spip_fetch_array($result)) {
			$date = $row['date_unix'];
			$visites = $row['total_visites'];
			$i++;
			$entrees["$date"] = $visites;
			$entrees_utiles["$date"] = $row["total_visites_utiles"];
		}
		// Pour la derniere date, rajouter les visites du jour sauf si premier jour du mois
		if (date("d",time()) > 1) {
			$entrees["$date"] += $visites_today;
		} else { // Premier jour du mois : le rajouter dans le tableau des date (car il n'etait pas dans le resultat de la requete SQL precedente)
			$date = date("Y-m",time());
			$entrees["$date"] = $visites_today;
		}
		
		if (count($entrees)>0){
		
			$max = max($entrees);
			$maxgraph = maxgraph($max);
			$rapport = 200/$maxgraph;

			$largeur = floor(420 / (count($entrees)));
			if ($largeur < 1) $largeur = 1;
			if ($largeur > 50) $largeur = 50;
		}
		
		echo "\n<table cellpadding='0' cellspacing='0' border='0'><tr>",
		  "\n<td ".http_style_background("fond-stats.gif").">";
		echo "\n<table cellpadding='0' cellspacing='0' border='0' class='bottom'><tr>";
		echo "\n<td class='trait_bas'>", http_img_rien(1, 200),"</td>";
		// Presentation graphique
		$decal = 0;
		$tab_moyenne = "";
			
		while (list($key, $value) = each($entrees)) {
			
			$mois = affdate_mois_annee($key);

			if ($decal == 30) $decal = 0;
			$decal ++;
			$tab_moyenne[$decal] = $value;
			
			$total_loc = $total_loc + $value;
			reset($tab_moyenne);
	
			$moyenne = 0;
			while (list(,$val_tab) = each($tab_moyenne))
				$moyenne += $val_tab;
			$moyenne = $moyenne / count($tab_moyenne);
			
			$hauteur_moyenne = round($entrees_utiles["$key"] * $rapport) - 1;
			$hauteur = round($value * $rapport) - 1;
			echo "\n<td style='width: ${largeur}px'>";

			$tagtitle= attribut_html(supprimer_tags("$mois | "
			._T('info_visites')." ".$value));

			if ($hauteur > 0){
				if ($hauteur_moyenne > $hauteur) {
					$difference = ($hauteur_moyenne - $hauteur) -1;
					echo http_img_rien($largeur, 1, 'trait_moyen');
					echo http_img_rien($largeur, $difference, '', $tagtitle);
					echo http_img_rien($largeur,1,"trait_haut");
					if (preg_match(",-01,",$key)){ // janvier en couleur foncee
					  echo http_img_rien($largeur,$hauteur,"couleur_janvier", $tagtitle);
					} 
					else {
					  echo http_img_rien($largeur,$hauteur,"couleur_mois", $tagtitle);
					}
				}
				else if ($hauteur_moyenne < $hauteur) {
					$difference = ($hauteur - $hauteur_moyenne) -1;
					echo http_img_rien($largeur,1,"trait_haut", $tagtitle);
					if (preg_match(",-01,",$key)){ // janvier en couleur foncee
						$couleur =  'couleur_janvier';
					} 
					else {
						$couleur = 'couleur_mois';
					}
					echo http_img_rien($largeur,$difference, $couleur, $tagtitle);
					echo http_img_rien($largeur,1,'trait_moyen',$tagtitle);
					echo http_img_rien($largeur,$hauteur_moyenne, $couleur, $tagtitle);
				}
				else {
				  echo http_img_rien($largeur,1,"trait_haut", $tagtitle);
					if (preg_match(",-01,",$key)){ // janvier en couleur foncee
					  echo http_img_rien($largeur, $hauteur, "couleur_janvier", $tagtitle);
					} 
					else {
					  echo http_img_rien($largeur,$hauteur, "couleur_mois", $tagtitle);
					}
				}
			}
			echo http_img_rien($largeur,1,'trait_bas', $tagtitle);
			echo "</td>\n";
		}
		
		echo "\n<td style='background-color: black'>", http_img_rien(1, 1),"</td>";
		echo "</tr></table>";
		echo "</td>",
		  "\n<td ".http_style_background("fond-stats.gif")." valign='bottom'>", http_img_rien(3, 1, 'trait_bas'),"</td>";
		echo "\n<td>", http_img_rien(5, 1),"</td>";
		echo "\n<td valign='top'><div style='font-size:small;' class='verdana1'>";
		echo "\n<table cellpadding='0' cellspacing='0' border='0'>";
		echo "\n<tr><td style='height: 15' valign='top'>";		
		echo "<span class='arial1 spip_x-small'><b>".round($maxgraph)."</b></span>";
		echo "</td></tr>";
		echo "\n<tr><td valign='middle' $class style='$style;height: 25px'>";		
		echo round(7*($maxgraph/8));
		echo "</td></tr>";
		echo "\n<tr><td style='height: 25px' valign='middle'>";		
		echo "<span class='arial1 spip_x-small'>".round(3*($maxgraph/4))."</span>";
		echo "</td></tr>";
		echo "\n<tr><td valign='middle' $class style='$style;height: 25px'>";		
		echo round(5*($maxgraph/8));
		echo "</td></tr>";
		echo "\n<tr><td style='height: 25px' valign='middle'>";		
		echo "<span class='arial1 spip_x-small'><b>".round($maxgraph/2)."</b></span>";
		echo "</td></tr>";
		echo "\n<tr><td valign='middle' $class style='$style;height: 25px'>";		
		echo round(3*($maxgraph/8));
		echo "</td></tr>";
		echo "\n<tr><td style='height: 25px' valign='middle'>";		
		echo "<span class='arial1 spip_x-small'>".round($maxgraph/4)."</span>";
		echo "</td></tr>";
		echo "\n<tr><td valign='middle' $class style='$style;height: 25px'>";		
		echo round(1*($maxgraph/8));
		echo "</td></tr>";
		echo "\n<tr><td style='height: 10px' valign='bottom'>";		
		echo "<span class='arial1 spip_x-small'><b>0</b></span>";
		echo "</td>";

		echo "</tr></table>";
		echo "</div></td></tr></table>";
	}
	
	/////
		
	fin_cadre_relief();

 }







////// PAGES VUES PAR VISITE

 if (!$origine) {

		$total_absolu = 0;
		if ($le_pays) {
			$table = "spip_pb_visites_pays";
			$where = "pays='$le_pays'";		
		} else {
			$table = "spip_pb_visites";
			$table_ref = "spip_referers";
			$where = "0=0";
		}
	
	$result = spip_query("SELECT UNIX_TIMESTAMP(date) AS date_unix FROM $table WHERE $where ORDER BY date LIMIT 1");

	while ($row = spip_fetch_array($result)) {
		$date_premier = $row['date_unix'];
	}

	$result=spip_query("SELECT UNIX_TIMESTAMP(date) AS date_unix, visites, visites_utiles, pages_vues, pages_vues_utiles FROM $table WHERE $where AND date > DATE_SUB(NOW(),INTERVAL $aff_jours DAY) ORDER BY date");

	$date_debut = '';
	$log = array();
	$log_utiles = array();
	
	while ($row = spip_fetch_array($result)) {
		$date = $row['date_unix'];
		if (!$date_debut) $date_debut = $date;
		if ($row["visites_utiles"] > 0) $log[$date] = round(100 * $row["pages_vues_utiles"] / $row['visites_utiles']) / 100 ;
		
		
		if ($row["visites"] > 0) $log_utiles[$date] = round(100 * $row["pages_vues"] / $row['visites']) / 100 ;

	}
	$tab_moyenne = array();

	// S'il y a au moins cinq minutes de stats :-)
	if (count($log)>0) {
		// les visites du jour
		$date_today = max(array_keys($log));
		$visites_today = $log[$date_today];
		// sauf s'il n'y en a pas :
		if (time()-$date_today>3600*24) {
			$date_today = time();
			$visites_today=0;
		}
		
		// le nombre maximum
		$max = ceil(max($log));
		$max_utiles = ceil(max($log_utiles));

		$nb_jours = floor(($date_today-$date_debut)/(3600*24));

		$maxgraph = max(maxgraph($max), maxgraph($max_utiles));
		
		$rapport = 200 / $maxgraph;

		if (count($log) < 420) $largeur = floor(450 / ($nb_jours+1));
		if ($largeur < 1) {
			$largeur = 1;
			$agreg = ceil(count($log) / 420);	
		} else {
			$agreg = 1;
		}
		if ($largeur > 50) $largeur = 50;

		debut_cadre_relief("statistiques-24.gif",false, "", "Nombre de pages vues par visite");
		

		$largeur_abs = 420 / $aff_jours;
		
		if ($largeur_abs > 1) {
			$inc = ceil($largeur_abs / 5);
			$aff_jours_plus = 420 / ($largeur_abs - $inc);
			$aff_jours_moins = 420 / ($largeur_abs + $inc);
		}
		
		if ($largeur_abs == 1) {
			$aff_jours_plus = 840;
			$aff_jours_moins = 210;
		}
		
		if ($largeur_abs < 1) {
			$aff_jours_plus = 420 * ((1/$largeur_abs) + 1);
			$aff_jours_moins = 420 * ((1/$largeur_abs) - 1);
		}
		
		$pour_article = $id_article ? "&id_article=$id_article" : '';
		
		if ($date_premier < $date_debut)
		  echo http_href(generer_url_ecrire("pb_statistiques","aff_jours=$aff_jours_plus$pour_article"),
				 http_img_pack('loupe-moins.gif',
					       _T('info_zoom'). '-', 
					       "style='border: 0px; vertical-align: middle;'"),
				 "&nbsp;");
		if ( (($date_today - $date_debut) / (24*3600)) > 30)
		  echo http_href(generer_url_ecrire("pb_statistiques","aff_jours=$aff_jours_moins$pour_article"), 
				 http_img_pack('loupe-plus.gif',
					       _T('info_zoom'). '+', 
					       "style='border: 0px; vertical-align: middle;'"),
				 "&nbsp;");
	
	
	
	echo "\n<table cellpadding='0' cellspacing='0' border='0'><tr>",
	  "\n<td ".http_style_background("fond-stats.gif").">";
	echo "\n<table cellpadding='0' cellspacing='0' border='0' class='bottom'><tr>";
	
	echo "\n<td style='background-color: black'>", http_img_rien(1,200), "</td>";
	
	$test_agreg = $decal = $jour_prec = $val_prec = $total_loc =0;

	// Presentation graphique (rq: on n'affiche pas le jour courant)
	foreach ($log as $key => $value) {
		# quand on atteint aujourd'hui, stop
		if ($key == $date_today) break; 

		$test_agreg ++;
		
		if ($test_agreg == $agreg) {	
				
			$test_agreg = 0;
			
			if ($decal == 30) $decal = 0;
			$decal ++;
			$tab_moyenne[$decal] = $value;
			// Inserer des jours vides si pas d'entrees	
			if ($jour_prec > 0) {
					$ecart = floor(($key-$jour_prec)/((3600*24)*$agreg)-1);
		
					for ($i=0; $i < $ecart; $i++){
						if ($decal == 30) $decal = 0;
						$decal ++;
						$tab_moyenne[$decal] = $value;
	
						$ce_jour=date("Y-m-d", $jour_prec+(3600*24*($i+1)));
						$jour = nom_jour($ce_jour).' '.affdate_jourcourt($ce_jour);
	
						reset($tab_moyenne);
						$moyenne = 0;
						while (list(,$val_tab) = each($tab_moyenne))
							$moyenne += $val_tab;
						$moyenne = $moyenne / count($tab_moyenne);
		
						$hauteur_moyenne = round(($moyenne) * $rapport) - 1;
						echo "\n<td style='width: ${largeur}px'>";
						$difference = ($hauteur_moyenne) -1;
						$moyenne = round($moyenne,2); // Pour affichage harmonieux
						$tagtitle= attribut_html(supprimer_tags("$jour | "
						._T('info_visites')." | "
						._T('info_moyenne')." $moyenne"));
						if ($difference > 0) {	
						  echo http_img_rien($largeur,1, 'trait_moyen', $tagtitle);
						  echo http_img_rien($largeur, $hauteur_moyenne, '', $tagtitle);
						}
						echo 
						    http_img_rien($largeur,1,'trait_bas', $tagtitle);
						echo "</td>";
					}
				}
	
				$ce_jour=date("Y-m-d", $key);
				$jour = nom_jour($ce_jour).' '.affdate_jourcourt($ce_jour);
	
				$total_loc = $total_loc + $value;
				reset($tab_moyenne);
	
				$moyenne = 0;
				while (list(,$val_tab) = each($tab_moyenne))
					$moyenne += $val_tab;
				$moyenne = $moyenne / count($tab_moyenne);
			
				$hauteur_moyenne = round($log_utiles["$key"] * $rapport) - 1;
				$hauteur = round($value * $rapport) - 1;
				$moyenne = round($moyenne,2); // Pour affichage harmonieux
				echo "\n<td style='width: ${largeur}px'>";
	
				$tagtitle= attribut_html(supprimer_tags("$jour | "
				._T('info_visites')." ".$value));
	
				if ($hauteur > 0){
					if ($hauteur_moyenne > $hauteur) {
						$difference = ($hauteur_moyenne - $hauteur) -1;
						echo http_img_rien($largeur, 1,'trait_moyen',$tagtitle);
						echo http_img_rien($largeur, $difference, '', $tagtitle);
						echo http_img_rien($largeur,1, "trait_haut", $tagtitle);
						if (date("w",$key) == "0") // Dimanche en couleur foncee
						  echo http_img_rien($largeur, $hauteur, "couleur_dimanche", $tagtitle);
						else
						  echo http_img_rien($largeur,$hauteur, "couleur_jour", $tagtitle);
					} else if ($hauteur_moyenne < $hauteur) {
						$difference = ($hauteur - $hauteur_moyenne) -1;
						echo http_img_rien($largeur,1,"trait_haut", $tagtitle);
						if (date("w",$key) == "0") // Dimanche en couleur foncee
							$couleur =  'couleur_dimanche';
						else
							$couleur = 'couleur_jour';
						echo http_img_rien($largeur, $difference, $couleur, $tagtitle);
						echo http_img_rien($largeur,1,"trait_moyen", $tagtitle);
						echo http_img_rien($largeur, $hauteur_moyenne, $couleur, $tagtitle);
					} else {
					  echo http_img_rien($largeur, 1, "trait_haut", $tagtitle);
						if (date("w",$key) == "0") // Dimanche en couleur foncee
						  echo http_img_rien($largeur, $hauteur, "couleur_dimanche", $tagtitle);
						else
						  echo http_img_rien($largeur,$hauteur, "couleur_jour", $tagtitle);
					}
				}
				echo http_img_rien($largeur, 1, 'trait_bas', $tagtitle);
				echo "</td>\n";
			
				$jour_prec = $key;
				$val_prec = $value;
			}
			}
	
			// Dernier jour
			$hauteur = round($visites_today * $rapport)	- 1;
			$total_absolu = $total_absolu + $visites_today;
			echo "\n<td style='width: ${largeur}px'>";
			// prevision de visites jusqu'a minuit
			// basee sur la moyenne (site) ou popularite (article)
			if (! $id_article) $val_popularite = $moyenne;
			$prevision = (1 - (date("H")*60 + date("i"))/(24*60)) * $val_popularite;
			$hauteurprevision = ceil($prevision * $rapport);
			// Afficher la barre tout en haut
			if ($hauteur+$hauteurprevision>0)
				echo http_img_rien($largeur, 1, "trait_haut");
			// preparer le texte de survol (prevision)
			$tagtitle= attribut_html(supprimer_tags(_T('info_aujourdhui')." $visites_today &rarr; ".(round($prevision,0)+$visites_today)));
			// afficher la barre previsionnelle
			if ($hauteurprevision>0)
				echo http_img_rien($largeur, $hauteurprevision,'couleur_prevision', $tagtitle);
				// afficher la barre deja realisee
			if ($hauteur>0)
				echo http_img_rien($largeur, $hauteur, 'couleur_realise', $tagtitle);
			// et afficher la ligne de base
			echo http_img_rien($largeur, 1, 'trait_bas');
			echo "</td>";


			echo "\n<td style='background-color: black'>",http_img_rien(1, 1),"</td>";
			echo "</tr></table>";
			echo "</td>",
			  "\n<td ".http_style_background("fond-stats.gif")."  valign='bottom'>", http_img_rien(3, 1, 'trait_bas'),"</td>";
			echo "\n<td>", http_img_rien(5, 1),"</td>";
			echo "\n<td valign='top'><div style='font-size:small;' class='verdana1'>";
			echo "\n<table cellpadding='0' cellspacing='0' border='0'>";
			echo "\n<tr><td style='height: 15' valign='top'>";		
			echo "<span class='arial1 spip_x-small'><b>".round($maxgraph)."</b></span>";
			echo "</td></tr>";
			echo "\n<tr><td valign='middle' $class style='$style;height: 25px'>";		
			echo round(7*($maxgraph/8));
			echo "</td></tr>";
			echo "\n<tr><td style='height: 25px' valign='middle'>";		
			echo "<span class='arial1 spip_x-small'>".round(3*($maxgraph/4))."</span>";
			echo "</td></tr>";
			echo "\n<tr><td valign='middle' $class style='$style;height: 25px'>";		
			echo round(5*($maxgraph/8));
			echo "</td></tr>";
			echo "\n<tr><td style='height: 25px' valign='middle'>";		
			echo "<span class='arial1 spip_x-small'><b>".round($maxgraph/2)."</b></span>";
			echo "</td></tr>";
			echo "\n<tr><td valign='middle' $class style='$style;height: 25px'>";		
			echo round(3*($maxgraph/8));
			echo "</td></tr>";
			echo "\n<tr><td style='height: 25px' valign='middle'>";		
			echo "<span class='arial1 spip_x-small'>".round($maxgraph/4)."</span>";
			echo "</td></tr>";
			echo "\n<tr><td valign='middle' $class style='$style;height: 25px'>";		
			echo round(1*($maxgraph/8));
			echo "</td></tr>";
			echo "\n<tr><td style='height: 10px' valign='bottom'>";		
			echo "<span class='arial1 spip_x-small'><b>0</b></span>";
			echo "</td>";
			echo "</tr></table>";
			echo "</div></td>";
			echo "</tr></table>";
			
			echo "<div style='position: relative; height: 15px'>";
			$gauche_prec = -50;
			for ($jour = $date_debut; $jour <= $date_today; $jour = $jour + (24*3600)) {
				$ce_jour = date("d", $jour);
				
				if ($ce_jour == "1") {
					$afficher = nom_mois(date("Y-m-d", $jour));
					if (date("m", $jour) == 1) $afficher = "<b>".annee(date("Y-m-d", $jour))."</b>";
					
				
					$gauche = floor($jour - $date_debut) * $largeur / ((24*3600)*$agreg);
					
					if ($gauche - $gauche_prec >= 40 OR date("m", $jour) == 1) {									
						echo "<div class='arial0' style='border-$spip_lang_left: 1px solid black; padding-$spip_lang_left: 2px; padding-top: 3px; position: absolute; $spip_lang_left: ".$gauche."px; top: -1px;'>".$afficher."</div>";
						$gauche_prec = $gauche;
					}
				}
			}
			echo "</div>";

		//}

		// cette ligne donne la moyenne depuis le debut
		// (desactive au profit de la moeynne "glissante")
		# $moyenne =  round($total_absolu / ((date("U")-$date_premier)/(3600*24)));

		echo "<span class='arial1 spip_x-small'>La courbe principale donne le rapport pour les visites utiles. La courbe secondaire (noire) donne le rapport absolu des pages vues par visites.</span>";
		echo "<br /><table cellpadding='0' cellspacing='0' border='0' width='100%'><tr style='width:100%;'>";
		echo "\n<td valign='top' style='width: 33%; ' class='verdana1'>", _T('info_maximum')." ".$max, "<br />"._T('info_moyenne')." ".round($moyenne), "</td>";
		echo "\n<td valign='top' style='width: 33%; ' class='verdana1'>";
		echo '<a href="' . generer_url_ecrire("statistiques_referers","").'" title="'._T('titre_liens_entrants').'">'._T('info_aujourdhui').'</a> '.$visites_today;
		if ($val_prec > 0) echo '<br /><a href="' . generer_url_ecrire("statistiques_referers","jour=veille").'"  title="'._T('titre_liens_entrants').'">'._T('info_hier').'</a> '.$val_prec;
		if ($id_article) echo "<br />"._T('info_popularite_5').' '.$val_popularite;

		echo "</td>";
		echo "\n<td valign='top' style='width: 33%; ' class='verdana1'>";
		echo "<b>"._T('info_total')." ".$total_absolu."</b>";
		
		if ($id_article) {
			if ($classement[$id_article] > 0) {
				if ($classement[$id_article] == 1)
				      $ch = _T('info_classement_1', array('liste' => $liste));
				else
				      $ch = _T('info_classement_2', array('liste' => $liste));
				echo "<br />".$classement[$id_article].$ch;
			}
		} else {
		  echo "<span class='spip_x-small'><br />"._T('info_popularite_2')." ", ceil($GLOBALS['meta']['popularite_total']), "</span>";
		}
		echo "</td></tr></table>";	
	}		
	
	if (count($log) > 60) {
		echo "<br />";
		echo "<span class='verdana1 spip_small'><b>"._T('info_visites_par_mois')."</b></span>";

		///////// Affichage par mois
		$result=spip_query("SELECT FROM_UNIXTIME(UNIX_TIMESTAMP(date),'%Y-%m') AS date_unix, SUM(visites) AS total_visites, SUM(pages_vues) AS total_pages, SUM(visites_utiles) AS total_visites_utiles, SUM(pages_vues_utiles) AS total_pages_utiles  FROM $table WHERE $where AND date > DATE_SUB(NOW(),INTERVAL 2700 DAY) GROUP BY date_unix ORDER BY date");

		$entrees = array();
		$i = 0;
		while ($row = spip_fetch_array($result)) {
			$date = $row['date_unix'];
			if ($row["total_visites_utiles"] > 0) $visites = $row['total_pages_utiles'] / $row['total_visites_utiles'];
			if ($row["total_visites"] > 0) $visites2 = $row['total_pages'] / $row['total_visites'];
			$i++;
			$entrees["$date"] = $visites;
			$entrees2["$date"] = $visites2;
		}
		// Pour la derniere date, rajouter les visites du jour sauf si premier jour du mois
		if (date("d",time()) > 1) {
			$entrees["$date"] += $visites_today;
		} else { // Premier jour du mois : le rajouter dans le tableau des date (car il n'etait pas dans le resultat de la requete SQL precedente)
			$date = date("Y-m",time());
			$entrees["$date"] = $visites_today;
		}
		
		if (count($entrees)>0){
		
			$max = ceil(max($entrees));
			$maxgraph = maxgraph($max);
			$rapport = 200/$maxgraph;
		
			$largeur = floor(420 / (count($entrees)));
			if ($largeur < 1) $largeur = 1;
			if ($largeur > 50) $largeur = 50;
		}
		
		echo "\n<table cellpadding='0' cellspacing='0' border='0'><tr>",
		  "\n<td ".http_style_background("fond-stats.gif").">";
		echo "\n<table cellpadding='0' cellspacing='0' border='0' class='bottom'><tr>";
		echo "\n<td class='trait_bas'>", http_img_rien(1, 200),"</td>";
		// Presentation graphique
		$decal = 0;
		$tab_moyenne = "";
			
		while (list($key, $value) = each($entrees)) {
			
			$mois = affdate_mois_annee($key);

			if ($decal == 30) $decal = 0;
			$decal ++;
			$tab_moyenne[$decal] = $value;
			
			$total_loc = $total_loc + $value;
			reset($tab_moyenne);
	
			$moyenne = 0;
			while (list(,$val_tab) = each($tab_moyenne))
				$moyenne += $val_tab;
			$moyenne = $moyenne / count($tab_moyenne);
			
			$hauteur_moyenne = round($entrees2["$key"] * $rapport) - 1;
			$hauteur = round($value * $rapport) - 1;
			echo "\n<td style='width: ${largeur}px'>";

			$tagtitle= attribut_html(supprimer_tags("$mois | "
			._T('info_visites')." ".$value));

			if ($hauteur > 0){
				if ($hauteur_moyenne > $hauteur) {
					$difference = ($hauteur_moyenne - $hauteur) -1;
					echo http_img_rien($largeur, 1, 'trait_moyen');
					echo http_img_rien($largeur, $difference, '', $tagtitle);
					echo http_img_rien($largeur,1,"trait_haut");
					if (preg_match(",-01,",$key)){ // janvier en couleur foncee
					  echo http_img_rien($largeur,$hauteur,"couleur_janvier", $tagtitle);
					} 
					else {
					  echo http_img_rien($largeur,$hauteur,"couleur_mois", $tagtitle);
					}
				}
				else if ($hauteur_moyenne < $hauteur) {
					$difference = ($hauteur - $hauteur_moyenne) -1;
					echo http_img_rien($largeur,1,"trait_haut", $tagtitle);
					if (preg_match(",-01,",$key)){ // janvier en couleur foncee
						$couleur =  'couleur_janvier';
					} 
					else {
						$couleur = 'couleur_mois';
					}
					echo http_img_rien($largeur,$difference, $couleur, $tagtitle);
					echo http_img_rien($largeur,1,'trait_moyen',$tagtitle);
					echo http_img_rien($largeur,$hauteur_moyenne, $couleur, $tagtitle);
				}
				else {
				  echo http_img_rien($largeur,1,"trait_haut", $tagtitle);
					if (preg_match(",-01,",$key)){ // janvier en couleur foncee
					  echo http_img_rien($largeur, $hauteur, "couleur_janvier", $tagtitle);
					} 
					else {
					  echo http_img_rien($largeur,$hauteur, "couleur_mois", $tagtitle);
					}
				}
			}
			echo http_img_rien($largeur,1,'trait_bas', $tagtitle);
			echo "</td>\n";
		}
		
		echo "\n<td style='background-color: black'>", http_img_rien(1, 1),"</td>";
		echo "</tr></table>";
		echo "</td>",
		  "\n<td ".http_style_background("fond-stats.gif")." valign='bottom'>", http_img_rien(3, 1, 'trait_bas'),"</td>";
		echo "\n<td>", http_img_rien(5, 1),"</td>";
		echo "\n<td valign='top'><div style='font-size:small;' class='verdana1'>";
		echo "\n<table cellpadding='0' cellspacing='0' border='0'>";
		echo "\n<tr><td style='height: 15' valign='top'>";		
		echo "<span class='arial1 spip_x-small'><b>".round($maxgraph)."</b></span>";
		echo "</td></tr>";
		echo "\n<tr><td valign='middle' $class style='$style;height: 25px'>";		
		echo round(7*($maxgraph/8));
		echo "</td></tr>";
		echo "\n<tr><td style='height: 25px' valign='middle'>";		
		echo "<span class='arial1 spip_x-small'>".round(3*($maxgraph/4))."</span>";
		echo "</td></tr>";
		echo "\n<tr><td valign='middle' $class style='$style;height: 25px'>";		
		echo round(5*($maxgraph/8));
		echo "</td></tr>";
		echo "\n<tr><td style='height: 25px' valign='middle'>";		
		echo "<span class='arial1 spip_x-small'><b>".round($maxgraph/2)."</b></span>";
		echo "</td></tr>";
		echo "\n<tr><td valign='middle' $class style='$style;height: 25px'>";		
		echo round(3*($maxgraph/8));
		echo "</td></tr>";
		echo "\n<tr><td style='height: 25px' valign='middle'>";		
		echo "<span class='arial1 spip_x-small'>".round($maxgraph/4)."</span>";
		echo "</td></tr>";
		echo "\n<tr><td valign='middle' $class style='$style;height: 25px'>";		
		echo round(1*($maxgraph/8));
		echo "</td></tr>";
		echo "\n<tr><td style='height: 10px' valign='bottom'>";		
		echo "<span class='arial1 spip_x-small'><b>0</b></span>";
		echo "</td>";

		echo "</tr></table>";
		echo "</div></td></tr></table>";
	}
	
	/////
		
	fin_cadre_relief();

 }










////// DUREE DES VISITES

 if (!$origine) {

	$total_absolu = 0;
		if ($le_pays) {
			$table = "spip_pb_visites_pays";
			$where = "pays='$le_pays'";		
		} else {
			$table = "spip_pb_visites";
			$table_ref = "spip_referers";
			$where = "0=0";
		}
	
	$result = spip_query("SELECT UNIX_TIMESTAMP(date) AS date_unix FROM $table WHERE $where ORDER BY date LIMIT 1");

	while ($row = spip_fetch_array($result)) {
		$date_premier = $row['date_unix'];
	}

	$result=spip_query("SELECT UNIX_TIMESTAMP(date) AS date_unix, visites_utiles, duree FROM $table WHERE $where AND date > DATE_SUB(NOW(),INTERVAL $aff_jours DAY) ORDER BY date");

	$date_debut = '';
	$log = array();
	while ($row = spip_fetch_array($result)) {
		$date = $row['date_unix'];
		if (!$date_debut) $date_debut = $date;
		if ($row["visites_utiles"] > 0) $log[$date] = round($row['duree'] / $row['visites_utiles']) / 60;
				
	}
	$tab_moyenne = array();


	// S'il y a au moins cinq minutes de stats :-)
	if (count($log)>0) {
		// les visites du jour
		$date_today = max(array_keys($log));
		$visites_today = $log[$date_today];
		// sauf s'il n'y en a pas :
		if (time()-$date_today>3600*24) {
			$date_today = time();
			$visites_today=0;
		}
		
		// le nombre maximum
		$max = ceil(max($log));
		$nb_jours = floor(($date_today-$date_debut)/(3600*24));

		$maxgraph = maxgraph($max);
		
		
		$rapport = 200 / $maxgraph;

		if (count($log) < 420) $largeur = floor(450 / ($nb_jours+1));
		if ($largeur < 1) {
			$largeur = 1;
			$agreg = ceil(count($log) / 420);	
		} else {
			$agreg = 1;
		}
		if ($largeur > 50) $largeur = 50;

		debut_cadre_relief("statistiques-24.gif",false, "", "Dur&eacute;e des visites");
		

		$largeur_abs = 420 / $aff_jours;
		
		if ($largeur_abs > 1) {
			$inc = ceil($largeur_abs / 5);
			$aff_jours_plus = 420 / ($largeur_abs - $inc);
			$aff_jours_moins = 420 / ($largeur_abs + $inc);
		}
		
		if ($largeur_abs == 1) {
			$aff_jours_plus = 840;
			$aff_jours_moins = 210;
		}
		
		if ($largeur_abs < 1) {
			$aff_jours_plus = 420 * ((1/$largeur_abs) + 1);
			$aff_jours_moins = 420 * ((1/$largeur_abs) - 1);
		}
		
		$pour_article = $id_article ? "&id_article=$id_article" : '';
		
		if ($date_premier < $date_debut)
		  echo http_href(generer_url_ecrire("pb_statistiques","aff_jours=$aff_jours_plus$pour_article"),
				 http_img_pack('loupe-moins.gif',
					       _T('info_zoom'). '-', 
					       "style='border: 0px; vertical-align: middle;'"),
				 "&nbsp;");
		if ( (($date_today - $date_debut) / (24*3600)) > 30)
		  echo http_href(generer_url_ecrire("pb_statistiques","aff_jours=$aff_jours_moins$pour_article"), 
				 http_img_pack('loupe-plus.gif',
					       _T('info_zoom'). '+', 
					       "style='border: 0px; vertical-align: middle;'"),
				 "&nbsp;");
	
	
	echo "\n<table cellpadding='0' cellspacing='0' border='0'><tr>",
	  "\n<td ".http_style_background("fond-stats.gif").">";
	echo "\n<table cellpadding='0' cellspacing='0' border='0' class='bottom'><tr>";
	
	echo "\n<td style='background-color: black'>", http_img_rien(1,200), "</td>";
	
	$test_agreg = $decal = $jour_prec = $val_prec = $total_loc =0;

	// Presentation graphique (rq: on n'affiche pas le jour courant)
	foreach ($log as $key => $value) {
		# quand on atteint aujourd'hui, stop
		if ($key == $date_today) break; 

		$test_agreg ++;
		
		if ($test_agreg == $agreg) {	
				
			$test_agreg = 0;
			
			if ($decal == 30) $decal = 0;
			$decal ++;
			$tab_moyenne[$decal] = $value;
			// Inserer des jours vides si pas d'entrees	
			if ($jour_prec > 0) {
					$ecart = floor(($key-$jour_prec)/((3600*24)*$agreg)-1);
		
					for ($i=0; $i < $ecart; $i++){
						if ($decal == 30) $decal = 0;
						$decal ++;
						$tab_moyenne[$decal] = $value;
	
						$ce_jour=date("Y-m-d", $jour_prec+(3600*24*($i+1)));
						$jour = nom_jour($ce_jour).' '.affdate_jourcourt($ce_jour);
	
						reset($tab_moyenne);
						$moyenne = 0;
						while (list(,$val_tab) = each($tab_moyenne))
							$moyenne += $val_tab;
						$moyenne = $moyenne / count($tab_moyenne);
		
						$hauteur_moyenne = round(($moyenne) * $rapport) - 1;
						echo "\n<td style='width: ${largeur}px'>";
						$difference = ($hauteur_moyenne) -1;
						$moyenne = round($moyenne,2); // Pour affichage harmonieux
						$tagtitle= attribut_html(supprimer_tags("$jour | "
						._T('info_visites')." | "
						._T('info_moyenne')." $moyenne"));
						if ($difference > 0) {	
						  echo http_img_rien($largeur,1, 'trait_moyen', $tagtitle);
						  echo http_img_rien($largeur, $hauteur_moyenne, '', $tagtitle);
						}
						echo 
						    http_img_rien($largeur,1,'trait_bas', $tagtitle);
						echo "</td>";
					}
				}
	
				$ce_jour=date("Y-m-d", $key);
				$jour = nom_jour($ce_jour).' '.affdate_jourcourt($ce_jour);
	
				$total_loc = $total_loc + $value;
				reset($tab_moyenne);
	
				$moyenne = 0;
				while (list(,$val_tab) = each($tab_moyenne))
					$moyenne += $val_tab;
				$moyenne = $moyenne / count($tab_moyenne);
			
				$hauteur_moyenne = round($moyenne * $rapport) - 1;
				$hauteur = round($value * $rapport) - 1;
				$moyenne = round($moyenne,2); // Pour affichage harmonieux
				echo "\n<td style='width: ${largeur}px'>";
	
				$tagtitle= attribut_html(supprimer_tags("$jour | "
				._T('info_visites')." ".$value));
	
				if ($hauteur > 0){
					if ($hauteur_moyenne > $hauteur) {
						$difference = ($hauteur_moyenne - $hauteur) -1;
						echo http_img_rien($largeur, 1,'trait_moyen',$tagtitle);
						echo http_img_rien($largeur, $difference, '', $tagtitle);
						echo http_img_rien($largeur,1, "trait_haut", $tagtitle);
						if (date("w",$key) == "0") // Dimanche en couleur foncee
						  echo http_img_rien($largeur, $hauteur, "couleur_dimanche", $tagtitle);
						else
						  echo http_img_rien($largeur,$hauteur, "couleur_jour", $tagtitle);
					} else if ($hauteur_moyenne < $hauteur) {
						$difference = ($hauteur - $hauteur_moyenne) -1;
						echo http_img_rien($largeur,1,"trait_haut", $tagtitle);
						if (date("w",$key) == "0") // Dimanche en couleur foncee
							$couleur =  'couleur_dimanche';
						else
							$couleur = 'couleur_jour';
						echo http_img_rien($largeur, $difference, $couleur, $tagtitle);
						echo http_img_rien($largeur,1,"trait_moyen", $tagtitle);
						echo http_img_rien($largeur, $hauteur_moyenne, $couleur, $tagtitle);
					} else {
					  echo http_img_rien($largeur, 1, "trait_haut", $tagtitle);
						if (date("w",$key) == "0") // Dimanche en couleur foncee
						  echo http_img_rien($largeur, $hauteur, "couleur_dimanche", $tagtitle);
						else
						  echo http_img_rien($largeur,$hauteur, "couleur_jour", $tagtitle);
					}
				}
				echo http_img_rien($largeur, 1, 'trait_bas', $tagtitle);
				echo "</td>\n";
			
				$jour_prec = $key;
				$val_prec = $value;
			}
			}
	
			// Dernier jour
			
			$hauteur = round($visites_today * $rapport)	- 1;
			$total_absolu = $total_absolu + $visites_today;
			echo "\n<td style='width: ${largeur}px'>";
			// Afficher la barre tout en haut
			$tagtitle= attribut_html(supprimer_tags(_T('info_aujourdhui')." $visites_today "));
			// afficher la barre previsionnelle
			if ($hauteur>0)
				echo http_img_rien($largeur, $hauteur, 'couleur_realise', $tagtitle);
			// et afficher la ligne de base
			echo http_img_rien($largeur, 1, 'trait_bas');
			echo "</td>";


			echo "\n<td style='background-color: black'>",http_img_rien(1, 1),"</td>";
			echo "</tr></table>";
			echo "</td>",
			  "\n<td ".http_style_background("fond-stats.gif")."  valign='bottom'>", http_img_rien(3, 1, 'trait_bas'),"</td>";
			echo "\n<td>", http_img_rien(5, 1),"</td>";
			echo "\n<td valign='top'><div style='font-size:small;' class='verdana1'>";
			echo "\n<table cellpadding='0' cellspacing='0' border='0'>";
			echo "\n<tr><td style='height: 15' valign='top'>";		
			echo "<span class='arial1 spip_x-small'><b>".round($maxgraph)." min</b></span>";
			echo "</td></tr>";
			echo "\n<tr><td valign='middle' $class style='$style;height: 25px'>";		
			echo round(7*($maxgraph/8));
			echo " min</td></tr>";
			echo "\n<tr><td style='height: 25px' valign='middle'>";		
			echo "<span class='arial1 spip_x-small'>".round(3*($maxgraph/4))." min</span>";
			echo "</td></tr>";
			echo "\n<tr><td valign='middle' $class style='$style;height: 25px'>";		
			echo round(5*($maxgraph/8));
			echo " min</td></tr>";
			echo "\n<tr><td style='height: 25px' valign='middle'>";		
			echo "<span class='arial1 spip_x-small'><b>".round($maxgraph/2)." min</b></span>";
			echo "</td></tr>";
			echo "\n<tr><td valign='middle' $class style='$style;height: 25px'>";		
			echo round(3*($maxgraph/8));
			echo " min</td></tr>";
			echo "\n<tr><td style='height: 25px' valign='middle'>";		
			echo "<span class='arial1 spip_x-small'>".round($maxgraph/4)." min</span>";
			echo "</td></tr>";
			echo "\n<tr><td valign='middle' $class style='$style;height: 25px'>";		
			echo round(1*($maxgraph/8));
			echo " min</td></tr>";
			echo "\n<tr><td style='height: 10px' valign='bottom'>";		
			echo "<span class='arial1 spip_x-small'><b>0</b></span>";
			echo "</td>";
			echo "</tr></table>";
			echo "</div></td>";
			echo "</tr></table>";
			
			echo "<div style='position: relative; height: 15px'>";
			$gauche_prec = -50;
			for ($jour = $date_debut; $jour <= $date_today; $jour = $jour + (24*3600)) {
				$ce_jour = date("d", $jour);
				
				if ($ce_jour == "1") {
					$afficher = nom_mois(date("Y-m-d", $jour));
					if (date("m", $jour) == 1) $afficher = "<b>".annee(date("Y-m-d", $jour))."</b>";
					
				
					$gauche = floor($jour - $date_debut) * $largeur / ((24*3600)*$agreg);
					
					if ($gauche - $gauche_prec >= 40 OR date("m", $jour) == 1) {									
						echo "<div class='arial0' style='border-$spip_lang_left: 1px solid black; padding-$spip_lang_left: 2px; padding-top: 3px; position: absolute; $spip_lang_left: ".$gauche."px; top: -1px;'>".$afficher."</div>";
						$gauche_prec = $gauche;
					}
				}
			}
			echo "</div>";

		//}

		// cette ligne donne la moyenne depuis le debut
		// (desactive au profit de la moeynne "glissante")
		# $moyenne =  round($total_absolu / ((date("U")-$date_premier)/(3600*24)));

		echo "<span class='arial1 spip_x-small'>"._T('texte_pb_statistiques')."</span>";
		echo "<br /><table cellpadding='0' cellspacing='0' border='0' width='100%'><tr style='width:100%;'>";
		echo "\n<td valign='top' style='width: 33%; ' class='verdana1'>", _T('info_maximum')." ".$max, "<br />"._T('info_moyenne')." ".round($moyenne), "</td>";
		echo "\n<td valign='top' style='width: 33%; ' class='verdana1'>";
		echo '<a href="' . generer_url_ecrire("statistiques_referers","").'" title="'._T('titre_liens_entrants').'">'._T('info_aujourdhui').'</a> '.$visites_today;
		if ($val_prec > 0) echo '<br /><a href="' . generer_url_ecrire("statistiques_referers","jour=veille").'"  title="'._T('titre_liens_entrants').'">'._T('info_hier').'</a> '.$val_prec;
		if ($id_article) echo "<br />"._T('info_popularite_5').' '.$val_popularite;

		echo "</td>";
		echo "\n<td valign='top' style='width: 33%; ' class='verdana1'>";
		echo "<b>"._T('info_total')." ".$total_absolu."</b>";
		
		if ($id_article) {
			if ($classement[$id_article] > 0) {
				if ($classement[$id_article] == 1)
				      $ch = _T('info_classement_1', array('liste' => $liste));
				else
				      $ch = _T('info_classement_2', array('liste' => $liste));
				echo "<br />".$classement[$id_article].$ch;
			}
		} else {
		  echo "<span class='spip_x-small'><br />"._T('info_popularite_2')." ", ceil($GLOBALS['meta']['popularite_total']), "</span>";
		}
		echo "</td></tr></table>";	
	}		
	
	if (count($log) > 60) {
		echo "<br />";
		echo "<span class='verdana1 spip_small'><b>"._T('info_visites_par_mois')."</b></span>";

		///////// Affichage par mois
		$result=spip_query("SELECT FROM_UNIXTIME(UNIX_TIMESTAMP(date),'%Y-%m') AS date_unix, SUM(visites_utiles) AS total_visites, SUM(duree) AS total_duree  FROM $table WHERE $where AND date > DATE_SUB(NOW(),INTERVAL 2700 DAY) GROUP BY date_unix ORDER BY date");

		$entrees = array();
		$entrees_utiles = array();
		
		$i = 0;
		while ($row = spip_fetch_array($result)) {
			$date = $row['date_unix'];
			$visites = ($row["total_duree"] / $row['total_visites']) / 60;
			$i++;
			$entrees["$date"] = $visites;
		}
		// Pour la derniere date, rajouter les visites du jour sauf si premier jour du mois
		if (date("d",time()) > 1) {
			$entrees["$date"] += $visites_today;
		} else { // Premier jour du mois : le rajouter dans le tableau des date (car il n'etait pas dans le resultat de la requete SQL precedente)
			$date = date("Y-m",time());
			$entrees["$date"] = $visites_today;
		}
		
		if (count($entrees)>0){
		
			$max = ceil(max($entrees));
			$maxgraph = maxgraph($max);
			
			$rapport = 200/$maxgraph;

			$largeur = floor(420 / (count($entrees)));
			if ($largeur < 1) $largeur = 1;
			if ($largeur > 50) $largeur = 50;
		}
		
		echo "\n<table cellpadding='0' cellspacing='0' border='0'><tr>",
		  "\n<td ".http_style_background("fond-stats.gif").">";
		echo "\n<table cellpadding='0' cellspacing='0' border='0' class='bottom'><tr>";
		echo "\n<td class='trait_bas'>", http_img_rien(1, 200),"</td>";
		// Presentation graphique
		$decal = 0;
		$tab_moyenne = "";
			
		while (list($key, $value) = each($entrees)) {
			
			$mois = affdate_mois_annee($key);

			if ($decal == 30) $decal = 0;
			$decal ++;
			$tab_moyenne[$decal] = $value;
			
			$total_loc = $total_loc + $value;
			reset($tab_moyenne);
	
			$moyenne = 0;
			while (list(,$val_tab) = each($tab_moyenne))
				$moyenne += $val_tab;
			$moyenne = $moyenne / count($tab_moyenne);
			
			$hauteur_moyenne = round($moyenne * $rapport) - 1;
			$hauteur = round($value * $rapport) - 1;
			echo "\n<td style='width: ${largeur}px'>";

			$tagtitle= attribut_html(supprimer_tags("$mois | "
			._T('info_visites')." ".$value));

			if ($hauteur > 0){
				if ($hauteur_moyenne > $hauteur) {
					$difference = ($hauteur_moyenne - $hauteur) -1;
					echo http_img_rien($largeur, 1, 'trait_moyen');
					echo http_img_rien($largeur, $difference, '', $tagtitle);
					echo http_img_rien($largeur,1,"trait_haut");
					if (preg_match(",-01,",$key)){ // janvier en couleur foncee
					  echo http_img_rien($largeur,$hauteur,"couleur_janvier", $tagtitle);
					} 
					else {
					  echo http_img_rien($largeur,$hauteur,"couleur_mois", $tagtitle);
					}
				}
				else if ($hauteur_moyenne < $hauteur) {
					$difference = ($hauteur - $hauteur_moyenne) -1;
					echo http_img_rien($largeur,1,"trait_haut", $tagtitle);
					if (preg_match(",-01,",$key)){ // janvier en couleur foncee
						$couleur =  'couleur_janvier';
					} 
					else {
						$couleur = 'couleur_mois';
					}
					echo http_img_rien($largeur,$difference, $couleur, $tagtitle);
					echo http_img_rien($largeur,1,'trait_moyen',$tagtitle);
					echo http_img_rien($largeur,$hauteur_moyenne, $couleur, $tagtitle);
				}
				else {
				  echo http_img_rien($largeur,1,"trait_haut", $tagtitle);
					if (preg_match(",-01,",$key)){ // janvier en couleur foncee
					  echo http_img_rien($largeur, $hauteur, "couleur_janvier", $tagtitle);
					} 
					else {
					  echo http_img_rien($largeur,$hauteur, "couleur_mois", $tagtitle);
					}
				}
			}
			echo http_img_rien($largeur,1,'trait_bas', $tagtitle);
			echo "</td>\n";
		}
		
		echo "\n<td style='background-color: black'>", http_img_rien(1, 1),"</td>";
		echo "</tr></table>";
		echo "</td>",
		  "\n<td ".http_style_background("fond-stats.gif")." valign='bottom'>", http_img_rien(3, 1, 'trait_bas'),"</td>";
		echo "\n<td>", http_img_rien(5, 1),"</td>";
		echo "\n<td valign='top'><div style='font-size:small;' class='verdana1'>";
		echo "\n<table cellpadding='0' cellspacing='0' border='0'>";
		echo "\n<tr><td style='height: 15' valign='top'>";		
		echo "<span class='arial1 spip_x-small'><b>".round($maxgraph)."</b></span>";
		echo "</td></tr>";
		echo "\n<tr><td valign='middle' $class style='$style;height: 25px'>";		
		echo round(7*($maxgraph/8));
		echo "</td></tr>";
		echo "\n<tr><td style='height: 25px' valign='middle'>";		
		echo "<span class='arial1 spip_x-small'>".round(3*($maxgraph/4))."</span>";
		echo "</td></tr>";
		echo "\n<tr><td valign='middle' $class style='$style;height: 25px'>";		
		echo round(5*($maxgraph/8));
		echo "</td></tr>";
		echo "\n<tr><td style='height: 25px' valign='middle'>";		
		echo "<span class='arial1 spip_x-small'><b>".round($maxgraph/2)."</b></span>";
		echo "</td></tr>";
		echo "\n<tr><td valign='middle' $class style='$style;height: 25px'>";		
		echo round(3*($maxgraph/8));
		echo "</td></tr>";
		echo "\n<tr><td style='height: 25px' valign='middle'>";		
		echo "<span class='arial1 spip_x-small'>".round($maxgraph/4)."</span>";
		echo "</td></tr>";
		echo "\n<tr><td valign='middle' $class style='$style;height: 25px'>";		
		echo round(1*($maxgraph/8));
		echo "</td></tr>";
		echo "\n<tr><td style='height: 10px' valign='bottom'>";		
		echo "<span class='arial1 spip_x-small'><b>0</b></span>";
		echo "</td>";

		echo "</tr></table>";
		echo "</div></td></tr></table>";
	}
	
	/////
		
	fin_cadre_relief();

 }







//
// Affichage des referers
//

if ($date_pays) {
	$result = spip_query("SELECT * FROM spip_pb_visites_squelettes WHERE date = '$date_pays' ORDER BY pages_vues DESC");
	
	
	if (spip_num_rows($result) > 0) {
		echo "<ul style='font-family: verdana, arial, sans; font-size: 11px;'>";
		while ($row = spip_fetch_array($result)) {
			$squelette = $row["squelette"];
			$pages_vues = $row["pages_vues"];
			
			if ($squelette == "") $squelette = "racine du site";
			
			echo "<li><b>$squelette</b> - $pages_vues</li>";
		}
		echo "</ul>";
	}
}



echo fin_gauche(), fin_page();
     }
?>
