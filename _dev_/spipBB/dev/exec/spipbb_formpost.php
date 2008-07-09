<?php
#-------------------------------------------------------------------#
#  Plugin  : spipbb - Licence : GPL                                 #
#  File    : exec/spipbb_formpost                                   #
#  Authors : scoty 2007                                             #
#  http://www.spip-contrib.net/Plugin-SpipBB#contributeurs          #
#  Source  : GAFoSPIP v. 0.5 - 21/08/07 - spip 1.9.2                #
#  Contact : Hugues AROUX scoty!@!koakidi!.!com                     #
# [fr] popup de rédaction de message                                #
#-------------------------------------------------------------------#
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

if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('inc/spipbb_common');
spipbb_log('included',2,__FILE__);

include_spip('inc/presentation');

// ------------------------------------------------------------------------------
// ------------------------------------------------------------------------------
function exec_spipbb_formpost() {

	# initialiser spipbb
	include_spip('inc/spipbb_init');

	# requis de cet exec
	include_spip('spipbb_notifications');
	include_spip("inc/spipbb_inc_formpost");
	include_spip("inc/traiter_imagerie");

	# requis spip
	include_spip("inc/actions");

	// reconstruire .. var=val des get et post
	// var :
	// .. Option .. utiliser : $var = _request($var);
/*
	foreach($_GET as $k => $v) { $$k=$_GET[$k]; }
	foreach($_POST as $k => $v) { $$k=$_POST[$k]; }
*/

	// elements spip
	global 	$connect_statut,
			$connect_toutes_rubriques,
			$connect_id_auteur,
			$couleur_claire, $couleur_foncee;


	$forum = intval(_request('forum'));
	
	include_spip('inc/headers');
	http_no_cache();
	include_spip('inc/commencer_page');
	echo init_entete(_T('spipbb:redige_post').' : '.$forum,'');

	echo "<body>\n";

	echo "<a name='haut_page'></a>";

	if ($connect_statut != '0minirezo') {
		echo _T('avis_non_acces_page');
		fin_page();
		exit;
	}

	if (_request('valid_post')) {
		// enregistrer le post
		enregistre_post_spipbb();

		?><script type='text/javascript'> self.close(); </script> <?php

	}
	else {
		// affiche formulaire
		echo "<div style='padding:10px;'>";	
		debut_cadre_relief("");

		# bouton fermer popup
		echo "<div style='float:right; padding:2px;'>\n";
		icone(_T('spipbb:icone_ferme'), "javascript:window.close();", _DIR_IMG_SPIPBB."gaf_post.gif", "supprimer.gif");
		echo "</div><br>\n";

		affiche_form_post();
		
		fin_cadre_relief();
		echo "</div>\n";
	}

	//
	echo "\n</body>\n</html>";
} // exec_spipbb_formpost

?>
