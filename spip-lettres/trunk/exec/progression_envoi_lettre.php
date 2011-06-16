<?php
/**
 * SPIP-Lettres
 *
 * Copyright (c) 2006-2009
 * Agence Artégo http://www.artego.fr
 *
 * Ce programme est un logiciel libre distribue sous licence GNU/GPLv3.
 * Pour plus de details voir http://www.gnu.org/licenses/gpl-3.0.html
 *
 **/


if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('lettres_fonctions');
include_spip('inc/delivrer');

function exec_progression_envoi_lettre() {

	if (!autoriser('editer', 'lettres')) {
		include_spip('inc/minipres');
		echo minipres();
		exit;
	}

	$lettre = new lettre(_request('id_lettre'));

	echo '<div class="notice">';
	echo _T('lettresprive:aide_lettres_envoi_en_cours');
	$nb_envois = $lettre->calculer_nb_envois();
	$envois_restants = intval(lettres_envois_restants($lettre->id_lettre));
	if ($nb_envois+$envois_restants) {
		echo "<p>";
		echo "<div style='float:right'>".http_img_pack("searching.gif", ' ', '')."</div>";
		echo _L("Envois restants&nbsp;: $envois_restants")." ";
		echo _L("(Déjà envoyés : $nb_envois )");
		echo "</p>";
	}
	echo "<em style='display:none;'>$envois_restants</em>";
	echo recuperer_fond('modeles/object_jobs_list',array('id_objet'=>$lettre->id_lettre,'objet'=>'lettre'));
	echo '</div>';
	// changer le statut si besoin, puisqu'on le voit !
	if (!$envois_restants) {
		if ($lettre->statut=='envoi_en_cours')
			$lettre->enregistrer_statut('envoyee');
	}
	else
		echo queue_afficher_cron();

}


?>