<?php

//    Fichier cr�� pour SPIP avec un bout de code emprunt� � celui ci.
//    Distribu� sans garantie sous licence GPL./
//    Copyright (C) 2006  Pierre ANDREWS
//
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

function exec_configurer_squelettesmots_dist(){

	include_spip("inc/presentation");
	include_spip("base/abstract_sql");

	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page(_T('squelettesmots:titre_page'), "configuration");


	include_spip('inc/autoriser');
	if (!autoriser('configurer', 'squelettesparmot')){
		echo _T('avis_non_acces_page');
		exit;
	}

	/*Affichage*/
	echo debut_gauche('', TRUE);

	echo debut_boite_info(TRUE);
	echo propre(_T('squelettesmots:help'));
	echo fin_boite_info(TRUE);

	echo debut_droite('', TRUE);

	echo gros_titre(_T('squelettesmots:titre_onglet'), '', FALSE);
	echo "<p>"._T('squelettesmots:gros_titre')."</p>";
	$config = charger_fonction('config', 'inc');
	$config();
	#echo avertissement_config();

	lire_fichier(find_in_path("squelettesmots.css"),$styles);
	echo "<style>$styles</style>";
	
	echo debut_cadre_relief("".find_in_path('spip_death.png')."", true);
	echo '<form action="'.generer_url_ecrire('configurer_squelettesmots').'" method="post">';

	$groupes_mots = '';
	$select = array('id_groupe', 'titre');
	$from = array('spip_groupes_mots');

	//	include_ecrire('inc_filtres');
	$rez = sql_select($select, $from);
	while ($row = sql_fetch($rez)){
		$groupes_mots[$row['id_groupe']] = extraire_multi($row['titre']);
	}
	sql_free($rez);

	//TODO: trouver automatiquement ces informations pour toutes les tables avec un jonction sur les mots
	$id_tables = array('articles' => 'id_article',
	                   'rubriques' => 'id_rubrique',
	                   'breves' => 'id_breve',
	                   'sites' => 'id_site');


	$fonds = unserialize(lire_meta('SquelettesMots:fond_pour_groupe'));

	$field_fonds = _request('fonds');
	$id_groupes = _request('tid_groupe');
	$types = _request('type');
	$actif = _request('actif');

	/*On transforme les _POST en jolie tableau*/
	if ($field_fonds){
		$new_fonds = array();
		foreach ($field_fonds as $index => $fond){
			$index = intval($index);
			$fond = addslashes($fond);
			if ($actif[$index]){
				$id_groupe = intval($id_groupes[$index]);
				$type = addslashes($types[$index]);
				$new_fonds[$fond] = array($id_groupe, $type, $id_tables[$type]);
			}
		}
		$fonds = $new_fonds;
	}

	$index = 0;
	if (is_array($fonds))
		foreach ($fonds as $fond => $a){
			list($id_groupe, $type, $id_table) = $a;
			$index++;
			echo '<fieldset class="regle">';
			echo '<legend>'._T('squelettesmots:reglei', array('id' => $index)).'</legend>';
			if (!find_in_path($fond.'.html')){
				echo '<div class="avertissement">';
				echo _T('squelettesmots:avertissement', array('squelette' => '<em>'.$fond.'.html'.'</em>'));
				echo '</div>';
			}
			// echo '<div class="champs">';
			echo "<input type=\"checkbox\" class=\"actif\" name=\"actif[$index]\" checked=\"true\"/>";
			echo "<label for=\"fond_$index\" class=\"fond\">"._T('squelettesmots:fond')."</label>";
			echo "<input type=\"text\" name=\"fonds[$index]\" class=\"forml\"  value=\"$fond\" id=\"fond_$index\"/>";
			echo "<label for=\"id_groupe_$index\" class=\"id_groupe\">"._T('squelettesmots:groupe')."</label>";
			echo "<select name=\"tid_groupe[$index]\" class=\"id_groupe forml\" id=\"id_groupe_$index\">";
			foreach ($groupes_mots as $id => $titre){
				echo "<option value=\"$id\"".(($id_groupe==$id) ? ' selected="true"' : '').">$titre</option>";
			}
			echo '</select>';
			echo "<label for=\"type_$index\" class=\"type\">"._T('squelettesmots:type')."</label>";
			echo "<select name=\"type[$index]\" class=\"type forml\" id=\"type_$index\">";
			foreach ($id_tables as $t => $x){
				echo "<option value=\"$t\"".(($type==$t) ? ' selected="true"' : '').">$t</option>";
			}
			echo '</select>';
			// echo '</div>';
			$select1 = array('titre');
			$from1 = array('spip_mots AS mots');
			$where1 = array("id_groupe=".intval($id_groupe));
			$rez = sql_select($select1, $from1, $where1);
			$liste_squel = '<ul>';
			$ext = 'html'; //On force a html, c'est pas beau, mais je vois pas la solution actuellement.
			$cnt_actif = 0;
			$cnt_inactif = 0;
			while ($r = sql_fetch($rez)){
				include_spip("inc/charsets");
				$n = translitteration(preg_replace('/["\'.\s]/', '_', extraire_multi($r['titre'])));
				if ($squel = find_in_path("$fond-$n.$ext")){
					$cnt_actif++;
					$liste_squel .= "<li><a href=\"$squel\">$fond-$n.$ext</a></li>";
				} else {
					$cnt_inactif++;
					$liste_squel .= "<li>$fond-$n.$ext</li>";
				}
				if ($squel = find_in_path("$fond=$n.$ext")){
					$cnt_actif++;
					$liste_squel .= "<li><a href=\"$squel\">$fond=$n.$ext</a></li>";
				} else {
					$cnt_inactif++;
					$liste_squel .= "<li>$fond=$n.$ext</li>";
				}
			}
			sql_free($rez);
			$liste_squel .= '</ul>';


			echo '<div class="possible">';
			if ($cnt_actif+$cnt_inactif>0){
				echo bouton_block_depliable(_T('squelettesmots:possibilites', array('total_actif' => $cnt_actif, 'total_inactif' => $cnt_inactif)), false);
				echo debut_block_depliable(false, "regle$index");
				echo $liste_squel;
				echo fin_block();
			}
			echo '</div>';

			echo '</fieldset>';
		}

	$index++;

	echo '<hr/>';
	echo '<fieldset class="nouvelle regle">';
	echo '<legend>'._T('squelettesmots:nouvelle_regle').'</legend>';
	echo "<input type=\"checkbox\" class=\"actif\" name=\"actif[$index]\"/>";
	echo "<label for=\"fond_$index\" class=\"fond\">"._T('squelettesmots:fond')."</label>";
	echo "<input type=\"text\" name=\"fonds[$index]\" class=\"forml\" value=\"article\"/>";

	echo "<label for=\"id_groupe_$index\" class=\"id_groupe\">"._T('squelettesmots:groupe')."</label>";
	echo "<select name=\"tid_groupe[$index]\" class=\"id_groupe forml\" id=\"id_groupe_$index\">";
	foreach ($groupes_mots as $id => $titre){
		echo "<option value=\"$id\">$titre</option>";
	}
	echo '</select>';
	echo "<label for=\"type_$index\" class=\"type\">"._T('squelettesmots:type')."</label>";
	echo "<select name=\"type[$index]\" class=\"type forml\" id=\"type_$index\">";
	foreach ($id_tables as $t => $x){
		echo "<option value=\"$t\">$t</option>";
	}
	echo '</select>';
	echo '</fieldset>';

	echo '<div style="text-align: right;"><input type="submit" class="fondo" value="'._T('bouton_valider').'"/></div>';
	echo '</form>';
	echo fin_cadre_relief(true);

	ecrire_meta('SquelettesMots:fond_pour_groupe', serialize($fonds));
	ecrire_metas();

	echo fin_gauche();
	echo fin_page();

}

?>
