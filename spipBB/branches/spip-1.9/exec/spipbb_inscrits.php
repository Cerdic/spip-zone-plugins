<?php
#---------------------------------------------------------------#
#  Plugin  : spipbb - Licence : GPL                             #
#  File    : exec/spipbb_inscrits - members management          #
#  Authors : Hugues AROUX scoty 2007                            #
#  https://contrib.spip.net/Plugin-SpipBB#contributeurs      #
#  Contact : scoty!@!koakidi!.!com                              #
# [fr] Page des inscrits                                        #
# [en] Members' management                                      #
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
if (!defined("_INC_SPIPBB_COMMON")) include_spip('inc/spipbb_common');
spipbb_log('included',2,__FILE__);

# initialiser spipbb
include_spip('inc/spipbb_init');
# requis de cet exec
include_spip('inc/traiter_imagerie');
# pour le javascript de (de)selection
include_spip('inc/spipbb_inc_formpost');

// ------------------------------------------------------------------------------
// ------------------------------------------------------------------------------
function exec_spipbb_inscrits() {
	# requis spip
	global 	$connect_statut,
			$connect_toutes_rubriques,
			$connect_id_auteur,
			$couleur_claire, $couleur_foncee;

	// c: 19/12/7 : a passer dans action
	// pour le moment on les passe au statut poubelle : il faudra : choisir ce que l'on fait de leurs message et verifier qu'on peut faire cela comme �a
	$sel_membre=_request('selectmembre');
	if (is_array($sel_membre) and count($sel_membre)>0) {
		//$list_id_del=join(",",$sel_membre);
		$list_id_del=sql_in('id_auteur',$sel_membre);
		switch(_request('supprmess')) {
		case 'nom' : // detacher les messages des auteurs a supprimer
			// c: 7/2/8 compat multibases
			//$forumq=sql_query("UPDATE spip_forums SET id_auteur=0 WHERE ($list_id_del)");
			@sql_updateq("spip_forums", "id_auteur=0", $list_id_del);
			break;
		case 'anonymes' : // detacher et rendre anonyme tous les messages de tous les auteurs a supprimer
			// c: 7/2/8 compat pg_sql
			//$forumq=sql_query("UPDATE spip_forums SET id_auteur=0,auteur='Anonyme' WHERE ($list_id_del)");
			@sql_updateq("spip_forums","id_auteur=0,auteur='Anonyme'",$list_id_del);
			break;
		case 'tous' : // effacer tous les messages de tous les auteurs a supprimer
			// il faut traiter les messages principaux s�par�ment des threads
			// c: 7/2/8 compat pg_sql
			//$req = sql_query ("SELECT id_forum FROM spip_forums WHERE ($list_id_del) AND id_parent=0");
			$req = sql_select("id_forum","spip_forums","$list_id_del AND id_parent=0");
			while ( $row = sql_fetch($req) ) {
				// c: 7/2/8 compat pg_sql
				//$req_fils = sql_query ("SELECT id_forum FROM spip_forums WHERE id_parent=".$row['id_forum']." ORDER BY id_forum LIMIT 0,1" );
				$req_fils = sql_select("id_forum","spip_forums","id_parent=".$row['id_forum'],"",array("id_forum"),"0,1" );
				if ( $row_fils=sql_fetch($req_fils) ) {
					// on decale le premier enfant comme nouveau pere
					// c: 7/2/8 compat pg_sql
					//$upd_fils = sql_query ("UPDATE spip_forums SET id_parent=0 WHERE id_forum=".$row_fils['id_forum']);
					@sql_updateq("spip_forums",array('id_parent'=>0),"id_forum=".$row_fils['id_forum']);
					// on lui rattache les eventuels autres enfants
					// c: 7/2/8 compat pg_sql
					//$upd_thread = sql_query ("UPDATE spip_forums SET id_parent=".$row_fils['id_forum']." WHERE id_parent=".$row['id_forum']);
					@sql_updateq("spip_forums",array('id_parent'=>$row_fils['id_forum']),"id_parent=".$row['id_forum']);
				}
			}
			// maintenant on peut tout effacer
			// c: 7/2/8 compat pg_sql
			//$forumq=sql_query("UPDATE spip_forums SET statut='5poubelle' WHERE ($list_id_del)");
			@sql_updateq("spip_forums",array('statut'=>'5poubelle'),$list_id_del);
			break;
		} // switch traitement des messages dans les forums
		// c: 7/2/8 compat pg_sql
		//$delq=sql_query("UPDATE spip_auteurs SET statut='5poubelle' WHERE ($list_id_del)");
		@sql_updateq("spip_auteurs",array('statut'=>'5poubelle'),$list_id_del);
	}

	$vl=intval(_request('vl'));

	# limites requete
	$dl=($vl+0);
	$fixlimit = $GLOBALS['spipbb']['fixlimit'];

	# tri
		$tri=_request('tri');
		if($tri=='nom') { $odb='nom'; }
		else { $odb='id_auteur'; }

	# c: 8/1/8 : filtrage sur les types de membres
	$sel_type=_request('seltype');
	if (!(is_array($sel_type) and count($sel_type)>0)) {
		$sel_type=array('6forum','nouveau');
	}

	# Les des differents types de membres r�ellement dans la base ( sauf poubelle ?)
	$sel_membre=sql_select("statut","spip_auteurs","statut!='5poubelle'","statut");
	$liste_types=array();
	while ($row=sql_fetch($sel_membre)) {
		$liste_types[]=$row['statut'];
	}
	if (count($liste_types)==0) $list_types=array('6forum','nouveau');

	# requete principale
	// c: 18/12/7 c'est surement une optimisation mais je doute que ce soit standard tous SQL confondus...
	// La magie du LEFT JOIN pour calculer le nombre de messages et ne pas masquer un membre qui n'aurait jamais poste !!!
	$types_query=sql_in('A.statut',$sel_type);
	/* c: 7/2/8 compat pg_sql
	$q=sql_query("SELECT SQL_CALC_FOUND_ROWS A.id_auteur, A.nom, A.email, A.statut, count(F.id_forum) as nb_mes "
					." FROM spip_auteurs AS A "
					." LEFT JOIN spip_forum AS F ON (A.id_auteur=F.id_auteur) "
//					." WHERE ( A.statut='6forum' OR A.statut='nouveau' ) "
					." WHERE ( $types_query ) "
					." GROUP BY A.id_auteur "
					." ORDER BY A.$odb "
					." LIMIT $dl,$fixlimit");
					# recup nombre total d'entree
	$nl= sql_query("SELECT FOUND_ROWS()");
	$r_found = @spip_fetch_array($nl);
	$nligne=$r_found['FOUND_ROWS()'];
	*/

	$r_found=sql_fetsel("COUNT(*) AS total",
					"spip_auteurs AS A LEFT JOIN spip_forum AS F ON (A.id_auteur=F.id_auteur) ",
					$types_query,
					"A.id_auteur",
					"",
					"$dl,$fixlimit");
	if (isset($r_found['total'])) $nligne=$r_found['total'];
		else $nligne=0;


	$q=sql_select(array("A.id_auteur","A.nom","A.email","A.statut","count(F.id_forum) as nb_mes"), // rows
					"spip_auteurs AS A LEFT JOIN spip_forum AS F ON (A.id_auteur=F.id_auteur) ", //from
					$types_query, // where
					"A.id_auteur", // groupby
					array("A.$odb "), //orderby
					"$dl,$fixlimit"); // limit

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

	// valeur de tranche affich�e
	$nba1 = $dl+1;
	//
	// adresse retour des tranche
	$retour_spipbb_local = generer_url_ecrire("spipbb_inscrits");

	echo gros_titre(_T('spipbb:admin_titre_page_'._request('exec')),'',false);

	// Pr�senter valeurs de la tranche de la requ�te
	echo "<div class='iconeoff verdana2' style='text-align:center;clear:both;'>\n";
	tranches_liste_forum($nba1,$retour_spipbb_local,$nligne);
	echo "\n</div>\n";
/*
	// c: 19/12/7 cest gore ce javascript je ne sais pas faire mieux !  attention il depends du nom du formulaire.
	echo "<script language=\"JavaScript\" type=\"text/javascript\">\n"
		. "<!--\n"
		. "	function check_switch(val)\n"
		. "	{\n"
		. "		for( i = 0; i < document.formmembre.elements.length; i++ )\n"
		. "		{\n"
		. "			document.formmembre.elements[i].checked = val;\n"
		. "		}\n"
		. "	}\n"
		. "//-->\n"
		. "</script>\n";
*/
	echo  "<form method='post' action='".generer_url_ecrire(_request('exec'))."' name='formfiltre'>\n";

	echo "<div style='text-align:right'>\n";
	# c 9/1/8 : la liste officielle des statuts cf ecrire/inc/instituer_auteur : $GLOBALS['liste_des_statuts']
	$trad_types = array(	"0minirezo" => _T('item_administrateur_2'),
					"1comite" =>  _T('intem_redacteur'),
					"6forum" => _T('item_visiteur'),
					"nouveau" => _T('item_nouvel_auteur'));

	reset($liste_types);
	while (list(,$type)=each($liste_types)) {
		$checked = (in_array($type,$sel_type)) ? "checked='checked' " :" ";
		echo "<input type='checkbox' name='seltype[]' id='seltype' value='$type' $checked/>\n";
		echo "<label for='seltype_$type'>".$trad_types[$type]."</label>\n";
	}
	echo "</div>\n";
	echo "<div style='text-align:right'><input type='submit' name='_spipbb_selectionner' value='"
		. _T('spipbb:filtrer')
		. "' class='fondo' /></div>\n";
	echo "</form>\n";
	echo  "<form method='post' action='".generer_url_ecrire(_request('exec'))."' name='formmembre'>\n";

	// ent�te ...
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
// c: 8/1/8 est-ce bien utile ici ?
//			"<td width='14%' style='text-align:center;'>"._T('spipbb:col_signature')."</td>\n".
//			"<td width='14%' style='text-align:center;'>"._T('spipbb:col_avatar')."</td>\n".
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
				. http_img_pack('fiche-perso-24.gif','ico'," border='0' valign='absmiddle'",_T('spipbb:signature'))
				. "</a>";
		}

		if($infos['date_crea_spipbb']!='') {
			$aff_date=affdate($infos['date_crea_spipbb'],'d/m/Y');
		}
		else $aff_date="";
		$aut_spec=($r['statut']=='nouveau') ? "*" : ( ($r['statut']=='0minirezo') ? "!" : "" );
		echo "<tr bgcolor='".$coul_ligne."'>\n".
			"<td>".$r['id_auteur'].$aut_spec."</td>".
			"<td><a href='".generer_url_ecrire("auteur_infos","id_auteur=".$r['id_auteur'])."'>".couper(typo($r['nom']),20)."</a></td>".
			"<td style='text-align:center;'>".
			"<a href='mailto:".htmlspecialchars($r['email'])."'>".
			http_img_pack('envoi-message-24.gif','mail'," border='0' align='absmiddle'",htmlspecialchars($r['email'])).
			"</a></td>".
			"<td style='text-align:center;'>".$aff_date."</td>".
