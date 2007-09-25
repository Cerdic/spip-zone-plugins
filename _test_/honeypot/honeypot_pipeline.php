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

/*ajoute un style pour cacher les pieges aux visiteurs*/
function honeypot_insert_head($flux) {
	$flux .= 	'<style>
.pluginhp'.lire_config('honeypot/hpfile').' {
display: none;
}
</style>';
	return $flux;
  }

?>