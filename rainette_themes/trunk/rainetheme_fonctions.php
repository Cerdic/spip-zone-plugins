<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

if (!defined('_RAINETTE_DEBUG_TRANSCODAGE')) {
	define('_RAINETTE_DEBUG_TRANSCODAGE', false);
}


$GLOBALS['rainette_icones']['apixu'] = array(
	1000 => array('texte' => 'Sunny/Clear', 'icone' => array('day/113', 'night/113')),
	1003 => array('texte' => 'Partly cloudy', 'icone' => array('day/116', 'night/116')),
	1006 => array('texte' => 'Cloudy', 'icone' => array('day/119', 'night/119')),
	1009 => array('texte' => 'Overcast', 'icone' => array('day/122', 'night/122')),
	1030 => array('texte' => 'Mist', 'icone' => array('day/143', 'night/143')),
	1063 => array('texte' => 'Patchy rain possible', 'icone' => array('day/176', 'night/176')),
	1066 => array('texte' => 'Patchy snow possible', 'icone' => array('day/179', 'night/179')),
	1069 => array('texte' => 'Patchy sleet possible', 'icone' => array('day/182', 'night/182')),
	1072 => array('texte' => 'Patchy freezing drizzle possible', 'icone' => array('day/185', 'night/185')),
	1087 => array('texte' => 'Thundery outbreaks possible', 'icone' => array('day/200', 'night/200')),
	1114 => array('texte' => 'Blowing snow', 'icone' => array('day/227', 'night/227')),
	1117 => array('texte' => 'Blizzard', 'icone' => array('day/230', 'night/230')),
	1135 => array('texte' => 'Fog', 'icone' => array('day/248', 'night/248')),
	1147 => array('texte' => 'Freezing fog', 'icone' => array('day/260', 'night/260')),
	1150 => array('texte' => 'Patchy light drizzle', 'icone' => array('day/263', 'night/263')),
	1153 => array('texte' => 'Light drizzle', 'icone' => array('day/266', 'night/266')),
	1168 => array('texte' => 'Freezing drizzle', 'icone' => array('day/281', 'night/281')),
	1171 => array('texte' => 'Heavy freezing drizzle', 'icone' => array('day/284', 'night/284')),
	1180 => array('texte' => 'Patchy light rain', 'icone' => array('day/293', 'night/293')),
	1183 => array('texte' => 'Light rain', 'icone' => array('day/296', 'night/296')),
	1186 => array('texte' => 'Moderate rain at times', 'icone' => array('day/299', 'night/299')),
	1189 => array('texte' => 'Moderate rain', 'icone' => array('day/302', 'night/302')),
	1192 => array('texte' => 'Heavy rain at times', 'icone' => array('day/305', 'night/305')),
	1195 => array('texte' => 'Heavy rain', 'icone' => array('day/308', 'night/308')),
	1198 => array('texte' => 'Light freezing rain', 'icone' => array('day/311', 'night/311')),
	1201 => array('texte' => 'Moderate or heavy freezing rain', 'icone' => array('day/314', 'night/314')),
	1204 => array('texte' => 'Light sleet', 'icone' => array('day/317', 'night/317')),
	1207 => array('texte' => 'Moderate or heavy sleet', 'icone' => array('day/320', 'night/320')),
	1210 => array('texte' => 'Patchy light snow', 'icone' => array('day/323', 'night/323')),
	1213 => array('texte' => 'Light snow', 'icone' => array('day/326', 'night/326')),
	1216 => array('texte' => 'Patchy moderate snow', 'icone' => array('day/329', 'night/329')),
	1219 => array('texte' => 'Moderate snow', 'icone' => array('day/332', 'night/332')),
	1222 => array('texte' => 'Patchy heavy snow', 'icone' => array('day/335', 'night/335')),
	1225 => array('texte' => 'Heavy snow', 'icone' => array('day/338', 'night/338')),
	1237 => array('texte' => 'Ice pellets', 'icone' => array('day/350', 'night/350')),
	1240 => array('texte' => 'Light rain shower', 'icone' => array('day/353', 'night/353')),
	1243 => array('texte' => 'Moderate or heavy rain shower', 'icone' => array('day/356', 'night/356')),
	1246 => array('texte' => 'Torrential rain shower', 'icone' => array('day/359', 'night/359')),
	1249 => array('texte' => 'Light sleet showers', 'icone' => array('day/362', 'night/362')),
	1252 => array('texte' => 'Moderate or heavy sleet showers', 'icone' => array('day/365', 'night/365')),
	1255 => array('texte' => 'Light snow showers', 'icone' => array('day/368', 'night/368')),
	1258 => array('texte' => 'Moderate or heavy snow showers', 'icone' => array('day/371', 'night/371')),
	1261 => array('texte' => 'Light showers of ice pellets', 'icone' => array('day/374', 'night/374')),
	1264 => array('texte' => 'Moderate or heavy showers of ice pellets', 'icone' => array('day/377', 'night/377')),
	1273 => array('texte' => 'Patchy light rain with thunder', 'icone' => array('day/386', 'night/386')),
	1276 => array('texte' => 'Moderate or heavy rain with thunder', 'icone' => array('day/389', 'night/389')),
	1279 => array('texte' => 'Patchy light snow with thunder', 'icone' => array('day/392', 'night/392')),
	1282 => array('texte' => 'Moderate or heavy snow with thunder', 'icone' => array('day/395', 'night/395'))
);

