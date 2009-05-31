<?php
#----------------------------------------------------------#
#  Plugin  : spipbb - Licence : GPL                        #
#  File    : visites.php - sauve les stats dans un fichier #
#  Authors : Chryjs, 2007 et als                           #
#  http://www.spip-contrib.net/Plugin-SpipBB#contributeurs #
#  Contact : chryjs!@!free!.!fr                            #
# [fr] Ce fichier inclu en pied de page voirsujet permet   #
# [fr] de stocker provisoirement les visites avant cron    #
#----------------------------------------------------------#

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

if (!defined('_INC_SPIPBB_COMMON')) include_spip('inc/spipbb_common'); // pour la log

	// Rejet des robots (qui sont pourtant des humains comme les autres)
	if (preg_match(
	',google|yahoo|msnbot|crawl|lycos|voila|slurp|jeeves|teoma,i',
	$_SERVER['HTTP_USER_AGENT']))
		return;

	// Identification du client
	$client_id = substr(md5(
		$GLOBALS['ip'] . $_SERVER['HTTP_USER_AGENT']
//		. $_SERVER['HTTP_ACCEPT'] # HTTP_ACCEPT peut etre present ou non selon que l'on est dans la requete initiale, ou dans les hits associes
		. $_SERVER['HTTP_ACCEPT_LANGUAGE']
		. $_SERVER['HTTP_ACCEPT_ENCODING']
	), 0,10);

	//
	// stockage sous forme de fichier tmp/spipbb-visites
	//

	spipbb_log("calcule les stats ".$GLOBALS['id_forum'],3,__FILE__);

	// 1. Chercher s'il existe deja une session pour ce numero IP.
	$content = array();
	$fichier = sous_repertoire(_DIR_TMP, 'spipbb-visites') . $client_id;
	if (lire_fichier($fichier, $content)) {
		spipbb_log("Contenu stats:".serialize($content),3,__FILE__);
		$content = @unserialize($content);
	}

	// 2. Plafonner le nombre de hits pris en compte pour un IP (robots etc.)
	// et ecrire la session
	if (count($content) < 200) {

//$id_forum=$GLOBALS['contexte']['id_forum'];

	// Identification de l'element
	// Attention il s'agit bien des $GLOBALS, regles (dans le cas des urls
	// personnalises), par la carte d'identite de la page... ne pas utiliser
	// _request() ici !
		if (isset($GLOBALS['id_forum']))
			$log_type = "forum";
		else
			$log_type = "";

		if ($log_type)
			$log_type .= "\t" . intval($GLOBALS["id_$log_type"]);
		else    $log_type = "autre\t0";

		if (isset($content[$log_type])) {
			$content[$log_type]++;
		}
		else	$content[$log_type] = 1; // bienvenue au club

		spipbb_log("Enregis stats:".serialize($content),3,__FILE__);
		ecrire_fichier($fichier, serialize($content));
	}

//@define('_DIR_RESTREINT_ABS', 'ecrire/');
//include_once _DIR_RESTREINT_ABS.'inc_version.php';

//include _DIR_RESTREINT_ABS.'public.php';


?>