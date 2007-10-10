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


$GLOBALS['honeypotdb_version'] = 5;

$spip_honeypot_cache = array(
							 "ip" 	=> "BIGINT NOT NULL", //stoquage avec ip2long de php pour etre plus leger
							 "age" 	=> "TINYINT UNSIGNED DEFAULT 0",
							 "status" 	=> "TINYINT UNSIGNED DEFAULT 0",
							 "threat" 	=> "TINYINT UNSIGNED DEFAULT 0",
							 "type" 	=> "TINYINT UNSIGNED DEFAULT 0",
							 "maj" 		=> "TIMESTAMP NOT NULL");

$spip_honeypot_cache_key = array(
								 "PRIMARY KEY" => "ip");

global $tables_auxiliaires;
$tables_auxiliaires['spip_honeypot_cache'] = array(
												   'field' => &$spip_honeypot_cache,
												   'key' => &$spip_honeypot_cache_key);
$spip_honeypot_stats = array(
							 'date' => "DATE NOT NULL",
							 "threat" 	=> "INT UNSIGNED DEFAULT 0",
							 "type" 	=> "TINYINT UNSIGNED DEFAULT 0",
							 "cnt" 	=> "INT UNSIGNED DEFAULT 0",
							 'filtre' => "TINYINT UNSIGNED DEFAULT 0",
							 "maj" 		=> "TIMESTAMP NOT NULL");

$spip_honeypot_stats_key = array(
								 "PRIMARY KEY" => "date, type, filtre");


$tables_auxiliaires['spip_honeypot_stats'] = array(
												   'field' => &$spip_honeypot_stats,
												   'key' => &$spip_honeypot_stats_key);


?>
