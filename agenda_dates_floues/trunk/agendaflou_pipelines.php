<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

// Déclarer les deux champs de date floue
function agendaflou_declarer_tables_objets_sql($flux){
	$flux['spip_evenements']['field']['date_debut_floue'] = 'varchar(10) not null default ""';
	$flux['spip_evenements']['field']['date_fin_floue'] = 'varchar(10) not null default ""';
	$flux['spip_evenements']['champs_editables'][] = 'date_debut_floue';
	$flux['spip_evenements']['champs_editables'][] = 'date_fin_floue';
	
	return $flux;
}

// Ajouter les champs dans le HTML
function agendaflou_formulaire_fond($flux){
	$dates_floues = recuperer_fond('formulaires/inc-agendaflou', $flux['args']['contexte']);
	
	$flux['data'] = preg_replace('@<li[^>]*class=(\'|")editer_repetitions[^\'"]*\\1[^>]*>@is', "$dates_floues$0", $flux['data']);
	
	return $flux;
}
