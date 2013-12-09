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
		array('maj_tables', array('spip_pays')),
		array('peupler_base_pays')
		);
	$maj['1.1.0'] = array(
		array('sql_drop_table', array('spip_pays')),
		array('maj_tables', array('spip_pays')),
		array('peupler_base_pays')
	);
	$maj['1.2.0'] = array(
		array('sql_update', array('spip_pays',array("code" => "0"),array("code='IR'", "id_pays=109", ))),
		array('sql_update', array('spip_pays',array("code" => "IR"),array("code='IQ'","id_pays=110", ))),
		array('sql_update', array('spip_pays',array("code" => "IQ"),array("code='0'", "id_pays=109", ))),
	);
	$maj['1.2.1'] = array(
		array('sql_update', array('spip_pays',array("code" => "0"),array("code='KR'", "id_pays=52", ))),
		array('sql_update', array('spip_pays',array("code" => "KP"),array("code='KR'","id_pays=51", ))),
		array('sql_update', array('spip_pays',array("code" => "KP"),array("code='0'", "id_pays=52", ))),
	);
	include_spip('base/upgrade');
	include_spip('base/pays_peupler_base');
	$maj['1.3.0'] = array(
		array('maj_130_pays'),
	);


	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}



function pays_vider_tables($nom_meta_base_version) {

	sql_drop_table("spip_pays");
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
sql_update('spip_pays', array('code_alpha3'=>'AND', 'code_num'=>20), "code='AD'");
sql_update('spip_pays', array('code_alpha3'=>'ARE', 'code_num'=>784), "code='AE'");
sql_update('spip_pays', array('code_alpha3'=>'AFG', 'code_num'=>4), "code='AF'");
sql_update('spip_pays', array('code_alpha3'=>'ATG', 'code_num'=>28), "code='AG'");
sql_update('spip_pays', array('code_alpha3'=>'AIA', 'code_num'=>660), "code='AI'");
sql_update('spip_pays', array('code_alpha3'=>'ALB', 'code_num'=>8), "code='AL'");
sql_update('spip_pays', array('code_alpha3'=>'ARM', 'code_num'=>51), "code='AM'");
sql_update('spip_pays', array('code_alpha3'=>'AGO', 'code_num'=>24), "code='AO'");
sql_update('spip_pays', array('code_alpha3'=>'ATA', 'code_num'=>10), "code='AQ'");
sql_update('spip_pays', array('code_alpha3'=>'ARG', 'code_num'=>32), "code='AR'");
sql_update('spip_pays', array('code_alpha3'=>'ASM', 'code_num'=>16), "code='AS'");
sql_update('spip_pays', array('code_alpha3'=>'AUT', 'code_num'=>40), "code='AT'");
sql_update('spip_pays', array('code_alpha3'=>'AUS', 'code_num'=>36), "code='AU'");
sql_update('spip_pays', array('code_alpha3'=>'ABW', 'code_num'=>533), "code='AW'");
sql_update('spip_pays', array('code_alpha3'=>'ALA', 'code_num'=>248), "code='AX'");
sql_update('spip_pays', array('code_alpha3'=>'AZE', 'code_num'=>31), "code='AZ'");
sql_update('spip_pays', array('code_alpha3'=>'BIH', 'code_num'=>70), "code='BA'");
sql_update('spip_pays', array('code_alpha3'=>'BRB', 'code_num'=>52), "code='BB'");
sql_update('spip_pays', array('code_alpha3'=>'BGD', 'code_num'=>50), "code='BD'");
sql_update('spip_pays', array('code_alpha3'=>'BEL', 'code_num'=>56), "code='BE'");
sql_update('spip_pays', array('code_alpha3'=>'BFA', 'code_num'=>854), "code='BF'");
sql_update('spip_pays', array('code_alpha3'=>'BGR', 'code_num'=>100), "code='BG'");
sql_update('spip_pays', array('code_alpha3'=>'BHR', 'code_num'=>48), "code='BH'");
sql_update('spip_pays', array('code_alpha3'=>'BDI', 'code_num'=>108), "code='BI'");
sql_update('spip_pays', array('code_alpha3'=>'BEN', 'code_num'=>204), "code='BJ'");
sql_update('spip_pays', array('code_alpha3'=>'BLM', 'code_num'=>652), "code='BL'");
sql_update('spip_pays', array('code_alpha3'=>'BMU', 'code_num'=>60), "code='BM'");
sql_update('spip_pays', array('code_alpha3'=>'BRN', 'code_num'=>96), "code='BN'");
sql_update('spip_pays', array('code_alpha3'=>'BOL', 'code_num'=>68), "code='BO'");
sql_update('spip_pays', array('code_alpha3'=>'BES', 'code_num'=>535), "code='BQ'");
sql_update('spip_pays', array('code_alpha3'=>'BRA', 'code_num'=>76), "code='BR'");
sql_update('spip_pays', array('code_alpha3'=>'BHS', 'code_num'=>44), "code='BS'");
sql_update('spip_pays', array('code_alpha3'=>'BTN', 'code_num'=>64), "code='BT'");
sql_update('spip_pays', array('code_alpha3'=>'BVT', 'code_num'=>74), "code='BV'");
sql_update('spip_pays', array('code_alpha3'=>'BWA', 'code_num'=>72), "code='BW'");
sql_update('spip_pays', array('code_alpha3'=>'BLR', 'code_num'=>112), "code='BY'");
sql_update('spip_pays', array('code_alpha3'=>'BLZ', 'code_num'=>84), "code='BZ'");
sql_update('spip_pays', array('code_alpha3'=>'CAN', 'code_num'=>124), "code='CA'");
sql_update('spip_pays', array('code_alpha3'=>'CCK', 'code_num'=>166), "code='CC'");
sql_update('spip_pays', array('code_alpha3'=>'COD', 'code_num'=>180), "code='CD'");
sql_update('spip_pays', array('code_alpha3'=>'CAF', 'code_num'=>140), "code='CF'");
sql_update('spip_pays', array('code_alpha3'=>'COG', 'code_num'=>178), "code='CG'");
sql_update('spip_pays', array('code_alpha3'=>'CHE', 'code_num'=>756), "code='CH'");
sql_update('spip_pays', array('code_alpha3'=>'CIV', 'code_num'=>384), "code='CI'");
sql_update('spip_pays', array('code_alpha3'=>'COK', 'code_num'=>184), "code='CK'");
sql_update('spip_pays', array('code_alpha3'=>'CHL', 'code_num'=>152), "code='CL'");
sql_update('spip_pays', array('code_alpha3'=>'CMR', 'code_num'=>120), "code='CM'");
sql_update('spip_pays', array('code_alpha3'=>'CHN', 'code_num'=>156), "code='CN'");
sql_update('spip_pays', array('code_alpha3'=>'COL', 'code_num'=>170), "code='CO'");
sql_update('spip_pays', array('code_alpha3'=>'CRI', 'code_num'=>188), "code='CR'");
sql_update('spip_pays', array('code_alpha3'=>'CUB', 'code_num'=>192), "code='CU'");
sql_update('spip_pays', array('code_alpha3'=>'CPV', 'code_num'=>132), "code='CV'");
sql_update('spip_pays', array('code_alpha3'=>'CUW', 'code_num'=>531), "code='CW'");
sql_update('spip_pays', array('code_alpha3'=>'CXR', 'code_num'=>162), "code='CX'");
sql_update('spip_pays', array('code_alpha3'=>'CYP', 'code_num'=>196), "code='CY'");
sql_update('spip_pays', array('code_alpha3'=>'CZE', 'code_num'=>203), "code='CZ'");
sql_update('spip_pays', array('code_alpha3'=>'DEU', 'code_num'=>276), "code='DE'");
sql_update('spip_pays', array('code_alpha3'=>'DJI', 'code_num'=>262), "code='DJ'");
sql_update('spip_pays', array('code_alpha3'=>'DNK', 'code_num'=>208), "code='DK'");
sql_update('spip_pays', array('code_alpha3'=>'DMA', 'code_num'=>212), "code='DM'");
sql_update('spip_pays', array('code_alpha3'=>'DOM', 'code_num'=>214), "code='DO'");
sql_update('spip_pays', array('code_alpha3'=>'DZA', 'code_num'=>12), "code='DZ'");
sql_update('spip_pays', array('code_alpha3'=>'ECU', 'code_num'=>218), "code='EC'");
sql_update('spip_pays', array('code_alpha3'=>'EST', 'code_num'=>233), "code='EE'");
sql_update('spip_pays', array('code_alpha3'=>'EGY', 'code_num'=>818), "code='EG'");
sql_update('spip_pays', array('code_alpha3'=>'ESH', 'code_num'=>732), "code='EH'");
sql_update('spip_pays', array('code_alpha3'=>'ERI', 'code_num'=>232), "code='ER'");
sql_update('spip_pays', array('code_alpha3'=>'ESP', 'code_num'=>724), "code='ES'");
sql_update('spip_pays', array('code_alpha3'=>'ETH', 'code_num'=>231), "code='ET'");
sql_update('spip_pays', array('code_alpha3'=>'FIN', 'code_num'=>246), "code='FI'");
sql_update('spip_pays', array('code_alpha3'=>'FJI', 'code_num'=>242), "code='FJ'");
sql_update('spip_pays', array('code_alpha3'=>'FLK', 'code_num'=>238), "code='FK'");
sql_update('spip_pays', array('code_alpha3'=>'FSM', 'code_num'=>583), "code='FM'");
sql_update('spip_pays', array('code_alpha3'=>'FRO', 'code_num'=>234), "code='FO'");
sql_update('spip_pays', array('code_alpha3'=>'FRA', 'code_num'=>250), "code='FR'");
sql_update('spip_pays', array('code_alpha3'=>'GAB', 'code_num'=>266), "code='GA'");
sql_update('spip_pays', array('code_alpha3'=>'GBR', 'code_num'=>826), "code='GB'");
sql_update('spip_pays', array('code_alpha3'=>'GRD', 'code_num'=>308), "code='GD'");
sql_update('spip_pays', array('code_alpha3'=>'GEO', 'code_num'=>268), "code='GE'");
sql_update('spip_pays', array('code_alpha3'=>'GUF', 'code_num'=>254), "code='GF'");
sql_update('spip_pays', array('code_alpha3'=>'GGY', 'code_num'=>831), "code='GG'");
sql_update('spip_pays', array('code_alpha3'=>'GHA', 'code_num'=>288), "code='GH'");
sql_update('spip_pays', array('code_alpha3'=>'GIB', 'code_num'=>292), "code='GI'");
sql_update('spip_pays', array('code_alpha3'=>'GRL', 'code_num'=>304), "code='GL'");
sql_update('spip_pays', array('code_alpha3'=>'GMB', 'code_num'=>270), "code='GM'");
sql_update('spip_pays', array('code_alpha3'=>'GIN', 'code_num'=>324), "code='GN'");
sql_update('spip_pays', array('code_alpha3'=>'GLP', 'code_num'=>312), "code='GP'");
sql_update('spip_pays', array('code_alpha3'=>'GNQ', 'code_num'=>226), "code='GQ'");
sql_update('spip_pays', array('code_alpha3'=>'GRC', 'code_num'=>300), "code='GR'");
sql_update('spip_pays', array('code_alpha3'=>'SGS', 'code_num'=>239), "code='GS'");
sql_update('spip_pays', array('code_alpha3'=>'GTM', 'code_num'=>320), "code='GT'");
sql_update('spip_pays', array('code_alpha3'=>'GUM', 'code_num'=>316), "code='GU'");
sql_update('spip_pays', array('code_alpha3'=>'GNB', 'code_num'=>624), "code='GW'");
sql_update('spip_pays', array('code_alpha3'=>'GUY', 'code_num'=>328), "code='GY'");
sql_update('spip_pays', array('code_alpha3'=>'HKG', 'code_num'=>344), "code='HK'");
sql_update('spip_pays', array('code_alpha3'=>'HMD', 'code_num'=>334), "code='HM'");
sql_update('spip_pays', array('code_alpha3'=>'HND', 'code_num'=>340), "code='HN'");
sql_update('spip_pays', array('code_alpha3'=>'HRV', 'code_num'=>191), "code='HR'");
sql_update('spip_pays', array('code_alpha3'=>'HTI', 'code_num'=>332), "code='HT'");
sql_update('spip_pays', array('code_alpha3'=>'HUN', 'code_num'=>348), "code='HU'");
sql_update('spip_pays', array('code_alpha3'=>'IDN', 'code_num'=>360), "code='ID'");
sql_update('spip_pays', array('code_alpha3'=>'IRL', 'code_num'=>372), "code='IE'");
sql_update('spip_pays', array('code_alpha3'=>'ISR', 'code_num'=>376), "code='IL'");
sql_update('spip_pays', array('code_alpha3'=>'IMN', 'code_num'=>833), "code='IM'");
sql_update('spip_pays', array('code_alpha3'=>'IND', 'code_num'=>356), "code='IN'");
sql_update('spip_pays', array('code_alpha3'=>'IOT', 'code_num'=>86), "code='IO'");
sql_update('spip_pays', array('code_alpha3'=>'IRQ', 'code_num'=>368), "code='IQ'");
sql_update('spip_pays', array('code_alpha3'=>'IRN', 'code_num'=>364), "code='IR'");
sql_update('spip_pays', array('code_alpha3'=>'ISL', 'code_num'=>352), "code='IS'");
sql_update('spip_pays', array('code_alpha3'=>'ITA', 'code_num'=>380), "code='IT'");
sql_update('spip_pays', array('code_alpha3'=>'JEY', 'code_num'=>832), "code='JE'");
sql_update('spip_pays', array('code_alpha3'=>'JAM', 'code_num'=>388), "code='JM'");
sql_update('spip_pays', array('code_alpha3'=>'JOR', 'code_num'=>400), "code='JO'");
sql_update('spip_pays', array('code_alpha3'=>'JPN', 'code_num'=>392), "code='JP'");
sql_update('spip_pays', array('code_alpha3'=>'KEN', 'code_num'=>404), "code='KE'");
sql_update('spip_pays', array('code_alpha3'=>'KGZ', 'code_num'=>417), "code='KG'");
sql_update('spip_pays', array('code_alpha3'=>'KHM', 'code_num'=>116), "code='KH'");
sql_update('spip_pays', array('code_alpha3'=>'KIR', 'code_num'=>296), "code='KI'");
sql_update('spip_pays', array('code_alpha3'=>'COM', 'code_num'=>174), "code='KM'");
sql_update('spip_pays', array('code_alpha3'=>'KNA', 'code_num'=>659), "code='KN'");
sql_update('spip_pays', array('code_alpha3'=>'PRK', 'code_num'=>408), "code='KP'");
sql_update('spip_pays', array('code_alpha3'=>'KOR', 'code_num'=>410), "code='KR'");
sql_update('spip_pays', array('code_alpha3'=>'KWT', 'code_num'=>414), "code='KW'");
sql_update('spip_pays', array('code_alpha3'=>'CYM', 'code_num'=>136), "code='KY'");
sql_update('spip_pays', array('code_alpha3'=>'KAZ', 'code_num'=>398), "code='KZ'");
sql_update('spip_pays', array('code_alpha3'=>'LAO', 'code_num'=>418), "code='LA'");
sql_update('spip_pays', array('code_alpha3'=>'LBN', 'code_num'=>422), "code='LB'");
sql_update('spip_pays', array('code_alpha3'=>'LCA', 'code_num'=>662), "code='LC'");
sql_update('spip_pays', array('code_alpha3'=>'LIE', 'code_num'=>438), "code='LI'");
sql_update('spip_pays', array('code_alpha3'=>'LKA', 'code_num'=>144), "code='LK'");
sql_update('spip_pays', array('code_alpha3'=>'LBR', 'code_num'=>430), "code='LR'");
sql_update('spip_pays', array('code_alpha3'=>'LSO', 'code_num'=>426), "code='LS'");
sql_update('spip_pays', array('code_alpha3'=>'LTU', 'code_num'=>440), "code='LT'");
sql_update('spip_pays', array('code_alpha3'=>'LUX', 'code_num'=>442), "code='LU'");
sql_update('spip_pays', array('code_alpha3'=>'LVA', 'code_num'=>428), "code='LV'");
sql_update('spip_pays', array('code_alpha3'=>'LBY', 'code_num'=>434), "code='LY'");
sql_update('spip_pays', array('code_alpha3'=>'MAR', 'code_num'=>504), "code='MA'");
sql_update('spip_pays', array('code_alpha3'=>'MCO', 'code_num'=>492), "code='MC'");
sql_update('spip_pays', array('code_alpha3'=>'MDA', 'code_num'=>498), "code='MD'");
sql_update('spip_pays', array('code_alpha3'=>'MNE', 'code_num'=>499), "code='ME'");
sql_update('spip_pays', array('code_alpha3'=>'MAF', 'code_num'=>663), "code='MF'");
sql_update('spip_pays', array('code_alpha3'=>'MDG', 'code_num'=>450), "code='MG'");
sql_update('spip_pays', array('code_alpha3'=>'MHL', 'code_num'=>584), "code='MH'");
sql_update('spip_pays', array('code_alpha3'=>'MKD', 'code_num'=>807), "code='MK'");
sql_update('spip_pays', array('code_alpha3'=>'MLI', 'code_num'=>466), "code='ML'");
sql_update('spip_pays', array('code_alpha3'=>'MMR', 'code_num'=>104), "code='MM'");
sql_update('spip_pays', array('code_alpha3'=>'MNG', 'code_num'=>496), "code='MN'");
sql_update('spip_pays', array('code_alpha3'=>'MAC', 'code_num'=>446), "code='MO'");
sql_update('spip_pays', array('code_alpha3'=>'MNP', 'code_num'=>580), "code='MP'");
sql_update('spip_pays', array('code_alpha3'=>'MTQ', 'code_num'=>474), "code='MQ'");
sql_update('spip_pays', array('code_alpha3'=>'MRT', 'code_num'=>478), "code='MR'");
sql_update('spip_pays', array('code_alpha3'=>'MSR', 'code_num'=>500), "code='MS'");
sql_update('spip_pays', array('code_alpha3'=>'MLT', 'code_num'=>470), "code='MT'");
sql_update('spip_pays', array('code_alpha3'=>'MUS', 'code_num'=>480), "code='MU'");
sql_update('spip_pays', array('code_alpha3'=>'MDV', 'code_num'=>462), "code='MV'");
sql_update('spip_pays', array('code_alpha3'=>'MWI', 'code_num'=>454), "code='MW'");
sql_update('spip_pays', array('code_alpha3'=>'MEX', 'code_num'=>484), "code='MX'");
sql_update('spip_pays', array('code_alpha3'=>'MYS', 'code_num'=>458), "code='MY'");
sql_update('spip_pays', array('code_alpha3'=>'MOZ', 'code_num'=>508), "code='MZ'");
sql_update('spip_pays', array('code_alpha3'=>'NAM', 'code_num'=>516), "code='NA'");
sql_update('spip_pays', array('code_alpha3'=>'NCL', 'code_num'=>540), "code='NC'");
sql_update('spip_pays', array('code_alpha3'=>'NER', 'code_num'=>562), "code='NE'");
sql_update('spip_pays', array('code_alpha3'=>'NFK', 'code_num'=>574), "code='NF'");
sql_update('spip_pays', array('code_alpha3'=>'NGA', 'code_num'=>566), "code='NG'");
sql_update('spip_pays', array('code_alpha3'=>'NIC', 'code_num'=>558), "code='NI'");
sql_update('spip_pays', array('code_alpha3'=>'NLD', 'code_num'=>528), "code='NL'");
sql_update('spip_pays', array('code_alpha3'=>'NOR', 'code_num'=>578), "code='NO'");
sql_update('spip_pays', array('code_alpha3'=>'NPL', 'code_num'=>524), "code='NP'");
sql_update('spip_pays', array('code_alpha3'=>'NRU', 'code_num'=>520), "code='NR'");
sql_update('spip_pays', array('code_alpha3'=>'NIU', 'code_num'=>570), "code='NU'");
sql_update('spip_pays', array('code_alpha3'=>'NZL', 'code_num'=>554), "code='NZ'");
sql_update('spip_pays', array('code_alpha3'=>'OMN', 'code_num'=>512), "code='OM'");
sql_update('spip_pays', array('code_alpha3'=>'PAN', 'code_num'=>591), "code='PA'");
sql_update('spip_pays', array('code_alpha3'=>'PER', 'code_num'=>604), "code='PE'");
sql_update('spip_pays', array('code_alpha3'=>'PYF', 'code_num'=>258), "code='PF'");
sql_update('spip_pays', array('code_alpha3'=>'PNG', 'code_num'=>598), "code='PG'");
sql_update('spip_pays', array('code_alpha3'=>'PHL', 'code_num'=>608), "code='PH'");
sql_update('spip_pays', array('code_alpha3'=>'PAK', 'code_num'=>586), "code='PK'");
sql_update('spip_pays', array('code_alpha3'=>'POL', 'code_num'=>616), "code='PL'");
sql_update('spip_pays', array('code_alpha3'=>'SPM', 'code_num'=>666), "code='PM'");
sql_update('spip_pays', array('code_alpha3'=>'PCN', 'code_num'=>612), "code='PN'");
sql_update('spip_pays', array('code_alpha3'=>'PRI', 'code_num'=>630), "code='PR'");
sql_update('spip_pays', array('code_alpha3'=>'PSE', 'code_num'=>275), "code='PS'");
sql_update('spip_pays', array('code_alpha3'=>'PRT', 'code_num'=>620), "code='PT'");
sql_update('spip_pays', array('code_alpha3'=>'PLW', 'code_num'=>585), "code='PW'");
sql_update('spip_pays', array('code_alpha3'=>'PRY', 'code_num'=>600), "code='PY'");
sql_update('spip_pays', array('code_alpha3'=>'QAT', 'code_num'=>634), "code='QA'");
sql_update('spip_pays', array('code_alpha3'=>'REU', 'code_num'=>638), "code='RE'");
sql_update('spip_pays', array('code_alpha3'=>'ROU', 'code_num'=>642), "code='RO'");
sql_update('spip_pays', array('code_alpha3'=>'SRB', 'code_num'=>688), "code='RS'");
sql_update('spip_pays', array('code_alpha3'=>'RUS', 'code_num'=>643), "code='RU'");
sql_update('spip_pays', array('code_alpha3'=>'RWA', 'code_num'=>646), "code='RW'");
sql_update('spip_pays', array('code_alpha3'=>'SAU', 'code_num'=>682), "code='SA'");
sql_update('spip_pays', array('code_alpha3'=>'SLB', 'code_num'=>90), "code='SB'");
sql_update('spip_pays', array('code_alpha3'=>'SYC', 'code_num'=>690), "code='SC'");
sql_update('spip_pays', array('code_alpha3'=>'SDN', 'code_num'=>729), "code='SD'");
sql_update('spip_pays', array('code_alpha3'=>'SWE', 'code_num'=>752), "code='SE'");
sql_update('spip_pays', array('code_alpha3'=>'SGP', 'code_num'=>702), "code='SG'");
sql_update('spip_pays', array('code_alpha3'=>'SHN', 'code_num'=>654), "code='SH'");
sql_update('spip_pays', array('code_alpha3'=>'SVN', 'code_num'=>705), "code='SI'");
sql_update('spip_pays', array('code_alpha3'=>'SJM', 'code_num'=>744), "code='SJ'");
sql_update('spip_pays', array('code_alpha3'=>'SVK', 'code_num'=>703), "code='SK'");
sql_update('spip_pays', array('code_alpha3'=>'SLE', 'code_num'=>694), "code='SL'");
sql_update('spip_pays', array('code_alpha3'=>'SMR', 'code_num'=>674), "code='SM'");
sql_update('spip_pays', array('code_alpha3'=>'SEN', 'code_num'=>686), "code='SN'");
sql_update('spip_pays', array('code_alpha3'=>'SOM', 'code_num'=>706), "code='SO'");
sql_update('spip_pays', array('code_alpha3'=>'SUR', 'code_num'=>740), "code='SR'");
sql_update('spip_pays', array('code_alpha3'=>'SSD', 'code_num'=>728), "code='SS'");
sql_update('spip_pays', array('code_alpha3'=>'STP', 'code_num'=>678), "code='ST'");
sql_update('spip_pays', array('code_alpha3'=>'SLV', 'code_num'=>222), "code='SV'");
sql_update('spip_pays', array('code_alpha3'=>'SXM', 'code_num'=>534), "code='SX'");
sql_update('spip_pays', array('code_alpha3'=>'SYR', 'code_num'=>760), "code='SY'");
sql_update('spip_pays', array('code_alpha3'=>'SWZ', 'code_num'=>748), "code='SZ'");
sql_update('spip_pays', array('code_alpha3'=>'TCA', 'code_num'=>796), "code='TC'");
sql_update('spip_pays', array('code_alpha3'=>'TCD', 'code_num'=>148), "code='TD'");
sql_update('spip_pays', array('code_alpha3'=>'ATF', 'code_num'=>260), "code='TF'");
sql_update('spip_pays', array('code_alpha3'=>'TGO', 'code_num'=>768), "code='TG'");
sql_update('spip_pays', array('code_alpha3'=>'THA', 'code_num'=>764), "code='TH'");
sql_update('spip_pays', array('code_alpha3'=>'TJK', 'code_num'=>762), "code='TJ'");
sql_update('spip_pays', array('code_alpha3'=>'TKL', 'code_num'=>772), "code='TK'");
sql_update('spip_pays', array('code_alpha3'=>'TLS', 'code_num'=>626), "code='TL'");
sql_update('spip_pays', array('code_alpha3'=>'TKM', 'code_num'=>795), "code='TM'");
sql_update('spip_pays', array('code_alpha3'=>'TUN', 'code_num'=>788), "code='TN'");
sql_update('spip_pays', array('code_alpha3'=>'TON', 'code_num'=>776), "code='TO'");
sql_update('spip_pays', array('code_alpha3'=>'TUR', 'code_num'=>792), "code='TR'");
sql_update('spip_pays', array('code_alpha3'=>'TTO', 'code_num'=>780), "code='TT'");
sql_update('spip_pays', array('code_alpha3'=>'TUV', 'code_num'=>798), "code='TV'");
sql_update('spip_pays', array('code_alpha3'=>'TWN', 'code_num'=>158), "code='TW'");
sql_update('spip_pays', array('code_alpha3'=>'TZA', 'code_num'=>834), "code='TZ'");
sql_update('spip_pays', array('code_alpha3'=>'UKR', 'code_num'=>804), "code='UA'");
sql_update('spip_pays', array('code_alpha3'=>'UGA', 'code_num'=>800), "code='UG'");
sql_update('spip_pays', array('code_alpha3'=>'UMI', 'code_num'=>581), "code='UM'");
sql_update('spip_pays', array('code_alpha3'=>'USA', 'code_num'=>840), "code='US'");
sql_update('spip_pays', array('code_alpha3'=>'URY', 'code_num'=>858), "code='UY'");
sql_update('spip_pays', array('code_alpha3'=>'UZB', 'code_num'=>860), "code='UZ'");
sql_update('spip_pays', array('code_alpha3'=>'VAT', 'code_num'=>336), "code='VA'");
sql_update('spip_pays', array('code_alpha3'=>'VCT', 'code_num'=>670), "code='VC'");
sql_update('spip_pays', array('code_alpha3'=>'VEN', 'code_num'=>862), "code='VE'");
sql_update('spip_pays', array('code_alpha3'=>'VGB', 'code_num'=>92), "code='VG'");
sql_update('spip_pays', array('code_alpha3'=>'VIR', 'code_num'=>850), "code='VI'");
sql_update('spip_pays', array('code_alpha3'=>'VNM', 'code_num'=>704), "code='VN'");
sql_update('spip_pays', array('code_alpha3'=>'VUT', 'code_num'=>548), "code='VU'");
sql_update('spip_pays', array('code_alpha3'=>'WLF', 'code_num'=>876), "code='WF'");
sql_update('spip_pays', array('code_alpha3'=>'WSM', 'code_num'=>882), "code='WS'");
sql_update('spip_pays', array('code_alpha3'=>'YEM', 'code_num'=>887), "code='YE'");
sql_update('spip_pays', array('code_alpha3'=>'MYT', 'code_num'=>175), "code='YT'");
sql_update('spip_pays', array('code_alpha3'=>'ZAF', 'code_num'=>710), "code='ZA'");
sql_update('spip_pays', array('code_alpha3'=>'ZMB', 'code_num'=>894), "code='ZM'");
sql_update('spip_pays', array('code_alpha3'=>'ZWE', 'code_num'=>716), "code='ZW'");
}


?>
