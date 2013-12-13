<?php

function eval_benchmark_insert_head_css($flux){
	$flux .= '<link rel="stylesheet" href="'.find_in_path('css/eval_benchmark.css').'" type="text/css" media="all" />';
	return $flux;
}

function eval_benchmark_jqueryui_plugins($scripts){
	$scripts[] = "jquery.ui.draggable";
	return $scripts;
}


/**
 * Pouvoir lier une évaluation à une rubrique ayant la composition 'eval_benchmark'
 *
 * @pipeline affiche_milieu
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
function eval_benchmark_affiche_milieu($flux) {

	$texte = "";
	$e = trouver_objet_exec($flux['args']['exec']);

	if (!$e['edition'] AND in_array($e['type'], array('rubrique'))) {

		$id_rubrique = $flux['args'][$e['id_table_objet']];

		if (isset($flux['args']['contexte']['composition'])) {
			$composition = $flux['args']['contexte']['composition'];
		} else {
			$composition = sql_getfetsel('composition', 'spip_rubriques', 'id_rubrique=' . $id_rubrique);
		}

		if ($composition == 'eval_benchmark') {
			$texte .= recuperer_fond('prive/objets/editer/liens', array(
					'table_source' => 'evaluations',
					'objet' => $e['type'],
					'id_objet' => $id_rubrique
			));
		}

	}

	if ($texte) {
		if ($p=strpos($flux['data'],"<!--affiche_milieu-->"))
			$flux['data'] = substr_replace($flux['data'],$texte,$p,0);
		else
			$flux['data'] .= $texte;
	}

	return $flux;
}

?>
