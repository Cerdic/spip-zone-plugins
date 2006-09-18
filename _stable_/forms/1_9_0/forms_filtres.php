<?php
/*
 * forms
 * version plug-in de spip_form
 *
 * Auteur :
 * Antoine Pitrou
 * adaptation en 182e puis plugin par cedric.morin@yterium.com
 * © 2005,2006 - Distribue sous licence GNU/GPL
 *
 */

include_spip("base/forms");
include_spip("inc/forms");

//
// Formulaires
//


	// A reintegrer dans echapper_html()
	function Forms_forms_avant_propre($texte) {
		static $reset;
	//echo "forms_avant_propre::";
		// Mecanisme de mise a jour des liens
		$forms = array();
		$maj_liens = ($_GET['exec']=='articles' AND $id_article = intval($_GET['id_article']));
		if ($maj_liens) {
			if (!$reset) {
				$query = "DELETE FROM spip_forms_articles WHERE id_article=$id_article";
				spip_query($query);
				$reset = true;
			}
		}

		// Remplacer les raccourcis de type <formXXX>
		if (is_int(strpos($texte, '<form')) &&
			preg_match_all(',<form(\d+)>,', $texte, $regs, PREG_SET_ORDER)) {
			foreach ($regs as $r) {
				$id_form = $r[1];
				$forms[$id_form] = $id_form;
				$cherche = $r[0];
				$remplace = Forms_afficher_formulaire($id_form);
				// passer en base64 pour echapper a la typo()
				$remplace = code_echappement($remplace);
				$texte = str_replace($cherche, $remplace, $texte);
			}
		}
		if ($maj_liens && $forms) {
			$query = "INSERT INTO spip_forms_articles (id_article, id_form) ".
				"VALUES ($id_article, ".join("), ($id_article, ", $forms).")";
			spip_query($query);
		}
	
		return $texte;
	}

	// Hack crade a cause des limitations du compilateur
	function _Forms_afficher_reponses_sondage($id_form) {
		return Forms_afficher_reponses_sondage($id_form);
	}

	function Forms_affiche_droite($flux){
		if (_request('exec')=='articles_edit'){
			$flux['data'] .= Forms_afficher_insertion_formulaire($flux['arg']['id_article']);
		}
		return $flux;
	}

?>