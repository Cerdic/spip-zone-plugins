<?php


	/**
	 * SPIP-Lettres
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
	include_spip('inc/documents');
	include_spip('lettres_fonctions');


	function exec_lettres_edit() {

		$new = (intval(_request('id_lettre'))==0);
		$id_lettre = intval(_request('id_lettre'));
		$id_rubrique = intval(_request('id_rubrique'));
		if (($new AND !autoriser('creerlettredans','rubrique',$id_rubrique)) OR
			(!$new AND (!autoriser('voir', 'lettre', $id_lettre) OR !autoriser('modifier','lettre', $id_lettre)))) {

			include_spip('inc/minipres');
			echo minipres();
			exit;
		}

		pipeline('exec_init', array('args' => array('exec' => 'lettres_edit', 'id_lettre' => $_GET['id_lettre']), 'data' => ''));


		if (!$new) {
			$lettre = new lettre($id_lettre);
		} else {
			if (!$id_rubrique) $id_rubrique = sql_getfetsel('id_rubrique', 'spip_rubriques', 'statut="publie"', 'id_rubrique', '1');
			if (!$id_rubrique) $id_rubrique = sql_getfetsel('id_rubrique', 'spip_rubriques', '', 'id_rubrique', '1');
			$lettre = new lettre();
			$lettre->titre			= _T('lettresprive:nouvelle_lettre');
			$lettre->id_rubrique	= $id_rubrique;
		}
		
		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page($lettre->titre, "naviguer", "lettres_tous");

		echo debut_grand_cadre(true);
		echo afficher_hierarchie($lettre->id_rubrique);
		echo fin_grand_cadre(true);

		echo debut_gauche("",true);

		if ($lettre->existe){
			echo afficher_documents_colonne($lettre->id_lettre, 'lettre');
		} else {
			# ICI GROS HACK
			# -------------
			echo afficher_documents_colonne(0-$GLOBALS['visiteur_session']['id_auteur'], 'lettre');
		}

		echo pipeline('affiche_gauche', array('args' => array('exec' => 'lettres_edit', 'id_lettre' => $lettre->id_lettre), 'data' => ''));
		echo creer_colonne_droite("",true);
		echo pipeline('affiche_droite', array('args' => array('exec' => 'lettres_edit', 'id_lettre' => $lettre->id_lettre), 'data' => ''));
		echo debut_droite("",true);


		$oups = ($new
			? generer_url_ecrire("naviguer","id_rubrique=".$id_rubrique)
			: generer_url_ecrire("lettres","id_lettre=".$id_lettre)
			);

		$contexte = array(
		'icone_retour'=>icone_inline(_T('icone_retour'), $oups, find_in_path("prive/images/lettre-24.png"), "rien.gif",$GLOBALS['spip_lang_left']),
		'redirect'=>generer_url_ecrire("lettres"),
		'titre'=>$lettre->titre,
		'new'=>$id_lettre,
		'id_rubrique'=>$lettre->id_rubrique,
		);

		$milieu = recuperer_fond("prive/editer/lettre", $contexte);

		echo pipeline('affiche_milieu',array('args'=>array('exec'=>'lettres_edit','id_lettre'=>$id_lettre),'data'=>$milieu));

	 	
		echo fin_gauche();

		echo fin_page();

	}

?>