<?php

/*
*   Plugin HoneyPot
*   Copyright (C) 2007 Pierre Andrews
*
*   This program is free software: you can redistribute it and/or modify
*   it under the terms of the GNU General Public License as published by
*   the Free Software Foundation, either version 3 of the License, or
*   (at your option) any later version.
*
*   This program is distributed in the hope that it will be useful,
*   but WITHOUT ANY WARRANTY; without even the implied warranty of
*   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*   GNU General Public License for more details.
*
*   You should have received a copy of the GNU General Public License
*   along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

  //see http://projecthoneypot.org/httpbl_api.php

define('HTTPBL_SEARCHENGINE', 0);
define('HTTPBL_SUSPICIOUS', 1);
define('HTTPBL_HARVESTER', 2);
define('HTTPBL_COMMENT_SPAMMER',4);

//original idea from
// http://planetozh.com/blog/my-projects/honey-pot-httpbl-simple-php-script/
function httpbl_test($ip,$apikey) {
	$query = $apikey . '.' . implode('.', array_reverse(explode ('.', $ip ))) . '.dnsbl.httpbl.org';
	
	$result = explode( '.', gethostbyname($query));

	if ($result[0] == 127) {
		// query successful !
	  return array('age'=>$result[1],
				   'threat'=> $result[2],
				   'type' => $result[3]);
	}
	return '';
}

function httpbl_is_searchengine($info) {
  return $info['type'] & HTTPBL_SEARCHENGINE;
}
function httpbl_is_suspicious($info) {
  return $info['type'] & HTTPBL_SUSPICIOUS;
}
function httpbl_is_harvester($info) {
  return $info['type'] & HTTPBL_HARVESTER;
}
function httpbl_is_comment_spammer($info) {
  return $info['type'] & HTTPBL_COMMENT_SPAMMER;
}


?>