$GLOBALS['rainette_icones']['owm'] = array(
	200 => array('texte' => 'thunderstorm with light rain', 'icone' => array('11d', '11d')),
	201 => array('texte' => 'thunderstorm with rain', 'icone' => array('11d', '11d')),
	202 => array('texte' => 'thunderstorm with heavy rain', 'icone' => array('11d', '11d')),
	210 => array('texte' => 'light thunderstorm', 'icone' => array('11d', '11d')),
	211 => array('texte' => 'thunderstorm', 'icone' => array('11d', '11d')),
	212 => array('texte' => 'heavy thunderstorm', 'icone' => array('11d', '11d')),
	221 => array('texte' => 'ragged thunderstorm', 'icone' => array('11d', '11d')),
	230 => array('texte' => 'thunderstorm with light drizzle', 'icone' => array('11d', '11d')),
	231 => array('texte' => 'thunderstorm with drizzle', 'icone' => array('11d', '11d')),
	232 => array('texte' => 'thunderstorm with heavy drizzle', 'icone' => array('11d', '11d')),
	300 => array('texte' => 'light intensity drizzle', 'icone' => array(' 09d', ' 09d')),
	301 => array('texte' => 'drizzle', 'icone' => array(' 09d', ' 09d')),
	302 => array('texte' => 'heavy intensity drizzle', 'icone' => array(' 09d', ' 09d')),
	310 => array('texte' => 'light intensity drizzle rain', 'icone' => array(' 09d', ' 09d')),
	311 => array('texte' => 'drizzle rain', 'icone' => array(' 09d', ' 09d')),
	312 => array('texte' => 'heavy intensity drizzle rain', 'icone' => array(' 09d', ' 09d')),
	313 => array('texte' => 'shower rain and drizzle', 'icone' => array(' 09d', ' 09d')),
	314 => array('texte' => 'heavy shower rain and drizzle', 'icone' => array(' 09d', ' 09d')),
	321 => array('texte' => 'shower drizzle', 'icone' => array(' 09d', ' 09d')),
	500 => array('texte' => 'light rain', 'icone' => array(' 10d', ' 10d')),
	501 => array('texte' => 'moderate rain', 'icone' => array(' 10d', ' 10d')),
	502 => array('texte' => 'heavy intensity rain', 'icone' => array(' 10d', ' 10d')),
	503 => array('texte' => 'very heavy rain', 'icone' => array(' 10d', ' 10d')),
	504 => array('texte' => 'extreme rain', 'icone' => array(' 10d', ' 10d')),
	511 => array('texte' => 'freezing rain', 'icone' => array(' 13d', ' 13d')),
	520 => array('texte' => 'light intensity shower rain', 'icone' => array(' 09d', ' 09d')),
	521 => array('texte' => 'shower rain', 'icone' => array(' 09d', ' 09d')),
	522 => array('texte' => 'heavy intensity shower rain', 'icone' => array(' 09d', ' 09d')),
	531 => array('texte' => 'ragged shower rain', 'icone' => array(' 09d', ' 09d')),
	600 => array('texte' => 'light snow', 'icone' => array(' 13d', ' 13d')),
	601 => array('texte' => 'snow', 'icone' => array(' 13d', ' 13d')),
	602 => array('texte' => 'heavy snow', 'icone' => array(' 13d', ' 13d')),
	611 => array('texte' => 'sleet', 'icone' => array(' 13d', ' 13d')),
	612 => array('texte' => 'shower sleet', 'icone' => array(' 13d', ' 13d')),
	615 => array('texte' => 'light rain and snow', 'icone' => array(' 13d', ' 13d')),
	616 => array('texte' => 'rain and snow', 'icone' => array(' 13d', ' 13d')),
	620 => array('texte' => 'light shower snow', 'icone' => array(' 13d', ' 13d')),
	621 => array('texte' => 'shower snow', 'icone' => array(' 13d', ' 13d')),
	622 => array('texte' => 'heavy shower snow', 'icone' => array(' 13d', ' 13d')),
	701 => array('texte' => 'mist', 'icone' => array(' 50d', ' 50d')),
	711 => array('texte' => 'smoke', 'icone' => array(' 50d', ' 50d')),
	721 => array('texte' => 'haze', 'icone' => array(' 50d', ' 50d')),
	731 => array('texte' => 'sand, dust whirls', 'icone' => array(' 50d', ' 50d')),
	741 => array('texte' => 'fog', 'icone' => array(' 50d', ' 50d')),
	751 => array('texte' => 'sand', 'icone' => array(' 50d', ' 50d')),
	761 => array('texte' => 'dust', 'icone' => array(' 50d', ' 50d')),
	762 => array('texte' => 'volcanic ash', 'icone' => array(' 50d', ' 50d')),
	771 => array('texte' => 'squalls', 'icone' => array(' 50d', ' 50d')),
	781 => array('texte' => 'tornado', 'icone' => array(' 50d', ' 50d')),
	800 => array('texte' => 'clear sky', 'icone' => array(' 01d', '01n')),
	801 => array('texte' => 'few clouds', 'icone' => array(' 02d', '02n')),
	802 => array('texte' => 'scattered clouds', 'icone' => array(' 03d', '03n')),
	803 => array('texte' => 'broken clouds', 'icone' => array(' 04d', '04n')),
	804 => array('texte' => 'overcast clouds', 'icone' => array(' 04d', '04n')),
	900 => array('texte' => 'tornado', 'icone' => array('', '')),
	901 => array('texte' => 'tropical storm', 'icone' => array('', '')),
	902 => array('texte' => 'hurricane', 'icone' => array('', '')),
	903 => array('texte' => 'cold', 'icone' => array('', '')),
	904 => array('texte' => 'hot', 'icone' => array('', '')),
	905 => array('texte' => 'windy', 'icone' => array('', '')),
	906 => array('texte' => 'hail', 'icone' => array('', '')),
	951 => array('texte' => 'calm', 'icone' => array('', '')),
	952 => array('texte' => 'light breeze', 'icone' => array('', '')),
	953 => array('texte' => 'gentle breeze', 'icone' => array('', '')),
	954 => array('texte' => 'moderate breeze', 'icone' => array('', '')),
	955 => array('texte' => 'fresh breeze', 'icone' => array('', '')),
	956 => array('texte' => 'strong breeze', 'icone' => array('', '')),
	957 => array('texte' => 'high wind, near gale', 'icone' => array('', '')),
	958 => array('texte' => 'gale', 'icone' => array('', '')),
	959 => array('texte' => 'severe gale', 'icone' => array('', '')),
	960 => array('texte' => 'storm', 'icone' => array('', '')),
	961 => array('texte' => 'violent storm', 'icone' => array('', '')),
	962 => array('texte' => 'hurricane', 'icone' => array('', ''))
);

