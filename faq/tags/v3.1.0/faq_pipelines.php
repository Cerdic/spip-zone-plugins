<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

function faq_insert_head_css($flux){
	// On inclut systématiquement les CSS de base
	$flux .= '<link rel="stylesheet" href="'.find_in_path('css/faq.css').'" type="text/css" media="all" />';

	// On ajoute si la config le demande les CSS propres aux dl
	include_spip('inc/config');
	$charger_css = lire_config('faq/charger_css') ? true : false;
	if ($charger_css)
		$flux .= '<link rel="stylesheet" href="'.find_in_path('css/faq_dl.css').'" type="text/css" media="all" />';
	return $flux;
}

function faq_insert_head($flux){
	include_spip('inc/config');
	$charger_js = lire_config('faq/charger_js') ? true : false;
	if ($charger_js)
		$flux .= '<script src="'.find_in_path('js/faq.js').'" type="text/javascript"></script>';
	return $flux;
}

function faq_porte_plume_barre_pre_charger($barres){
	// on ajoute les boutons dans la barre d'édition seulement
	foreach (array('edition') as $nom) {
		$barre = &$barres[$nom];
		$barre->ajouterPlusieursApres('grpCaracteres', array(
			array(
				"id" => "faq_sep",
				"separator" => "---------------",
				"display"   => true,
			),
			array(
				"id"          => 'faq',
				"name"        => _T('faq:outil_inserer_faq'),
				"className"   => 'outil_faq',
				"openBlockWith" => "<faq>\n",
				"closeBlockWith" => "\n</faq>",
				"replaceWith" => "function(h){ return outil_faq(h, '?', true);}",
				"selectionType" => "line",
				"display"     => true,
				"dropMenu" => array(
					// bouton ?
					array(
						"id"          => 'faq_question',
						"name"        => _T('faq:outil_inserer_question'),
						"replaceWith" => "function(h){ return outil_faq(h, '?');}",
						"className"   => 'outil_faq_question',
						"selectionType" => "line",
						"forceMultiline" => true,
						"display"     => true,
					),
					array(
						"id"          => 'faq_titre',
						"name"        => _T('faq:outil_inserer_titre'),
						"replaceWith" => "function(h){ return outil_faq(h, ':Nouveau titre');}",
						"className"   => 'outil_faq_titre',
						"selectionType" => "line",
						"forceMultiline" => true,
						"display"     => true,
					),
				)
			)
		));
		$barre->ajouterFonction("function outil_faq(h, c,recursif) {
					if(recursif){
						// Cas de la sélection de click sur le bouton de création de faq complète
						s = h.selection;
						lines = h.selection.split(/\\r?\\n/);
						var lines_final = [];
						for (j = 0, n = lines.length, i = 0; i < n; i++) {
							// si une seule ligne, on se fiche de savoir qu'elle est vide,
							// c'est volontaire si on clique le bouton
							if (n == 1 || $.trim(lines[i]) !== '') {
								if(r = lines[i].match(/^([+-o]) (.*)$/)){
									r[1] = r[1].replace(/[+-o]/g, c);
									lines_final[j] = r[1]+' '+r[2];
									j++;
								} else {
									lines_final[j] = c + ' '+lines[i];
									j++;
								}
							}
						}
						return lines_final.join('\\n');
					}
					// Click sur les autres boutons
					if ((s = h.selection) && (r = s.match(/^([+-o]) (.*)$/))){
						r[1] = r[1].replace(/[+-o]/g, c);
						s = r[1]+' '+r[2];
					} else {
						s = c + ' '+s;
					}
					return s;
				}");
	}
	return $barres;
}

function faq_porte_plume_lien_classe_vers_icone($flux){
	return array_merge($flux, array(
		'outil_faq'=>'faq-16.png',
		'outil_faq_question'=>'faq_question-16.png',
		'outil_faq_titre'=>'faq_titre-16.png'
	));
}

?>