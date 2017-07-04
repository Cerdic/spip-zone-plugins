<?php
/**
 * Plugin Pays pour Spip 3.0
 * Licence GPL
 * Auteur Organisation Internationale de Normalisation http://www.iso.org/iso/fr/country_codes/iso_3166_code_lists.htm
 * Cedric Morin et Collectif SPIP pour version spip_geo_pays
 * Portage sous SPIP par Cyril MARION - Ateliers CYM http://www.cym.fr
 *
 */
if (!defined("_ECRIRE_INC_VERSION")) return;

function pays_upgrade($nom_meta_base_version, $version_cible){

	$maj = array();
	$maj['create'] = array(
		array('maj_tables', array('spip_pays', 'spip_pays_liens')),
		array('peupler_base_pays')
		);
	$maj['1.1.0'] = array(
		array('sql_drop_table', array('spip_pays')),
		array('maj_tables', array('spip_pays')),
		array('peupler_base_pays')
	);

	include_spip('base/upgrade');
	include_spip('base/pays_peupler_base');
	$maj['1.3.0'] = array(
		array('maj_130_pays'),
	);
	$maj['1.3.1'] = array(
		array('maj_tables', array('spip_pays')),
	);
	$maj['1.4.0'] = array(
		array('maj_tables', array('spip_pays_liens')),
	);

	// upgrades 1.2.0, 1.2.1, 1.4.2, 1.4.3 en erreur… on les refait correctement …
	// (bien échapper l’insertion, et tenir compte des index uniques !)
	$maj['1.4.3'] = array(
		// Inversion de 2 pays
		array('sql_updateq', 'spip_pays', array('code' => '0'), 'id_pays=52'),
		array('sql_updateq', 'spip_pays', array('code' => 'KP', 'code_num' => 408, 'code_alpha3' => 'PRK'), 'id_pays=51'),
		array('sql_updateq', 'spip_pays', array('code' => 'KR', 'code_num' => 410, 'code_alpha3' => 'KOR'), 'id_pays=52'),
		// Inversion de 2 pays
		array('sql_updateq', 'spip_pays', array('code' => '0'), 'id_pays=110'),
		array('sql_updateq', 'spip_pays', array('code' => 'IQ', 'code_num' => 368, 'code_alpha3' => 'IRQ'), 'id_pays=109'),
		array('sql_updateq', 'spip_pays', array('code' => 'IR', 'code_num' => 364, 'code_alpha3' => 'IRN'), 'id_pays=110'),
		// Coquille code alpha3.
		array('sql_updateq', 'spip_pays', array('code' => 'CZ', 'code_num' => 203, 'code_alpha3' => 'CZE'), 'id_pays=185'),
	);

	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}



function pays_vider_tables($nom_meta_base_version) {

	sql_drop_table("spip_pays");
	sql_drop_table("spip_pays_liens");
	effacer_meta($nom_meta_base_version);
}

