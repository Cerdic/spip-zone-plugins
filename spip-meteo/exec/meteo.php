<?php


	/**
	 * SPIP-Météo
	 *
	 * Copyright (c) 2006-2009
	 * Agence Artégo http://www.artego.fr
	 *  
	 * Ce programme est un logiciel libre distribue sous licence GNU/GPLv3.
	 * Pour plus de details voir http://www.gnu.org/licenses/gpl-3.0.html
	 *  
	 **/


	if (!defined("_ECRIRE_INC_VERSION")) return;
	include_spip('inc/presentation');
	include_spip('meteo_fonctions');


	function exec_meteo() {

		if (!autoriser('voir', 'meteo')) {
			include_spip('inc/minipres');
			echo minipres();
			exit;
		}

		if (empty($_GET['id_meteo'])) {
			$url = generer_url_ecrire('meteo_tous');
			echo '<script language="javascript" type="text/javascript">window.location.replace("'.$url.'");</script>';
			exit();
		}

		$id_meteo = $_GET['id_meteo'];

		pipeline('exec_init',array('args'=>array('exec'=>'meteo','id_meteo'=>$id_meteo),'data'=>''));

		if (!empty($_GET['supprimer'])) {
			sql_delete('spip_previsions', 'id_meteo='.$id_meteo);
			sql_delete('spip_meteo', 'id_meteo='.$id_meteo);
			$url = generer_url_ecrire('meteo_tous');
			echo '<script language="javascript" type="text/javascript">window.location.replace("'.$url.'");</script>';
			exit();
		}

		if (!empty($_GET['recharger'])) {
			genie_meteo('');
			$url = generer_url_ecrire('meteo', 'id_meteo='.$id_meteo, true);
			echo '<script language="javascript" type="text/javascript">window.location.replace("'.$url.'");</script>';
			exit();
		}

		$res = sql_select('*', 'spip_meteo', 'id_meteo='.$id_meteo);
		$arr = sql_fetch($res);
		$id_meteo	= $arr['id_meteo'];
		$ville		= $arr['ville'];
		$code		= $arr['code'];
		$statut		= $arr['statut'];
		$maj		= $arr['maj'];
		
		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('meteoprive:meteo'), "naviguer", "meteo");

		echo debut_gauche('', true);

		echo '<div class="cadre cadre-info verdana1">';
		echo '<div class="cadre_padding">';
		echo '<div class="infos">';
		echo '<div class="numero">';
		echo _T('meteoprive:meteo_numero').' :';
		echo '<p>'.$id_meteo.'</p>';
		echo '</div>';
		echo '<ul class="instituer instituer_article">';
		echo '<li>';
		echo _T('meteoprive:cette_meteo');
		echo '<ul>';
		if ($statut == "publie") {
			echo '<li class="publie selected">'._T('meteoprive:publiee_en_ligne').'</li>';
		} else {
			echo '<li class="refuse selected">'._T('meteoprive:en_erreur').'</li>';
		}
		echo '<li class="prop"><a href="'.generer_url_ecrire('meteo', 'id_meteo='.$id_meteo.'&recharger=1').'">'._T('meteoprive:recharger').'</a></li>';
		echo '<li class="poubelle"><a href="'.generer_url_ecrire('meteo', 'id_meteo='.$id_meteo.'&supprimer=1').'">'._T('meteoprive:a_la_poubelle').'</a></li>';
		echo '</ul>';
		echo '</li>';
		echo '</ul>';
		if ($statut == "publie")
			echo '<table class="cellule-h-table" cellpadding="0" style="vertical-align: middle"><tr><td><a href="'.generer_url_meteo($id_meteo).'" class="cellule-h"><span class="cell-i"><img src="../prive/images/rien.gif" alt="'._T('meteoprive:voir_en_ligne').'"  style="background: url(../prive/images/racine-24.gif) center center no-repeat;" /></span></a></td><td class="cellule-h-lien"><a href="'.generer_url_meteo($id_meteo).'" class="cellule-h">'._T('meteoprive:voir_en_ligne').'</a></td></tr></table>';
		echo '</div>';
		echo '</div>';
		echo '</div>';

		echo pipeline('affiche_gauche',array('args'=>array('exec'=>'meteo','id_meteo'=>$_GET['id_meteo']),'data'=>''));

		echo bloc_des_raccourcis(icone_horizontale(_T('meteoprive:retour_liste_meteo'), generer_url_ecrire("meteo_tous"), _DIR_PLUGIN_METEO."prive/images/meteo-24.png", 'rien.gif', false));

		echo creer_colonne_droite('', true);
		echo pipeline('affiche_droite',array('args'=>array('exec'=>'meteo','id_meteo'=>$_GET['id_meteo']),'data'=>''));

		echo debut_droite('', true);

		echo '<div class="fiche_objet">';
		echo '<div class="bandeau_actions">';
		echo '<div style="float: right;">';
		echo icone_inline(_T('meteoprive:modifier_meteo'), generer_url_ecrire("meteo_edit", "id_meteo=$id_meteo"), _DIR_PLUGIN_METEO.'/prive/images/meteo-24.png', "edit.gif", $GLOBALS['spip_lang_left']);
		echo '</div>';
		echo '</div>';
		echo '<h1>'.ucfirst($ville).'</h1>';
		echo '<p>'._T('meteoprive:code_ville').' : '.$code.'</p>';
		if ($statut == "publie") {
			echo '<p>'._T('meteoprive:date_derniere_maj').' : '.affdate_heure($maj).'.</p>';
		} else {
			echo '<p style="color: red;">';
			echo _T('meteoprive:texte_probleme_recuperation_flux');
			echo '</p>';
		}
		echo '</div>';

		echo afficher_objets('prevision', _T('meteoprive:previsions_meteo'), array('FROM' => 'spip_previsions', 'WHERE' => 'id_meteo='.$id_meteo, 'ORDER BY' => 'date ASC'));

		echo fin_gauche();
		echo fin_page();

	}


?>