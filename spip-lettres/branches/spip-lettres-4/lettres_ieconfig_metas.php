<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function lettres_ieconfig_metas($table){
	$table['lettres']['titre'] = _T('lettres:lettres_information');
	$table['lettres']['icone'] = 'prive/themes/spip/images/lettres-16.png';
	$table['lettres']['metas_brutes'] = 'spip_lettres_abonnement_par_defaut,spip_lettres_admin_abo_toutes_rubriques,spip_lettres_cliquer_anonyme,spip_lettres_envois_recurrents,spip_lettres_fond_formulaire_lettres,spip_lettres_fond_lettre_html,spip_lettres_fond_lettre_texte,spip_lettres_fond_lettre_titre,spip_lettres_notifier_suppression_abonne,spip_lettres_utiliser_articles,spip_lettres_utiliser_chapo,spip_lettres_utiliser_descriptif,spip_lettres_utiliser_ps,spip_lettres_version';
	return $table;
}

?>