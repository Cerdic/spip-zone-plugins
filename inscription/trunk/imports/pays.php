<?php
/**
 * Plugin Inscription3 pour SPIP
 * © 2007-2010 - cmtmt, BoOz, kent1
 * Licence GPL v3
 *
 * Liste des pays à insérer dans spip_geo_pays
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

$GLOBALS['liste_pays'] = array(

// A
	1=>array('code_iso' => 'AF','nom' => '<multi>[fr]Afghanistan[en]Afghanistan[de]Afghanistan[es]Afganist&aacute;n[it]Afghanistan[nl]Afghanistan[pt]Afeganist&atilde;o</multi>'),
	2=>array('code_iso' => 'ZA','nom' => '<multi>[fr]Afrique du Sud[en]South Africa[de]S&uuml;d-Afrika[es]Sud&aacute;frica[it]Sud Africa[nl]Zuid-Afrika[pt]&aacute;frica do Sul</multi>'),
	3=>array('code_iso' => 'AX','nom' => '<multi>[fr]&Aring;land[en]&Aring;land[de]&Aring;land[es]&Aring;land[it]&Aring;land[nl]&Aring;land[pt]&Aring;land</multi>'),
	4=>array('code_iso' => 'AL','nom' => '<multi>[fr]Albanie[en]Albania[de]Albanien[es]Albania[it]Albania[nl]Albani&euml;[pt]Alb&acirc;nia</multi>'),
	5=>array('code_iso' => 'DZ','nom' => '<multi>[fr]Alg&eacute;rie[en]Algeria[de]Algerien[es]Argelia[it]Algeria[nl]Algerije[pt]Arg&eacute;lia</multi>'),
	6=>array('code_iso' => 'DE','nom' => '<multi>[fr]Allemagne[en]Germany[de]Deutschland[es]Alemania[it]Germania[nl]Duitsland[pt]Alemanha</multi>'),
	7=>array('code_iso' => 'AD','nom' => '<multi>[fr]Andorre[en]Andorra[de]Andorra[es]Andorra[it]Andorra[nl]Andorra[pt]Andorra</multi>'),
	8=>array('code_iso' => 'AO','nom' => '<multi>[fr]Angola[en]Angola[de]Angola[es]Angola[it]Angola[nl]Angola[pt]Angola</multi>'),
	9=>array('code_iso' => 'AI','nom' => '<multi>[fr]Anguilla[en]Anguilla[de]Anguilla[es]Anguila[it]Anguilla[nl]Anguilla[pt]Anguilla</multi>'),
	10=>array('code_iso' => 'AQ','nom' => '<multi>[fr]Antarctique[en]Antarctica[de]Antarktis[es]Ant&aacute;rtida[it]Antartide[pt]Ant&aacute;rtica</multi>'),
	11=>array('code_iso' => 'AG','nom' => '<multi>[fr]Antigua-et-Barbuda[en]Antigua and Barbuda[de]Antigua und Barbuda[es]Antigua y Barbuda[it]Antigua e Barbuda[nl]Antigua en Barbuda[pt]Antigua e Barbuda</multi>'),
	12=>array('code_iso' => 'AN','nom' => '<multi>[fr]Antilles n&eacute;erlandaises[en]Netherlands Antilles[de]Niederl&auml;ndische Antillen[es]Antillas Neerlandesas[it]Antille olandesi[nl]Nederlandse Antillen[pt]Antilhas Holandesas</multi>'),
	13=>array('code_iso' => 'SA','nom' => '<multi>[fr]Arabie saoudite[en]Saudi Arabia[de]Saudi-Arabien[es]Arabia Saudita[it]Arabia Saudita[nl]Saoedi-Arabi&euml;[pt]Ar&aacute;bia Saudita</multi>'),
	14=>array('code_iso' => 'AR','nom' => '<multi>[fr]Argentine[en]Argentina[de]Argentinien[es]Argentina[it]Argentina[nl]Argentini&euml;[pt]Argentina</multi>'),
	15=>array('code_iso' => 'AM','nom' => '<multi>[fr]Arm&eacute;nie[en]Armenia[de]Armenien[es]Armenia[it]Armenia[nl]Armeni&euml;[pt]Arm&eacute;nia</multi>'),
	16=>array('code_iso' => 'AW','nom' => '<multi>[fr]Aruba[en]Aruba[de]Aruba[es]Aruba[it]Aruba[nl]Aruba[pt]Aruba</multi>'),
	17=>array('code_iso' => 'AU','nom' => '<multi>[fr]Australie[en]Australia[de]Australien[es]Australia[it]Australia[nl]Australi&euml;[pt]Austr&aacute;lia</multi>'),
	18=>array('code_iso' => 'AT','nom' => '<multi>[fr]Autriche[en]Austria[de]&Ouml;sterreich[es]Austria[it]Austria[nl]Oostenrijk[pt]&aacute;ustria</multi>'),
	19=>array('code_iso' => 'AZ','nom' => '<multi>[fr]Azerba&iuml;djan[en]Azerbaijan[de]Aserbaidschan[es]Azerbaiy&aacute;n[it]Azerbaigian[nl]Azerbeidzjan[pt]Azerbaij&atilde;o</multi>'),

	// B
	20=>array('code_iso' => 'BS','nom' => "<multi>[fr]Bahamas[en]Bahamas[de]Bahamas[es]Bahamas[it]Bahamas[nl]Bahama's[pt]Bahamas</multi>"),
	21=>array('code_iso' => 'BH','nom' => '<multi>[fr]Bahre&iuml;n[en]Bahrain[de]Bahrain[es]Bahrein[it]Bahrein[nl]Bahrein[pt]Bahrein</multi>'),
	22=>array('code_iso' => 'BD','nom' => '<multi>[fr]Bangladesh[en]Bangladesh[de]Bangladesch[es]Bangladesh[it]Bangladesh[nl]Bangladesh[pt]Bangladesh</multi>'),
	23=>array('code_iso' => 'BB','nom' => '<multi>[fr]Barbade[en]Barbados[de]Barbados[es]Barbados[it]Barbados[nl]Barbados[pt]Barbados</multi>'),
	24=>array('code_iso' => 'BE','nom' => '<multi>[fr]Belgique[en]Belgium[de]Belgien[es]B&eacute;lgica[it]Belgio[nl]Belgi&euml;[pt]B&eacute;lgica</multi>'),
	25=>array('code_iso' => 'BZ','nom' => '<multi>[fr]Belize[en]Belize[de]Belize[es]Belice[it]Belize[nl]Belize[pt]Belize</multi>'),
	26=>array('code_iso' => 'BJ','nom' => '<multi>[fr]B&eacute;nin[en]Benin[de]Benin[es]Benin[it]Benin[nl]Benin[pt]Benin</multi>'),
	27=>array('code_iso' => 'BM','nom' => '<multi>[fr]Bermudes[en]Bermuda[de]Bermuda[es]Bermudas[it]Bermuda[nl]Bermuda[pt]Bermudas</multi>'),
	28=>array('code_iso' => 'BT','nom' => '<multi>[fr]Bhoutan[en]Bhutan[de]Bhutan[es]But&aacute;n[it]Bhutan[nl]Bhutan[pt]But&atilde;o</multi>'),
	29=>array('code_iso' => 'BY','nom' => '<multi>[fr]Bi&eacute;lorussie[en]Belarus[de]Wei&#167;russland[es]Bielorrusia[it]Bielorussia[nl]Wit-Rusland[pt]Belarus</multi>'),
	30=>array('code_iso' => 'MM','nom' => '<multi>[fr]Birmanie[en]Burma[de]Birma[es]Birmania[it]Birmania[nl]Myanmar[pt]Birm&acirc;nia</multi>'),
	31=>array('code_iso' => 'BO','nom' => '<multi>[fr]Bolivie[en]Bolivia[de]Bolivien[es]Bolivia[it]Bolivia[nl]Bolivia[pt]Bol&iacute;via</multi>'),
	32=>array('code_iso' => 'BA','nom' => '<multi>[fr]Bosnie-Herz&eacute;govine[en]Bosnia and Herzegovina[de]Bosnien-Herzegowina[es]Bosnia y Herzegovina[it]Bosnia-Erzegovina[nl]Bosni&euml; en Herzegovina[pt]B&oacute;snia e Herzegovina</multi>'),
	33=>array('code_iso' => 'BW','nom' => '<multi>[fr]Botswana[en]Botswana[de]Botswana[es]Botswana[it]Botswana[nl]Botswana[pt]Botsuana</multi>'),
	34=>array('code_iso' => 'BR','nom' => '<multi>[fr]Br&eacute;sil[en]Brazil[de]Brasilien[es]Brasil[it]Brasile[nl]Brazili&euml;[pt]Brasil</multi>'),
	35=>array('code_iso' => 'BN','nom' => '<multi>[fr]Brunei[en]Brunei[de]Brunei[es]Brunei[it]Brunei[nl]Brunei[pt]Brunei</multi>'),
	36=>array('code_iso' => 'BG','nom' => '<multi>[fr]Bulgarie[en]Bulgaria[de]Bulgarien[es]Bulgaria[it]Bulgaria[nl]Bulgarije[pt]Bulg&aacute;ria</multi>'),
	37=>array('code_iso' => 'BF','nom' => '<multi>[fr]Burkina Faso[en]Burkina Faso[de]Burkina Faso[es]Burkina Faso[it]Burkina Faso[nl]Burkina Faso[pt]Burkina Faso</multi>'),
	38=>array('code_iso' => 'BI','nom' => '<multi>[fr]Burundi[en]Burundi[de]Burundi[es]Burundi[it]Burundi[nl]Burundi[pt]Burundi</multi>'),

	// C
	39=>array('code_iso' => 'KH','nom' => '<multi>[fr]Cambodge[en]Cambodia[de]Kambodscha[es]Camboya[it]Cambogia[nl]Cambodja[pt]Camboja</multi>'),
	40=>array('code_iso' => 'CA','nom' => '<multi>[fr]Cameroun[en]Cameroon[de]Kamerun[es]Camer&uacute;n[it]Camerun[nl]Kameroen[pt]Camar&otilde;es</multi>'),
	41=>array('code_iso' => 'CM','nom' => '<multi>[fr]Canada[en]Canada[de]Kanada[es]Canad&aacute;[it]Canada[nl]Canada[pt]Canad&aacute;</multi>'),
	42=>array('code_iso' => 'CV','nom' => '<multi>[fr]Cap-Vert[en]Cape Verde[de]Kap Verde[es]Cabo Verde[it]Capo Verde[nl]Kaapverdi&euml;[pt]Cabo Verde</multi>'),
	43=>array('code_iso' => 'CF','nom' => '<multi>[fr]Centrafrique[en]Central African Republic[de]Zentralafrika[es]Rep. Centroafricana[it]Repubblica Centrafricana[nl]Centraal-Afrikaanse Republiek[pt]Rep&ugrave;blica Centro-Africana</multi>'),
	44=>array('code_iso' => 'CL','nom' => '<multi>[fr]Chili[en]Chile[de]Chile[es]Chile[it]Cile[nl]Chili[pt]Chile</multi>'),
	45=>array('code_iso' => 'CN','nom' => '<multi>[fr]Chine[en]China[de]China[es]China[it]Cina[nl]China[pt]China</multi>'),
	46=>array('code_iso' => 'CY','nom' => '<multi>[fr]Chypre[en]Cyprus[de]Zypern[es]Chipre[it]Cipro[nl]Cyprus[pt]Chipre</multi>'),
	47=>array('code_iso' => 'CO','nom' => '<multi>[fr]Colombie[en]Colombia[de]Kolumbien[es]Colombia[it]Colombia[nl]Colombia[pt]Col&ocirc;mbia</multi>'),
	48=>array('code_iso' => 'KM','nom' => '<multi>[fr]Comores[en]Comoros[de]Komoren[es]Comoras[it]Comore[nl]Comoren[pt]Comores</multi>'),
	49=>array('code_iso' => 'CG','nom' => '<multi>[fr]Congo-Brazzaville[en]Congo-Brazzaville[de]Kongo-Brazzaville[es]Congo-Brazzaville[it]Congo-Brazzaville[nl]Congo-Brazzaville[pt]Congo-Brazzaville</multi>'),
	50=>array('code_iso' => 'CD','nom' => '<multi>[fr]Congo-Kinshasa[en]Congo-Kinshasa[de]Kongo-Kinshasa[es]Congo-Kinshasa[it]Congo-Kinshasa[nl]Congo-Kinshasa[pt]Congo-Kinshasa</multi>'),
	51=>array('code_iso' => 'KR','nom' => '<multi>[fr]Cor&eacute;e du Nord[en]North Korea[de]Nordkorea[es]Corea del Norte[it]Corea del Nord[nl]Noord-Korea[pt]Coreia do Norte</multi>'),
	52=>array('code_iso' => 'KP','nom' => '<multi>[fr]Cor&eacute;e du Sud[en]South Korea[de]S&uuml;dkorea[es]Corea del Sur[it]Corea del Sud[nl]Zuid-Korea[pt]Cor&eacute;ia do Sul</multi>'),
	53=>array('code_iso' => 'CR','nom' => '<multi>[fr]Costa Rica[en]Costa Rica[de]Costa Rica[es]Costa Rica[it]Costa Rica[nl]Costa Rica[pt]Costa Rica</multi>'),
	54=>array('code_iso' => 'CI','nom' => "<multi>[fr]C&ocirc;te d'Ivoire[en]C&ocirc;te d'Ivoire[de]C&ocirc;te d'Ivoire[es]C&ocirc;te d'Ivoire[it]Costa d'Avorio[nl]Ivoorkust[pt]Costa do Marfim</multi>"),
	55=>array('code_iso' => 'HR','nom' => '<multi>[fr]Croatie[en]Croatia[de]Kroatien[es]Croacia[it]Croazia[nl]Kroati&euml;[pt]Cro&aacute;cia</multi>'),
	56=>array('code_iso' => 'CU','nom' => '<multi>[fr]Cuba[en]Cuba[de]Kuba[es]Cuba[it]Cuba[nl]Cuba[pt]Cuba</multi>'),

	// D
	57=>array('code_iso' => 'DK','nom' => '<multi>[fr]Danemark[en]Denmark[de]D&auml;nemark[es]Dinamarca[it]Danimarca[nl]Denemarken[pt]Dinamarca</multi>'),
	58=>array('code_iso' => 'DJ','nom' => '<multi>[fr]Djibouti[en]Djibouti[de]Dschibuti[es]Djibouti[it]Gibuti[nl]Djibouti[pt]Djibouti</multi>'),
	59=>array('code_iso' => 'DM','nom' => '<multi>[fr]Dominique[en]Dominica[de]Dominica[es]Dominica[it]Dominica[nl]Dominica[pt]Dominica</multi>'),

	// E
	60=>array('code_iso' => 'EG','nom' => '<multi>[fr]&Eacute;gypte[en]Egypt[de]&auml;gypten[es]Egipto[it]Egitto[nl]Egypte[pt]Egito</multi>'),
	61=>array('code_iso' => 'AE','nom' => '<multi>[fr]&Eacute;mirats arabes unis[en]United Arab Emirates[de]Vereinigte Arabische Emirate[es]Emiratos &aacute;rabes Unidos[it]Emirati Arabi Uniti[nl]Verenigde Arabische Emiraten[pt]Emirados &aacute;rabes Unidos</multi>'),
	62=>array('code_iso' => 'EC','nom' => '<multi>[fr]&Eacute;quateur[en]Ecuador[de]Ecuador[es]Ecuador[it]Ecuador[nl]Ecuador[pt]Equador</multi>'),
	63=>array('code_iso' => 'ER','nom' => '<multi>[fr]&Eacute;rythr&eacute;e[en]Eritrea[de]Eritrea[es]Eritrea[it]Eritrea[nl]Eritrea[pt]Eritreia</multi>'),
	64=>array('code_iso' => 'ES','nom' => '<multi>[fr]Espagne[en]Spain[de]Spanien[es]Espa&ntilde;a[it]Spagna[nl]Spanje[pt]Espanha</multi>'),
	65=>array('code_iso' => 'EE','nom' => '<multi>[fr]Estonie[en]Estonia[de]Estland[es]Estonia[it]Estonia[nl]Estland[pt]Est&oacute;nia</multi>'),
	66=>array('code_iso' => 'US','nom' => '<multi>[fr]&Eacute;tats-Unis[en]United States[de]Vereinigte Staaten[es]Estados Unidos[it]Stati Uniti[nl]Verenigde Staten[pt]Estados Unidos</multi>'),
	67=>array('code_iso' => 'ET','nom' => '<multi>[fr]&Eacute;thiopie[en]Ethiopia[de]&auml;thiopien[es]Etiop&iacute;a[it]Etiopia[nl]Ethiopi&euml;[pt]Eti&oacute;pia</multi>'),

	// F
	68=>array('code_iso' => 'FJ','nom' => '<multi>[fr]Fidji[en]Fiji[de]Fidschi[es]Fiji[it]Figi[nl]Fiji[pt]Fiji</multi>'),
	69=>array('code_iso' => 'FI','nom' => '<multi>[fr]Finlande[en]Finland[de]Finnland[es]Finlandia[it]Finlandia[nl]Finland[pt]Finl&acirc;ndia</multi>'),
	70=>array('code_iso' => 'FR','nom' => '<multi>[fr]France[en]France[de]Frankreich[es]Francia[it]Francia[nl]Frankrijk[pt]Fran&ccedil;a</multi>'),

	// G
	71=>array('code_iso' => 'GA','nom' => '<multi>[fr]Gabon[en]Gabon[de]Gabun[es]Gab&oacute;n[it]Gabon[nl]Gabon[pt]Gab&atilde;o</multi>'),
	72=>array('code_iso' => 'GM','nom' => '<multi>[fr]Gambie[en]Gambia[de]Gambia[es]Gambia[it]Gambia[nl]Gambia[pt]G&acirc;mbia</multi>'),
	73=>array('code_iso' => 'GS','nom' => '<multi>[fr]G&eacute;orgie[en]Georgia[de]Georgien[es]Georgia[it]Georgia[nl]Georgi&euml;[pt]Ge&oacute;rgia</multi>'),
	74=>array('code_iso' => 'GE','nom' => '<multi>[fr]G&eacute;orgie du Sud et les &icirc;les Sandwich du Sud[en]South Georgia and the South Sandwich Islands[de]S&uuml;d-Georgien und die S&uuml;d-Sandwich-Inseln[es]Georgia del Sur y las Islas Sandwich del Sur[it]Georgia del Sud e il Sud e Isole Sandwich[nl]Zuid-Georgi&euml; en de Zuidelijke Sandwicheilanden[pt]Ge&oacute;rgia do Sul e Sandwich do Sul Ilhas</multi>'),
	75=>array('code_iso' => 'GH','nom' => '<multi>[fr]Ghana[en]Ghana[de]Ghana[es]Ghana[it]Ghana[nl]Ghana[pt]Gana</multi>'),
	76=>array('code_iso' => 'GI','nom' => '<multi>[fr]Gibraltar[en]Gibraltar[de]Gibraltar[es]Gibraltar[it]Gibilterra[nl]Gibraltar[pt]Gibraltar</multi>'),
	77=>array('code_iso' => 'GR','nom' => '<multi>[fr]Gr&egrave;ce[en]Greece[de]Griechenland[es]Grecia[it]Grecia[nl]Griekenland[pt]Gr&eacute;cia</multi>'),
	78=>array('code_iso' => 'GD','nom' => '<multi>[fr]Grenade[en]Grenada[de]Granada[es]Granada[it]Grenada[nl]Grenada[pt]Grenada</multi>'),
	79=>array('code_iso' => 'GL','nom' => '<multi>[fr]Groenland[en]Greenland[de]Gr&Ouml;nland[es]Groenlandia[it]Groenlandia[nl]Groenland[pt]Gronel&acirc;ndia</multi>'),
	80=>array('code_iso' => 'GP','nom' => '<multi>[fr]Guadeloupe[en]Guadeloupe[de]Guadeloupe[es]Guadalupe[it]Guadalupa[nl]Guadeloupe[pt]Guadalupe</multi>'),
	81=>array('code_iso' => 'GU','nom' => '<multi>[fr]Guam[en]Guam[de]Guam[es]Guam[it]Guam[nl]Guam[pt]Guam</multi>'),
	82=>array('code_iso' => 'GT','nom' => '<multi>[fr]Guatemala[en]Guatemala[de]Guatemala[es]Guatemala[it]Guatemala[nl]Guatemala[pt]Guatemala</multi>'),
	83=>array('code_iso' => 'GG','nom' => '<multi>[fr]Guernesey[en]Guernsey[de]Guernsey[es]Guernsey[it]Guernsey[nl]Guernsey[pt]Guernsey</multi>'),
	84=>array('code_iso' => 'GN','nom' => '<multi>[fr]Guin&eacute;e[en]Guinea[de]Guinea[es]Guinea[it]Guinea[nl]Guinee[pt]Guin&eacute;</multi>'),
	85=>array('code_iso' => 'GW','nom' => '<multi>[fr]Guin&eacute;e &eacute;quatoriale[en]Equatorial Guinea[de]&auml;quatorialguinea[es]Guinea Ecuatorial[it]Guinea Equatoriale[nl]Equatoriaal-Guinea[pt]Guin&eacute; Equatorial</multi>'),
	86=>array('code_iso' => 'GQ','nom' => '<multi>[fr]Guin&eacute;e-Bissau[en]Guinea-Bissau[de]Guinea-Bissau[es]Guinea-Bissau[it]Guinea-Bissau[nl]Guinee-Bissau[pt]Guin&eacute;-Bissau</multi>'),
	87=>array('code_iso' => 'GY','nom' => '<multi>[fr]Guyana[en]Guyana[de]Guyana[es]Guyana[it]Guyana[nl]Guyana[pt]Guiana</multi>'),
	88=>array('code_iso' => 'GF','nom' => '<multi>[fr]Guyane[en]Guyana[de]Guyana[es]Guyana[it]Guyana[nl]Frans-Guyana[pt]Guiana</multi>'),

	// H
	89=>array('code_iso' => 'HT','nom' => '<multi>[fr]Ha&iuml;ti[en]Haiti[de]Haiti[es]Hait&iacute;[it]Haiti[nl]Ha&iuml;ti[pt]Haiti</multi>'),
	90=>array('code_iso' => 'HN','nom' => '<multi>[fr]Honduras[en]Honduras[de]Honduras[es]Honduras[it]Honduras[nl]Honduras[pt]Honduras</multi>'),
	91=>array('code_iso' => 'HK','nom' => '<multi>[fr]Hong Kong[en]Hong Kong[de]Hong Kong[es]Hong Kong[it]Hong Kong[nl]Hongkong[pt]Hong Kong</multi>'),
	92=>array('code_iso' => 'HU','nom' => '<multi>[fr]Hongrie[en]Hungary[de]Ungarn[es]Hungr&iacute;a[it]Ungheria[nl]Hongarije[pt]Hungria</multi>'),

	// I
	93=>array('code_iso' => 'BV','nom' => '<multi>[fr]&Icirc;le Bouvet[en]Bouvet Island[de]Bouvet-Insel[es]Isla Bouvet[it]Isola Bouvet[nl]Bouvet[pt]Ilha Bouvet</multi>'),
	94=>array('code_iso' => 'CX','nom' => '<multi>[fr]&Icirc;le Christmas[en]Christmas Island[de]Osterinsel[es]Christmas Island[it]Isola di Natale[nl]Christmaseiland[pt]Christmas Island</multi>'),
	95=>array('code_iso' => 'IM','nom' => '<multi>[fr]&Icirc;le de Man[en]Isle of Man[de]Isle of Man[es]Isla de Man[it]Isola di Man[nl]Man[pt]Isle of Man</multi>'),
	96=>array('code_iso' => 'KY','nom' => '<multi>[fr]&Icirc;les Ca&iuml;manes[en]Cayman Islands[de]Kaimaninseln[es]De las Islas Caim&aacute;n[it]Isole Cayman[nl]Caymaneilanden[pt]Ilhas Cayman</multi>'),
	97=>array('code_iso' => 'CC','nom' => '<multi>[fr]&Icirc;les Cocos[en]Cocos[de]Kokosinseln[es]Cocos[it]Isole Cocos[nl]Cocoseilanden[pt]Cocos</multi>'),
	98=>array('code_iso' => 'CK','nom' => '<multi>[fr]&Icirc;les Cook[en]Cook Islands[de]Cook-Inseln[es]Islas Cook[it]Isole Cook[nl]Cookeilanden[pt]Ilhas Cook</multi>'),
	99=>array('code_iso' => 'FO','nom' => '<multi>[fr]&Icirc;les F&eacute;ro&eacute;[en]Faroe Islands[de]F&auml;r&Ouml;er-Inseln[es]Islas Feroe[it]Isole Faroe[nl]FaerÃ¶er[pt]Ilhas Faroe</multi>'),
	100=>array('code_iso' => 'FK','nom' => '<multi>[fr]&Icirc;les Malouines (Falkland)[en]Islas Malvinas (Falkland)[de]Falklandinseln (Falkland)[es]Islas Malvinas (Falkland)[it]Islas Malvinas (Falkland)[nl]Falklandeilanden[pt]Islas Malvinas (Falkland)</multi>'),
	101=>array('code_iso' => 'MP','nom' => '<multi>[fr]&Icirc;les Mariannes du Nord[en]Northern Mariana Islands[de]N&Ouml;rdliche Marianen[es]Islas Marianas del Norte[it]Isole Marianne Settentrionali[nl]Noordelijke Marianen[pt]Ilhas Marianas do Norte</multi>'),
	102=>array('code_iso' => 'UM','nom' => '<multi>[fr]&Icirc;les mineures &eacute;loign&eacute;es des &Eacute;tats-Unis[en]Minor Outlying Islands of the United States[de]Kleinere entlegenen Inseln der USA[es]Islas menores alejadas de los Estados Unidos[it]Isole Minori degli Stati Uniti[nl]Kleine afgelegen eilanden van de Verenigde Staten[pt]Territ&oacute;rios Insulares dos Estados Unidos da</multi>'),
	103=>array('code_iso' => 'SB','nom' => '<multi>[fr]&Icirc;les Salomon[en]Solomon Islands[de]Salomonen[es]Islas Salom&oacute;n[it]Isole Salomone[nl]Salomonseilanden[pt]Ilhas Salom&atilde;o</multi>'),
	104=>array('code_iso' => 'TC','nom' => '<multi>[fr]&Icirc;les Turques et Ca&iuml;ques[en]Turks and Caicos Islands[de]Turks-und Caicosinseln[es]Islas Turcas y Caicos[it]Isole Turks e Caicos[nl]Turks- en Caicoseilanden[pt]Ilhas Turcas e Caicos</multi>'),
	105=>array('code_iso' => 'VI','nom' => '<multi>[fr]&Icirc;les Vierges am&eacute;ricaines[en]United States Virgin Islands[de]Amerikanische Jungferninseln[es]Islas V&iacute;rgenes de los Estados Unidos[it]United States Virgin Islands[nl]Amerikaanse Maagdeneilanden[pt]Estados Unidos Ilhas Virgens</multi>'),
	106=>array('code_iso' => 'VG','nom' => '<multi>[fr]&Icirc;les Vierges britanniques[en]BVI[de]Britische Jungferninseln[es]Islas V&iacute;rgenes Brit&aacute;nicas[it]BVI[nl]Britse Maagdeneilanden[pt]BVI</multi>'),
	107=>array('code_iso' => 'IN','nom' => '<multi>[fr]Inde[en]India[de]Indien[es]India[it]India[nl]India[pt]&iacute;ndia</multi>'),
	108=>array('code_iso' => 'ID','nom' => '<multi>[fr]Indon&eacute;sie[en]Indonesia[de]Indonesien[es]Indonesia[it]Indonesia[nl]Indonesi&euml;[pt]Indon&eacute;sia</multi>'),
	109=>array('code_iso' => 'IR','nom' => '<multi>[fr]Irak[en]Iraq[de]Irak[es]Iraq[it]Iraq[nl]Irak[pt]Iraque</multi>'),
	110=>array('code_iso' => 'IQ','nom' => '<multi>[fr]Iran[en]Iran[de]Iran[es]Ir&aacute;n[it]Iran[nl]Iran[pt]Ir&atilde;o</multi>'),
	111=>array('code_iso' => 'IE','nom' => '<multi>[fr]Irlande[en]Ireland[de]Irland[es]Irlanda[it]Irlanda[nl]Ierland[pt]Irlanda</multi>'),
	112=>array('code_iso' => 'IS','nom' => '<multi>[fr]Islande[en]Iceland[de]Island[es]Islandia[it]Islanda[nl]IJsland[pt]Isl&acirc;ndia</multi>'),
	113=>array('code_iso' => 'IL','nom' => '<multi>[fr]Isra&euml;l[en]Israel[de]Israel[es]Israel[it]Israele[nl]Isra&euml;l[pt]Israel</multi>'),
	114=>array('code_iso' => 'IT','nom' => '<multi>[fr]Italie[en]Italy[de]Italien[es]Italia[it]Italia[nl]Itali&euml;[pt]It&aacute;lia</multi>'),

	// J
	115=>array('code_iso' => 'JM','nom' => '<multi>[fr]Jama&iuml;que[en]Jamaica[de]Jamaika[es]Jamaica[it]Giamaica[nl]Jamaica[pt]Jamaica</multi>'),
	116=>array('code_iso' => 'JP','nom' => '<multi>[fr]Japon[en]Japan[de]Japan[es]Jap&oacute;n[it]Giappone[nl]Japan[pt]Jap&atilde;o</multi>'),
	117=>array('code_iso' => 'JE','nom' => '<multi>[fr]Jersey[en]Jersey[de]Jersey[es]Jersey[it]Maglia[nl]Jersey[pt]Jersey</multi>'),
	118=>array('code_iso' => 'JO','nom' => '<multi>[fr]Jordanie[en]Jordan[de]Jordanien[es]Jordania[it]Giordania[nl]Jordani&euml;[pt]Jord&acirc;nia</multi>'),

	// K
	119=>array('code_iso' => 'KZ','nom' => '<multi>[fr]Kazakhstan[en]Kazakhstan[de]Kasachstan[es]Kazajst&aacute;n[it]Kazakistan[nl]Kazachstan[pt]Cazaquist&atilde;o</multi>'),
	120=>array('code_iso' => 'KE','nom' => '<multi>[fr]Kenya[en]Kenya[de]Kenia[es]Kenia[it]Kenya[nl]Kenia[pt]Qu&eacute;nia</multi>'),
	121=>array('code_iso' => 'KG','nom' => '<multi>[fr]Kirghizstan[en]Kyrgyzstan[de]Kirgisistan[es]Kirguist&aacute;n[it]Kirghizistan[nl]Kirgizi&euml;[pt]Quirguist&atilde;o</multi>'),
	122=>array('code_iso' => 'KI','nom' => '<multi>[fr]Kiribati[en]Kiribati[de]Kiribati[es]Kiribati[it]Kiribati[nl]Kiribati[pt]Kiribati</multi>'),
	123=>array('code_iso' => 'KW','nom' => '<multi>[fr]Kowe&iuml;t[en]Kuwait[de]Kuwait[es]Kuwait[it]Kuwait[nl]Koeweit[pt]Kuwait</multi>'),

	// L
	124=>array('code_iso' => 'LA','nom' => '<multi>[fr]Laos[en]Laos[de]Laos[es]Laos[it]Laos[nl]Laos[pt]Laos</multi>'),
	125=>array('code_iso' => 'LS','nom' => '<multi>[fr]Lesotho[en]Lesotho[de]Lesotho[es]Lesotho[it]Lesotho[nl]Lesotho[pt]Lesoto</multi>'),
	126=>array('code_iso' => 'LV','nom' => '<multi>[fr]Lettonie[en]Latvia[de]Lettland[es]Letonia[it]Lettonia[nl]Letland[pt]Let&oacute;nia</multi>'),
	127=>array('code_iso' => 'LB','nom' => '<multi>[fr]Liban[en]Lebanon[de]Libanon[es]L&iacute;bano[it]Libano[nl]Libanon[pt]L&iacute;bano</multi>'),
	128=>array('code_iso' => 'LR','nom' => '<multi>[fr]Lib&eacute;ria[en]Liberia[de]Liberia[es]Liberia[it]Liberia[nl]Liberia[pt]Lib&eacute;ria</multi>'),
	129=>array('code_iso' => 'LY','nom' => '<multi>[fr]Libye[en]Libya[de]Libyen[es]Libia[it]Libia[nl]Libi&euml;[pt]L&iacute;bia</multi>'),
	130=>array('code_iso' => 'LI','nom' => '<multi>[fr]Liechtenstein[en]Liechtenstein[de]Liechtenstein[es]Liechtenstein[it]Liechtenstein[nl]Liechtenstein[pt]Liechtenstein</multi>'),
	131=>array('code_iso' => 'LT','nom' => '<multi>[fr]Lituanie[en]Lithuania[de]Litauen[es]Lituania[it]Lituania[nl]Litouwen[pt]Litu&acirc;nia</multi>'),
	132=>array('code_iso' => 'LU','nom' => '<multi>[fr]Luxembourg[en]Luxembourg[de]Luxemburg[es]Luxemburgo[it]Lussemburgo[nl]Luxemburg[pt]Luxemburgo</multi>'),

	// M
	133=>array('code_iso' => 'MO','nom' => '<multi>[fr]Macao[en]Macao[de]Macau[es]Macao[it]Macao[nl]Macau[pt]Macau</multi>'),
	134=>array('code_iso' => 'MK','nom' => '<multi>[fr]Mac&eacute;doine[en]Macedonia[de]Mazedonien[es]Macedonia[it]Macedonia[nl]Macedoni&euml;[pt]Maced&oacute;nia</multi>'),
	135=>array('code_iso' => 'MG','nom' => '<multi>[fr]Madagascar[en]Madagascar[de]Madagaskar[es]Madagascar[it]Madagascar[nl]Madagaskar[pt]Madagascar</multi>'),
	136=>array('code_iso' => 'MY','nom' => '<multi>[fr]Malaisie[en]Malaysia[de]Malaysia[es]Malasia[it]Malesia[nl]Maleisi&euml;[pt]Mal&aacute;sia</multi>'),
	137=>array('code_iso' => 'MW','nom' => '<multi>[fr]Malawi[en]Malawi[de]Malawi[es]Malawi[it]Malawi[nl]Malawi[pt]Malawi</multi>'),
	138=>array('code_iso' => 'MV','nom' => '<multi>[fr]Maldives[en]Maldives[de]Malediven[es]Maldivas[it]Maldive[nl]Maldiven[pt]Maldivas</multi>'),
	139=>array('code_iso' => 'ML','nom' => '<multi>[fr]Mali[en]Mali[de]Mali[es]Mal&iacute;[it]Mali[nl]Mali[pt]Mali</multi>'),
	140=>array('code_iso' => 'MT','nom' => '<multi>[fr]Malte[en]Malta[de]Malta[es]Malta[it]Malta[nl]Malta[pt]Malta</multi>'),
	141=>array('code_iso' => 'MA','nom' => '<multi>[fr]Maroc[en]Morocco[de]Marokko[es]Marruecos[it]Marocco[nl]Marokko[pt]Marrocos</multi>'),
	142=>array('code_iso' => 'MH','nom' => '<multi>[fr]Marshall[en]Marshall[de]Marshall[es]Marshall[it]Marshall[nl]Marshalleilanden[pt]Marshall</multi>'),
	143=>array('code_iso' => 'MQ','nom' => '<multi>[fr]Martinique[en]Martinique[de]Martinique[es]Martinica[it]Martinica[nl]Martinique[pt]Martinica</multi>'),
	144=>array('code_iso' => 'MU','nom' => '<multi>[fr]Maurice[en]Mauritius[de]Mauritius[es]Mauricio[it]Maurizio[nl]Mauritius[pt]Maur&iacute;cia</multi>'),
	145=>array('code_iso' => 'MR','nom' => '<multi>[fr]Mauritanie[en]Mauritania[de]Mauretanien[es]Mauritania[it]Mauritania[nl]Mauritani&euml;[pt]Maurit&acirc;nia</multi>'),
	146=>array('code_iso' => 'YT','nom' => '<multi>[fr]Mayotte[en]Mayotte[de]Mayotte[es]Mayotte[it]Mayotte[nl]Mayotte[pt]Mayotte</multi>'),
	147=>array('code_iso' => 'MX','nom' => '<multi>[fr]Mexique[en]Mexico[de]Mexiko[es]Mexico[it]Messico[nl]Mexico[pt]M&eacute;xico</multi>'),
	148=>array('code_iso' => 'FM','nom' => '<multi>[fr]Micron&eacute;sie[en]Micronesia[de]Mikronesien[es]Micronesia[it]Micronesia[nl]Micronesia[pt]Micron&eacute;sia</multi>'),
	149=>array('code_iso' => 'MD','nom' => '<multi>[fr]Moldavie[en]Moldova[de]Moldawien[es]Moldavia[it]Moldavia[nl]Moldavi&euml;[pt]Mold&aacute;via</multi>'),
	150=>array('code_iso' => 'MC','nom' => '<multi>[fr]Monaco[en]Monaco[de]Monaco[es]M&oacute;naco[it]Monaco[nl]Monaco[pt]Monaco</multi>'),
	151=>array('code_iso' => 'MN','nom' => '<multi>[fr]Mongolie[en]Mongolia[de]Mongolei[es]Mongolia[it]Mongolia[nl]Mongoli&euml;[pt]Mong&oacute;lia</multi>'),
	152=>array('code_iso' => 'ME','nom' => '<multi>[fr]Mont&eacute;n&eacute;gro[en]Montenegro[de]Montenegro[es]Montenegro[it]Montenegro[nl]Montenegro[pt]Montenegro</multi>'),
	153=>array('code_iso' => 'MS','nom' => '<multi>[fr]Montserrat[en]Montserrat[de]Montserrat[es]Montserrat[it]Montserrat[nl]Montserrat[pt]Montserrat</multi>'),
	154=>array('code_iso' => 'MZ','nom' => '<multi>[fr]Mozambique[en]Mozambique[de]Mosambik[es]Mozambique[it]Mozambico[nl]Mozambique[pt]Mo&ccedil;ambique</multi>'),

	// N
	155=>array('code_iso' => 'NA','nom' => '<multi>[fr]Namibie[en]Namibia[de]Namibia[es]Namibia[it]Namibia[nl]Namibi&euml;[pt]Nam&iacute;bia</multi>'),
	156=>array('code_iso' => 'NR','nom' => '<multi>[fr]Nauru[en]Nauru[de]Nauru[es]Nauru[it]Nauru[nl]Nauru[pt]Nauru</multi>'),
	157=>array('code_iso' => 'NP','nom' => '<multi>[fr]N&eacute;pal[en]Nepal[de]Nepal[es]Nepal[it]Nepal[nl]Nepal[pt]Nepal</multi>'),
	158=>array('code_iso' => 'NI','nom' => '<multi>[fr]Nicaragua[en]Nicaragua[de]Nicaragua[es]Nicaragua[it]Nicaragua[nl]Nicaragua[pt]Nicar&aacute;gua</multi>'),
	159=>array('code_iso' => 'NE','nom' => '<multi>[fr]Niger[en]Niger[de]Niger[es]N&iacute;ger[it]Niger[nl]Niger[pt]N&iacute;ger</multi>'),
	160=>array('code_iso' => 'NG','nom' => '<multi>[fr]Nigeria[en]Nigeria[de]Nigeria[es]Nigeria[it]Nigeria[nl]Nigeria[pt]Nig&eacute;ria</multi>'),
	161=>array('code_iso' => 'NU','nom' => '<multi>[fr]Niu&eacute;[en]Niue[de]Niue[es]Niue[it]Niue[nl]Niue[pt]Niue</multi>'),
	162=>array('code_iso' => 'NF','nom' => '<multi>[fr]Norfolk[en]Norfolk[de]Norfolk[es]Norfolk[it]Norfolk[nl]Norfolk[pt]Norfolk</multi>'),
	163=>array('code_iso' => 'NO','nom' => '<multi>[fr]Norv&egrave;ge[en]Norway[de]Norwegen[es]Noruega[it]Norvegia[nl]Noorwegen[pt]Noruega</multi>'),
	164=>array('code_iso' => 'NC','nom' => '<multi>[fr]Nouvelle-Cal&eacute;donie[en]New Caledonia[de]Neukaledonien[es]Nueva Caledonia[it]Nuova Caledonia[nl]Nieuw-Caledoni&euml;[pt]Nova Caled&ocirc;nia</multi>'),
	165=>array('code_iso' => 'NZ','nom' => '<multi>[fr]Nouvelle-Z&eacute;lande[en]New Zealand[de]Neuseeland[es]Nueva Zelandia[it]Nuova Zelanda[nl]Nieuw-Zeeland[pt]Nova Zel&acirc;ndia</multi>'),

	// O
	166=>array('code_iso' => 'OM','nom' => '<multi>[fr]Oman[en]Oman[de]Oman[es]Om&aacute;n[it]Oman[nl]Oman[pt]Om&atilde;</multi>'),
	167=>array('code_iso' => 'UG','nom' => '<multi>[fr]Ouganda[en]Uganda[de]Uganda[es]Uganda[it]Uganda[nl]Uganda[pt]Uganda</multi>'),
	168=>array('code_iso' => 'UZ','nom' => '<multi>[fr]Ouzb&eacute;kistan[en]Uzbekistan[de]Usbekistan[es]Uzbekist&aacute;n[it]Uzbekistan[nl]Oezbekistan[pt]Uzbequist&atilde;o</multi>'),

	// P
	169=>array('code_iso' => 'PK','nom' => '<multi>[fr]Pakistan[en]Pakistan[de]Pakistan[es]Pakist&aacute;n[it]Pakistan[nl]Pakistan[pt]Paquist&atilde;o</multi>'),
	170=>array('code_iso' => 'PW','nom' => '<multi>[fr]Palaos[en]Palau[de]Palau[es]Palau[it]Palau[nl]Palau[pt]Palau</multi>'),
	171=>array('code_iso' => 'PS','nom' => '<multi>[fr]Palestine[en]Palestine[de]Pal&auml;stina[es]Palestina[it]Palestina[nl]Palestijnse Autoriteit[pt]Palestina</multi>'),
	172=>array('code_iso' => 'PA','nom' => '<multi>[fr]Panam&aacute;[en]Panam&aacute;[de]Panama-Stadt[es]Panam&aacute;[it]Panam&aacute;[nl]Panama[pt]Panam&aacute;</multi>'),
	173=>array('code_iso' => 'PG','nom' => '<multi>[fr]Papouasie-Nouvelle-Guin&eacute;e[en]Papua New Guinea[de]Papua-Neuguinea[es]Papua Nueva Guinea[it]Papua Nuova Guinea[nl]Papoea-Nieuw-Guinea[pt]Papua Nova Guin&eacute;</multi>'),
	174=>array('code_iso' => 'PY','nom' => '<multi>[fr]Paraguay[en]Paraguay[de]Paraguay[es]Paraguay[it]Paraguay[nl]Paraguay[pt]Paraguai</multi>'),
	175=>array('code_iso' => 'NL','nom' => '<multi>[fr]Pays-Bas[en]Netherlands[de]Niederlande[es]Pa&iacute;ses Bajos[it]Paesi Bassi[nl]Nederland[[pt]Holanda</multi>'),
	176=>array('code_iso' => 'PE','nom' => '<multi>[fr]P&eacute;rou[en]Peru[de]Peru[es]Per&uacute;[it]Per&ugrave;[nl]Peru[pt]Peru</multi>'),
	177=>array('code_iso' => 'PH','nom' => '<multi>[fr]Philippines[en]Philippines[de]Philippinen[es]Filipinas[it]Filippine[nl]Filipijnen[pt]Filipinas</multi>'),
	178=>array('code_iso' => 'PN','nom' => '<multi>[fr]Pitcairn[en]Pitcairn[de]Pitcairn[es]Pitcairn[it]Pitcairn[nl]Pitcairneilanden[pt]Pitcairn</multi>'),
	179=>array('code_iso' => 'PL','nom' => '<multi>[fr]Pologne[en]Poland[de]Polen[es]Polonia[it]Polonia[nl]Polen[pt]Pol&ocirc;nia</multi>'),
	180=>array('code_iso' => 'PF','nom' => '<multi>[fr]Polyn&eacute;sie fran&ccedil;aise[en]French Polynesia[de]Franz&Ouml;sisch-Polynesien[es]Polinesia franc&eacute;s[it]Polinesia Francese[nl]Frans-Polynesi&euml;[pt]Polin&eacute;sia Francesa</multi>'),
	181=>array('code_iso' => 'PR','nom' => '<multi>[fr]Porto Rico[en]Puerto Rico[de]Puerto Rico[es]Puerto Rico[it]Puerto Rico[nl]Puerto Rico[pt]Porto Rico</multi>'),
	182=>array('code_iso' => 'PT','nom' => '<multi>[fr]Portugal[en]Portugal[de]Portugal[es]Portugal[it]Portogallo[nl]Portugal[pt]Portugal</multi>'),

	// Q
	183=>array('code_iso' => 'QA','nom' => '<multi>[fr]Qatar[en]Qatar[de]Katar[es]Qatar[it]Qatar[nl]Qatar[pt]Qatar</multi>'),

	// R
	184=>array('code_iso' => 'DO','nom' => '<multi>[fr]R&eacute;publique dominicaine[en]Dominican Republic[de]Dominikanische Republik[es]Rep&uacute;blica Dominicana[it]Repubblica Dominicana[nl]Dominicaanse Republiek[pt]Rep&uacute;blica Dominicana</multi>'),
	185=>array('code_iso' => 'CZ','nom' => '<multi>[fr]R&eacute;publique tch&egrave;que[en]Czech Republic[de]Tschechische Republik[es]Rep&uacute;blica Checa[it]Repubblica Ceca[nl]Tsjechi&euml;[pt]Rep&uacute;blica Checa</multi>'),
	186=>array('code_iso' => 'RE','nom' => '<multi>[fr]R&eacute;union[en]Reunion[de]R&eacute;union[es]Reuni&oacute;n[it]Reunion[nl]R&eacute;union[pt]Reunion</multi>'),
	187=>array('code_iso' => 'RO','nom' => '<multi>[fr]Roumanie[en]Romania[de]Rum&auml;nien[es]Rumania[it]Romania[nl]Roemeni&euml;[pt]Rom&eacute;nia</multi>'),
	188=>array('code_iso' => 'GB','nom' => '<multi>[fr]Royaume-Uni[en]United Kingdom[de]Gro&#167;britannien[es]Reino Unido[it]Regno Unito[nl]Verenigd Koninkrijk[pt]Reino Unido</multi>'),
	189=>array('code_iso' => 'RU','nom' => '<multi>[fr]Russie[en]Russia[de]Russland[es]Rusia[it]Russia[nl]Rusland[pt]R&uacute;ssia</multi>'),
	190=>array('code_iso' => 'RW','nom' => '<multi>[fr]Rwanda[en]Rwanda[de]Ruanda[es]Ruanda[it]Ruanda[nl]Rwanda[pt]Ruanda</multi>'),

	// S
	191=>array('code_iso' => 'EH','nom' => '<multi>[fr]Sahara occidental[en]Western Sahara[de]Westsahara[es]S&aacute;hara Occidental[it]Sahara Occidentale[pt]Sara Ocidental</multi>'),
	192=>array('code_iso' => 'KN','nom' => '<multi>[fr]Saint-Christophe-et-Ni&eacute;v&egrave;s[en]St. Christopher St Kitts and Nevis[de]St. Kitts und Nevis[es]St Christopher St Kitts y Nevis[it]St Christopher St Kitts e Nevis[nl]Saint Kitts en Nevis[pt]S&atilde;o Crist&oacute;v&atilde;o St Kitts e Nevis</multi>'),
	193=>array('code_iso' => 'SH','nom' => '<multi>[fr]Sainte-H&eacute;l&egrave;ne[en]St. Helena[de]St. Helena[es]Santa Elena[it]St Helena[nl]Sint-Helena, Ascension en Tristan da Cunha[pt]Santa Helena</multi>'),
	194=>array('code_iso' => 'LC','nom' => '<multi>[fr]Sainte-Lucie[en]St. Lucia[de]St. Lucia[es]Santa Luc&iacute;a[it]Santa Lucia[nl]Saint Lucia[pt]St. Lucia</multi>'),
	195=>array('code_iso' => 'SM','nom' => '<multi>[fr]Saint-Marin[en]San Marino[de]San Marino[es]San Marino[it]San Marino[nl]San Marino[pt]San Marino</multi>'),
	196=>array('code_iso' => 'PM','nom' => '<multi>[fr]Saint-Pierre-et-Miquelon[en]Saint-Pierre and Miquelon[de]Saint-Pierre und Miquelon[es]Saint-Pierre y Miquel&oacute;n[it]Saint-Pierre e Miquelon[nl]Saint-Pierre en Miquelon[pt]Saint-Pierre e Miquelon</multi>'),
	197=>array('code_iso' => 'VC','nom' => '<multi>[fr]Saint-Vincent-et-les Grenadines[en]Saint Vincent and the Grenadines[de]St. Vincent und die Grenadinen[es]San Vicente y las Granadinas[it]Saint Vincent e Grenadine[nl]Saint Vincent en de Grenadines[pt]S&atilde;o Vicente e Granadinas</multi>'),
	198=>array('code_iso' => 'SV','nom' => '<multi>[fr]Salvador[en]Salvador[de]Salvador[es]Salvador[it]Salvador[nl]El Salvador[pt]Salvador</multi>'),
	199=>array('code_iso' => 'WS','nom' => '<multi>[fr]Samoa[en]Samoa[de]Samoa[es]Samoa[it]Samoa[nl]Samoa[pt]Samoa</multi>'),
	200=>array('code_iso' => 'AS','nom' => '<multi>[fr]Samoa am&eacute;ricaines[en]American Samoa[de]Amerikanisch-Samoa[es]Samoa Americana[it]Samoa Americane[nl]Amerikaans-Samoa[pt]Samoa Americana</multi>'),
	201=>array('code_iso' => 'ST','nom' => '<multi>[fr]S&atilde;o Tom&eacute;-et-Principe[en]Sao Tome and Principe[de]S&atilde;o Tom&eacute; und Principe[es]Santo Tom&eacute; y Pr&iacute;ncipe[it]Sao Tome e Principe[pt]S&atilde;o Tom&eacute; e Pr&iacute;ncipe[nl]Sao Tom&eacute; en Principe</multi>'),
	202=>array('code_iso' => 'SN','nom' => '<multi>[fr]S&eacute;n&eacute;gal[en]Senegal[de]Senegal[es]Senegal[it]Senegal[nl]Senegal[pt]Senegal</multi>'),
	203=>array('code_iso' => 'RS','nom' => '<multi>[fr]Serbie[en]Serbia[de]Serbien[es]Serbia[it]Serbia[nl]Servi&euml;[pt]S&eacute;rvia</multi>'),
	204=>array('code_iso' => 'SC','nom' => '<multi>[fr]Seychelles[en]Seychelles[de]Seychellen[es]Seychelles[it]Seychelles[nl]Seychellen[pt]Seychelles</multi>'),
	205=>array('code_iso' => 'SL','nom' => '<multi>[fr]Sierra Leone[en]Sierra Leone[de]Sierra Leone[es]Sierra Leona[it]Sierra Leone[nl]Sierra Leone[pt]Serra Leoa</multi>'),
	206=>array('code_iso' => 'SG','nom' => '<multi>[fr]Singapour[en]Singapore[de]Singapur[es]Singapur[it]Singapore[nl]Singapore[pt]Singapura</multi>'),
	207=>array('code_iso' => 'SK','nom' => '<multi>[fr]Slovaquie[en]Slovakia[de]Slowakei[es]Eslovaquia[it]Slovacchia[nl]Slowakije[pt]Eslov&aacute;quia</multi>'),
	208=>array('code_iso' => 'SI','nom' => '<multi>[fr]Slov&eacute;nie[en]Slovenia[de]Slowenien[es]Eslovenia[it]Slovenia[nl]Sloveni&euml;[pt]Eslov&eacute;nia</multi>'),
	209=>array('code_iso' => 'SO','nom' => '<multi>[fr]Somalie[en]Somalia[de]Somalia[es]Somalia[it]Somalia[nl]Somali&euml;[pt]Som&aacute;lia</multi>'),
	210=>array('code_iso' => 'SD','nom' => '<multi>[fr]Soudan[en]Sudan[de]Sudan[es]Sud&aacute;n[it]Sudan[nl]Soedan[pt]Sud&atilde;o</multi>'),
	211=>array('code_iso' => 'LK','nom' => '<multi>[fr]Sri Lanka[en]Sri Lanka[de]Sri Lanka[es]Sri Lanka[it]Sri Lanka[nl]Sri Lanka[pt]Sri Lanka</multi>'),
	212=>array('code_iso' => 'SE','nom' => '<multi>[fr]Su&egrave;de[en]Sweden[de]Schweden[es]Suecia[it]Svezia[nl]Zweden[pt]Su&eacute;cia</multi>'),
	213=>array('code_iso' => 'CH','nom' => '<multi>[fr]Suisse[en]Switzerland[de]Schweiz[es]Suiza[it]Svizzera[nl]Zwitserland[pt]Su&iacute;&ccedil;a</multi>'),
	214=>array('code_iso' => 'SR','nom' => '<multi>[fr]Suriname[en]Suriname[de]Suriname[es]Surinam[it]Suriname[nl]Suriname[pt]Suriname</multi>'),
	215=>array('code_iso' => 'SJ','nom' => '<multi>[fr]Svalbard et &icirc;le Jan Mayen[en]Svalbard and Jan Mayen Island[de]Svalbard und Jan Mayen Insel[es]Svalbard y Jan Mayen Island[it]Svalbard e Jan Mayen, isola[nl]Jan Mayen[pt]Svalbard e Jan Mayen Island</multi>'),
	216=>array('code_iso' => 'SZ','nom' => '<multi>[fr]Swaziland[en]Swaziland[de]Swasiland[es]Swazilandia[it]Swaziland[nl]Swaziland[pt]Suazil&acirc;ndia</multi>'),
	217=>array('code_iso' => 'SY','nom' => '<multi>[fr]Syrie[en]Syria[de]Syrien[es]Siria[it]Siria[nl]Syri&euml;[pt]S&iacute;ria</multi>'),

	// T
	218=>array('code_iso' => 'TJ','nom' => '<multi>[fr]Tadjikistan[en]Tajikistan[de]Tadschikistan[es]Tayikist&aacute;n[it]Tagikistan[nl]Tadzjikistan[pt]Tajiquist&atilde;o</multi>'),
	219=>array('code_iso' => 'TW','nom' => '<multi>[fr]Ta&iuml;wan[en]Taiwan[de]Taiwan[es]Taiw&aacute;n[it]Taiwan[nl]Taiwan[pt]Taiwan</multi>'),
	220=>array('code_iso' => 'TZ','nom' => '<multi>[fr]Tanzanie[en]Tanzania[de]Tansania[es]Tanzania[it]Tanzania[nl]Tanzania[pt]Tanz&acirc;nia</multi>'),
	221=>array('code_iso' => 'TD','nom' => '<multi>[fr]Tchad[en]Chad[de]Tschad[es]Chad[it]Ciad[nl]Tsjaad[pt]Chade</multi>'),
	222=>array('code_iso' => 'TF','nom' => '<multi>[fr]Terres australes et antarctiques fran&ccedil;aises[en]French Southern and Antarctic Territories[de]Wallis und Futuna[es]Franc&eacute;s australes y ant&aacute;rticas[it]Terre australi e antartiche francesi[nl]Franse Zuidelijke en Antarctische Gebieden[pt]Territ&oacute;rios Austrais e Ant&aacute;rcticos Franceses</multi>'),
	223=>array('code_iso' => 'IO','nom' => "<multi>[fr]Territoire britannique de l'oc&eacute;an Indien[en]British Indian Ocean[de]Britisches Territorium im Indischen Ozean[es]Brit&aacute;nico del Oc&eacute;ano &iacute;ndico[it]Britannici dell'Oceano Indiano[pt]Brit&acirc;nico do Oceano &iacute;ndico[nl]Brits Territorium in de Indische Oceaan</multi>"),
	224=>array('code_iso' => '__','nom' => "<multi>[fr]Territoires ext&eacute;rieurs de l'Australie[en]External territories of Australia[de]Gebiete au&#167;erhalb von Australien[es]Territorios externos de Australia[it]Territori esterni di Australia[pt]External territ&oacute;rios da Austr&aacute;lia</multi>"),
	225=>array('code_iso' => 'TH','nom' => '<multi>[fr]Tha&iuml;lande[en]Thailand[de]Thailand[es]Tailandia[it]Thailandia[nl]Thailand[pt]Tail&acirc;ndia</multi>'),
	226=>array('code_iso' => 'TL','nom' => '<multi>[fr]Timor oriental[en]East Timor[de]Osttimor[es]Timor Oriental[it]Timor Est[nl]Oost-Timor[pt]Timor-Leste</multi>'),
	227=>array('code_iso' => 'TG','nom' => '<multi>[fr]Togo[en]Togo[de]Togo[es]Togo[it]Togo[nl]Togo[pt]Togo</multi>'),
	228=>array('code_iso' => 'TK','nom' => '<multi>[fr]Tokelau[en]Tokelau[de]Tokelau[es]Tokelau[it]Tokelau[nl]Tokelau-eilanden[pt]Tokelau</multi>'),
	229=>array('code_iso' => 'TO','nom' => '<multi>[fr]Tonga[en]Tonga[de]Tonga[es]Tonga[it]Tonga[nl]Tonga[pt]Tonga</multi>'),
	230=>array('code_iso' => 'TT','nom' => '<multi>[fr]Trinit&eacute;-et-Tobago[en]Trinidad and Tobago[de]Trinidad und Tobago[es]Trinidad y Tobago[it]Trinidad e Tobago[nl]Trinidad en Tobago[pt]Trinidad e Tobago</multi>'),
	231=>array('code_iso' => 'TN','nom' => '<multi>[fr]Tunisie[en]Tunisia[de]Tunesien[es]T&uacute;nez[it]Tunisia[nl]Tunesi&euml;[pt]Tun&iacute;sia</multi>'),
	232=>array('code_iso' => 'TM','nom' => '<multi>[fr]Turkm&eacute;nistan[en]Turkmenistan[de]Turkmenistan[es]Turkmenist&aacute;n[it]Turkmenistan[nl]Turkmenistan[pt]Turquemenist&atilde;o</multi>'),
	233=>array('code_iso' => 'TR','nom' => '<multi>[fr]Turquie[en]Turkey[de]T&uuml;rkei[es]Turqu&iacute;a[it]Turchia[nl]Turkije[pt]Turquia</multi>'),
	234=>array('code_iso' => 'TV','nom' => '<multi>[fr]Tuvalu[en]Tuvalu[de]Tuvalu[es]Tuvalu[it]Tuvalu[nl]Tuvalu[pt]Tuvalu</multi>'),

	// U
	235=>array('code_iso' => 'UA','nom' => '<multi>[fr]Ukraine[en]Ukraine[de]Ukraine[es]Ucrania[it]Ucraina[nl]Oekra&iuml;ne[[pt]Ucr&acirc;nia</multi>'),
	236=>array('code_iso' => 'UY','nom' => '<multi>[fr]Uruguay[en]Uruguay[de]Uruguay[es]Uruguay[it]Uruguay[nl]Uruguay[pt]Uruguai</multi>'),

	// V
	237=>array('code_iso' => 'VU','nom' => '<multi>[fr]Vanuatu[en]Vanuatu[de]Vanuatu[es]Vanuatu[it]Vanuatu[nl]Vanuatu[pt]Vanuatu</multi>'),
	238=>array('code_iso' => 'VA','nom' => '<multi>[fr]Vatican[en]Vatican[de]Vatikan[es]Vaticano[it]Vaticano[nl]Vaticaanstad[pt]Vaticano</multi>'),
	239=>array('code_iso' => 'VE','nom' => '<multi>[fr]V&eacute;n&eacute;zuela[en]Venezuela[de]Venezuela[es]Venezuela[it]Venezuela[nl]Venezuela[pt]Venezuela</multi>'),
	240=>array('code_iso' => 'VN','nom' => '<multi>[fr]Vi&ecirc;t Nam[en]Vietnam[de]Vietnam[es]Vietnam[it]Vietnam[nl]Vietnam[pt]Vietn&atilde;</multi>'),

	// W
	241=>array('code_iso' => 'WF','nom' => '<multi>[fr]Wallis-et-Futuna[en]Wallis and Futuna[de]Wallis und Futuna[es]Wallis y Futuna[it]Wallis e Futuna[nl]Wallis en Futuna[pt]Wallis e Futuna</multi>'),

	// Y
	242=>array('code_iso' => 'YE','nom' => '<multi>[fr]Y&eacute;men[en]Yemen[de]Jemen[es]Yemen[it]Yemen[nl]Jemen[pt]I&eacute;men</multi>'),

	// Z
	243=>array('code_iso' => 'ZM','nom' => '<multi>[fr]Zambie[en]Zambia[de]Sambia[es]Zambia[it]Zambia[nl]Zambia[pt]Z&acirc;mbia</multi>'),
	244=>array('code_iso' => 'ZW','nom' => '<multi>[fr]Zimbabwe[en]Zimbabwe[de]Simbabwe[es]Zimbabwe[it]Zimbabwe[nl]Zimbabwe[pt]Zimbabwe</multi>')
);
