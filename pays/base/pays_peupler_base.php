<?php
/**
 * Plugin Pays pour Spip 2.0
 * Licence GPL
 * Auteur Organisation Internationale de Normalisation http://www.iso.org/iso/fr/country_codes/iso_3166_code_lists.htm
 * Cedric Morin et Collectif SPIP pour version spip_geo_pays
 * Portage sous SPIP par Cyril MARION - Ateliers CYM http://www.cym.fr
 *
 */
if (!defined("_ECRIRE_INC_VERSION")) return;

function peupler_base_pays() {

	sql_insertq_multi('spip_pays', array(
		array('id_pays'=>'1','code'=>'AF','nom'=>'<multi>[fr]Afghanistan[en]Afghanistan[de]Afghanistan[es]Afganistán[it]Afghanistan[nl]Afghanistan[pt]Afeganistão</multi>'),
		array('id_pays'=>'2','code'=>'ZA','nom'=>'<multi>[fr]Afrique du Sud[en]South Africa[de]Süd-Afrika[es]Sudáfrica[it]Sud Africa[nl]Zuid-Afrika[pt]áfrica do Sul</multi>'),
		array('id_pays'=>'3','code'=>'AX','nom'=>'<multi>[fr]Åland[en]Åland[de]Åland[es]Åland[it]Åland[nl]Åland[pt]Åland</multi>'),
		array('id_pays'=>'4','code'=>'AL','nom'=>'<multi>[fr]Albanie[en]Albania[de]Albanien[es]Albania[it]Albania[nl]Albanië[pt]Albânia</multi>'),
		array('id_pays'=>'5','code'=>'DZ','nom'=>'<multi>[fr]Algérie[en]Algeria[de]Algerien[es]Argelia[it]Algeria[nl]Algerije[pt]Argélia</multi>'),
		array('id_pays'=>'6','code'=>'DE','nom'=>'<multi>[fr]Allemagne[en]Germany[de]Deutschland[es]Alemania[it]Germania[nl]Duitsland[pt]Alemanha</multi>'),
		array('id_pays'=>'7','code'=>'AD','nom'=>'<multi>[fr]Andorre[en]Andorra[de]Andorra[es]Andorra[it]Andorra[nl]Andorra[pt]Andorra</multi>'),
		array('id_pays'=>'8','code'=>'AO','nom'=>'<multi>[fr]Angola[en]Angola[de]Angola[es]Angola[it]Angola[nl]Angola[pt]Angola</multi>'),
		array('id_pays'=>'9','code'=>'AI','nom'=>'<multi>[fr]Anguilla[en]Anguilla[de]Anguilla[es]Anguila[it]Anguilla[nl]Anguilla[pt]Anguilla</multi>'),
		array('id_pays'=>'10','code'=>'AQ','nom'=>'<multi>[fr]Antarctique[en]Antarctica[de]Antarktis[es]Antártida[it]Antartide[pt]Antártica</multi>'),
		array('id_pays'=>'11','code'=>'AG','nom'=>'<multi>[fr]Antigua-et-Barbuda[en]Antigua and Barbuda[de]Antigua und Barbuda[es]Antigua y Barbuda[it]Antigua e Barbuda[nl]Antigua en Barbuda[pt]Antigua e Barbuda</multi>'),
		array('id_pays'=>'12','code'=>'AN','nom'=>'<multi>[fr]Antilles néerlandaises[en]Netherlands Antilles[de]Niederländische Antillen[es]Antillas Neerlandesas[it]Antille olandesi[nl]Nederlandse Antillen[pt]Antilhas Holandesas</multi>'),
		array('id_pays'=>'13','code'=>'SA','nom'=>'<multi>[fr]Arabie saoudite[en]Saudi Arabia[de]Saudi-Arabien[es]Arabia Saudita[it]Arabia Saudita[nl]Saoedi-Arabië[pt]Arábia Saudita</multi>'),
		array('id_pays'=>'14','code'=>'AR','nom'=>'<multi>[fr]Argentine[en]Argentina[de]Argentinien[es]Argentina[it]Argentina[nl]Argentinië[pt]Argentina</multi>'),
		array('id_pays'=>'15','code'=>'AM','nom'=>'<multi>[fr]Arménie[en]Armenia[de]Armenien[es]Armenia[it]Armenia[nl]Armenië[pt]Arménia</multi>'),
		array('id_pays'=>'16','code'=>'AW','nom'=>'<multi>[fr]Aruba[en]Aruba[de]Aruba[es]Aruba[it]Aruba[nl]Aruba[pt]Aruba</multi>'),
		array('id_pays'=>'17','code'=>'AU','nom'=>'<multi>[fr]Australie[en]Australia[de]Australien[es]Australia[it]Australia[nl]Australië[pt]Austrália</multi>'),
		array('id_pays'=>'18','code'=>'AT','nom'=>'<multi>[fr]Autriche[en]Austria[de]Österreich[es]Austria[it]Austria[nl]Oostenrijk[pt]áustria</multi>'),
		array('id_pays'=>'19','code'=>'AZ','nom'=>'<multi>[fr]Azerbaïdjan[en]Azerbaijan[de]Aserbaidschan[es]Azerbaiyán[it]Azerbaigian[nl]Azerbeidzjan[pt]Azerbaijão</multi>'),
		array('id_pays'=>'20','code'=>'BS','nom'=>'<multi>[fr]Bahamas[en]Bahamas[de]Bahamas[es]Bahamas[it]Bahamas[nl]Bahama\'s[pt]Bahamas</multi>'),
		array('id_pays'=>'21','code'=>'BH','nom'=>'<multi>[fr]Bahreïn[en]Bahrain[de]Bahrain[es]Bahrein[it]Bahrein[nl]Bahrein[pt]Bahrein</multi>'),
		array('id_pays'=>'22','code'=>'BD','nom'=>'<multi>[fr]Bangladesh[en]Bangladesh[de]Bangladesch[es]Bangladesh[it]Bangladesh[nl]Bangladesh[pt]Bangladesh</multi>'),
		array('id_pays'=>'23','code'=>'BB','nom'=>'<multi>[fr]Barbade[en]Barbados[de]Barbados[es]Barbados[it]Barbados[nl]Barbados[pt]Barbados</multi>'),
		array('id_pays'=>'24','code'=>'BE','nom'=>'<multi>[fr]Belgique[en]Belgium[de]Belgien[es]Bélgica[it]Belgio[nl]België[pt]Bélgica</multi>'),
		array('id_pays'=>'25','code'=>'BZ','nom'=>'<multi>[fr]Belize[en]Belize[de]Belize[es]Belice[it]Belize[nl]Belize[pt]Belize</multi>'),
		array('id_pays'=>'26','code'=>'BJ','nom'=>'<multi>[fr]Bénin[en]Benin[de]Benin[es]Benin[it]Benin[nl]Benin[pt]Benin</multi>'),
		array('id_pays'=>'27','code'=>'BM','nom'=>'<multi>[fr]Bermudes[en]Bermuda[de]Bermuda[es]Bermudas[it]Bermuda[nl]Bermuda[pt]Bermudas</multi>'),
		array('id_pays'=>'28','code'=>'BT','nom'=>'<multi>[fr]Bhoutan[en]Bhutan[de]Bhutan[es]Bután[it]Bhutan[nl]Bhutan[pt]Butão</multi>'),
		array('id_pays'=>'29','code'=>'BY','nom'=>'<multi>[fr]Biélorussie[en]Belarus[de]Wei§russland[es]Bielorrusia[it]Bielorussia[nl]Wit-Rusland[pt]Belarus</multi>'),
		array('id_pays'=>'30','code'=>'MM','nom'=>'<multi>[fr]Birmanie[en]Burma[de]Birma[es]Birmania[it]Birmania[nl]Myanmar[pt]Birmânia</multi>'),
		array('id_pays'=>'31','code'=>'BO','nom'=>'<multi>[fr]Bolivie[en]Bolivia[de]Bolivien[es]Bolivia[it]Bolivia[nl]Bolivia[pt]Bolívia</multi>'),
		array('id_pays'=>'32','code'=>'BA','nom'=>'<multi>[fr]Bosnie-Herzégovine[en]Bosnia and Herzegovina[de]Bosnien-Herzegowina[es]Bosnia y Herzegovina[it]Bosnia-Erzegovina[nl]Bosnië en Herzegovina[pt]Bósnia e Herzegovina</multi>'),
		array('id_pays'=>'33','code'=>'BW','nom'=>'<multi>[fr]Botswana[en]Botswana[de]Botswana[es]Botswana[it]Botswana[nl]Botswana[pt]Botsuana</multi>'),
		array('id_pays'=>'34','code'=>'BR','nom'=>'<multi>[fr]Brésil[en]Brazil[de]Brasilien[es]Brasil[it]Brasile[nl]Brazilië[pt]Brasil</multi>'),
		array('id_pays'=>'35','code'=>'BN','nom'=>'<multi>[fr]Brunei[en]Brunei[de]Brunei[es]Brunei[it]Brunei[nl]Brunei[pt]Brunei</multi>'),
		array('id_pays'=>'36','code'=>'BG','nom'=>'<multi>[fr]Bulgarie[en]Bulgaria[de]Bulgarien[es]Bulgaria[it]Bulgaria[nl]Bulgarije[pt]Bulgária</multi>'),
		array('id_pays'=>'37','code'=>'BF','nom'=>'<multi>[fr]Burkina Faso[en]Burkina Faso[de]Burkina Faso[es]Burkina Faso[it]Burkina Faso[nl]Burkina Faso[pt]Burkina Faso</multi>'),
		array('id_pays'=>'38','code'=>'BI','nom'=>'<multi>[fr]Burundi[en]Burundi[de]Burundi[es]Burundi[it]Burundi[nl]Burundi[pt]Burundi</multi>'),
		array('id_pays'=>'39','code'=>'KH','nom'=>'<multi>[fr]Cambodge[en]Cambodia[de]Kambodscha[es]Camboya[it]Cambogia[nl]Cambodja[pt]Camboja</multi>'),
		array('id_pays'=>'40','code'=>'CM','nom'=>'<multi>[fr]Cameroun[en]Cameroon[de]Kamerun[es]Camerún[it]Camerun[nl]Kameroen[pt]Camarões</multi>'),
		array('id_pays'=>'41','code'=>'CA','nom'=>'<multi>[fr]Canada[en]Canada[de]Kanada[es]Canadá[it]Canada[nl]Canada[pt]Canadá</multi>'),
		array('id_pays'=>'42','code'=>'CV','nom'=>'<multi>[fr]Cap-Vert[en]Cape Verde[de]Kap Verde[es]Cabo Verde[it]Capo Verde[nl]Kaapverdië[pt]Cabo Verde</multi>'),
		array('id_pays'=>'43','code'=>'CF','nom'=>'<multi>[fr]Centrafrique[en]Central African Republic[de]Zentralafrika[es]Rep. Centroafricana[it]Repubblica Centrafricana[nl]Centraal-Afrikaanse Republiek[pt]Repùblica Centro-Africana</multi>'),
		array('id_pays'=>'44','code'=>'CL','nom'=>'<multi>[fr]Chili[en]Chile[de]Chile[es]Chile[it]Cile[nl]Chili[pt]Chile</multi>'),
		array('id_pays'=>'45','code'=>'CN','nom'=>'<multi>[fr]Chine[en]China[de]China[es]China[it]Cina[nl]China[pt]China</multi>'),
		array('id_pays'=>'46','code'=>'CY','nom'=>'<multi>[fr]Chypre[en]Cyprus[de]Zypern[es]Chipre[it]Cipro[nl]Cyprus[pt]Chipre</multi>'),
		array('id_pays'=>'47','code'=>'CO','nom'=>'<multi>[fr]Colombie[en]Colombia[de]Kolumbien[es]Colombia[it]Colombia[nl]Colombia[pt]Colômbia</multi>'),
		array('id_pays'=>'48','code'=>'KM','nom'=>'<multi>[fr]Comores[en]Comoros[de]Komoren[es]Comoras[it]Comore[nl]Comoren[pt]Comores</multi>'),
		array('id_pays'=>'49','code'=>'CG','nom'=>'<multi>[fr]Congo-Brazzaville[en]Congo-Brazzaville[de]Kongo-Brazzaville[es]Congo-Brazzaville[it]Congo-Brazzaville[nl]Congo-Brazzaville[pt]Congo-Brazzaville</multi>'),
		array('id_pays'=>'50','code'=>'CD','nom'=>'<multi>[fr]Congo-Kinshasa[en]Congo-Kinshasa[de]Kongo-Kinshasa[es]Congo-Kinshasa[it]Congo-Kinshasa[nl]Congo-Kinshasa[pt]Congo-Kinshasa</multi>'),
		array('id_pays'=>'51','code'=>'KR','nom'=>'<multi>[fr]Corée du Nord[en]North Korea[de]Nordkorea[es]Corea del Norte[it]Corea del Nord[nl]Noord-Korea[pt]Coreia do Norte</multi>'),
		array('id_pays'=>'52','code'=>'KP','nom'=>'<multi>[fr]Corée du Sud[en]South Korea[de]Südkorea[es]Corea del Sur[it]Corea del Sud[nl]Zuid-Korea[pt]Coréia do Sul</multi>'),
		array('id_pays'=>'53','code'=>'CR','nom'=>'<multi>[fr]Costa Rica[en]Costa Rica[de]Costa Rica[es]Costa Rica[it]Costa Rica[nl]Costa Rica[pt]Costa Rica</multi>'),
		array('id_pays'=>'54','code'=>'CI','nom'=>'<multi>[fr]Côte d\'Ivoire[en]Côte d\'Ivoire[de]Côte d\'Ivoire[es]Côte d\'Ivoire[it]Costa d\'Avorio[nl]Ivoorkust[pt]Costa do Marfim</multi>'),
		array('id_pays'=>'55','code'=>'HR','nom'=>'<multi>[fr]Croatie[en]Croatia[de]Kroatien[es]Croacia[it]Croazia[nl]Kroatië[pt]Croácia</multi>'),
		array('id_pays'=>'56','code'=>'CU','nom'=>'<multi>[fr]Cuba[en]Cuba[de]Kuba[es]Cuba[it]Cuba[nl]Cuba[pt]Cuba</multi>'),
		array('id_pays'=>'57','code'=>'DK','nom'=>'<multi>[fr]Danemark[en]Denmark[de]Dänemark[es]Dinamarca[it]Danimarca[nl]Denemarken[pt]Dinamarca</multi>'),
		array('id_pays'=>'58','code'=>'DJ','nom'=>'<multi>[fr]Djibouti[en]Djibouti[de]Dschibuti[es]Djibouti[it]Gibuti[nl]Djibouti[pt]Djibouti</multi>'),
		array('id_pays'=>'59','code'=>'DM','nom'=>'<multi>[fr]Dominique[en]Dominica[de]Dominica[es]Dominica[it]Dominica[nl]Dominica[pt]Dominica</multi>'),
		array('id_pays'=>'60','code'=>'EG','nom'=>'<multi>[fr]Égypte[en]Egypt[de]ägypten[es]Egipto[it]Egitto[nl]Egypte[pt]Egito</multi>'),
		array('id_pays'=>'61','code'=>'AE','nom'=>'<multi>[fr]Émirats arabes unis[en]United Arab Emirates[de]Vereinigte Arabische Emirate[es]Emiratos árabes Unidos[it]Emirati Arabi Uniti[nl]Verenigde Arabische Emiraten[pt]Emirados árabes Unidos</multi>'),
		array('id_pays'=>'62','code'=>'EC','nom'=>'<multi>[fr]Équateur[en]Ecuador[de]Ecuador[es]Ecuador[it]Ecuador[nl]Ecuador[pt]Equador</multi>'),
		array('id_pays'=>'63','code'=>'ER','nom'=>'<multi>[fr]Érythrée[en]Eritrea[de]Eritrea[es]Eritrea[it]Eritrea[nl]Eritrea[pt]Eritreia</multi>'),
		array('id_pays'=>'64','code'=>'ES','nom'=>'<multi>[fr]Espagne[en]Spain[de]Spanien[es]España[it]Spagna[nl]Spanje[pt]Espanha</multi>'),
		array('id_pays'=>'65','code'=>'EE','nom'=>'<multi>[fr]Estonie[en]Estonia[de]Estland[es]Estonia[it]Estonia[nl]Estland[pt]Estónia</multi>'),
		array('id_pays'=>'66','code'=>'US','nom'=>'<multi>[fr]États-Unis[en]United States[de]Vereinigte Staaten[es]Estados Unidos[it]Stati Uniti[nl]Verenigde Staten[pt]Estados Unidos</multi>'),
		array('id_pays'=>'67','code'=>'ET','nom'=>'<multi>[fr]Éthiopie[en]Ethiopia[de]äthiopien[es]Etiopía[it]Etiopia[nl]Ethiopië[pt]Etiópia</multi>'),
		array('id_pays'=>'68','code'=>'FJ','nom'=>'<multi>[fr]Fidji[en]Fiji[de]Fidschi[es]Fiji[it]Figi[nl]Fiji[pt]Fiji</multi>'),
		array('id_pays'=>'69','code'=>'FI','nom'=>'<multi>[fr]Finlande[en]Finland[de]Finnland[es]Finlandia[it]Finlandia[nl]Finland[pt]Finlândia</multi>'),
		array('id_pays'=>'70','code'=>'FR','nom'=>'<multi>[fr]France[en]France[de]Frankreich[es]Francia[it]Francia[nl]Frankrijk[pt]França</multi>'),
		array('id_pays'=>'71','code'=>'GA','nom'=>'<multi>[fr]Gabon[en]Gabon[de]Gabun[es]Gabón[it]Gabon[nl]Gabon[pt]Gabão</multi>'),
		array('id_pays'=>'72','code'=>'GM','nom'=>'<multi>[fr]Gambie[en]Gambia[de]Gambia[es]Gambia[it]Gambia[nl]Gambia[pt]Gâmbia</multi>'),
		array('id_pays'=>'73','code'=>'GE','nom'=>'<multi>[fr]Géorgie[en]Georgia[de]Georgien[es]Georgia[it]Georgia[nl]Georgië[pt]Geórgia</multi>'),
		array('id_pays'=>'74','code'=>'GS','nom'=>'<multi>[fr]Géorgie du Sud et les îles Sandwich du Sud[en]South Georgia and the South Sandwich Islands[de]Süd-Georgien und die Süd-Sandwich-Inseln[es]Georgia del Sur y las Islas Sandwich del Sur[it]Georgia del Sud e il Sud e Isole Sandwich[nl]Zuid-Georgië en de Zuidelijke Sandwicheilanden[pt]Geórgia do Sul e Sandwich do Sul Ilhas</multi>'),
		array('id_pays'=>'75','code'=>'GH','nom'=>'<multi>[fr]Ghana[en]Ghana[de]Ghana[es]Ghana[it]Ghana[nl]Ghana[pt]Gana</multi>'),
		array('id_pays'=>'76','code'=>'GI','nom'=>'<multi>[fr]Gibraltar[en]Gibraltar[de]Gibraltar[es]Gibraltar[it]Gibilterra[nl]Gibraltar[pt]Gibraltar</multi>'),
		array('id_pays'=>'77','code'=>'GR','nom'=>'<multi>[fr]Grèce[en]Greece[de]Griechenland[es]Grecia[it]Grecia[nl]Griekenland[pt]Grécia</multi>'),
		array('id_pays'=>'78','code'=>'GD','nom'=>'<multi>[fr]Grenade[en]Grenada[de]Granada[es]Granada[it]Grenada[nl]Grenada[pt]Grenada</multi>'),
		array('id_pays'=>'79','code'=>'GL','nom'=>'<multi>[fr]Groenland[en]Greenland[de]GrÖnland[es]Groenlandia[it]Groenlandia[nl]Groenland[pt]Gronelândia</multi>'),
		array('id_pays'=>'80','code'=>'GP','nom'=>'<multi>[fr]Guadeloupe[en]Guadeloupe[de]Guadeloupe[es]Guadalupe[it]Guadalupa[nl]Guadeloupe[pt]Guadalupe</multi>'),
		array('id_pays'=>'81','code'=>'GU','nom'=>'<multi>[fr]Guam[en]Guam[de]Guam[es]Guam[it]Guam[nl]Guam[pt]Guam</multi>'),
		array('id_pays'=>'82','code'=>'GT','nom'=>'<multi>[fr]Guatemala[en]Guatemala[de]Guatemala[es]Guatemala[it]Guatemala[nl]Guatemala[pt]Guatemala</multi>'),
		array('id_pays'=>'83','code'=>'GG','nom'=>'<multi>[fr]Guernesey[en]Guernsey[de]Guernsey[es]Guernsey[it]Guernsey[nl]Guernsey[pt]Guernsey</multi>'),
		array('id_pays'=>'84','code'=>'GN','nom'=>'<multi>[fr]Guinée[en]Guinea[de]Guinea[es]Guinea[it]Guinea[nl]Guinee[pt]Guiné</multi>'),
		array('id_pays'=>'85','code'=>'GW','nom'=>'<multi>[fr]Guinée équatoriale[en]Equatorial Guinea[de]äquatorialguinea[es]Guinea Ecuatorial[it]Guinea Equatoriale[nl]Equatoriaal-Guinea[pt]Guiné Equatorial</multi>'),
		array('id_pays'=>'86','code'=>'GQ','nom'=>'<multi>[fr]Guinée-Bissau[en]Guinea-Bissau[de]Guinea-Bissau[es]Guinea-Bissau[it]Guinea-Bissau[nl]Guinee-Bissau[pt]Guiné-Bissau</multi>'),
		array('id_pays'=>'87','code'=>'GY','nom'=>'<multi>[fr]Guyana[en]Guyana[de]Guyana[es]Guyana[it]Guyana[nl]Guyana[pt]Guiana</multi>'),
		array('id_pays'=>'88','code'=>'GF','nom'=>'<multi>[fr]Guyane[en]Guyana[de]Guyana[es]Guyana[it]Guyana[nl]Frans-Guyana[pt]Guiana</multi>'),
		array('id_pays'=>'89','code'=>'HT','nom'=>'<multi>[fr]Haïti[en]Haiti[de]Haiti[es]Haití[it]Haiti[nl]Haïti[pt]Haiti</multi>'),
		array('id_pays'=>'90','code'=>'HN','nom'=>'<multi>[fr]Honduras[en]Honduras[de]Honduras[es]Honduras[it]Honduras[nl]Honduras[pt]Honduras</multi>'),
		array('id_pays'=>'91','code'=>'HK','nom'=>'<multi>[fr]Hong Kong[en]Hong Kong[de]Hong Kong[es]Hong Kong[it]Hong Kong[nl]Hongkong[pt]Hong Kong</multi>'),
		array('id_pays'=>'92','code'=>'HU','nom'=>'<multi>[fr]Hongrie[en]Hungary[de]Ungarn[es]Hungría[it]Ungheria[nl]Hongarije[pt]Hungria</multi>'),
		array('id_pays'=>'93','code'=>'BV','nom'=>'<multi>[fr]Île Bouvet[en]Bouvet Island[de]Bouvet-Insel[es]Isla Bouvet[it]Isola Bouvet[nl]Bouvet[pt]Ilha Bouvet</multi>'),
		array('id_pays'=>'94','code'=>'CX','nom'=>'<multi>[fr]Île Christmas[en]Christmas Island[de]Osterinsel[es]Christmas Island[it]Isola di Natale[nl]Christmaseiland[pt]Christmas Island</multi>'),
		array('id_pays'=>'95','code'=>'IM','nom'=>'<multi>[fr]Île de Man[en]Isle of Man[de]Isle of Man[es]Isla de Man[it]Isola di Man[nl]Man[pt]Isle of Man</multi>'),
		array('id_pays'=>'96','code'=>'KY','nom'=>'<multi>[fr]Îles Caïmanes[en]Cayman Islands[de]Kaimaninseln[es]De las Islas Caimán[it]Isole Cayman[nl]Caymaneilanden[pt]Ilhas Cayman</multi>'),
		array('id_pays'=>'97','code'=>'CC','nom'=>'<multi>[fr]Îles Cocos[en]Cocos[de]Kokosinseln[es]Cocos[it]Isole Cocos[nl]Cocoseilanden[pt]Cocos</multi>'),
		array('id_pays'=>'98','code'=>'CK','nom'=>'<multi>[fr]Îles Cook[en]Cook Islands[de]Cook-Inseln[es]Islas Cook[it]Isole Cook[nl]Cookeilanden[pt]Ilhas Cook</multi>'),
		array('id_pays'=>'99','code'=>'FO','nom'=>'<multi>[fr]Îles Féroé[en]Faroe Islands[de]FärÖer-Inseln[es]Islas Feroe[it]Isole Faroe[nl]FaerÃ¶er[pt]Ilhas Faroe</multi>'),
		array('id_pays'=>'100','code'=>'FK','nom'=>'<multi>[fr]Îles Malouines (Falkland)[en]Islas Malvinas (Falkland)[de]Falklandinseln (Falkland)[es]Islas Malvinas (Falkland)[it]Islas Malvinas (Falkland)[nl]Falklandeilanden[pt]Islas Malvinas (Falkland)</multi>'),
		array('id_pays'=>'101','code'=>'MP','nom'=>'<multi>[fr]Îles Mariannes du Nord[en]Northern Mariana Islands[de]NÖrdliche Marianen[es]Islas Marianas del Norte[it]Isole Marianne Settentrionali[nl]Noordelijke Marianen[pt]Ilhas Marianas do Norte</multi>'),
		array('id_pays'=>'102','code'=>'UM','nom'=>'<multi>[fr]Îles mineures éloignées des États-Unis[en]Minor Outlying Islands of the United States[de]Kleinere entlegenen Inseln der USA[es]Islas menores alejadas de los Estados Unidos[it]Isole Minori degli Stati Uniti[nl]Kleine afgelegen eilanden van de Verenigde Staten[pt]Territórios Insulares dos Estados Unidos da</multi>'),
		array('id_pays'=>'103','code'=>'SB','nom'=>'<multi>[fr]Îles Salomon[en]Solomon Islands[de]Salomonen[es]Islas Salomón[it]Isole Salomone[nl]Salomonseilanden[pt]Ilhas Salomão</multi>'),
		array('id_pays'=>'104','code'=>'TC','nom'=>'<multi>[fr]Îles Turques et Caïques[en]Turks and Caicos Islands[de]Turks-und Caicosinseln[es]Islas Turcas y Caicos[it]Isole Turks e Caicos[nl]Turks- en Caicoseilanden[pt]Ilhas Turcas e Caicos</multi>'),
		array('id_pays'=>'105','code'=>'VI','nom'=>'<multi>[fr]Îles Vierges américaines[en]United States Virgin Islands[de]Amerikanische Jungferninseln[es]Islas Vírgenes de los Estados Unidos[it]United States Virgin Islands[nl]Amerikaanse Maagdeneilanden[pt]Estados Unidos Ilhas Virgens</multi>'),
		array('id_pays'=>'106','code'=>'VG','nom'=>'<multi>[fr]Îles Vierges britanniques[en]BVI[de]Britische Jungferninseln[es]Islas Vírgenes Británicas[it]BVI[nl]Britse Maagdeneilanden[pt]BVI</multi>'),
		array('id_pays'=>'107','code'=>'IN','nom'=>'<multi>[fr]Inde[en]India[de]Indien[es]India[it]India[nl]India[pt]índia</multi>'),
		array('id_pays'=>'108','code'=>'ID','nom'=>'<multi>[fr]Indonésie[en]Indonesia[de]Indonesien[es]Indonesia[it]Indonesia[nl]Indonesië[pt]Indonésia</multi>'),
		array('id_pays'=>'109','code'=>'IQ','nom'=>'<multi>[fr]Irak[en]Iraq[de]Irak[es]Iraq[it]Iraq[nl]Irak[pt]Iraque</multi>'),
		array('id_pays'=>'110','code'=>'IR','nom'=>'<multi>[fr]Iran[en]Iran[de]Iran[es]Irán[it]Iran[nl]Iran[pt]Irão</multi>'),
		array('id_pays'=>'111','code'=>'IE','nom'=>'<multi>[fr]Irlande[en]Ireland[de]Irland[es]Irlanda[it]Irlanda[nl]Ierland[pt]Irlanda</multi>'),
		array('id_pays'=>'112','code'=>'IS','nom'=>'<multi>[fr]Islande[en]Iceland[de]Island[es]Islandia[it]Islanda[nl]IJsland[pt]Islândia</multi>'),
		array('id_pays'=>'113','code'=>'IL','nom'=>'<multi>[fr]Israël[en]Israel[de]Israel[es]Israel[it]Israele[nl]Israël[pt]Israel</multi>'),
		array('id_pays'=>'114','code'=>'IT','nom'=>'<multi>[fr]Italie[en]Italy[de]Italien[es]Italia[it]Italia[nl]Italië[pt]Itália</multi>'),
		array('id_pays'=>'115','code'=>'JM','nom'=>'<multi>[fr]Jamaïque[en]Jamaica[de]Jamaika[es]Jamaica[it]Giamaica[nl]Jamaica[pt]Jamaica</multi>'),
		array('id_pays'=>'116','code'=>'JP','nom'=>'<multi>[fr]Japon[en]Japan[de]Japan[es]Japón[it]Giappone[nl]Japan[pt]Japão</multi>'),
		array('id_pays'=>'117','code'=>'JE','nom'=>'<multi>[fr]Jersey[en]Jersey[de]Jersey[es]Jersey[it]Maglia[nl]Jersey[pt]Jersey</multi>'),
		array('id_pays'=>'118','code'=>'JO','nom'=>'<multi>[fr]Jordanie[en]Jordan[de]Jordanien[es]Jordania[it]Giordania[nl]Jordanië[pt]Jordânia</multi>'),
		array('id_pays'=>'119','code'=>'KZ','nom'=>'<multi>[fr]Kazakhstan[en]Kazakhstan[de]Kasachstan[es]Kazajstán[it]Kazakistan[nl]Kazachstan[pt]Cazaquistão</multi>'),
		array('id_pays'=>'120','code'=>'KE','nom'=>'<multi>[fr]Kenya[en]Kenya[de]Kenia[es]Kenia[it]Kenya[nl]Kenia[pt]Quénia</multi>'),
		array('id_pays'=>'121','code'=>'KG','nom'=>'<multi>[fr]Kirghizstan[en]Kyrgyzstan[de]Kirgisistan[es]Kirguistán[it]Kirghizistan[nl]Kirgizië[pt]Quirguistão</multi>'),
		array('id_pays'=>'122','code'=>'KI','nom'=>'<multi>[fr]Kiribati[en]Kiribati[de]Kiribati[es]Kiribati[it]Kiribati[nl]Kiribati[pt]Kiribati</multi>'),
		array('id_pays'=>'123','code'=>'KW','nom'=>'<multi>[fr]Koweït[en]Kuwait[de]Kuwait[es]Kuwait[it]Kuwait[nl]Koeweit[pt]Kuwait</multi>'),
		array('id_pays'=>'124','code'=>'LA','nom'=>'<multi>[fr]Laos[en]Laos[de]Laos[es]Laos[it]Laos[nl]Laos[pt]Laos</multi>'),
		array('id_pays'=>'125','code'=>'LS','nom'=>'<multi>[fr]Lesotho[en]Lesotho[de]Lesotho[es]Lesotho[it]Lesotho[nl]Lesotho[pt]Lesoto</multi>'),
		array('id_pays'=>'126','code'=>'LV','nom'=>'<multi>[fr]Lettonie[en]Latvia[de]Lettland[es]Letonia[it]Lettonia[nl]Letland[pt]Letónia</multi>'),
		array('id_pays'=>'127','code'=>'LB','nom'=>'<multi>[fr]Liban[en]Lebanon[de]Libanon[es]Líbano[it]Libano[nl]Libanon[pt]Líbano</multi>'),
		array('id_pays'=>'128','code'=>'LR','nom'=>'<multi>[fr]Libéria[en]Liberia[de]Liberia[es]Liberia[it]Liberia[nl]Liberia[pt]Libéria</multi>'),
		array('id_pays'=>'129','code'=>'LY','nom'=>'<multi>[fr]Libye[en]Libya[de]Libyen[es]Libia[it]Libia[nl]Libië[pt]Líbia</multi>'),
		array('id_pays'=>'130','code'=>'LI','nom'=>'<multi>[fr]Liechtenstein[en]Liechtenstein[de]Liechtenstein[es]Liechtenstein[it]Liechtenstein[nl]Liechtenstein[pt]Liechtenstein</multi>'),
		array('id_pays'=>'131','code'=>'LT','nom'=>'<multi>[fr]Lituanie[en]Lithuania[de]Litauen[es]Lituania[it]Lituania[nl]Litouwen[pt]Lituânia</multi>'),
		array('id_pays'=>'132','code'=>'LU','nom'=>'<multi>[fr]Luxembourg[en]Luxembourg[de]Luxemburg[es]Luxemburgo[it]Lussemburgo[nl]Luxemburg[pt]Luxemburgo</multi>'),
		array('id_pays'=>'133','code'=>'MO','nom'=>'<multi>[fr]Macao[en]Macao[de]Macau[es]Macao[it]Macao[nl]Macau[pt]Macau</multi>'),
		array('id_pays'=>'134','code'=>'MK','nom'=>'<multi>[fr]Macédoine[en]Macedonia[de]Mazedonien[es]Macedonia[it]Macedonia[nl]Macedonië[pt]Macedónia</multi>'),
		array('id_pays'=>'135','code'=>'MG','nom'=>'<multi>[fr]Madagascar[en]Madagascar[de]Madagaskar[es]Madagascar[it]Madagascar[nl]Madagaskar[pt]Madagascar</multi>'),
		array('id_pays'=>'136','code'=>'MY','nom'=>'<multi>[fr]Malaisie[en]Malaysia[de]Malaysia[es]Malasia[it]Malesia[nl]Maleisië[pt]Malásia</multi>'),
		array('id_pays'=>'137','code'=>'MW','nom'=>'<multi>[fr]Malawi[en]Malawi[de]Malawi[es]Malawi[it]Malawi[nl]Malawi[pt]Malawi</multi>'),
		array('id_pays'=>'138','code'=>'MV','nom'=>'<multi>[fr]Maldives[en]Maldives[de]Malediven[es]Maldivas[it]Maldive[nl]Maldiven[pt]Maldivas</multi>'),
		array('id_pays'=>'139','code'=>'ML','nom'=>'<multi>[fr]Mali[en]Mali[de]Mali[es]Malí[it]Mali[nl]Mali[pt]Mali</multi>'),
		array('id_pays'=>'140','code'=>'MT','nom'=>'<multi>[fr]Malte[en]Malta[de]Malta[es]Malta[it]Malta[nl]Malta[pt]Malta</multi>'),
		array('id_pays'=>'141','code'=>'MA','nom'=>'<multi>[fr]Maroc[en]Morocco[de]Marokko[es]Marruecos[it]Marocco[nl]Marokko[pt]Marrocos</multi>'),
		array('id_pays'=>'142','code'=>'MH','nom'=>'<multi>[fr]Marshall[en]Marshall[de]Marshall[es]Marshall[it]Marshall[nl]Marshalleilanden[pt]Marshall</multi>'),
		array('id_pays'=>'143','code'=>'MQ','nom'=>'<multi>[fr]Martinique[en]Martinique[de]Martinique[es]Martinica[it]Martinica[nl]Martinique[pt]Martinica</multi>'),
		array('id_pays'=>'144','code'=>'MU','nom'=>'<multi>[fr]Maurice[en]Mauritius[de]Mauritius[es]Mauricio[it]Maurizio[nl]Mauritius[pt]Maurícia</multi>'),
		array('id_pays'=>'145','code'=>'MR','nom'=>'<multi>[fr]Mauritanie[en]Mauritania[de]Mauretanien[es]Mauritania[it]Mauritania[nl]Mauritanië[pt]Mauritânia</multi>'),
		array('id_pays'=>'146','code'=>'YT','nom'=>'<multi>[fr]Mayotte[en]Mayotte[de]Mayotte[es]Mayotte[it]Mayotte[nl]Mayotte[pt]Mayotte</multi>'),
		array('id_pays'=>'147','code'=>'MX','nom'=>'<multi>[fr]Mexique[en]Mexico[de]Mexiko[es]México[it]Messico[nl]Mexico[pt]México</multi>'),
		array('id_pays'=>'148','code'=>'FM','nom'=>'<multi>[fr]Micronésie[en]Micronesia[de]Mikronesien[es]Micronesia[it]Micronesia[nl]Micronesia[pt]Micronésia</multi>'),
		array('id_pays'=>'149','code'=>'MD','nom'=>'<multi>[fr]Moldavie[en]Moldova[de]Moldawien[es]Moldavia[it]Moldavia[nl]Moldavië[pt]Moldávia</multi>'),
		array('id_pays'=>'150','code'=>'MC','nom'=>'<multi>[fr]Monaco[en]Monaco[de]Monaco[es]Mónaco[it]Monaco[nl]Monaco[pt]Monaco</multi>'),
		array('id_pays'=>'151','code'=>'MN','nom'=>'<multi>[fr]Mongolie[en]Mongolia[de]Mongolei[es]Mongolia[it]Mongolia[nl]Mongolië[pt]Mongólia</multi>'),
		array('id_pays'=>'152','code'=>'ME','nom'=>'<multi>[fr]Monténégro[en]Montenegro[de]Montenegro[es]Montenegro[it]Montenegro[nl]Montenegro[pt]Montenegro</multi>'),
		array('id_pays'=>'153','code'=>'MS','nom'=>'<multi>[fr]Montserrat[en]Montserrat[de]Montserrat[es]Montserrat[it]Montserrat[nl]Montserrat[pt]Montserrat</multi>'),
		array('id_pays'=>'154','code'=>'MZ','nom'=>'<multi>[fr]Mozambique[en]Mozambique[de]Mosambik[es]Mozambique[it]Mozambico[nl]Mozambique[pt]Moçambique</multi>'),
		array('id_pays'=>'155','code'=>'NA','nom'=>'<multi>[fr]Namibie[en]Namibia[de]Namibia[es]Namibia[it]Namibia[nl]Namibië[pt]Namíbia</multi>'),
		array('id_pays'=>'156','code'=>'NR','nom'=>'<multi>[fr]Nauru[en]Nauru[de]Nauru[es]Nauru[it]Nauru[nl]Nauru[pt]Nauru</multi>'),
		array('id_pays'=>'157','code'=>'NP','nom'=>'<multi>[fr]Népal[en]Nepal[de]Nepal[es]Nepal[it]Nepal[nl]Nepal[pt]Nepal</multi>'),
		array('id_pays'=>'158','code'=>'NI','nom'=>'<multi>[fr]Nicaragua[en]Nicaragua[de]Nicaragua[es]Nicaragua[it]Nicaragua[nl]Nicaragua[pt]Nicarágua</multi>'),
		array('id_pays'=>'159','code'=>'NE','nom'=>'<multi>[fr]Niger[en]Niger[de]Niger[es]Níger[it]Niger[nl]Niger[pt]Níger</multi>'),
		array('id_pays'=>'160','code'=>'NG','nom'=>'<multi>[fr]Nigeria[en]Nigeria[de]Nigeria[es]Nigeria[it]Nigeria[nl]Nigeria[pt]Nigéria</multi>'),
		array('id_pays'=>'161','code'=>'NU','nom'=>'<multi>[fr]Niué[en]Niue[de]Niue[es]Niue[it]Niue[nl]Niue[pt]Niue</multi>'),
		array('id_pays'=>'162','code'=>'NF','nom'=>'<multi>[fr]Norfolk[en]Norfolk[de]Norfolk[es]Norfolk[it]Norfolk[nl]Norfolk[pt]Norfolk</multi>'),
		array('id_pays'=>'163','code'=>'NO','nom'=>'<multi>[fr]Norvège[en]Norway[de]Norwegen[es]Noruega[it]Norvegia[nl]Noorwegen[pt]Noruega</multi>'),
		array('id_pays'=>'164','code'=>'NC','nom'=>'<multi>[fr]Nouvelle-Calédonie[en]New Caledonia[de]Neukaledonien[es]Nueva Caledonia[it]Nuova Caledonia[nl]Nieuw-Caledonië[pt]Nova Caledônia</multi>'),
		array('id_pays'=>'165','code'=>'NZ','nom'=>'<multi>[fr]Nouvelle-Zélande[en]New Zealand[de]Neuseeland[es]Nueva Zelandia[it]Nuova Zelanda[nl]Nieuw-Zeeland[pt]Nova Zelândia</multi>'),
		array('id_pays'=>'166','code'=>'OM','nom'=>'<multi>[fr]Oman[en]Oman[de]Oman[es]Omán[it]Oman[nl]Oman[pt]Omã</multi>'),
		array('id_pays'=>'167','code'=>'UG','nom'=>'<multi>[fr]Ouganda[en]Uganda[de]Uganda[es]Uganda[it]Uganda[nl]Uganda[pt]Uganda</multi>'),
		array('id_pays'=>'168','code'=>'UZ','nom'=>'<multi>[fr]Ouzbékistan[en]Uzbekistan[de]Usbekistan[es]Uzbekistán[it]Uzbekistan[nl]Oezbekistan[pt]Uzbequistão</multi>'),
		array('id_pays'=>'169','code'=>'PK','nom'=>'<multi>[fr]Pakistan[en]Pakistan[de]Pakistan[es]Pakistán[it]Pakistan[nl]Pakistan[pt]Paquistão</multi>'),
		array('id_pays'=>'170','code'=>'PW','nom'=>'<multi>[fr]Palaos[en]Palau[de]Palau[es]Palau[it]Palau[nl]Palau[pt]Palau</multi>'),
		array('id_pays'=>'171','code'=>'PS','nom'=>'<multi>[fr]Palestine[en]Palestine[de]Palästina[es]Palestina[it]Palestina[nl]Palestijnse Autoriteit[pt]Palestina</multi>'),
		array('id_pays'=>'172','code'=>'PA','nom'=>'<multi>[fr]Panamá[en]Panamá[de]Panama-Stadt[es]Panamá[it]Panamá[nl]Panama[pt]Panamá</multi>'),
		array('id_pays'=>'173','code'=>'PG','nom'=>'<multi>[fr]Papouasie-Nouvelle-Guinée[en]Papua New Guinea[de]Papua-Neuguinea[es]Papua Nueva Guinea[it]Papua Nuova Guinea[nl]Papoea-Nieuw-Guinea[pt]Papua Nova Guiné</multi>'),
		array('id_pays'=>'174','code'=>'PY','nom'=>'<multi>[fr]Paraguay[en]Paraguay[de]Paraguay[es]Paraguay[it]Paraguay[nl]Paraguay[pt]Paraguai</multi>'),
		array('id_pays'=>'175','code'=>'NL','nom'=>'<multi>[fr]Pays-Bas[en]Netherlands[de]Niederlande[es]Países Bajos[it]Paesi Bassi[nl]Nederland[[pt]Holanda</multi>'),
		array('id_pays'=>'176','code'=>'PE','nom'=>'<multi>[fr]Pérou[en]Peru[de]Peru[es]Perú[it]Perù[nl]Peru[pt]Peru</multi>'),
		array('id_pays'=>'177','code'=>'PH','nom'=>'<multi>[fr]Philippines[en]Philippines[de]Philippinen[es]Filipinas[it]Filippine[nl]Filipijnen[pt]Filipinas</multi>'),
		array('id_pays'=>'178','code'=>'PN','nom'=>'<multi>[fr]Pitcairn[en]Pitcairn[de]Pitcairn[es]Pitcairn[it]Pitcairn[nl]Pitcairneilanden[pt]Pitcairn</multi>'),
		array('id_pays'=>'179','code'=>'PL','nom'=>'<multi>[fr]Pologne[en]Poland[de]Polen[es]Polonia[it]Polonia[nl]Polen[pt]Polônia</multi>'),
		array('id_pays'=>'180','code'=>'PF','nom'=>'<multi>[fr]Polynésie française[en]French Polynesia[de]FranzÖsisch-Polynesien[es]Polinesia francés[it]Polinesia Francese[nl]Frans-Polynesië[pt]Polinésia Francesa</multi>'),
		array('id_pays'=>'181','code'=>'PR','nom'=>'<multi>[fr]Porto Rico[en]Puerto Rico[de]Puerto Rico[es]Puerto Rico[it]Puerto Rico[nl]Puerto Rico[pt]Porto Rico</multi>'),
		array('id_pays'=>'182','code'=>'PT','nom'=>'<multi>[fr]Portugal[en]Portugal[de]Portugal[es]Portugal[it]Portogallo[nl]Portugal[pt]Portugal</multi>'),
		array('id_pays'=>'183','code'=>'QA','nom'=>'<multi>[fr]Qatar[en]Qatar[de]Katar[es]Qatar[it]Qatar[nl]Qatar[pt]Qatar</multi>'),
		array('id_pays'=>'184','code'=>'DO','nom'=>'<multi>[fr]République dominicaine[en]Dominican Republic[de]Dominikanische Republik[es]República Dominicana[it]Repubblica Dominicana[nl]Dominicaanse Republiek[pt]República Dominicana</multi>'),
		array('id_pays'=>'185','code'=>'CZ','nom'=>'<multi>[fr]République tchèque[en]Czech Republic[de]Tschechische Republik[es]República Checa[it]Repubblica Ceca[nl]Tsjechië[pt]República Checa</multi>'),
		array('id_pays'=>'186','code'=>'RE','nom'=>'<multi>[fr]Réunion[en]Reunion[de]Réunion[es]Reunión[it]Reunion[nl]Réunion[pt]Reunion</multi>'),
		array('id_pays'=>'187','code'=>'RO','nom'=>'<multi>[fr]Roumanie[en]Romania[de]Rumänien[es]Rumania[it]Romania[nl]Roemenië[pt]Roménia</multi>'),
		array('id_pays'=>'188','code'=>'GB','nom'=>'<multi>[fr]Royaume-Uni[en]United Kingdom[de]Gro§britannien[es]Reino Unido[it]Regno Unito[nl]Verenigd Koninkrijk[pt]Reino Unido</multi>'),
		array('id_pays'=>'189','code'=>'RU','nom'=>'<multi>[fr]Russie[en]Russia[de]Russland[es]Rusia[it]Russia[nl]Rusland[pt]Rússia</multi>'),
		array('id_pays'=>'190','code'=>'RW','nom'=>'<multi>[fr]Rwanda[en]Rwanda[de]Ruanda[es]Ruanda[it]Ruanda[nl]Rwanda[pt]Ruanda</multi>'),
		array('id_pays'=>'191','code'=>'EH','nom'=>'<multi>[fr]Sahara occidental[en]Western Sahara[de]Westsahara[es]Sáhara Occidental[it]Sahara Occidentale[pt]Sara Ocidental</multi>'),
		array('id_pays'=>'192','code'=>'KN','nom'=>'<multi>[fr]Saint-Christophe-et-Niévès[en]St. Christopher St Kitts and Nevis[de]St. Kitts und Nevis[es]St Christopher St Kitts y Nevis[it]St Christopher St Kitts e Nevis[nl]Saint Kitts en Nevis[pt]São Cristóvão St Kitts e Nevis</multi>'),
		array('id_pays'=>'193','code'=>'SH','nom'=>'<multi>[fr]Sainte-Hélène[en]St. Helena[de]St. Helena[es]Santa Elena[it]St Helena[nl]Sint-Helena, Ascension en Tristan da Cunha[pt]Santa Helena</multi>'),
		array('id_pays'=>'194','code'=>'LC','nom'=>'<multi>[fr]Sainte-Lucie[en]St. Lucia[de]St. Lucia[es]Santa Lucía[it]Santa Lucia[nl]Saint Lucia[pt]St. Lucia</multi>'),
		array('id_pays'=>'195','code'=>'SM','nom'=>'<multi>[fr]Saint-Marin[en]San Marino[de]San Marino[es]San Marino[it]San Marino[nl]San Marino[pt]San Marino</multi>'),
		array('id_pays'=>'196','code'=>'PM','nom'=>'<multi>[fr]Saint-Pierre-et-Miquelon[en]Saint-Pierre and Miquelon[de]Saint-Pierre und Miquelon[es]Saint-Pierre y Miquelón[it]Saint-Pierre e Miquelon[nl]Saint-Pierre en Miquelon[pt]Saint-Pierre e Miquelon</multi>'),
		array('id_pays'=>'197','code'=>'VC','nom'=>'<multi>[fr]Saint-Vincent-et-les Grenadines[en]Saint Vincent and the Grenadines[de]St. Vincent und die Grenadinen[es]San Vicente y las Granadinas[it]Saint Vincent e Grenadine[nl]Saint Vincent en de Grenadines[pt]São Vicente e Granadinas</multi>'),
		array('id_pays'=>'198','code'=>'SV','nom'=>'<multi>[fr]Salvador[en]Salvador[de]Salvador[es]Salvador[it]Salvador[nl]El Salvador[pt]Salvador</multi>'),
		array('id_pays'=>'199','code'=>'WS','nom'=>'<multi>[fr]Samoa[en]Samoa[de]Samoa[es]Samoa[it]Samoa[nl]Samoa[pt]Samoa</multi>'),
		array('id_pays'=>'200','code'=>'AS','nom'=>'<multi>[fr]Samoa américaines[en]American Samoa[de]Amerikanisch-Samoa[es]Samoa Americana[it]Samoa Americane[nl]Amerikaans-Samoa[pt]Samoa Americana</multi>'),
		array('id_pays'=>'201','code'=>'ST','nom'=>'<multi>[fr]São Tomé-et-Principe[en]Sao Tome and Principe[de]São Tomé und Principe[es]Santo Tomé y Príncipe[it]Sao Tome e Principe[pt]São Tomé e Príncipe[nl]Sao Tomé en Principe</multi>'),
		array('id_pays'=>'202','code'=>'SN','nom'=>'<multi>[fr]Sénégal[en]Senegal[de]Senegal[es]Senegal[it]Senegal[nl]Senegal[pt]Senegal</multi>'),
		array('id_pays'=>'203','code'=>'RS','nom'=>'<multi>[fr]Serbie[en]Serbia[de]Serbien[es]Serbia[it]Serbia[nl]Servië[pt]Sérvia</multi>'),
		array('id_pays'=>'204','code'=>'SC','nom'=>'<multi>[fr]Seychelles[en]Seychelles[de]Seychellen[es]Seychelles[it]Seychelles[nl]Seychellen[pt]Seychelles</multi>'),
		array('id_pays'=>'205','code'=>'SL','nom'=>'<multi>[fr]Sierra Leone[en]Sierra Leone[de]Sierra Leone[es]Sierra Leona[it]Sierra Leone[nl]Sierra Leone[pt]Serra Leoa</multi>'),
		array('id_pays'=>'206','code'=>'SG','nom'=>'<multi>[fr]Singapour[en]Singapore[de]Singapur[es]Singapur[it]Singapore[nl]Singapore[pt]Singapura</multi>'),
		array('id_pays'=>'207','code'=>'SK','nom'=>'<multi>[fr]Slovaquie[en]Slovakia[de]Slowakei[es]Eslovaquia[it]Slovacchia[nl]Slowakije[pt]Eslováquia</multi>'),
		array('id_pays'=>'208','code'=>'SI','nom'=>'<multi>[fr]Slovénie[en]Slovenia[de]Slowenien[es]Eslovenia[it]Slovenia[nl]Slovenië[pt]Eslovénia</multi>'),
		array('id_pays'=>'209','code'=>'SO','nom'=>'<multi>[fr]Somalie[en]Somalia[de]Somalia[es]Somalia[it]Somalia[nl]Somalië[pt]Somália</multi>'),
		array('id_pays'=>'210','code'=>'SD','nom'=>'<multi>[fr]Soudan[en]Sudan[de]Sudan[es]Sudán[it]Sudan[nl]Soedan[pt]Sudão</multi>'),
		array('id_pays'=>'211','code'=>'LK','nom'=>'<multi>[fr]Sri Lanka[en]Sri Lanka[de]Sri Lanka[es]Sri Lanka[it]Sri Lanka[nl]Sri Lanka[pt]Sri Lanka</multi>'),
		array('id_pays'=>'212','code'=>'SE','nom'=>'<multi>[fr]Suède[en]Sweden[de]Schweden[es]Suecia[it]Svezia[nl]Zweden[pt]Suécia</multi>'),
		array('id_pays'=>'213','code'=>'CH','nom'=>'<multi>[fr]Suisse[en]Switzerland[de]Schweiz[es]Suiza[it]Svizzera[nl]Zwitserland[pt]Suíça</multi>'),
		array('id_pays'=>'214','code'=>'SR','nom'=>'<multi>[fr]Suriname[en]Suriname[de]Suriname[es]Surinam[it]Suriname[nl]Suriname[pt]Suriname</multi>'),
		array('id_pays'=>'215','code'=>'SJ','nom'=>'<multi>[fr]Svalbard et île Jan Mayen[en]Svalbard and Jan Mayen Island[de]Svalbard und Jan Mayen Insel[es]Svalbard y Jan Mayen Island[it]Svalbard e Jan Mayen, isola[nl]Jan Mayen[pt]Svalbard e Jan Mayen Island</multi>'),
		array('id_pays'=>'216','code'=>'SZ','nom'=>'<multi>[fr]Swaziland[en]Swaziland[de]Swasiland[es]Swazilandia[it]Swaziland[nl]Swaziland[pt]Suazilândia</multi>'),
		array('id_pays'=>'217','code'=>'SY','nom'=>'<multi>[fr]Syrie[en]Syria[de]Syrien[es]Siria[it]Siria[nl]Syrië[pt]Síria</multi>'),
		array('id_pays'=>'218','code'=>'TJ','nom'=>'<multi>[fr]Tadjikistan[en]Tajikistan[de]Tadschikistan[es]Tayikistán[it]Tagikistan[nl]Tadzjikistan[pt]Tajiquistão</multi>'),
		array('id_pays'=>'219','code'=>'TW','nom'=>'<multi>[fr]Taïwan[en]Taiwan[de]Taiwan[es]Taiwán[it]Taiwan[nl]Taiwan[pt]Taiwan</multi>'),
		array('id_pays'=>'220','code'=>'TZ','nom'=>'<multi>[fr]Tanzanie[en]Tanzania[de]Tansania[es]Tanzania[it]Tanzania[nl]Tanzania[pt]Tanzânia</multi>'),
		array('id_pays'=>'221','code'=>'TD','nom'=>'<multi>[fr]Tchad[en]Chad[de]Tschad[es]Chad[it]Ciad[nl]Tsjaad[pt]Chade</multi>'),
		array('id_pays'=>'222','code'=>'TF','nom'=>'<multi>[fr]Terres australes et antarctiques françaises[en]French Southern and Antarctic Territories[de]Wallis und Futuna[es]Francés australes y antárticas[it]Terre australi e antartiche francesi[nl]Franse Zuidelijke en Antarctische Gebieden[pt]Territórios Austrais e Antárcticos Franceses</multi>'),
		array('id_pays'=>'223','code'=>'IO','nom'=>'<multi>[fr]Territoire britannique de l\'océan Indien[en]British Indian Ocean[de]Britisches Territorium im Indischen Ozean[es]Británico del Océano índico[it]Britannici dell\'Oceano Indiano[pt]Britânico do Oceano índico[nl]Brits Territorium in de Indische Oceaan</multi>'),
		array('id_pays'=>'224','code'=>'__','nom'=>'<multi>[fr]Territoires extérieurs de l\'Australie[en]External territories of Australia[de]Gebiete au§erhalb von Australien[es]Territorios externos de Australia[it]Territori esterni di Australia[pt]External territórios da Austrália</multi>'),
		array('id_pays'=>'225','code'=>'TH','nom'=>'<multi>[fr]Thaïlande[en]Thailand[de]Thailand[es]Tailandia[it]Thailandia[nl]Thailand[pt]Tailândia</multi>'),
		array('id_pays'=>'226','code'=>'TL','nom'=>'<multi>[fr]Timor oriental[en]East Timor[de]Osttimor[es]Timor Oriental[it]Timor Est[nl]Oost-Timor[pt]Timor-Leste</multi>'),
		array('id_pays'=>'227','code'=>'TG','nom'=>'<multi>[fr]Togo[en]Togo[de]Togo[es]Togo[it]Togo[nl]Togo[pt]Togo</multi>'),
		array('id_pays'=>'228','code'=>'TK','nom'=>'<multi>[fr]Tokelau[en]Tokelau[de]Tokelau[es]Tokelau[it]Tokelau[nl]Tokelau-eilanden[pt]Tokelau</multi>'),
		array('id_pays'=>'229','code'=>'TO','nom'=>'<multi>[fr]Tonga[en]Tonga[de]Tonga[es]Tonga[it]Tonga[nl]Tonga[pt]Tonga</multi>'),
		array('id_pays'=>'230','code'=>'TT','nom'=>'<multi>[fr]Trinité-et-Tobago[en]Trinidad and Tobago[de]Trinidad und Tobago[es]Trinidad y Tobago[it]Trinidad e Tobago[nl]Trinidad en Tobago[pt]Trinidad e Tobago</multi>'),
		array('id_pays'=>'231','code'=>'TN','nom'=>'<multi>[fr]Tunisie[en]Tunisia[de]Tunesien[es]Túnez[it]Tunisia[nl]Tunesië[pt]Tunísia</multi>'),
		array('id_pays'=>'232','code'=>'TM','nom'=>'<multi>[fr]Turkménistan[en]Turkmenistan[de]Turkmenistan[es]Turkmenistán[it]Turkmenistan[nl]Turkmenistan[pt]Turquemenistão</multi>'),
		array('id_pays'=>'233','code'=>'TR','nom'=>'<multi>[fr]Turquie[en]Turkey[de]Türkei[es]Turquía[it]Turchia[nl]Turkije[pt]Turquia</multi>'),
		array('id_pays'=>'234','code'=>'TV','nom'=>'<multi>[fr]Tuvalu[en]Tuvalu[de]Tuvalu[es]Tuvalu[it]Tuvalu[nl]Tuvalu[pt]Tuvalu</multi>'),
		array('id_pays'=>'235','code'=>'UA','nom'=>'<multi>[fr]Ukraine[en]Ukraine[de]Ukraine[es]Ucrania[it]Ucraina[nl]Oekraïne[[pt]Ucrânia</multi>'),
		array('id_pays'=>'236','code'=>'UY','nom'=>'<multi>[fr]Uruguay[en]Uruguay[de]Uruguay[es]Uruguay[it]Uruguay[nl]Uruguay[pt]Uruguai</multi>'),
		array('id_pays'=>'237','code'=>'VU','nom'=>'<multi>[fr]Vanuatu[en]Vanuatu[de]Vanuatu[es]Vanuatu[it]Vanuatu[nl]Vanuatu[pt]Vanuatu</multi>'),
		array('id_pays'=>'238','code'=>'VA','nom'=>'<multi>[fr]Vatican[en]Vatican[de]Vatikan[es]Vaticano[it]Vaticano[nl]Vaticaanstad[pt]Vaticano</multi>'),
		array('id_pays'=>'239','code'=>'VE','nom'=>'<multi>[fr]Vénézuela[en]Venezuela[de]Venezuela[es]Venezuela[it]Venezuela[nl]Venezuela[pt]Venezuela</multi>'),
		array('id_pays'=>'240','code'=>'VN','nom'=>'<multi>[fr]Viêt Nam[en]Vietnam[de]Vietnam[es]Vietnam[it]Vietnam[nl]Vietnam[pt]Vietnã</multi>'),
		array('id_pays'=>'241','code'=>'WF','nom'=>'<multi>[fr]Wallis-et-Futuna[en]Wallis and Futuna[de]Wallis und Futuna[es]Wallis y Futuna[it]Wallis e Futuna[nl]Wallis en Futuna[pt]Wallis e Futuna</multi>'),
		array('id_pays'=>'242','code'=>'YE','nom'=>'<multi>[fr]Yémen[en]Yemen[de]Jemen[es]Yemen[it]Yemen[nl]Jemen[pt]Iémen</multi>'),
		array('id_pays'=>'243','code'=>'ZM','nom'=>'<multi>[fr]Zambie[en]Zambia[de]Sambia[es]Zambia[it]Zambia[nl]Zambia[pt]Zâmbia</multi>'),
		array('id_pays'=>'244','code'=>'ZW','nom'=>'<multi>[fr]Zimbabwe[en]Zimbabwe[de]Simbabwe[es]Zimbabwe[it]Zimbabwe[nl]Zimbabwe[pt]Zimbabwe</multi>'),
		)
	);
}
?>