function maj_130_pays() {
	sql_alter("TABLE spip_pays ADD code_alpha3 varchar(3) NOT NULL default '' AFTER code");
	sql_alter("TABLE spip_pays ADD code_num int(3) UNSIGNED ZEROFILL NOT NULL default 0 AFTER code_alpha3");

sql_insertq('spip_pays', array('code'=>'BL', 'code_num'=>652, 'code_alpha3'=>'BLM', 'nom'=>'<multi>[fr]Saint-Barthélemy[en]Saint-Barthélemy[de]Saint-Barthélemy[es]Saint-Barthélemy[it]Saint-Barthélemy[nl]Saint-Barthelemy[pt]Saint-Barthélemy</multi>'));
sql_insertq('spip_pays', array('code'=>'BQ', 'code_num'=>535, 'code_alpha3'=>'BES', 'nom'=>'<multi>[fr]Pays-Bas caribéens[en]Netherlands Caribbean[de]Niederlande Karibik[es]Holanda del Caribe[it]Netherlands Caribbean[nl]Caribisch Nederland[pt]Holanda Caribe</multi>'));
sql_insertq('spip_pays', array('code'=>'CW', 'code_num'=>531, 'code_alpha3'=>'CUW', 'nom'=>'<multi>[fr]Curaçao[en]Curacao[de]Curacao[es]Curacao[it]Curacao[nl]Curacao[pt]Curaçao</multi>'));
sql_insertq('spip_pays', array('code'=>'HM', 'code_num'=>334, 'code_alpha3'=>'HMD', 'nom'=>'<multi>[fr]Îles Heard-et-McDonald[en]Heard and McDonald Islands[de]Heard und McDonaldinseln[es]Islas Heard y McDonald[it]Isole Heard e McDonald[nl]Heard- en McDonaldeilanden[pt]Ilhas Heard e McDonald</multi>'));
sql_insertq('spip_pays', array('code'=>'MF', 'code_num'=>663, 'code_alpha3'=>'MAF', 'nom'=>'<multi>[fr]Saint-Martin[en]Saint-Martin[de]Saint-Martin[es]Saint-Martin[it]Saint-Martin[nl]Saint-Martin[pt]Saint-Martin</multi>'));
sql_insertq('spip_pays', array('code'=>'SS', 'code_num'=>728, 'code_alpha3'=>'SSD', 'nom'=>'<multi>[fr]Soudan du Sud[en]South Sudan[de]Süd-Sudan[es]Sudán del Sur[it]Sud Sudan[nl]Zuid-Soedan[pt]Sudão do Sul</multi>'));
sql_insertq('spip_pays', array('code'=>'SX', 'code_num'=>534, 'code_alpha3'=>'SXM', 'nom'=>'<multi>[fr]Sint Maarten[en]Sint Maarten[de]Sint Maarten[es]Sint Maarten[it]Sint Maarten[nl]Sint Maarten[pt]Sint Maarten</multi>'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('AND'), 'code_num'=>20), 'code=' . sql_quote('AD'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('ARE'), 'code_num'=>784), 'code=' . sql_quote('AE'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('AFG'), 'code_num'=>4), 'code=' . sql_quote('AF'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('ATG'), 'code_num'=>28), 'code=' . sql_quote('AG'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('AIA'), 'code_num'=>660), 'code=' . sql_quote('AI'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('ALB'), 'code_num'=>8), 'code=' . sql_quote('AL'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('ARM'), 'code_num'=>51), 'code=' . sql_quote('AM'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('ANT'), 'code_num'=>530), 'code=' . sql_quote('AN'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('AGO'), 'code_num'=>24), 'code=' . sql_quote('AO'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('ATA'), 'code_num'=>10), 'code=' . sql_quote('AQ'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('ARG'), 'code_num'=>32), 'code=' . sql_quote('AR'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('ASM'), 'code_num'=>16), 'code=' . sql_quote('AS'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('AUT'), 'code_num'=>40), 'code=' . sql_quote('AT'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('AUS'), 'code_num'=>36), 'code=' . sql_quote('AU'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('ABW'), 'code_num'=>533), 'code=' . sql_quote('AW'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('ALA'), 'code_num'=>248), 'code=' . sql_quote('AX'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('AZE'), 'code_num'=>31), 'code=' . sql_quote('AZ'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('BIH'), 'code_num'=>70), 'code=' . sql_quote('BA'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('BRB'), 'code_num'=>52), 'code=' . sql_quote('BB'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('BGD'), 'code_num'=>50), 'code=' . sql_quote('BD'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('BEL'), 'code_num'=>56), 'code=' . sql_quote('BE'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('BFA'), 'code_num'=>854), 'code=' . sql_quote('BF'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('BGR'), 'code_num'=>100), 'code=' . sql_quote('BG'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('BHR'), 'code_num'=>48), 'code=' . sql_quote('BH'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('BDI'), 'code_num'=>108), 'code=' . sql_quote('BI'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('BEN'), 'code_num'=>204), 'code=' . sql_quote('BJ'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('BLM'), 'code_num'=>652), 'code=' . sql_quote('BL'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('BMU'), 'code_num'=>60), 'code=' . sql_quote('BM'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('BRN'), 'code_num'=>96), 'code=' . sql_quote('BN'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('BOL'), 'code_num'=>68), 'code=' . sql_quote('BO'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('BES'), 'code_num'=>535), 'code=' . sql_quote('BQ'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('BRA'), 'code_num'=>76), 'code=' . sql_quote('BR'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('BHS'), 'code_num'=>44), 'code=' . sql_quote('BS'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('BTN'), 'code_num'=>64), 'code=' . sql_quote('BT'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('BVT'), 'code_num'=>74), 'code=' . sql_quote('BV'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('BWA'), 'code_num'=>72), 'code=' . sql_quote('BW'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('BLR'), 'code_num'=>112), 'code=' . sql_quote('BY'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('BLZ'), 'code_num'=>84), 'code=' . sql_quote('BZ'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('CAN'), 'code_num'=>124), 'code=' . sql_quote('CA'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('CCK'), 'code_num'=>166), 'code=' . sql_quote('CC'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('COD'), 'code_num'=>180), 'code=' . sql_quote('CD'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('CAF'), 'code_num'=>140), 'code=' . sql_quote('CF'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('COG'), 'code_num'=>178), 'code=' . sql_quote('CG'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('CHE'), 'code_num'=>756), 'code=' . sql_quote('CH'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('CIV'), 'code_num'=>384), 'code=' . sql_quote('CI'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('COK'), 'code_num'=>184), 'code=' . sql_quote('CK'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('CHL'), 'code_num'=>152), 'code=' . sql_quote('CL'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('CMR'), 'code_num'=>120), 'code=' . sql_quote('CM'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('CHN'), 'code_num'=>156), 'code=' . sql_quote('CN'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('COL'), 'code_num'=>170), 'code=' . sql_quote('CO'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('CRI'), 'code_num'=>188), 'code=' . sql_quote('CR'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('CUB'), 'code_num'=>192), 'code=' . sql_quote('CU'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('CPV'), 'code_num'=>132), 'code=' . sql_quote('CV'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('CUW'), 'code_num'=>531), 'code=' . sql_quote('CW'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('CXR'), 'code_num'=>162), 'code=' . sql_quote('CX'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('CYP'), 'code_num'=>196), 'code=' . sql_quote('CY'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('CZE'), 'code_num'=>203), 'code=' . sql_quote('CZ'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('DEU'), 'code_num'=>276), 'code=' . sql_quote('DE'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('DJI'), 'code_num'=>262), 'code=' . sql_quote('DJ'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('DNK'), 'code_num'=>208), 'code=' . sql_quote('DK'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('DMA'), 'code_num'=>212), 'code=' . sql_quote('DM'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('DOM'), 'code_num'=>214), 'code=' . sql_quote('DO'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('DZA'), 'code_num'=>12), 'code=' . sql_quote('DZ'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('ECU'), 'code_num'=>218), 'code=' . sql_quote('EC'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('EST'), 'code_num'=>233), 'code=' . sql_quote('EE'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('EGY'), 'code_num'=>818), 'code=' . sql_quote('EG'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('ESH'), 'code_num'=>732), 'code=' . sql_quote('EH'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('ERI'), 'code_num'=>232), 'code=' . sql_quote('ER'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('ESP'), 'code_num'=>724), 'code=' . sql_quote('ES'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('ETH'), 'code_num'=>231), 'code=' . sql_quote('ET'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('FIN'), 'code_num'=>246), 'code=' . sql_quote('FI'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('FJI'), 'code_num'=>242), 'code=' . sql_quote('FJ'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('FLK'), 'code_num'=>238), 'code=' . sql_quote('FK'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('FSM'), 'code_num'=>583), 'code=' . sql_quote('FM'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('FRO'), 'code_num'=>234), 'code=' . sql_quote('FO'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('FRA'), 'code_num'=>250), 'code=' . sql_quote('FR'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('GAB'), 'code_num'=>266), 'code=' . sql_quote('GA'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('GBR'), 'code_num'=>826), 'code=' . sql_quote('GB'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('GRD'), 'code_num'=>308), 'code=' . sql_quote('GD'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('GEO'), 'code_num'=>268), 'code=' . sql_quote('GE'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('GUF'), 'code_num'=>254), 'code=' . sql_quote('GF'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('GGY'), 'code_num'=>831), 'code=' . sql_quote('GG'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('GHA'), 'code_num'=>288), 'code=' . sql_quote('GH'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('GIB'), 'code_num'=>292), 'code=' . sql_quote('GI'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('GRL'), 'code_num'=>304), 'code=' . sql_quote('GL'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('GMB'), 'code_num'=>270), 'code=' . sql_quote('GM'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('GIN'), 'code_num'=>324), 'code=' . sql_quote('GN'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('GLP'), 'code_num'=>312), 'code=' . sql_quote('GP'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('GNQ'), 'code_num'=>226), 'code=' . sql_quote('GQ'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('GRC'), 'code_num'=>300), 'code=' . sql_quote('GR'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('SGS'), 'code_num'=>239), 'code=' . sql_quote('GS'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('GTM'), 'code_num'=>320), 'code=' . sql_quote('GT'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('GUM'), 'code_num'=>316), 'code=' . sql_quote('GU'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('GNB'), 'code_num'=>624), 'code=' . sql_quote('GW'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('GUY'), 'code_num'=>328), 'code=' . sql_quote('GY'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('HKG'), 'code_num'=>344), 'code=' . sql_quote('HK'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('HMD'), 'code_num'=>334), 'code=' . sql_quote('HM'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('HND'), 'code_num'=>340), 'code=' . sql_quote('HN'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('HRV'), 'code_num'=>191), 'code=' . sql_quote('HR'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('HTI'), 'code_num'=>332), 'code=' . sql_quote('HT'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('HUN'), 'code_num'=>348), 'code=' . sql_quote('HU'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('IDN'), 'code_num'=>360), 'code=' . sql_quote('ID'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('IRL'), 'code_num'=>372), 'code=' . sql_quote('IE'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('ISR'), 'code_num'=>376), 'code=' . sql_quote('IL'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('IMN'), 'code_num'=>833), 'code=' . sql_quote('IM'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('IND'), 'code_num'=>356), 'code=' . sql_quote('IN'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('IOT'), 'code_num'=>86), 'code=' . sql_quote('IO'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('IRQ'), 'code_num'=>368), 'code=' . sql_quote('IQ'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('IRN'), 'code_num'=>364), 'code=' . sql_quote('IR'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('ISL'), 'code_num'=>352), 'code=' . sql_quote('IS'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('ITA'), 'code_num'=>380), 'code=' . sql_quote('IT'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('JEY'), 'code_num'=>832), 'code=' . sql_quote('JE'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('JAM'), 'code_num'=>388), 'code=' . sql_quote('JM'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('JOR'), 'code_num'=>400), 'code=' . sql_quote('JO'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('JPN'), 'code_num'=>392), 'code=' . sql_quote('JP'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('KEN'), 'code_num'=>404), 'code=' . sql_quote('KE'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('KGZ'), 'code_num'=>417), 'code=' . sql_quote('KG'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('KHM'), 'code_num'=>116), 'code=' . sql_quote('KH'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('KIR'), 'code_num'=>296), 'code=' . sql_quote('KI'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('COM'), 'code_num'=>174), 'code=' . sql_quote('KM'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('KNA'), 'code_num'=>659), 'code=' . sql_quote('KN'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('PRK'), 'code_num'=>408), 'code=' . sql_quote('KP'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('KOR'), 'code_num'=>410), 'code=' . sql_quote('KR'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('KWT'), 'code_num'=>414), 'code=' . sql_quote('KW'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('CYM'), 'code_num'=>136), 'code=' . sql_quote('KY'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('KAZ'), 'code_num'=>398), 'code=' . sql_quote('KZ'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('LAO'), 'code_num'=>418), 'code=' . sql_quote('LA'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('LBN'), 'code_num'=>422), 'code=' . sql_quote('LB'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('LCA'), 'code_num'=>662), 'code=' . sql_quote('LC'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('LIE'), 'code_num'=>438), 'code=' . sql_quote('LI'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('LKA'), 'code_num'=>144), 'code=' . sql_quote('LK'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('LBR'), 'code_num'=>430), 'code=' . sql_quote('LR'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('LSO'), 'code_num'=>426), 'code=' . sql_quote('LS'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('LTU'), 'code_num'=>440), 'code=' . sql_quote('LT'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('LUX'), 'code_num'=>442), 'code=' . sql_quote('LU'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('LVA'), 'code_num'=>428), 'code=' . sql_quote('LV'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('LBY'), 'code_num'=>434), 'code=' . sql_quote('LY'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('MAR'), 'code_num'=>504), 'code=' . sql_quote('MA'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('MCO'), 'code_num'=>492), 'code=' . sql_quote('MC'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('MDA'), 'code_num'=>498), 'code=' . sql_quote('MD'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('MNE'), 'code_num'=>499), 'code=' . sql_quote('ME'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('MAF'), 'code_num'=>663), 'code=' . sql_quote('MF'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('MDG'), 'code_num'=>450), 'code=' . sql_quote('MG'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('MHL'), 'code_num'=>584), 'code=' . sql_quote('MH'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('MKD'), 'code_num'=>807), 'code=' . sql_quote('MK'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('MLI'), 'code_num'=>466), 'code=' . sql_quote('ML'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('MMR'), 'code_num'=>104), 'code=' . sql_quote('MM'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('MNG'), 'code_num'=>496), 'code=' . sql_quote('MN'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('MAC'), 'code_num'=>446), 'code=' . sql_quote('MO'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('MNP'), 'code_num'=>580), 'code=' . sql_quote('MP'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('MTQ'), 'code_num'=>474), 'code=' . sql_quote('MQ'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('MRT'), 'code_num'=>478), 'code=' . sql_quote('MR'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('MSR'), 'code_num'=>500), 'code=' . sql_quote('MS'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('MLT'), 'code_num'=>470), 'code=' . sql_quote('MT'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('MUS'), 'code_num'=>480), 'code=' . sql_quote('MU'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('MDV'), 'code_num'=>462), 'code=' . sql_quote('MV'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('MWI'), 'code_num'=>454), 'code=' . sql_quote('MW'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('MEX'), 'code_num'=>484), 'code=' . sql_quote('MX'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('MYS'), 'code_num'=>458), 'code=' . sql_quote('MY'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('MOZ'), 'code_num'=>508), 'code=' . sql_quote('MZ'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('NAM'), 'code_num'=>516), 'code=' . sql_quote('NA'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('NCL'), 'code_num'=>540), 'code=' . sql_quote('NC'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('NER'), 'code_num'=>562), 'code=' . sql_quote('NE'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('NFK'), 'code_num'=>574), 'code=' . sql_quote('NF'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('NGA'), 'code_num'=>566), 'code=' . sql_quote('NG'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('NIC'), 'code_num'=>558), 'code=' . sql_quote('NI'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('NLD'), 'code_num'=>528), 'code=' . sql_quote('NL'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('NOR'), 'code_num'=>578), 'code=' . sql_quote('NO'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('NPL'), 'code_num'=>524), 'code=' . sql_quote('NP'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('NRU'), 'code_num'=>520), 'code=' . sql_quote('NR'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('NIU'), 'code_num'=>570), 'code=' . sql_quote('NU'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('NZL'), 'code_num'=>554), 'code=' . sql_quote('NZ'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('OMN'), 'code_num'=>512), 'code=' . sql_quote('OM'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('PAN'), 'code_num'=>591), 'code=' . sql_quote('PA'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('PER'), 'code_num'=>604), 'code=' . sql_quote('PE'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('PYF'), 'code_num'=>258), 'code=' . sql_quote('PF'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('PNG'), 'code_num'=>598), 'code=' . sql_quote('PG'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('PHL'), 'code_num'=>608), 'code=' . sql_quote('PH'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('PAK'), 'code_num'=>586), 'code=' . sql_quote('PK'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('POL'), 'code_num'=>616), 'code=' . sql_quote('PL'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('SPM'), 'code_num'=>666), 'code=' . sql_quote('PM'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('PCN'), 'code_num'=>612), 'code=' . sql_quote('PN'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('PRI'), 'code_num'=>630), 'code=' . sql_quote('PR'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('PSE'), 'code_num'=>275), 'code=' . sql_quote('PS'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('PRT'), 'code_num'=>620), 'code=' . sql_quote('PT'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('PLW'), 'code_num'=>585), 'code=' . sql_quote('PW'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('PRY'), 'code_num'=>600), 'code=' . sql_quote('PY'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('QAT'), 'code_num'=>634), 'code=' . sql_quote('QA'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('REU'), 'code_num'=>638), 'code=' . sql_quote('RE'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('ROU'), 'code_num'=>642), 'code=' . sql_quote('RO'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('SRB'), 'code_num'=>688), 'code=' . sql_quote('RS'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('RUS'), 'code_num'=>643), 'code=' . sql_quote('RU'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('RWA'), 'code_num'=>646), 'code=' . sql_quote('RW'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('SAU'), 'code_num'=>682), 'code=' . sql_quote('SA'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('SLB'), 'code_num'=>90), 'code=' . sql_quote('SB'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('SYC'), 'code_num'=>690), 'code=' . sql_quote('SC'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('SDN'), 'code_num'=>729), 'code=' . sql_quote('SD'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('SWE'), 'code_num'=>752), 'code=' . sql_quote('SE'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('SGP'), 'code_num'=>702), 'code=' . sql_quote('SG'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('SHN'), 'code_num'=>654), 'code=' . sql_quote('SH'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('SVN'), 'code_num'=>705), 'code=' . sql_quote('SI'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('SJM'), 'code_num'=>744), 'code=' . sql_quote('SJ'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('SVK'), 'code_num'=>703), 'code=' . sql_quote('SK'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('SLE'), 'code_num'=>694), 'code=' . sql_quote('SL'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('SMR'), 'code_num'=>674), 'code=' . sql_quote('SM'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('SEN'), 'code_num'=>686), 'code=' . sql_quote('SN'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('SOM'), 'code_num'=>706), 'code=' . sql_quote('SO'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('SUR'), 'code_num'=>740), 'code=' . sql_quote('SR'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('SSD'), 'code_num'=>728), 'code=' . sql_quote('SS'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('STP'), 'code_num'=>678), 'code=' . sql_quote('ST'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('SLV'), 'code_num'=>222), 'code=' . sql_quote('SV'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('SXM'), 'code_num'=>534), 'code=' . sql_quote('SX'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('SYR'), 'code_num'=>760), 'code=' . sql_quote('SY'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('SWZ'), 'code_num'=>748), 'code=' . sql_quote('SZ'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('TCA'), 'code_num'=>796), 'code=' . sql_quote('TC'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('TCD'), 'code_num'=>148), 'code=' . sql_quote('TD'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('ATF'), 'code_num'=>260), 'code=' . sql_quote('TF'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('TGO'), 'code_num'=>768), 'code=' . sql_quote('TG'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('THA'), 'code_num'=>764), 'code=' . sql_quote('TH'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('TJK'), 'code_num'=>762), 'code=' . sql_quote('TJ'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('TKL'), 'code_num'=>772), 'code=' . sql_quote('TK'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('TLS'), 'code_num'=>626), 'code=' . sql_quote('TL'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('TKM'), 'code_num'=>795), 'code=' . sql_quote('TM'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('TUN'), 'code_num'=>788), 'code=' . sql_quote('TN'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('TON'), 'code_num'=>776), 'code=' . sql_quote('TO'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('TUR'), 'code_num'=>792), 'code=' . sql_quote('TR'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('TTO'), 'code_num'=>780), 'code=' . sql_quote('TT'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('TUV'), 'code_num'=>798), 'code=' . sql_quote('TV'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('TWN'), 'code_num'=>158), 'code=' . sql_quote('TW'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('TZA'), 'code_num'=>834), 'code=' . sql_quote('TZ'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('UKR'), 'code_num'=>804), 'code=' . sql_quote('UA'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('UGA'), 'code_num'=>800), 'code=' . sql_quote('UG'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('UMI'), 'code_num'=>581), 'code=' . sql_quote('UM'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('USA'), 'code_num'=>840), 'code=' . sql_quote('US'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('URY'), 'code_num'=>858), 'code=' . sql_quote('UY'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('UZB'), 'code_num'=>860), 'code=' . sql_quote('UZ'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('VAT'), 'code_num'=>336), 'code=' . sql_quote('VA'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('VCT'), 'code_num'=>670), 'code=' . sql_quote('VC'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('VEN'), 'code_num'=>862), 'code=' . sql_quote('VE'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('VGB'), 'code_num'=>92), 'code=' . sql_quote('VG'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('VIR'), 'code_num'=>850), 'code=' . sql_quote('VI'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('VNM'), 'code_num'=>704), 'code=' . sql_quote('VN'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('VUT'), 'code_num'=>548), 'code=' . sql_quote('VU'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('WLF'), 'code_num'=>876), 'code=' . sql_quote('WF'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('WSM'), 'code_num'=>882), 'code=' . sql_quote('WS'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('YEM'), 'code_num'=>887), 'code=' . sql_quote('YE'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('MYT'), 'code_num'=>175), 'code=' . sql_quote('YT'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('ZAF'), 'code_num'=>710), 'code=' . sql_quote('ZA'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('ZMB'), 'code_num'=>894), 'code=' . sql_quote('ZM'));
sql_update('spip_pays', array('code_alpha3'=>sql_quote('ZWE'), 'code_num'=>716), 'code=' . sql_quote('ZW'));
}


?>
