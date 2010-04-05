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

include_spip('inc/lettres_fonctions');
include_spip('classes/lettre');
include_spip('classes/abonne');
include_spip('base/abstract_sql');

function lettres_programmer_envois($id_lettre){
	$lettre = new lettre($id_lettre);
	$rubriques = lettres_recuperer_toutes_les_rubriques_parentes($lettre->id_rubrique);

	$abonnes = sql_select(
		'A.id_abonne, A.format',
		'spip_abonnes_rubriques AS AR INNER JOIN spip_abonnes AS A ON A.id_abonne=AR.id_abonne',
		sql_in('AR.id_rubrique',$rubriques).' AND AR.statut="valide"', 'A.id_abonne');

	while ($a = sql_fetch($abonnes)) {
		if ($id_job = job_queue_add(
						'lettres_envoyer_une_lettre',
						"Lettre $id_lettre -> abonne ".$a['id_abonne']." (".$a['format'].")",
						array($id_lettre,$a['id_abonne']),'inc/delivrer',true))
			queue_link_job($id_job,array(
				array('objet'=>'lettre','id_objet'=>$id_lettre),
				array('objet'=>'abonne','id_objet'=>$a['id_abonne'])
				));
	}
}

function lettres_envoyer_une_lettre($id_lettre,$id_abonne){
	$abonne = new abonne($id_abonne);
	$resultat = $abonne->envoyer_lettre($id_lettre);
	$abonne->enregistrer_envoi($id_lettre, $resultat);

	spip_log("Envoi lettre $id_lettre->$id_abonne / Restant:".lettres_envois_restants($id_lettre),'delivrer');
	// si plus de job concernant cette lettre, changer son statut
	// attention, il reste encore le job en cours dans la table
	if (lettres_envois_restants($id_lettre)<=1){
		$lettre = new lettre($id_lettre);
		$lettre->enregistrer_statut('envoyee');
	}
}

function lettres_envois_restants($id_lettre){
	return sql_countsel('spip_jobs_liens', "objet='lettre' AND id_objet=".intval($id_lettre));
}

function lettres_delivrer_surveille_ajax($id_lettre,$end_url){
	if (!lettres_envois_restants($id_lettre))
		return '';
	$periode = 5*1000;

	$exec = generer_url_ecrire('progression_envoi_lettre','id_lettre='.$id_lettre,true);
	$res = <<<surveille
<div id="progression_envoi_lettre"></div>
<script type="text/javascript">
function progression_envoi_lettre(){
	jQuery.get('$exec',function(data) {
		var c = jQuery(data);
		var remains = jQuery('em',c).html();
		if (remains=='0') {
			window.location.href='$end_url';
		}
		else
			jQuery('#progression_envoi_lettre').html(data);
		// reprogrammer une maj
		setTimeout(progression_envoi_lettre,$periode);
	});
}
jQuery(function(){
	progression_envoi_lettre();
});
</script>
surveille;
	return $res;
}
?>