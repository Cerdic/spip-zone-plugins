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
	// Ajouter les champs de dates floues
	$dates_floues = recuperer_fond('formulaires/inc-agendaflou', $flux['args']['contexte']);
	$flux['data'] = preg_replace('@<li[^>]*class=(\'|")editer_repetitions[^\'"]*\\1[^>]*>@is', "$dates_floues$0", $flux['data']);
	
	// Ajouter une case pour afficher/masquer les dates floues
	$masque = '<label for="utiliser_dates_floues">'._T('agendaflou:date_floue_utiliser').'</label><input onclick="if (this.checked==false) { $(\'.editer_dates_floues\').hide(\'fast\').find(\'option:selected\').removeAttr(\'selected\');} else {$(\'.editer_dates_floues\').show(\'fast\');}" id="utiliser_dates_floues" class="checkbox" type="checkbox" '.(($flux['args']['contexte']['date_debut_floue'] or $flux['args']['contexte']['date_fin_floue']) ? 'checked="checked"' : '').'/>';
	$flux['data'] = preg_replace('@<input[^>]*name=(\'|")horaire\\1[^>]*>@is', "$0$masque", $flux['data']);
	
	return $flux;
}
