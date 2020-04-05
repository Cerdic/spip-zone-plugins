<?php

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Charger les infos de l'histoire a partir du texte
 * @param array $flux
 * @return array
 */
function storify_formulaire_charger($flux){

	if ($flux['args']['form']=='editer_article'
	  AND isset($flux['data']['texte'])){

		include_spip('inc/storify');
		$v = storify_from_texte($flux['data']['texte'], true, _request('storify'));
		$flux['data'] = array_merge($flux['data'], $v);
	}
	return $flux;
}

/**
 * Afficher le formulaire d'edition de l'histoire
 * @param array $flux
 * @return array
 */
function storify_formulaire_fond($flux){
	if ($flux['args']['form']=='editer_article'){

		if (preg_match(',<(li|div)[^>]*editer_texte\b[^>]*>,Uims', $flux['data'], $m)) {

			$extra = recuperer_fond("formulaires/inc-editer_article_story",$flux['args']['contexte']);
			$p = strpos($flux['data'],$m[0]);
			$flux['data'] = substr_replace($flux['data'],$extra,$p,0);
		}
		
	}

	return $flux;
}

/**
 * Etape de verification : on traite les demandes d'ajout/suppression de bloc
 * Storify/Unstorify
 *
 * @param $flux
 * @return mixed
 */
function storify_formulaire_verifier($flux){
	if ($flux['args']['form']=='editer_article'){
		$keep_edit = false;
		if (_request('unstorify')) {
			set_request('storify', 0);
			$keep_edit = true;
		}
		elseif (_request('storify')) {
			set_request('storify', 1);
			$keep_edit = true;
		}
		elseif (_request('storified')) {
			set_request('storify', 1);
		}
		if (_request('story_line_change_type')) {
			$keep_edit = true;
		}
		if (_request('story_line_change_type')) {
			$keep_edit = true;
		}
		if ($k = _request('story_line_up')) {
			include_spip('inc/storify');
			$k = array_keys($k);
			$k = reset($k);
			$story_lines = storify_up_line($k, _request('story_lines'));
			set_request('story_lines', $story_lines);
			$keep_edit = true;
		}
		if ($k = _request('story_line_down')) {
			include_spip('inc/storify');
			$k = array_keys($k);
			$k = reset($k);
			$story_lines = storify_down_line($k, _request('story_lines'));
			set_request('story_lines', $story_lines);
			$keep_edit = true;
		}
		if (_request('story_line_append_line')) {
			$story_lines = _request('story_lines');
			$story_lines[] = array();
			include_spip('inc/storify');
			$story_lines = storify_valide_story($story_lines);
			set_request('story_lines', $story_lines);
			$keep_edit = true;
		}

		if ($keep_edit) {
			$flux['data']['_edit_story'] = ' ';
			if (!isset($flux['data']['message_erreur'])) {
				$flux['data']['message_erreur'] = '';
			}
		}
		if (!$keep_edit and !count($flux['data'])) {
			if (_request('storified')) {
				include_spip('inc/storify');
				$texte = storify_story_to_texte(_request('story_lines'));
				set_request('texte', $texte);
			}
		}
	}
	return $flux;
}