$GLOBALS['rainette_icones']['weatherbit'] = array(
	200 => array('texte' => 'Thunderstorm with light rain', 'icone' => array('t01d', 't01n')),
	201 => array('texte' => 'Thunderstorm with rain', 'icone' => array('t02d', 't02n')),
	202 => array('texte' => 'Thunderstorm with heavy rain', 'icone' => array('t03d', 't03n')),
	230 => array('texte' => 'Thunderstorm with light drizzle', 'icone' => array('t04d', 't04n')),
	231 => array('texte' => 'Thunderstorm with drizzle', 'icone' => array('t04d', 't04n')),
	232 => array('texte' => 'Thunderstorm with heavy drizzle', 'icone' => array('t04d', 't04n')),
	233 => array('texte' => 'Thunderstorm with Hail', 'icone' => array('t05d', 't05n')),
	300 => array('texte' => 'Light Drizzle', 'icone' => array('d01d', 'd01n')),
	301 => array('texte' => 'Drizzle', 'icone' => array('d02d', 'd02n')),
	302 => array('texte' => 'Heavy Drizzle', 'icone' => array('d03d', 'd03n')),
	500 => array('texte' => 'Light Rain', 'icone' => array('r01d', 'r01n')),
	501 => array('texte' => 'Moderate Rain', 'icone' => array('r02d', 'r02n')),
	502 => array('texte' => 'Heavy Rain', 'icone' => array('r03d', 'r03n')),
	511 => array('texte' => 'Freezing rain', 'icone' => array('f01d', 'f01n')),
	520 => array('texte' => 'Light shower rain', 'icone' => array('r04d', 'r04n')),
	521 => array('texte' => 'Shower rain', 'icone' => array('r05d', 'r05n')),
	522 => array('texte' => 'Heavy shower rain', 'icone' => array('r06d', 'r06n')),
	600 => array('texte' => 'Light snow', 'icone' => array('s01d', 's01n')),
	601 => array('texte' => 'Snow', 'icone' => array('s02d', 's02n')),
	602 => array('texte' => 'Heavy Snow', 'icone' => array('s03d', 's03n')),
	610 => array('texte' => 'Mix snow/rain', 'icone' => array('s04d', 's04n')),
	611 => array('texte' => 'Sleet', 'icone' => array('s05d', 's05n')),
	612 => array('texte' => 'Heavy sleet', 'icone' => array('s05d', 's05n')),
	621 => array('texte' => 'Snow shower', 'icone' => array('s01d', 's01n')),
	622 => array('texte' => 'Heavy snow shower', 'icone' => array('s02d', 's02n')),
	623 => array('texte' => 'Flurries', 'icone' => array('s06d', 's06n')),
	700 => array('texte' => 'Mist', 'icone' => array('a01d', 'a01n')),
	711 => array('texte' => 'Smoke', 'icone' => array('a02d', 'a02n')),
	721 => array('texte' => 'Haze', 'icone' => array('a03d', 'a03n')),
	731 => array('texte' => 'Sand/dust', 'icone' => array('a04d', 'a04n')),
	741 => array('texte' => 'Fog', 'icone' => array('a05d', 'a05n')),
	751 => array('texte' => 'Freezing Fog', 'icone' => array('a06d', 'a06n')),
	800 => array('texte' => 'Clear sky', 'icone' => array('c01d', 'c01n')),
	801 => array('texte' => 'Few clouds', 'icone' => array('c02d', 'c02n')),
	802 => array('texte' => 'Scattered clouds', 'icone' => array('c02d', 'c02n')),
	803 => array('texte' => 'Broken clouds', 'icone' => array('c03d', 'c03n')),
	804 => array('texte' => 'Overcast clouds', 'icone' => array('c04d', 'c04n')),
	900 => array('texte' => 'Unknown Precipitation', 'icone' => array('u00d', 'u00n'))
);

