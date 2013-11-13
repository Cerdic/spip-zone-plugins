<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

function qr_insert_head_css($flux){
	$flux .= '<link rel="stylesheet" href="'.find_in_path('css/qr.css').'" type="text/css" media="all" />';
	return $flux;
}

function qr_header_prive($flux){
	$flux .= '<script src="'.find_in_path('js/qr.js').'" type="text/javascript"></script>';
	return $flux;
}

function qr_porte_plume_barre_pre_charger($barres){
	// on ajoute les boutons dans la barre d'édition seulement
	foreach (array('edition') as $nom) {
		$barre = &$barres[$nom];
		$barre->ajouterPlusieursApres('grpCaracteres', array(
			array(
				"id" => "qr_sep",
				"separator" => "---------------",
				"display"   => true,
			),
			array(
				"id"          => 'qr',
				"name"        => _T('qr:outil_inserer_qr'),
				"className"   => 'outil_qr',
				"openBlockWith" => "<faq format=\"dl\">\n",
				"closeBlockWith" => "\n</faq>",
				"replaceWith" => "function(h){ return outil_qr(h, '?', true);}",
				"selectionType" => "line",
				"display"     => true,
				"dropMenu" => array(
					// bouton ?
					array(
						"id"          => 'qr_question',
						"name"        => _T('qr:outil_inserer_question'),
						"replaceWith" => "function(h){ return outil_qr(h, '?');}",
						"className"   => 'outil_qr_question',
						"selectionType" => "line",
						"forceMultiline" => true,
						"display"     => true,
					),
					array(
						"id"          => 'qr_titre',
						"name"        => _T('qr:outil_inserer_titre'),
						"replaceWith" => "function(h){ return outil_qr(h, ':Nouveau titre');}",
						"className"   => 'outil_qr_titre',
						"selectionType" => "line",
						"forceMultiline" => true,
						"display"     => true,
					),
				)
			)
		));
		$barre->ajouterFonction("function outil_qr(h, c,recursif) {
					if(recursif){
						// Cas de la sélection de click sur le bouton de création de todo complète
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

function qr_porte_plume_lien_classe_vers_icone($flux){
	return array_merge($flux, array(
		'outil_qr'=>'qr-16.png',
		'outil_qr_question'=>'qr_question-16.png',
		'outil_qr_titre'=>'qr_titre-16.png'
	));
}
?>
