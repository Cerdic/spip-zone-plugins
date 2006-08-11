<?php
/*
 * charts
 *
 * Auteur :
 * Cedric MORIN
 * © 2006 - Distribue sous licence GNU/GPL
 *
 */

include_spip("base/charts");
include_spip("inc/charts");

	function charts_pre_propre($texte) {
		static $reset;
		// Mecanisme de mise a jour des liens
		$charts = array();
		$maj_liens = ($_GET['exec']=='articles' AND $id_article = intval($_GET['id_article']));
		if ($maj_liens) {
			if (!$reset) {
				$query = "DELETE FROM spip_charts_articles WHERE id_article=$id_article";
				spip_query($query);
				$reset = true;
			}
		}

		// Remplacer les raccourcis de type <chartXXX|modificateur>
		// par le produit du squelette modele_chart[_modificateur]
		if ((strpos($texte, '<chart')!==NULL) &&
			preg_match_all(',<chart([0-9]+)([|]([a-z_0-9]+))?'.'>,', $texte, $regs, PREG_SET_ORDER)) {
			foreach ($regs as $r) {
				$id_chart = $r[1];
				$charts[$id_chart] = $id_chart;
				
				$fond = 'modele_chart'.($r[3]?("_".$r[3]):'');
				include_spip('public/assembler');
				$contexte = array('id_chart' => $id_chart);
				$page = recuperer_fond($fond, $contexte);
				
				$texte = str_replace($r[0], code_echappement($page), $texte);
			}
		}
		if ($maj_liens && $charts) {
			$query = "INSERT INTO spip_charts_articles (id_article, id_chart) ".
				"VALUES ($id_article, ".join("), ($id_article, ", $charts).")";
			spip_query($query);
		}
	
		return $texte;
	}

	function charts_affiche_droite($flux){
		if (_request('exec')=='articles_edit'){
			$flux['data'] .= charts_afficher_insertion_chart($flux['arg']['id_article']);
		}
		return $flux;
	}
	function charts_insert_head($flux){
		$flux .= 	"<link rel='stylesheet' href='".find_in_path('spip_charts.css')."' type='text/css' media='all' />\n";
		return $flux;
	}
	function charts_header_prive($flux){
		$flux .= 	"<link rel='stylesheet' href='".find_in_path('spip_charts.css')."' type='text/css' media='all' />\n";
		return $flux;
	}

?>