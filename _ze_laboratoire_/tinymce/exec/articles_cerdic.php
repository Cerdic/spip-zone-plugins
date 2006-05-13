<?php

  /** traitement d'un champs de formulaire au retour
   *  d'une edition eventuellement faite via tinyMCE
   */
function retourTinyMCE($str) {
	static $tag='<!-- TINY_MCE -->';
	$len=strlen($tag);
	if(substr($str, 0, $len)==$tag) {
		include_spip('inc/sale');
		$str= sale(substr($str, $len));
		return $str;
	}
	return $str;
}

function exec_articles() {
	global $ajout_auteur, $annee, $annee_redac, $avec_redac, $champs_extra, $change_accepter_forum, $change_petition, $changer_lang, $changer_virtuel, $chapo, $cherche_auteur, $cherche_mot, $connect_id_auteur, $date, $date_redac, $debut, $descriptif, $email_unique, $heure, $heure_redac, $id_article, $id_article_bloque, $id_parent, $id_rubrique_old, $id_secteur, $jour, $jour_redac, $langue_article, $lier_trad, $message, $minute, $minute_redac, $mois, $mois_redac, $new, $nom_select, $nom_site, $nouv_auteur, $nouv_mot, $ps, $row, $site_obli, $site_unique, $soustitre, $statut_nouv, $supp_auteur, $supp_mot, $surtitre, $texte, $texte_petition, $texte_plus, $titre, $titre_article, $url_site, $virtuel; 
	global $descriptif_propre,$chapo_propre,$texte_propre;	
	if ($GLOBALS['surtitre_propre']) $GLOBALS['surtitre']=retourTinyMCE($GLOBALS['surtitre_propre']);
	if ($GLOBALS['titre_propre']) $GLOBALS['titre']=retourTinyMCE($GLOBALS['titre_propre']);
	if ($GLOBALS['soustitre_propre']) $GLOBALS['soustitre']=retourTinyMCE($GLOBALS['soustitre_propre']);
	if ($GLOBALS['descriptif_propre']) $GLOBALS['descriptif']=retourTinyMCE($GLOBALS['descriptif_propre']);
	if ($GLOBALS['chapo_propre']) $GLOBALS['chapo']=retourTinyMCE($GLOBALS['chapo_propre']);
	if ($GLOBALS['texte_propre']) $GLOBALS['texte']=retourTinyMCE($GLOBALS['texte_propre']);
	if ($GLOBALS['ps_propre']) $GLOBALS['ps']=retourTinyMCE($GLOBALS['ps_propre']);

	include_once(_DIR_RESTREINT.'exec/articles.php');
	return exec_articles_dist();
}

?>
