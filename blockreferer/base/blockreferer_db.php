<?php

/*
 *   Plugin Blockreferer
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


$GLOBALS['blockrefererdb_version'] = 1;

$spip_blockreferer_blacklist = array(
							 "referer_md5" => "BIGINT UNSIGNED NOT NULL",
							 "referer"	=> "VARCHAR (255) NOT NULL",
							 "maj" 		=> "TIMESTAMP");

$spip_blockreferer_blacklist_key = array(
								 "PRIMARY KEY" => "referer_md5");

global $tables_auxiliaires;
$tables_auxiliaires['spip_blockreferer_blacklist'] = array(
												   'field' => &$spip_blockreferer_blacklist,
												   'key' => &$spip_blockreferer_blacklist_key);

?>

