<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');
include_spip('inc/mots');
include_spip('base/abstract_sql');

function dspip_query($q) {
	error_log($q);
	spip_query($q);
}

error_log("G $from,$after,$into");

function action_bouger_mots() {
	global $from, $after, $into;

	error_log("$from,$after,$into");
	if($from) {
		$res=spip_query("select debut, fin, niveau, id_groupe from spip_mots where id_mot=$from");
		$row= spip_fetch_array($res);
		$debut_from= $row['debut']; $fin_from= $row['fin'];
		$niv_from= $row['niveau'];
		$groupe_from= $row['id_groupe'];
		$largeur_from= $fin_from-$debut_from+1;
	} else {
		die("from ??");
	}

	if($after || $into) {
		if($after) {
			$to= $after;
			$quoi='after';
		} else {
			$to= $into;
			$quoi='into';
		}
		// déplacer le noeud SOUS le noeud, c'est à dire en première position de
		// la sous arbo de ce noeud
		$res=spip_query("SELECT debut, fin, niveau, id_groupe
			 FROM spip_mots WHERE id_mot=$to");
		$row= spip_fetch_array($res);
		$debut_to= $row['debut']; $fin_to= $row['fin'];
		$niv_to= $row['niveau'];
		if($quoi=='into') {
			$niv_to++; // +1 puisqu'on deplace sous ce niveau
		}
		$groupe_to= $row['id_groupe'];

		error_log("$quoi $debut_from/$fin_from -> $debut_to/$fin_to");

		// déplacement vers le bas ou le haut, mais pas dans ses propres
		// sous branches
		if($fin_from<$fin_to || $debut_from>$debut_to) {
			// determiner le delta de niveaux
			if($niv_to!=$niv_from) {
				$delta= $niv_to-$niv_from;
				dspip_query("update spip_mots
 						   set niveau=niveau+$delta
 						 where debut>=$debut_from and fin<=$fin_from");
			}

			// mettre cette branche de cote
			dspip_query("update spip_mots
 				   set debut=-debut, fin=-fin
 				 where debut>=$debut_from and fin<=$fin_from");
			// et la changer de groupe si nécessaire
			if($groupe_from!=$groupe_to) {
				dspip_query("update spip_mots
 					   set id_groupe=$groupe_to
 					 where debut<0");
			}

			// retasser le trou qu'on vient de laisser
			dspip_query("update spip_mots
 				   set fin=fin-$largeur_from
 				 where fin>$fin_from");
			dspip_query("update spip_mots
 				   set debut=debut-$largeur_from
 				 where debut>$fin_from");

			if($debut_from<$debut_to) {
				// déplacement vers le bas => la destination
				// s'est décalée qaund on a dégagé la branche source
				$debut_to -= $largeur_from;
				$fin_to -= $largeur_from;
			}

			if($quoi=='after') {
				// faire un trou pour la destination
				dspip_query("update spip_mots
 					   set fin=fin+$largeur_from
 					 where fin>$fin_to");
				dspip_query("update spip_mots
 					   set debut=debut+$largeur_from
 					 where debut>$fin_to");
				// y mettre la branche source
				$delta=$fin_to+1-$debut_from;
			} else {
				// faire un trou pour la destination
				dspip_query("update spip_mots
 					   set fin=fin+$largeur_from
 					 where fin>$debut_to");
				dspip_query("update spip_mots
 					   set debut=debut+$largeur_from
 					 where debut>$debut_to");
				// y mettre la branche source
				$delta=$debut_to+1-$debut_from;
			}
			dspip_query("update spip_mots
 				   set debut=-debut+$delta, fin=-fin+$delta
 				 where debut<0");

			if($groupe_from!=$groupe_to) {
				// dans ce cas, faut renuméroter les groupes
				// (même si ça sert peut être à rien ?)
				// A REVOIR : cette requète est pourrie, comment la faire mieux ?
				dspip_query("UPDATE spip_groupes_mots
				   SET debut=(
					SELECT debut FROM spip_mots
					 WHERE spip_mots.id_groupe = spip_groupes_mots.id_groupe
					   AND niveau=0),
				       fin=(
					SELECT fin FROM spip_mots
					 WHERE spip_mots.id_groupe = spip_groupes_mots.id_groupe
					   AND niveau=0)");
			}
		} else {
			die("ça, on peut pas ... : $debut_from/$fin_from -> $debut_to/$fin_to");
		}
	} else {
		die('???');
	}

	redirige_par_entete("ecrire?exec=mots_arbo");
}

?>