$GLOBALS['rainette_icones']['wwo'] = array(
	113 => array('texte' => 'Clear/Sunny', 'icone' => array('day/113', 'night/113')),
	116 => array('texte' => 'Partly Cloudy', 'icone' => array('day/116', 'night/116')),
	119 => array('texte' => 'Cloudy', 'icone' => array('day/119', 'night/119')),
	122 => array('texte' => 'Overcast', 'icone' => array('day/122', 'night/122')),
	143 => array('texte' => 'Mist', 'icone' => array('day/143', 'night/143')),
	176 => array('texte' => 'Patchy rain nearby', 'icone' => array('day/176', 'night/176')),
	179 => array('texte' => 'Patchy snow nearby', 'icone' => array('day/179', 'night/179')),
	182 => array('texte' => 'Patchy sleet nearby', 'icone' => array('day/182', 'night/182')),
	185 => array('texte' => 'Patchy freezing drizzle nearby', 'icone' => array('day/185', 'night/185')),
	200 => array('texte' => 'Thundery outbreaks in nearby', 'icone' => array('day/200', 'night/200')),
	227 => array('texte' => 'Blowing snow', 'icone' => array('day/227', 'night/227')),
	230 => array('texte' => 'Blizzard', 'icone' => array('day/230', 'night/230')),
	248 => array('texte' => 'Fog', 'icone' => array('day/248', 'night/248')),
	260 => array('texte' => 'Freezing fog', 'icone' => array('day/260', 'night/260')),
	263 => array('texte' => 'Patchy light drizzle', 'icone' => array('day/263', 'night/263')),
	266 => array('texte' => 'Light drizzle', 'icone' => array('day/266', 'night/266')),
	281 => array('texte' => 'Freezing drizzle', 'icone' => array('day/281', 'night/281')),
	284 => array('texte' => 'Heavy freezing drizzle', 'icone' => array('day/284', 'night/284')),
	293 => array('texte' => 'Patchy light rain', 'icone' => array('day/293', 'night/293')),
	296 => array('texte' => 'Light rain', 'icone' => array('day/296', 'night/296')),
	299 => array('texte' => 'Moderate rain at times', 'icone' => array('day/299', 'night/299')),
	302 => array('texte' => 'Moderate rain', 'icone' => array('day/302', 'night/302')),
	305 => array('texte' => 'Heavy rain at times', 'icone' => array('day/305', 'night/305')),
	308 => array('texte' => 'Heavy rain', 'icone' => array('day/308', 'night/308')),
	311 => array('texte' => 'Light freezing rain', 'icone' => array('day/311', 'night/311')),
	314 => array('texte' => 'Moderate or Heavy freezing rain', 'icone' => array('day/314', 'night/314')),
	317 => array('texte' => 'Light sleet', 'icone' => array('day/317', 'night/317')),
	320 => array('texte' => 'Moderate or heavy sleet', 'icone' => array('day/320', 'night/320')),
	323 => array('texte' => 'Patchy light snow', 'icone' => array('day/323', 'night/323')),
	326 => array('texte' => 'Light snow', 'icone' => array('day/326', 'night/326')),
	329 => array('texte' => 'Patchy moderate snow', 'icone' => array('day/329', 'night/329')),
	332 => array('texte' => 'Moderate snow', 'icone' => array('day/332', 'night/332')),
	335 => array('texte' => 'Patchy heavy snow', 'icone' => array('day/335', 'night/335')),
	338 => array('texte' => 'Heavy snow', 'icone' => array('day/338', 'night/338')),
	350 => array('texte' => 'Ice pellets', 'icone' => array('day/350', 'night/350')),
	353 => array('texte' => 'Light rain shower', 'icone' => array('day/353', 'night/353')),
	356 => array('texte' => 'Moderate or heavy rain shower', 'icone' => array('day/356', 'night/356')),
	359 => array('texte' => 'Torrential rain shower', 'icone' => array('day/359', 'night/359')),
	362 => array('texte' => 'Light sleet showers', 'icone' => array('day/362', 'night/362')),
	365 => array('texte' => 'Moderate or heavy sleet showers', 'icone' => array('day/365', 'night/365')),
	368 => array('texte' => 'Light snow showers', 'icone' => array('day/368', 'night/368')),
	371 => array('texte' => 'Moderate or heavy snow showers', 'icone' => array('day/371', 'night/371')),
	374 => array('texte' => 'Light showers of ice pellets', 'icone' => array('day/374', 'night/374')),
	377 => array('texte' => 'Moderate or heavy showers of ice pellets', 'icone' => array('day/377', 'night/377')),
	386 => array('texte' => 'Patchy light rain in area with thunder', 'icone' => array('day/386', 'night/386')),
	389 => array('texte' => 'Moderate or heavy rain in area with thunder', 'icone' => array('day/389', 'night/389')),
	392 => array('texte' => 'Patchy light snow in area with thunder', 'icone' => array('day/392', 'night/392')),
	395 => array('texte' => 'Moderate or heavy snow in area with thunder', 'icone' => array('day/395', 'night/395'))
);

