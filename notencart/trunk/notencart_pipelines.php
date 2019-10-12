<?php
/*
 * plugin: notencart
 * licence: GPL3
 * copyleft: 2019-09-20 -- 9999-12-31
 *
 */

if ( !defined('_ECRIRE_INC_VERSION') ) {
	return;
}

/** 
 * Insertion des CSS dans le prive
 *
 * @param string $flux
 *
 * @return string
 */
function notencart_header_prive($flux) {
	return $flux
		. '<link href="'
		. find_in_path('css/notencart.css')
		. '" rel="stylesheet" type="text/css" />'
		. "\n" ;
}

/** 
 * Insertion des CSS dans le public
 *
 * @param string $flux
 *
 * @return string
 */
function notencart_insert_head_css($flux) {
	return $flux
		. '<link href="'
		. find_in_path('css/notencart.css')
		. '" rel="stylesheet" type="text/css" />'
		. "\n" ;
}

/**
 * Liste des notes supportees
 * plus correspondances (abreviations et autres langues)
 *
 * @return array
 */
function notencart_connus() {
	$notetypes = array(
		'alert' => 'fatal',
		'alerte' => 'fatal',
		'astuce' => 'tip',
		'attention' => 'important',
		'attn' => 'important',
		'avert' => 'warning',
		'avertissement' => 'warning',
		'avoid' => 'bad',
		'beware' => 'important',
		'bloquante' => 'warning',
		'bon' => 'good',
		'careful' => 'important',
		'caution' => 'important',
		'classic' => 'information',
		'classique' => 'information',
		'clue' => 'hint',
		'conseil' => 'tip',
		'correct' => 'good',
		'correcte' => 'good',
		'cqfd' => 'qed',
		'critical' => 'warning',
		'critique' => 'warning',
		'demo' => 'qed',
		'demonstration' => 'qed',
		'detective' => 'hint',
		'do' => 'good',
		'do' => 'good',
		'doit' => 'good',
		'dont' => 'bad',
		'erreur' => 'fatal',
		'error' => 'fatal',
		'eureka' => 'tip',
		'eviter' => 'bad',
		'faire' => 'good',
		'fatale' => 'fatal',
		'flop' => 'bad',
		'idea' => 'tip',
		'idee' => 'tip',
		'importante' => 'important',
		'indice' => 'hint',
		'info' => 'information',
		'infos' => 'information',
		'jamais' => 'bad',
		'ko' => 'bad',
		'loupe' => 'hint',
		'mal' => 'bad',
		'memo' => 'notice',
		'nepas' => 'bad',
		'never' => 'bad',
		'non' => 'bad',
		'not' => 'bad',
		'ok' => 'good',
		'oui' => 'good',
		'postit' => 'notice',
		'preuve' => 'qed',
		'proof' => 'qed',
		'prudence' => 'important',
		'reveal' => 'spoil',
		'revelation' => 'spoil',
		'search' => 'hint',
		'secret' => 'spoil',
		'simple' => 'information',
		'siren' => 'fatal',
		'sirene' => 'fatal',
		'spoil' => 'spoil',
		'spoiler' => 'spoil',
		'standard' => 'notice',
		'top' => 'good',
		'truc' => 'tip',
		'tuyeau' => 'tip',
		'warn' => 'warning',
		'yes' => 'good',
		'zoom' => 'hint',
	);
	return $notetypes ;
}

function notencart_name2class($notetype) {
	return trim(strtolower(str_replace(' ', '', $notetype))) ;
}

/**
 * Teste la presence de notes dans un texte
 *
 * @param string $texte
 *
 * @return bool
 */
function notencart_teste($texte='') {
	if ( 
		( strpos($texte, '</note_') === false
			or
			strpos($texte, '<note_') === false )
		and ( strpos($texte, '</note ') === false
			or
			strpos($texte, '<note ') === false )
		and ( strpos($texte, '[/note_') === false
			or
			strpos($texte, '[note_') === false )
		and ( strpos($texte, '[/note ') === false
			or
			strpos($texte, '[note ') === false )
	) {
		return false ;
	}
	return true ;
}

/**
 * Evite les transformations de typo dans les balises comme
 * 'html|code|cadre|frame|script|acronym|cite' etc.
 *
 * @param string $texte
 *   texte a filtrer
 * @param string|false $filtre
 *   le filtre a appliquer pour transformer le texte
 * @param string|false $balises
 *   balises concernees par l'echappement
 *   si '' alors c'est la protection par defaut sur la liste de _PROTEGE_BLOCS
 *   si false alors le texte est utilise tel quel
 * @param null|array $args
 *   arguments supplementaires a passer au filtre
 *
 * @return string
 */
function notencart_filtre_texte_echappe($texte, $filtre, 
	$balises='', $args=null) {
	if ( !strlen($texte) ) {
		return '' ;
	}
	if ( $filtre !== false ) {
		$fonction = chercher_filtre($filtre, false);
		if ( !$fonction ) {
			spip_log("notencart_filtre_texte_echappe(): $filtre() non defini",
			_LOG_ERREUR) ;
			return $texte ;
		}
		$filtre = $fonction ;
	}
	// protection du texte
	if ( $balises !== false ) {
		if ( !strlen($balises) ) {
			$balise = _PROTEGE_BLOCS ;
		}
		else {
			$balises = ',<(' . $balise
				. ')(\s[^>]*)?>(.*)</\1>,UimsS' ;
		}
		if ( !function_exists('echappe_html') ) {
			include_spip('inc/texte_mini') ;
		}
		$texte = echappe_html($texte, 'FILTRETEXTECHAPPE',
			true, $balises) ;
	}
	// retour du texte simplement protege
	if ( $filtre === false ) {
		return $texte ;
	}
	// transformation par $fonction
	if ( !$args ) {
		$texte = $filtre($texte) ;
	}
	else {
		array_unshift($args, $texte) ;
		$texte = call_user_func_array($filtre, $args) ;
	}
	// deprotection des balises
	return echappe_retour($texte, 'FILTRETEXTECHAPPE') ;
}

