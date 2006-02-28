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

include_spip("inc/forms_base");
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
			//include_once("inc_forms.php");
			foreach ($regs as $r) {
				$id_form = $r[1];
				$forms[$id_form] = $id_form;
				$cherche = $r[0];
				$remplace = "<html>".Forms_afficher_formulaire($id_form)."</html>";
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

	function Forms_generer_url_sondage($id_form) {
		return generer_url_public("sondage","id_form=$id_form");
	}

	// Hack crade a cause des limitations du compilateur
	function _Forms_afficher_reponses_sondage($id_form) {
		return Forms_afficher_reponses_sondage($id_form);
	}



?>