//			"<td style='text-align:center;'>".$ico_signature."</td>".
//			"<td style='text-align:center;'>".$ico_avatar."</td>".
			"<td style='text-align:center;'>";
		if ($r['nb_mes']>0) echo "<a href='".generer_url_public('liste_messages',"id_auteur=".$r['id_auteur']."&id_rubrique=".$GLOBALS['spipbb']['id_secteur'])."'>".$r['nb_mes']."</a>";
		else echo $r['nb_mes'];
		echo "</td>".
			"<td width='2%' style='text-align:center;'><input type='checkbox' name='selectmembre[]' value='".$r['id_auteur']."' /></td>".
			"</tr>\n";
	}
	echo "</table>\n";
	echo tout_de_selectionner("formmembre");
	/*
	echo "<div style='text-align:right;margin: 4px 0 4px 0;'><a href=\"javascript:check_switch(true);\">"
		. _T('spipbb:bouton_select_all')
		. "</a> :: <a href=\"javascript:check_switch();\">"
		. _T('spipbb:bouton_unselect_all')."</a></div>\n";
*/

	# c 8/1/8 liste des actions possibles (en checkbox ?)
	# supprimer membre
	# + supprimer messages
	# ou laisser messages avec champ nom (et pas d'id_auteur)
	# ou rendre anonyme messages
	echo "<div style='text-align:right;'>\n"
		. _T('spipbb:messages_supprimer_titre_dpt')
		. "<select name='supprmess' style='margin:0 4px 0 0;font-size:90%'>"
		. "<option value='tous' selected='selected'>". _T('spipbb:messages_supprimer_tous') . "</option>"
		. "<option value='nom'>". _T('spipbb:messages_laisser_nom') . "</option>"
		. "<option value='anonymes'>". _T('spipbb:messages_anonymes') . "</option>"
		. "</select>\n" ;

	echo "<input type='submit' name='_spipbb_supprimer' value='"
		. _T('spipbb:supprimer')
		. "' class='fondo' /></div>\n";
	echo "</form>\n";
	echo "<div id='code'></div>\n";
	echo "<div id='code_sign'></div>\n";

	echo fin_cadre_formulaire(true);


	# pied page exec
	bouton_retour_haut();

	echo fin_gauche(), fin_page();

} // exec_spipbb_inscrits

?>