$GLOBALS['rainette_icones']['wunderground'] = array(
	'chanceflurries' => array('texte' => 'Chance of Flurries', 'icone' => array('chanceflurries', 'nt_chanceflurries')),
	'chancerain' => array('texte' => 'Chance of Rain', 'icone' => array('chancerain', 'nt_chancerain')),
//	'chancerain' => array('texte' => 'Chance Rain', 'icone' => array('chancerain', 'nt_chancerain')),
//	'chancesleet' => array('texte' => 'Chance of Freezing Rain', 'icone' => array('chancesleet', 'nt_chancesleet')),
	'chancesleet' => array('texte' => 'Chance of Sleet', 'icone' => array('chancesleet', 'nt_chancesleet')),
	'chancesnow' => array('texte' => 'Chance of Snow', 'icone' => array('chancesnow', 'nt_chancesnow')),
	'chancetstorms' => array('texte' => 'Chance of Thunderstorms', 'icone' => array('chancetstorms', 'nt_chancetstorms')),
//	'chancetstorms' => array('texte' => 'Chance of a Thunderstorm', 'icone' => array('chancetstorms', 'nt_chancetstorms')),
	'clear' => array('texte' => 'Clear', 'icone' => array('clear', 'nt_clear')),
	'cloudy' => array('texte' => 'Cloudy', 'icone' => array('cloudy', 'nt_cloudy')),
	'flurries' => array('texte' => 'Flurries', 'icone' => array('flurries', 'nt_flurries')),
	'fog' => array('texte' => 'Fog', 'icone' => array('fog', 'nt_fog')),
	'hazy' => array('texte' => 'Haze', 'icone' => array('hazy', 'nt_hazy')),
	'mostlycloudy' => array('texte' => 'Mostly Cloudy', 'icone' => array('mostlycloudy', 'nt_mostlycloudy')),
	'mostlysunny' => array('texte' => 'Mostly Sunny', 'icone' => array('mostlysunny', 'nt_mostlysunny')),
	'partlycloudy' => array('texte' => 'Partly Cloudy', 'icone' => array('partlycloudy', 'nt_partlycloudy')),
	'partlysunny' => array('texte' => 'Partly Sunny', 'icone' => array('partlysunny', 'nt_partlysunny')),
//	'sleet' => array('texte' => 'Freezing Rain', 'icone' => array('sleet', 'nt_sleet')),
	'rain' => array('texte' => 'Rain', 'icone' => array('rain', 'nt_rain')),
	'sleet' => array('texte' => 'Sleet', 'icone' => array('sleet', 'nt_sleet')),
	'snow' => array('texte' => 'Snow', 'icone' => array('snow', 'nt_snow')),
	'sunny' => array('texte' => 'Sunny', 'icone' => array('sunny', 'nt_sunny')),
//	'tstorms' => array('texte' => 'Thunderstorms', 'icone' => array('tstorms', 'nt_tstorms')),
	'tstorms' => array('texte' => 'Thunderstorm', 'icone' => array('tstorms', 'nt_tstorms')),
	'unknown' => array('texte' => 'Unknown', 'icone' => array('unknown', 'nt_unknown')),
//	'cloudy' => array('texte' => 'Overcast', 'icone' => array('cloudy', 'nt_cloudy')),
//	'partlycloudy' => array('texte' => 'Scattered Clouds', 'icone' => array('partlycloudy', 'nt_partlycloudy'))
);

