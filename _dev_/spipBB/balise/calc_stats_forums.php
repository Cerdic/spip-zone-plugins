<?php
#-----------------------------------------------------------------#
#  Plugin  : spipbb - Licence : GPL                               #
#  File    : balise/calc_stats_forums - balise #CALC_STATS_FORUMS #
#  Authors : Chryjs, 2007 +                                       #
#  http://www.spip-contrib.net/Plugin-SpipBB#contributeurs        #
#  Contact : chryjs¡@!free¡.!fr                                   #
#-----------------------------------------------------------------#

//    This program is free software; you can redistribute it and/or modify
//    it under the terms of the GNU General Public License as published by
//    the Free Software Foundation; either version 2 of the License, or any later version.
//
//    This program is distributed in the hope that it will be useful,
//    but WITHOUT ANY WARRANTY; without even the implied warranty of
//    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//    GNU General Public License for more details.
//
//    You should have received a copy of the GNU General Public License
//    along with this program; if not, write to the Free Software
//    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

/* Cette balise permet de declencher le stockage des stats des forums */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/spipbb'); // Compatibilite 192

function balise_CALC_STATS_FORUMS($p)
{
	return calculer_balise_dynamique($p,'CALC_STATS_FORUMS', array());
}

function balise_CALC_STATS_FORUMS_stat($args, $filtres)
{
	return $args;
}

function balise_CALC_STATS_FORUMS_dyn($id_forum)
{
	if (empty($id_forum)) return '';
	$row = sql_fetsel('visites','spip_visites_forums',"id_forum=$id_forum");

	if (is_array($row) and (!empty($row['visites'])) ) {
		$r = sql_updateq( "spip_visites_forums", array(
					'date'	=>	date("Y-m-d"),
					'visites' =>	$row['visites']+1
					),
				"id_forum=$id_forum");
	}
	else {
		$spip_id = sql_insertq('spip_visites_forums', array(
						'id_forum' => $id_forum,
						'date' => date("Y-m-d"),
						'visites' => "1" )
					);
	}

	return ''; // "<p> spip_visites_forums $id_forum updated </p>"
}

?>
