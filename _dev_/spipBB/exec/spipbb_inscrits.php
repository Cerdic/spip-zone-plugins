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
include_spip('inc/spipbb_common');
spipbb_log('included',2,__FILE__);

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

	// c: 19/12/7 : a passer dans action
	// pour le moment on les passe au statut poubelle : il faudra : choisir ce que l'on fait de leurs message et verifier qu'on peut faire cela comme ça
	$sel_membre=_request('selectmembre');
	if (is_array($sel_membre) and count($sel_membre)>0) {
		$list_id_del=join(",",$sel_membre);
		$delq=sql_query("UPDATE spip_auteurs SET statut='5poubelle' WHERE id_auteur IN ($list_id_del)");
	}
	
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
	// La magie du LEFT JOIN pour calculer le nombre de messages et ne pas masquer un membre qui n'aurait jamais poste !!!
	$q=sql_query("SELECT SQL_CALC_FOUND_ROWS A.id_auteur, A.nom, A.email, A.statut, count(F.id_forum) as nb_mes
					FROM spip_auteurs AS A
					LEFT JOIN spip_forum AS F ON (A.id_auteur=F.id_auteur)
					WHERE ( A.statut='6forum' OR A.statut='nouveau' )
					GROUP BY A.id_auteur
					ORDER BY A.$odb 
					LIMIT $dl,$fixlimit");

					# recup nombre total d'entree
	$nl= sql_query("SELECT FOUND_ROWS()");
	$r_found = @spip_fetch_array($nl);
	$nligne=$r_found['FOUND_ROWS()'];


	#
	# affichage
	#
	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page(_T('spipbb:admin_titre_page_'._request('exec')), "forum", "spipbb_admin", '');
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

	echo gros_titre(_T('spipbb:admin_titre_page_'._request('exec')),'',false);

	// Présenter valeurs de la tranche de la requête
	echo "<div align='center' class='iconeoff verdana2' style='clear:both;'>\n";
	tranches_liste_forum($nba1,$retour_spipbb_local,$nligne);
	echo "\n</div>\n";
	// c: 19/12/7 cest gorse ce javascript je ne sais pas faire mieux !  attention il depends du nom du formulaire.
	echo "<script language=\"JavaScript\" type=\"text/javascript\">\n".
		"<!--\n".
		"	function check_switch(val)\n".
		"	{\n".
		"		for( i = 0; i < document.formmembre.elements.length; i++ )\n".
		"		{\n".
		"			document.formmembre.elements[i].checked = val;\n".
		"		}\n".
		"	}\n".
		"//-->\n".
		"</script>\n";
	echo  "<form method='post' action='' name='formmembre'>";
	
	// entête ...
	echo "<table border='0' cellpadding='2' cellspacing='0' width='100%'>\n
			<tr>\n".
			"<td width='8%'>";
			if($odb=='id_auteur') { echo "<b>&gt;"._T('spipbb:admin_id_mjsc')."&lt;</b>"; }
			else { echo "<a href='".parametre_url(self(),'tri','')."'>"._T('spipbb:admin_id_mjsc')."</a>"; }
			echo "</td>\n".
			"<td width='30%'>";
			if($odb=='nom') { echo "<b>&gt;"._T('spipbb:auteur')."&lt;</b>"; }
			else { echo "<a href='".parametre_url(self(),'tri','nom')."'>"._T('spipbb:auteur')."</a>"; }
			echo "</td>\n".
			"<td width='10%' style='text-align:center;'>"._T('spipbb:email')."</td>\n".
			"<td width='14%' style='text-align:center;'>"._T('spipbb:col_date_crea')."</td>\n".
			"<td width='14%' style='text-align:center;'>"._T('spipbb:col_signature')."</td>\n".
			"<td width='14%' style='text-align:center;'>"._T('spipbb:col_avatar')."</td>\n".
			"<td width='8%' style='text-align:center;'>"._T('spipbb:admin_total_posts')."</td>\n".
			"<td width='2%' style='text-align:center;'>"._T('spipbb:col_marquer')."</td>\n".
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
		$aut_nouv=($r['statut']=='nouveau') ? "*" : "";
		echo "<tr bgcolor='".$coul_ligne."'>\n".
			"<td>".$r['id_auteur'].$aut_nouv."</td>".
			"<td><a href='".generer_url_ecrire("auteur_infos","id_auteur=".$r['id_auteur'])."'>".couper(typo($r['nom']),20)."</a></td>".
			"<td style='text-align:center;'>".
			"<a href='mailto:".htmlspecialchars($r['email'])."'>".
			http_img_pack('envoi-message-24.gif','mail'," border='0' align='absmiddle'",htmlspecialchars($r['email'])).
			"</a></td>".
			"<td style='text-align:center;'>".$aff_date."</td>".
			"<td style='text-align:center;'>".$ico_signature."</td>".
			"<td style='text-align:center;'>".$ico_avatar."</td>".
			"<td style='text-align:center;'>".$r['nb_mes']."</td>".
			"<td width='2%' style='text-align:center;'><input type='checkbox' name='selectmembre[]' value='".$r['id_auteur']."' /></td>".
			"</tr>\n";
	}
	echo "</table>\n";
	echo "<div align='right'><a href=\"javascript:check_switch(true);\">"._T('spipbb:bouton_select_all')."</a> :: <a href=\"javascript:check_switch();\">"._T('spipbb:bouton_unselect_all')."</a></div>\n";
	echo "<div align='right'><input type='submit' name='_spipbb_supprimer' value='"._T('supprimer')."' class='fondo' /></div>\n";
	echo "</form>\n";
	echo "<div id='code'></div>";
	echo "<div id='code_sign'></div>";
		
	echo fin_cadre_formulaire(true);


	# pied page exec
	bouton_retour_haut();

	echo fin_gauche(), fin_page();

} // exec_spipbb_inscrits

?>