/**
 * Remplacer les raccourcis de note dans un bloc de texte
 *
 * @param string $texte
 *
 * @return string
 */
function notencart_remplacer($texte) {
	if ( !notencart_teste($texte) ) {
		return $texte ;
	}
	// premiere passe : raccourcis et standards en fr
	$rempl = array() ;
	$notetypes = notencart_connus() ;
	// 1.1. generer les correspondances
	foreach( $notetypes as $n_fr => $n_en ) {
		$c_en = notencart_name2class($n_en) ;
		$c_fr = notencart_name2class($n_fr) ;
		$rempl["[$n_fr]"] = "<div class='$c_en'>" ;
		$rempl["[/$n_fr]"] = '</div>' ;
		$rempl["[note $n_fr]"] = "<div class='notencart note_$c_en'>" ;
		$rempl["[note_$n_fr]"] = "<div class='notencart note_$c_en'>" ;
		$rempl["<note $n_fr>"] = "<div class='notencart note_$c_en'>" ;
		$rempl["<note_$n_fr>"] = "<div class='notencart note_$c_en'>" ;
		$rempl["</$n_fr>"] = '</div>' ;
		$rempl["<$n_en>"] = "<div class='notencart note_$c_en'>" ;
		$rempl["</$n_en>"] = '</div>' ;
		$rempl["[$n_en]"] = "<div class='notencart note_$c_en'>" ;
		$rempl["[/$n_en]"] = '</div>' ;
	}
	// 1.2. enfin les fermetures generiques
	$rempl['</note>'] = '</div>' ;
	$rempl['[/note]'] = '</div>' ;
	// balancer la sauce maintenant
	$texte = str_replace(array_keys($rempl), 
		array_values($rempl), $texte) ;
	// seconde passe pour les fermetures et les perso...
	$texte = preg_replace( '#<note(_|\s+)(\w+?)>#',
		'<div class="notencart note_${2}">', $texte) ;
	$texte = preg_replace( '#</note(_|\s+)(\w+?)>#',
		'</div>', $texte) ;
	$texte = preg_replace( '#\[note(_|\s+)(\w+?)\]#',
		'<div class="notencart note_${2}">', $texte) ;
	$texte = preg_replace( '#\[/note(_|\s+)(\w+?)\]#',
		'</div>', $texte) ;
	return $texte ;
}

/**
 * Lancer le remplacement des notes en dehors des balises, si besoin
 *
 * @param string $texte
 *
 * @return string
 */
function notencart_pre_typo($texte) {
	if ( !notencart_teste($texte) ) {
		return $texte ;
	}
	// appeler notencart_remplacer() une fois les balises protegees
	return notencart_filtre_texte_echappe($texte, 'notencart_remplacer') ;
}

/**
 * Inserer les icones dans le Porte-Plume
 *
 * @param array $barres
 *
 * @return array
 */
function notencart_porte_plume_barre_pre_charger($barres) {
	$notetypes = notencart_connus() ;
	// boutons sous-menus
	$smenu = array() ;
	foreach ( array_unique($notetypes) as $k => $v ) {
		$id = notencart_name2class($v) ;
		if ( find_in_path("icones_barre/note-$id-16.png") ) {
			$smenu[] = array(
				'id' => "notencart_$id",
				'name' => _T('notencart:note_'.$id),
				'className' => "notencart_$id",
				'openBlockWith' => "[note_$v]\n",
				'closeBlockWith' => "\n[/note_$v]",
				'selectionType' => 'line',
				'forceMultiline' => true,
				'display' => true,
			) ;
		}
	}
	// on ajoute les boutons dans la barre d'edition seulement
	foreach (array('edition') as $nom) {
		$barre = &$barres[$nom] ;
		$barre->ajouterPlusieursApres('grpCaracteres', array(
			array(
				'id' => 'sepNotencart',
				'separator' => '---------------',
				'display' => true,
			),
			array(
				'id' => 'notencarts',
				'name' => _T('notencart:inserer_note'),
				'className' => 'notencarts', 
				#'openBlockWith' => "<note>\n",
				#'closeBlockWith' => "\n</note>",
				#'replaceWith' => '',
				'selectionType' => 'line',
				'display' => true,
				'dropMenu' => $smenu,
			),
		) );
	}
	return $barres ;
}

/**
 * Definir les icones correspondant a chaque bouton note dans PP
 *
 * @param array $flux
 *
 * @return array
 */
function notencart_porte_plume_lien_classe_vers_icone($flux) {
	$icones = array( 
		'notencarts' => 'notencart-16.png', 
	);
	$notetypes = notencart_connus() ;
	foreach ( array_unique($notetypes) as $k => $v ) {
		$id = notencart_name2class($v) ;
		if ( find_in_path("icones_barre/note-$id-16.png") ) {
			$icones["notencart_$id"] = "note-$id-16.png" ;
		}
	}
	return array_merge($flux, $icones);
}

