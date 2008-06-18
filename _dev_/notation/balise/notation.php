<?php
/**
* Plugin Notation v.0.1
* par JEM (jean-marc.viglino@ign.fr)
* 
* Copyright (c) 2007
* Logiciel libre distribue sous licence GNU/GPL.
*  
* Definition des balises (recuperer id_article courant dans le formulaire)
*  
**/

include_spip('inc/notation_util');
include_spip('inc/vieilles_defs');
include_spip('balise/notation_balises');

if (!defined("_ECRIRE_INC_VERSION")) return;	#securite


function balise_NOTATION ($p) {
	return calculer_balise_dynamique($p,'NOTATION', array('id_article'));
}

function balise_NOTATION_stat($args, $filtres) {
	// Pas d'id_article ? Erreur de squelette
	if (!$args[0])
		return erreur_squelette(
			_T('zbug_champ_hors_motif',
				array ('champ' => '#NOTATION',
					'motif' => 'ARTICLES')), '');
	return $args;
}

function balise_NOTATION_dyn($id_article=0, $auteur_session=array()) {
	
	if ($GLOBALS["auteur_session"]) {
		$id_auteur = $GLOBALS['auteur_session']['id_auteur'];
	}else{
		$id_auteur = 0;
	}

	$ip	= $_SERVER['REMOTE_ADDR'];
	
	//recuperation des champs
	$note = intval(_request('note'));
	$robot = _request('content');
	$id_donnees	= _request('id_donnees');
	$acces = notation_get_acces();
	
	$erreur = '';

	//  s'assurer que l'article existe bien
	if (sql_countsel('spip_articles', 'id_article=' . sql_quote($id_article))){
		
		// Est-on autorise a voter
		$isauteur = ($statut=="0minirezo" || $statut=="1comite");
		if ($acces=='all')
			$canvote= true;
		else{
			$statut = $auteur_session['statut'];
			if (($acces=='ide' && $statut!='') || ($acces=='aut' && isauteur) || ($acces=='adm' && $statut=="0minirezo"))
				$canvote= true;
		}
		
		// On est en train de voter
		if ($canvote && ($id_donnees==$id_article) && $robot==''){	// Note correcte ?
			if($note<1 || $note>notation_get_nb_notes()){
				$erreur = _T('notation:note_hors_plage');
			}else{
				include_spip('base/abstract_sql');
				include_spip('ecrire/inc_connect');
				// Si pas inscrit : recuperer la note de l'article sur l'IP
				if ($id_auteur == 0){
					$res = sql_select(
						'spip_notations.id_notation,spip_notations.id_auteur,spip_notations.note',
						'spip_notations',
						'id_article=' . sql_quote($id_article) . ' AND ip=' . sql_quote($ip)
						);
				// Sinon rechercher la note de l'auteur
				}else{
					$res = sql_select(
						'spip_notations.id_notation,spip_notations.id_auteur,spip_notations.note',
						'spip_notations',
						'id_article=' . sql_quote($id_article) . ' AND id_auteur=' . sql_quote($id_auteur)
						);
				}
				// Premier vote
				if (sql_count($res) == 0){  // Remplir la table de notation
					sql_insertq('spip_notations', array(
						'id_article' => $id_article,
						'id_auteur' => $id_auteur,
						'ip' => $ip,
						'note' => $note
						));
					$duchangement = true;
				}else{  // Modifier la note
					$row = sql_fetch($res);
					// Seulement si elle a changee ou que l'auteur a change
					if ($row['note'] != $note || ($row['id_auteur'] != $id_auteur)){  // Un auteur non reference ne remplace pas la note d'un auteur reference
						if ($row['id_auteur'] == 0 || $id_auteur != 0){
							sql_update('spip_notations', array(
								'note' => $note,
								'id_auteur' => $id_auteur),
								'id_notation=' . sql_quote($row['id_notation'])
								);
							$duchangement = true;
						}
					}
				}
				// Calculer la nouvelle note de l'article
				if ($duchangement){
					$res = sql_select(
						'spip_notations.id_article,spip_notations.note',
						'spip_notations',
						'id_article=' . sql_quote($id_article)
						);
					$lanote = 0;
					$total = 0;
					while ($row =sql_fetch($res)){
						$lanote += $row['note'];
						$total++;
					}
					$lanote = $lanote/$total;
					$lanote = intval($lanote*100)/100;
					$note = round($lanote);
					$note_ponderee = notation_ponderee ($lanote, $total);
					// Remplir la table de notation des articles
					sql_insertq('spip_notations_articles', array(
						'id_article' => $id_article,
						'note' => $note,
						'note_ponderee' => $note_ponderee,
						'nb' => $nb
						));
					// Mettre ajour dans les autres cas
					sql_update('spip_notations_articles', array(
						'note' => $lanote,
						'note_ponderee' => $note_ponderee,
						'nb' => $total),
						'id_article=' . sql_quote($id_article)
						);
				}
			}
		}
		$res = sql_select(
			'spip_notations_articles.id_article,spip_notations_articles.note_ponderee,spip_notations_articles.nb',
			'spip_notations_articles',
			'id_article=' . sql_quote($id_article)
			);
		$lanote=0;
		$total=0;
		if ($row = sql_fetch($res)){
			$lanote = $row['note_ponderee'];
			$total = $row['nb'];
		}
		$note = round($lanote);
	}

	return array(
		'formulaires/notation',
		0,
		array(
			'id_article'=>$id_article,
			'canvote'=>$canvote,
			'note'=>$note,
			'total'=>$total,
			'erreur' => $erreur
		)
	);
}

?>