$GLOBALS['rainette_icones']['darksky'] = array(
	'clear-day'           => array('texte' => 'Clear (day)', 'icone' => array('clear-day', '')),
	'clear-night'         => array('texte' => 'Clear (night)', 'icone' => array('clear-night', '')),
	'cloudy'              => array('texte' => 'Cloudy', 'icone' => array('cloudy', '')),
	'fog'                 => array('texte' => 'Foggy', 'icone' => array('fog', '')),
	'partly-cloudy-day'   => array('texte' => 'Partly cloudy (day)', 'icone' => array('partly-cloudy-day', '')),
	'partly-cloudy-night' => array('texte' => 'Partly cloudy (night)', 'icone' => array('partly-cloudy-night', '')),
	'rain'                => array('texte' => 'Rain', 'icone' => array('rain', '')),
	'sleet'               => array('texte' => 'Sleet', 'icone' => array('sleet', '')),
	'snow'                => array('texte' => 'Snow', 'icone' => array('snow', '')),
	'wind'                => array('texte' => 'Windy', 'icone' => array('wind', '')),
	'hail'                => array('texte' => 'Hail', 'icone' => array('hail', '')),
	'thunderstorm'        => array('texte' => 'Thunderstorms', 'icone' => array('thunderstorm', '')),
	'tornado'             => array('texte' => 'Tornado', 'icone' => array('tornado', ''))
);

