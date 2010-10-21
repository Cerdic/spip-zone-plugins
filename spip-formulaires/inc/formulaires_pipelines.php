<?php


	/**
	 * SPIP-Formulaires
	 *
	 * Copyright (c) 2006-2009
	 * Agence Artégo http://www.artego.fr
	 *  
	 * Ce programme est un logiciel libre distribue sous licence GNU/GPLv3.
	 * Pour plus de details voir http://www.gnu.org/licenses/gpl-3.0.html
	 *  
	 **/


	function formulaires_header_prive($texte) {
		$blocs_tous.= '    $("#blocs_tous").load("'.generer_url_ecrire('blocs_tous', '', true).'&id_formulaire="+id_form, function(){'."\n";
		$blocs_tous.= '      $("a.editer_position_bloc").bind("click", editer_position_bloc);'."\n";
		$blocs_tous.= '      $("a.supprimer_bloc").bind("click", supprimer_bloc);'."\n";
		$blocs_tous.= '      $("a.editer_position_question").bind("click", editer_position_question);'."\n";
		$blocs_tous.= '      $("a.supprimer_question").bind("click", supprimer_question);'."\n";
		$blocs_tous.= '      $("a.editer_position_choix_question").bind("click", editer_position_choix_question);'."\n";
		$blocs_tous.= '      $("a.supprimer_choix_question").bind("click", supprimer_choix_question);'."\n";
		$blocs_tous.= '    })'."\n";

		$js.= '<script type="text/javascript">'."\n";
		$js.= '$(document).ready(function() {'."\n";
		$js.= '  $("a.editer_position_bloc").click(editer_position_bloc);'."\n";
		$js.= '  $("a.supprimer_bloc").click(supprimer_bloc);'."\n";
		$js.= '  $("a.editer_position_question").click(editer_position_question);'."\n";
		$js.= '  $("a.supprimer_question").click(supprimer_question);'."\n";
		$js.= '  $("a.editer_position_choix_question").click(editer_position_choix_question);'."\n";
		$js.= '  $("a.supprimer_choix_question").click(supprimer_choix_question);'."\n";
		$js.= '});'."\n";
		$js.= 'function editer_position_bloc() {'."\n";
		$js.= '  var chaine;'."\n";
		$js.= '  chaine = this.href.match(/id_formulaire=\d*/);'."\n";
		$js.= '  chaine = chaine.toString();'."\n";
		$js.= '  var tableau = chaine.split(/=/);'."\n";
		$js.= '  var id_form = tableau[1];'."\n";
		$js.= '  var chaine;'."\n";
		$js.= '  chaine = this.href.match(/id_bloc=\d*/);'."\n";
		$js.= '  chaine = chaine.toString();'."\n";
		$js.= '  var tableau = chaine.split(/=/);'."\n";
		$js.= '  var id = tableau[1];'."\n";
		$js.= '  chaine = this.href.match(/position=\d*/);'."\n";
		$js.= '  chaine = chaine.toString();'."\n";
		$js.= '  var tableau = chaine.split(/=/);'."\n";
		$js.= '  var pos = tableau[1];'."\n";
		$js.= '  $("#searching-formulaire").css("visibility","visible");'."\n";
		$js.= '  $.post("'.generer_url_action('editer_position_bloc', '', true, true).'", { id_formulaire: id_form, id_bloc: id, position: pos, ajax: 1 }, function(data) {'."\n";
		$js.= $blocs_tous;
		$js.= '  });'."\n";
		$js.= '  return false;'."\n";
		$js.= '}'."\n";
		$js.= 'function supprimer_bloc() {'."\n";
		$js.= '  var chaine;'."\n";
		$js.= '  chaine = this.href.match(/id_formulaire=\d*/);'."\n";
		$js.= '  chaine = chaine.toString();'."\n";
		$js.= '  var tableau = chaine.split(/=/);'."\n";
		$js.= '  var id_form = tableau[1];'."\n";
		$js.= '  var chaine;'."\n";
		$js.= '  chaine = this.href.match(/id_bloc=\d*/);'."\n";
		$js.= '  chaine = chaine.toString();'."\n";
		$js.= '  var tableau = chaine.split(/=/);'."\n";
		$js.= '  var id = tableau[1];'."\n";
		$js.= '  $("#searching-formulaire").css("visibility","visible");'."\n";
		$js.= '  $.post("'.generer_url_action('supprimer_bloc', '', true, true).'", { id_formulaire: id_form, id_bloc: id, ajax: 1 }, function(data) {'."\n";
		$js.= $blocs_tous;
		$js.= '  });'."\n";
		$js.= '  return false;'."\n";
		$js.= '}'."\n";
		$js.= 'function editer_position_question() {'."\n";
		$js.= '  var chaine;'."\n";
		$js.= '  chaine = this.href.match(/id_formulaire=\d*/);'."\n";
		$js.= '  chaine = chaine.toString();'."\n";
		$js.= '  var tableau = chaine.split(/=/);'."\n";
		$js.= '  var id_form = tableau[1];'."\n";
		$js.= '  var chaine;'."\n";
		$js.= '  chaine = this.href.match(/id_bloc=\d*/);'."\n";
		$js.= '  chaine = chaine.toString();'."\n";
		$js.= '  var tableau = chaine.split(/=/);'."\n";
		$js.= '  var id_blo = tableau[1];'."\n";
		$js.= '  var chaine;'."\n";
		$js.= '  chaine = this.href.match(/id_question=\d*/);'."\n";
		$js.= '  chaine = chaine.toString();'."\n";
		$js.= '  var tableau = chaine.split(/=/);'."\n";
		$js.= '  var id = tableau[1];'."\n";
		$js.= '  chaine = this.href.match(/position=\d*/);'."\n";
		$js.= '  chaine = chaine.toString();'."\n";
		$js.= '  var tableau = chaine.split(/=/);'."\n";
		$js.= '  var pos = tableau[1];'."\n";
		$js.= '  $("#searching-formulaire").css("visibility","visible");'."\n";
		$js.= '  $.post("'.generer_url_action('editer_position_question', '', true, true).'", { id_formulaire: id_form, id_bloc: id_blo, id_question: id, position: pos, ajax: 1 }, function(data) {'."\n";
		$js.= $blocs_tous;
		$js.= '  });'."\n";
		$js.= '  return false;'."\n";
		$js.= '}'."\n";
		$js.= 'function supprimer_question() {'."\n";
		$js.= '  var chaine;'."\n";
		$js.= '  chaine = this.href.match(/id_formulaire=\d*/);'."\n";
		$js.= '  chaine = chaine.toString();'."\n";
		$js.= '  var tableau = chaine.split(/=/);'."\n";
		$js.= '  var id_form = tableau[1];'."\n";
		$js.= '  var chaine;'."\n";
		$js.= '  chaine = this.href.match(/id_bloc=\d*/);'."\n";
		$js.= '  chaine = chaine.toString();'."\n";
		$js.= '  var tableau = chaine.split(/=/);'."\n";
		$js.= '  var id_blo = tableau[1];'."\n";
		$js.= '  var chaine;'."\n";
		$js.= '  chaine = this.href.match(/id_question=\d*/);'."\n";
		$js.= '  chaine = chaine.toString();'."\n";
		$js.= '  var tableau = chaine.split(/=/);'."\n";
		$js.= '  var id = tableau[1];'."\n";
		$js.= '  $("#searching-formulaire").css("visibility","visible");'."\n";
		$js.= '  $.post("'.generer_url_action('supprimer_question', '', true, true).'", { id_formulaire: id_form, id_bloc: id_blo, id_question: id, ajax: 1 }, function(data) {'."\n";
		$js.= $blocs_tous;
		$js.= '  });'."\n";
		$js.= '  return false;'."\n";
		$js.= '}'."\n";
		$js.= 'function editer_position_choix_question() {'."\n";
		$js.= '  var chaine;'."\n";
		$js.= '  chaine = this.href.match(/id_formulaire=\d*/);'."\n";
		$js.= '  chaine = chaine.toString();'."\n";
		$js.= '  var tableau = chaine.split(/=/);'."\n";
		$js.= '  var id_form = tableau[1];'."\n";
		$js.= '  var chaine;'."\n";
		$js.= '  chaine = this.href.match(/id_bloc=\d*/);'."\n";
		$js.= '  chaine = chaine.toString();'."\n";
		$js.= '  var tableau = chaine.split(/=/);'."\n";
		$js.= '  var id_blo = tableau[1];'."\n";
		$js.= '  var chaine;'."\n";
		$js.= '  chaine = this.href.match(/id_question=\d*/);'."\n";
		$js.= '  chaine = chaine.toString();'."\n";
		$js.= '  var tableau = chaine.split(/=/);'."\n";
		$js.= '  var id_q = tableau[1];'."\n";
		$js.= '  var chaine;'."\n";
		$js.= '  chaine = this.href.match(/id_choix_question=\d*/);'."\n";
		$js.= '  chaine = chaine.toString();'."\n";
		$js.= '  var tableau = chaine.split(/=/);'."\n";
		$js.= '  var id = tableau[1];'."\n";
		$js.= '  chaine = this.href.match(/position=\d*/);'."\n";
		$js.= '  chaine = chaine.toString();'."\n";
		$js.= '  var tableau = chaine.split(/=/);'."\n";
		$js.= '  var pos = tableau[1];'."\n";
		$js.= '  $("#searching-formulaire").css("visibility","visible");'."\n";
		$js.= '  $.post("'.generer_url_action('editer_position_choix_question', '', true, true).'", { id_formulaire: id_form, id_bloc: id_blo, id_question: id_q, id_choix_question: id, position: pos, ajax: 1 }, function(data) {'."\n";
		$js.= $blocs_tous;
		$js.= '  });'."\n";
		$js.= '  return false;'."\n";
		$js.= '}'."\n";
		$js.= 'function supprimer_choix_question() {'."\n";
		$js.= '  var chaine;'."\n";
		$js.= '  chaine = this.href.match(/id_formulaire=\d*/);'."\n";
		$js.= '  chaine = chaine.toString();'."\n";
		$js.= '  var tableau = chaine.split(/=/);'."\n";
		$js.= '  var id_form = tableau[1];'."\n";
		$js.= '  var chaine;'."\n";
		$js.= '  chaine = this.href.match(/id_bloc=\d*/);'."\n";
		$js.= '  chaine = chaine.toString();'."\n";
		$js.= '  var tableau = chaine.split(/=/);'."\n";
		$js.= '  var id_blo = tableau[1];'."\n";
		$js.= '  var chaine;'."\n";
		$js.= '  chaine = this.href.match(/id_question=\d*/);'."\n";
		$js.= '  chaine = chaine.toString();'."\n";
		$js.= '  var tableau = chaine.split(/=/);'."\n";
		$js.= '  var id_q = tableau[1];'."\n";
		$js.= '  var chaine;'."\n";
		$js.= '  chaine = this.href.match(/id_choix_question=\d*/);'."\n";
		$js.= '  chaine = chaine.toString();'."\n";
		$js.= '  var tableau = chaine.split(/=/);'."\n";
		$js.= '  var id = tableau[1];'."\n";
		$js.= '  $("#searching-formulaire").css("visibility","visible");'."\n";
		$js.= '  $.post("'.generer_url_action('supprimer_choix_question', '', true, true).'", { id_formulaire: id_form, id_bloc: id_blo, id_question: id_q, id_choix_question: id, ajax: 1 }, function(data) {'."\n";
		$js.= $blocs_tous;
		$js.= '  });'."\n";
		$js.= '  return false;'."\n";
		$js.= '}'."\n";
		$js.= '</script>'."\n";
		$texte.= $js;
		return $texte;
	}


	function formulaires_declarer_tables_objets_surnoms($surnoms) {
		$surnoms['formulaire'] = 'formulaires';
		$surnoms['applicant'] = 'applicants';
		return $surnoms;
	}
	
	
	function formulaires_rechercher_liste_des_champs($tables) {
		$tables['formulaire']['titre']		= 8;
		$tables['formulaire']['descriptif']	= 4;
		$tables['formulaire']['chapo']		= 3;
		$tables['formulaire']['texte']		= 2;
		$tables['formulaire']['ps']			= 1;
		$tables['applicant']['email']		= 8;
		return $tables;
	}


	function formulaires_tester_rubrique_vide($flux) {
		$flux['data']+= sql_countsel('spip_formulaires', 'id_rubrique='.$flux['args']['id_rubrique']);
		return $flux;
	}


	function formulaires_calculer_rubriques($flux) {
		$r = sql_select("rub.id_rubrique AS id, max(fille.date) AS date_h", "spip_rubriques AS rub, spip_formulaires AS fille", "rub.id_rubrique = fille.id_rubrique AND rub.date_tmp <= fille.date AND fille.statut='en_ligne' ", "rub.id_rubrique");
		while ($row = sql_fetch($r))
		  sql_updateq('spip_rubriques', array('statut_tmp'=>'publie', 'date_tmp'=>$row['date_h']), "id_rubrique=".$row['id']);
		return $flux;
	}


	function formulaires_trig_propager_les_secteurs($flux) {
		$r = sql_select("fille.id_formulaire AS id, maman.id_secteur AS secteur", "spip_formulaires AS fille, spip_rubriques AS maman", "fille.id_rubrique = maman.id_rubrique AND fille.id_secteur <> maman.id_secteur");
		while ($row = sql_fetch($r))
			sql_update("spip_formulaires", array("id_secteur" => $row['secteur']), "id_formulaire=".$row['id']);
		return $flux;
	}


	function formulaires_calculer_langues_rubriques($flux) {
		$s = sql_select("fils.id_formulaire AS id_formulaire, mere.lang AS lang", "spip_formulaires AS fils, spip_rubriques AS mere", "fils.id_rubrique = mere.id_rubrique AND fils.langue_choisie != 'oui' AND (fils.lang='' OR mere.lang<>'') AND mere.lang<>fils.lang");
		while ($row = sql_fetch($s)) {
			$id_formulaire = $row['id_formulaire'];
			sql_updateq('spip_formulaires', array("lang"=> $row['lang'], 'langue_choisie'=>'non'), "id_formulaire=$id_formulaire");
		}
		return $flux;
	}


	function formulaires_contenu_naviguer($flux) {
		global $spip_lang_right;
		$id_rubrique = $flux['args']['id_rubrique'];
		$opt['id_rubrique'] = $id_rubrique;
		if (autoriser('voir', 'formulaires', NULL, NULL, $opt)) {
			if ($id_rubrique) {
				$flux['data'].= afficher_objets('formulaire', _T('formulairesprive:tous_formulaires_rubrique'), array('FROM' => 'spip_formulaires', 'WHERE' => 'id_rubrique='.intval($id_rubrique), 'ORDER BY' => 'maj DESC'));
				$flux['data'].= icone_inline(_T('formulairesprive:creer_nouveau_formulaire'), generer_url_ecrire("formulaires_edit", "id_rubrique=$id_rubrique"), _DIR_PLUGIN_FORMULAIRES.'/prive/images/formulaire-24.png',"creer.gif", $spip_lang_right);
				$flux['data'].= '<br class="nettoyeur" />';
			}
		}
		return $flux;
	}


	function formulaires_editer_contenu_objet($flux){
		if ($flux['args']['type'] == 'groupe_mot'){
			$checked = in_array('formulaires', $flux['args']['contexte']['tables_liees']);
			$checked = $checked ? ' checked="checked"' : '';
			$input = '<div class="choix"><input type="checkbox" class="checkbox" name="tables_liees&#91;&#93;" value="formulaires" id="formulaires"'.$checked.' /><label for="formulaires">'._T('formulairesprive:item_mots_cles_association_formulaires').'</label></div>';
			$flux['data'] = str_replace('<!--choix_tables-->',"$input\n<!--choix_tables-->", $flux['data']);
		}
		return $flux;
	}


	function formulaires_libelle_association_mots($libelles){
		$libelles['formulaires'] = 'formulairesprive:formulaires';
		return $libelles;
	}


?>