<?php


	/**
	 * SPIP-Sondages
	 *
	 * Copyright (c) 2006-2009
	 * Agence Artégo http://www.artego.fr
	 *  
	 * Ce programme est un logiciel libre distribue sous licence GNU/GPLv3.
	 * Pour plus de details voir http://www.gnu.org/licenses/gpl-3.0.html
	 *  
	 **/


	function sondages_header_prive($texte) {
		$js = '<script type="text/javascript">'."\n";
		$js.= '$(document).ready(function() {'."\n";
		$js.= '  $("a.editer_position_choix").click(editer_position_choix);'."\n";
		$js.= '  $("a.supprimer_choix").click(supprimer_choix);'."\n";
		$js.= '});'."\n";
		$js.= 'function editer_position_choix() {'."\n";
		$js.= '  var chaine;'."\n";
		$js.= '  chaine = this.href.match(/id_sondage=\d*/);'."\n";
		$js.= '  chaine = chaine.toString();'."\n";
		$js.= '  var tableau = chaine.split(/=/);'."\n";
		$js.= '  var id_son = tableau[1];'."\n";
		$js.= '  var chaine;'."\n";
		$js.= '  chaine = this.href.match(/id_choix=\d*/);'."\n";
		$js.= '  chaine = chaine.toString();'."\n";
		$js.= '  var tableau = chaine.split(/=/);'."\n";
		$js.= '  var id = tableau[1];'."\n";
		$js.= '  chaine = this.href.match(/position=\d*/);'."\n";
		$js.= '  chaine = chaine.toString();'."\n";
		$js.= '  var tableau = chaine.split(/=/);'."\n";
		$js.= '  var pos = tableau[1];'."\n";
		$js.= '  $("#searching-choix").css("visibility","visible");'."\n";
		$js.= '  $.post("'.generer_url_action('editer_position_choix', '', true, true).'", { id_sondage: id_son, id_choix: id, position: pos, ajax: 1 }, function(data) {'."\n";
		$js.= '    $("#choix_tous").load("'.generer_url_ecrire('choix_tous', '', true).'&id_sondage="+id_son, function(){'."\n";
		$js.= '      $("a.editer_position_choix").bind("click", editer_position_choix);'."\n";
		$js.= '      $("a.supprimer_choix").bind("click", supprimer_choix);'."\n";
		$js.= '    })'."\n";
		$js.= '  });'."\n";
		$js.= '  return false;'."\n";
		$js.= '}'."\n";
		$js.= 'function supprimer_choix() {'."\n";
		$js.= '  var chaine;'."\n";
		$js.= '  chaine = this.href.match(/id_sondage=\d*/);'."\n";
		$js.= '  chaine = chaine.toString();'."\n";
		$js.= '  var tableau = chaine.split(/=/);'."\n";
		$js.= '  var id_son = tableau[1];'."\n";
		$js.= '  var chaine;'."\n";
		$js.= '  chaine = this.href.match(/id_choix=\d*/);'."\n";
		$js.= '  chaine = chaine.toString();'."\n";
		$js.= '  var tableau = chaine.split(/=/);'."\n";
		$js.= '  var id = tableau[1];'."\n";
		$js.= '  $("#searching-choix").css("visibility","visible");'."\n";
		$js.= '  $.post("'.generer_url_action('supprimer_choix', '', true, true).'", { id_sondage: id_son, id_choix: id, ajax: 1 }, function(data) {'."\n";
		$js.= '    $("#choix_tous").load("'.generer_url_ecrire('choix_tous', '', true).'&id_sondage="+id_son, function(){'."\n";
		$js.= '      $("a.editer_position_choix").bind("click", editer_position_choix);'."\n";
		$js.= '      $("a.supprimer_choix").bind("click", supprimer_choix);'."\n";
		$js.= '    })'."\n";
		$js.= '  });'."\n";
		$js.= '  return false;'."\n";
		$js.= '}'."\n";
		$js.= '</script>'."\n";
		$texte.= $js;
		return $texte;
	}


	function sondages_declarer_tables_objets_surnoms($surnoms) {
		$surnoms['sondage'] = 'sondages';
		$surnoms['choix'] = 'choix';
		return $surnoms;
	}
	
	
	function sondages_rechercher_liste_des_champs($tables) {
		$tables['sondage']['titre'] = 8;
		$tables['sondage']['texte'] = 1;
		return $tables;
	}


	function sondages_rechercher_liste_des_jointures($tables) {
		$tables['sondage']['choix']['titre'] = 1;
		return $tables;
	}

	function sondages_tester_rubrique_vide($flux) {
		$flux['data']+= sql_countsel('spip_sondages', 'id_rubrique='.$flux['args']['id_rubrique']);
		return $flux;
	}


	function sondages_calculer_rubriques($flux) {
		$r = sql_select("rub.id_rubrique AS id, max(fille.date) AS date_h", "spip_rubriques AS rub, spip_sondages AS fille", "rub.id_rubrique = fille.id_rubrique AND rub.date_tmp <= fille.date AND fille.statut IN ('publie','termine')", "rub.id_rubrique");
		while ($row = sql_fetch($r))
		  sql_updateq('spip_rubriques', array('statut_tmp'=>'publie', 'date_tmp'=>$row['date_h']), "id_rubrique=".$row['id']);
		return $flux;
	}


	function sondages_trig_propager_les_secteurs($flux) {
		$r = sql_select("fille.id_lettre AS id, maman.id_secteur AS secteur", "spip_sondages AS fille, spip_rubriques AS maman", "fille.id_rubrique = maman.id_rubrique AND fille.id_secteur <> maman.id_secteur");
		while ($row = sql_fetch($r))
			sql_update("spip_sondages", array("id_secteur" => $row['secteur']), "id_sondage=".$row['id']);
		return $flux;
	}


	function sondages_calculer_langues_rubriques($flux) {
		$s = sql_select("fils.id_sondage AS id_sondage, mere.lang AS lang", "spip_sondages AS fils, spip_rubriques AS mere", "fils.id_rubrique = mere.id_rubrique AND fils.langue_choisie != 'oui' AND (fils.lang='' OR mere.lang<>'') AND mere.lang<>fils.lang");
		while ($row = sql_fetch($s)) {
			$id_sondage = $row['id_sondage'];
			sql_updateq('spip_sondages', array("lang" => $row['lang'], 'langue_choisie' => 'non'), "id_sondage=$id_sondage");
		}
		return $flux;
	}


	function sondages_contenu_naviguer($flux) {
		global $spip_lang_right;
		if (autoriser('voir', 'sondages')) {
			$id_rubrique = $flux['args']['id_rubrique'];
			if ($id_rubrique) {
				$flux['data'].= afficher_objets('sondage', _T('sondagesprive:tous_sondages_rubrique'), array('FROM' => 'spip_sondages', 'WHERE' => 'id_rubrique='.intval($id_rubrique), 'ORDER BY' => 'maj DESC'));
				$flux['data'].= icone_inline(_T('sondagesprive:creer_nouveau_sondage'), generer_url_ecrire("sondages_edit", "id_rubrique=$id_rubrique"), _DIR_PLUGIN_SONDAGES.'/prive/images/sondage-24.png',"creer.gif", $spip_lang_right);
				$flux['data'].= '<br class="nettoyeur" />';
			}
		}
		return $flux;
	}
	
	
	function sondages_editer_contenu_objet($flux){
		if ($flux['args']['type'] == 'groupe_mot'){
			// ajouter l'input sur les sondages
			$checked = in_array('sondages', $flux['args']['contexte']['tables_liees']);
			$checked = $checked ? ' checked="checked"' : '';
			$input = '<div class="choix"><input type="checkbox" class="checkbox" name="tables_liees&#91;&#93;" value="sondages" id="sondages"'.$checked.' /><label for="sondages">'._T('sondagesprive:item_mots_cles_association_sondages').'</label></div>';
			$flux['data'] = str_replace('<!--choix_tables-->',"$input\n<!--choix_tables-->", $flux['data']);
		}
		return $flux;
	}


	function sondages_libelle_association_mots($libelles){
		$libelles['sondages'] = 'sondagesprive:sondages';
		return $libelles;
	}


?>