$GLOBALS['rainette_icones']['accuweather'] = array(
	1  => array('texte' => 'Sunny', 'icone' => array('1', '')),
	2  => array('texte' => 'Mostly Sunny', 'icone' => array('2', '')),
	3  => array('texte' => 'Partly Sunny', 'icone' => array('3', '')),
	4  => array('texte' => 'Intermittent Clouds', 'icone' => array('4', '')),
	5  => array('texte' => 'Hazy Sunshine', 'icone' => array('5', '')),
	6  => array('texte' => 'Mostly Cloudy', 'icone' => array('6', '')),
	7  => array('texte' => 'Cloudy', 'icone' => array('7', '7')),
	8  => array('texte' => 'Dreary (Overcast)', 'icone' => array('8', '8')),
	11 => array('texte' => 'Fog', 'icone' => array('11', '11')),
	12 => array('texte' => 'Showers', 'icone' => array('12', '12')),
	13 => array('texte' => 'Mostly Cloudy w/ Showers', 'icone' => array('13', '')),
	14 => array('texte' => 'Partly Sunny w/ Showers', 'icone' => array('14', '')),
	15 => array('texte' => 'T-Storms', 'icone' => array('15', '15')),
	16 => array('texte' => 'Mostly Cloudy w/ T-Storms', 'icone' => array('16', '')),
	17 => array('texte' => 'Partly Sunny w/ T-Storms', 'icone' => array('17', '')),
	18 => array('texte' => 'Rain', 'icone' => array('18', '18')),
	19 => array('texte' => 'Flurries', 'icone' => array('19', '19')),
	20 => array('texte' => 'Mostly Cloudy w/ Flurries', 'icone' => array('20', '')),
	21 => array('texte' => 'Partly Sunny w/ Flurries', 'icone' => array('21', '')),
	22 => array('texte' => 'Snow', 'icone' => array('22', '22')),
	23 => array('texte' => 'Mostly Cloudy w/ Snow', 'icone' => array('23', '')),
	24 => array('texte' => 'Ice', 'icone' => array('24', '24')),
	25 => array('texte' => 'Sleet', 'icone' => array('25', '25')),
	26 => array('texte' => 'Freezing Rain', 'icone' => array('26', '26')),
	29 => array('texte' => 'Rain and Snow', 'icone' => array('29', '29')),
	30 => array('texte' => 'Hot', 'icone' => array('30', '30')),
	31 => array('texte' => 'Cold', 'icone' => array('31', '31')),
	32 => array('texte' => 'Windy', 'icone' => array('32', '32')),
	33 => array('texte' => 'Clear', 'icone' => array('', '33')),
	34 => array('texte' => 'Mostly Clear', 'icone' => array('', '34')),
	35 => array('texte' => 'Partly Cloudy', 'icone' => array('', '35')),
	36 => array('texte' => 'Intermittent Clouds', 'icone' => array('', '36')),
	37 => array('texte' => 'Hazy Moonlight', 'icone' => array('', '37')),
	38 => array('texte' => 'Mostly Cloudy', 'icone' => array('', '38')),
	39 => array('texte' => 'Partly Cloudy w/ Showers', 'icone' => array('', '39')),
	40 => array('texte' => 'Mostly Cloudy w/ Showers', 'icone' => array('', '40')),
	41 => array('texte' => 'Partly Cloudy w/ T-Storms', 'icone' => array('', '41')),
	42 => array('texte' => 'Mostly Cloudy w/ T-Storms', 'icone' => array('', '42')),
	43 => array('texte' => 'Mostly Cloudy w/ Flurries', 'icone' => array('', '43')),
	44 => array('texte' => 'Mostly Cloudy w/ Snow ', 'icone' => array('', '44')),
);


