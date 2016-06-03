<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

define('_DIR_LIB_GEOIP', 'lib/geoip-api-php/');
define('_DIR_LIB_GEOIP2', 'lib/GeoIP2-php/');
define('_DIR_LIB_GEOIP_DB', 'lib/geoip-api-php/maxmind-db/');
define('_DIR_LIB_GEOIP2_DB', 'lib/GeoIP2-php/maxmind-db/');
define('_SRC_LIB_GEOIP_DB', 'http://geolite.maxmind.com/download/geoip/database/GeoLiteCountry/GeoIP.dat.gz');
define('_SRC_LIB_GEOIP2_DB', 'http://geolite.maxmind.com/download/geoip/database/GeoLite2-City.mmdb.gz');
define('_FILENAME_GEOIP_DB', 'GeoIP.dat');
define('_FILENAME_GEOIP2_DB', 'GeoLite2-City.mmdb');
