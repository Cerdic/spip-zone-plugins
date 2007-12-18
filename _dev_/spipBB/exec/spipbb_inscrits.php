<?php
#---------------------------------------------------------------#
#  Plugin  : spipbb - Licence : GPL                             #
#  File    : exec/spipbb_inscrits - members management          #
#  Authors : Hugues AROUX scoty 2007                            #
#  http://www.spip-contrib.net/Plugin-SpipBB#contributeurs      #
#  Contact : scoty!@!koakidi!.!com                              #
# [fr] Page des inscrits                                        #
# [en]                                                          #
#---------------------------------------------------------------#

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

// * [fr] Acces restreint, plugin pour SPIP * //
// * [en] Restricted access, SPIP plugin * //

if (!defined("_ECRIRE_INC_VERSION")) return;
spip_log(__FILE__.' : included','spipbb');

// ------------------------------------------------------------------------------
// ------------------------------------------------------------------------------
function exec_spipbb_inscrits() {
	# requis spip
	global 	$connect_statut,
			$connect_toutes_rubriques,
			$connect_id_auteur,
			$couleur_claire, $couleur_foncee;

	# initialiser spipbb
	include_spip('inc/spipbb_init');

	# requis de cet exec
	include_spip("inc/traiter_imagerie");

	$vl=intval(_request('vl'));

	# limites requete
	$dl=($vl+0);
	$fixlimit = $GLOBALS['spipbb']['fixlimit'];

	# tri
		$tri=_request('tri');
		if($tri=='nom') { $odb='nom'; }
		else { $odb='id_auteur'; }

	# requete principale
	// c: 18/12/7 c'est surement une optimisation mais je doute que ce soit standard tous SQL confondus...
	$q=sql_query("SELECT SQL_CALC_FOUND_ROWS id_auteur, nom, email, extra 
					FROM spip_auteurs 
					WHERE statut='6forum' 
					ORDER BY $odb 
					LIMIT $dl,$fixlimit");

	# recup nombre total d'entree
	$nl= sql_query("SELECT FOUND_ROWS()");
	$r_found = @spip_fetch_array($nl);
	$nligne=$r_found['FOUND_ROWS()'];


	#
	# affichage
	#
	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page(_L('titre_page_'._request('exec')), "forum", "spipbb_admin", '');
	echo "<a name='haut_page'></a>";

	echo debut_gauche('',true);
	spipbb_menus_gauche(_request('exec'),$id_salon,$id_art);


	echo debut_droite('',true);


	echo debut_cadre_formulaire('',true);

	// affichage tableau

	// valeur de tranche affichée
	$nba1 = $dl+1;
	//
	// adresse retour des tranche
	$retour_spipbb_local = generer_url_ecrire("spipbb_inscrits");

	echo gros_titre(_T('spipbb:inscrits_visiteurs'),'',false);

	// Présenter valeurs de la tranche de la requête
	echo "<div align='center' class='iconeoff verdana2' style='clear:both;'>\n";
	tranches_liste_forum($nba1,$retour_spipbb_local,$nligne);
	echo "\n</div>\n";

	// entête ...
	echo "<table border='0' cellpadding='2' cellspacing='0' width='100%'>\n
			<tr>\n".
			"<td width='8%'>";
			if($odb=='id_auteur') { echo "<b>&gt;"._T('spipbb:id_mjsc')."&lt;</b>"; }
			else { echo "<a href='".parametre_url(self(),'tri','')."'>"._T('spipbb:id_mjsc')."</a>"; }
			echo "</td>\n".
			"<td width='30%'>";
			if($odb=='nom') { echo "<b>&gt;"._T('spipbb:nom')."&lt;</b>"; }
			else { echo "<a href='".parametre_url(self(),'tri','nom')."'>"._T('spipbb:nom')."</a>"; }
			echo "</td>\n".
			"<td width='10%' style='text-align:center;'>"._T('spipbb:email')."</td>\n".
			"<td width='15%' style='text-align:center;'>"._L('date_crea')."</td>\n".
			"<td width='15%' style='text-align:center;'>"._L('signature')."</td>\n".
			"<td width='22%' style='text-align:center;'>"._T('spipbb:avatar')."</td>\n".
			"</tr>\n";


	// corps du tableau
	$ifond=0;
	while ($r=sql_fetch($q)) {
		$ifond = $ifond ^ 1;
		$coul_ligne = ($ifond) ? $couleur_claire : '#ffffff';

		# options gafospip
		$infos=spipbb_donnees_auteur($r['id_auteur']);
		$ico_avatar='';
		$ico_signature='';
		
		if($infos['avatar']!='') {
			$ico_avatar = "<a href='".$infos['avatar']."' class='load'>".
			http_img_pack('fiche-perso-24.gif','ico'," border='0' valign='absmiddle'",_T('spipbb:avatar')).
			"</a>";
		}
		if($infos['signature_post']!='') {
			$ico_signature = 
				"<a href='#' class='afftxt' id='"
				. $r['id_auteur']."'><p id='p".$r['id_auteur']
				. "' class='hidesign'>".$infos['signature']."</p>"
				. http_img_pack('fiche-perso-24.gif','ico'," border='0' valign='absmiddle'",_L('Signature'))
				. "</a>";
		}
		if($infos['date_crea_spipbb']!='') {
			$aff_date=affdate($infos['date_crea_spipbb'],'d/m/Y');
		}
		
		echo "<tr bgcolor='".$coul_ligne."'>".
			"<td>".$r['id_auteur']."</td>".
			"<td><a href='".generer_url_ecrire("auteur_infos","id_auteur=".$r['id_auteur'])."'>".typo($r['nom'])."</a></td>".
			"<td style='text-align:center;'>".
			"<a href='mailto:".htmlspecialchars($r['email'])."'>".
			http_img_pack('envoi-message-24.gif','mail'," border='0' align='absmiddle'",htmlspecialchars($r['email'])).
			"</a></td>".
			"<td style='text-align:center;'>".$aff_date."</td>".
			"<td style='text-align:center;'>".$ico_signature."</td>".
			"<td style='text-align:center;'>".$ico_avatar."</td>".
			"</tr>";
	}
	echo "</table>\n";
	echo "<div id='code'></div>";
	echo "<div id='code_sign'></div>";
		
	echo fin_cadre_formulaire(true);


	# pied page exec
	bouton_retour_haut();

	echo fin_gauche(), fin_page();

} // exec_spipbb_inscrits

?>