/**
 * @param string $service
 * @param string $source
 *
 * @return array
 */
function rainette_lister_codes($service) {

	$codes = array();

	$traduire = charger_fonction('traduire', 'inc');
	if ($service == 'weather') {
		for ($_code = 0; $_code < 48; $_code++) {
			$codes[$_code] = $traduire("rainette:meteo_${_code}", 'en');
		}
		$codes['na'] = $traduire('rainette:meteo_na', 'en');
	} else {
		// Récupération de la configuration des icones du service concerné.
		$configuration_icones = $GLOBALS['rainette_icones'][$service];

		// On extrait juste la colonne du texte de résumé.
		$codes = array_column($configuration_icones, 'texte');
	}

	return $codes;
}


/**
 * @param string $service
 * @param string $source
 *
 * @return array
 */
function rainette_lister_icones($service, $theme, $periode = 0) {

	$icones = array();

	include_spip('inc/rainette_normaliser');

	if (in_array($service, array('weather'))) {
		$codes = rainette_lister_codes($service);
		foreach ($codes as $_code => $_resume) {
			$fichier = find_in_path(icone_weather_normaliser($_code, $theme));
			$icones[$_code] = array(
				'resume' => $_resume,
				'icone'  => array('code' => $_code, 'source' => $fichier)
			);
		}
	} else {
		// Récupération de la configuration des icones du service concerné.
		$configuration_icones = $GLOBALS['rainette_icones'][$service];
		foreach ($configuration_icones as $_code => $_configuration) {
			$fichier = find_in_path(icone_local_normaliser(
				"{$_configuration['icone'][$periode]}.png",
				$service,
				$theme));
			$icones[$_code] = array(
				'resume' => $_configuration['texte'],
				'icone'  => array('code' => $_code, 'source' => $fichier)
			);
		}
	}

	return $icones;
}
