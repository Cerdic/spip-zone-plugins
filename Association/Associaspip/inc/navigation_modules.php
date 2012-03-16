<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations             *
 *                                                                         *
 *  Copyright (c) 2007 Bernard Blazin & François de Montlivault (V1)       *
 *  Copyright (c) 2010-2011 Emmanuel Saint-James & Jeannot Lapin (V2)       *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/


include_spip('inc/presentation'); // utilise par "association_onglet1" (pour "onglet") puis aussi dans les pages appelantes
include_spip('inc/autoriser'); // utilise par "association_onglet1" (pour le test "autoriser") puis aussi dans les pages appelantes

// Afficher le titre de la/le page/module courante puis (en dessous) les onglets des differents modules actives dans la configuration
function onglets_association($titre='', $INSERT_HEAD=TRUE)
{

	/* onglet de retour a la page d'accueil */
	$res = association_onglet1(_T('asso:menu2_titre_association'), 'association', 'Association', 'annonce.gif');

	/* onglet de gestion des membres */
	$res .= association_onglet1(_T('asso:menu2_titre_gestion_membres'), 'adherents', 'Membres', 'annonce.gif');

	/* onglet de gestion des dons */
	if ($GLOBALS['association_metas']['dons']) {
		$res .= association_onglet1(_T('asso:menu2_titre_gestion_dons'), 'dons', 'Dons', 'dons.gif');
	}

	/* onglet de gestion des ventes */
	if ($GLOBALS['association_metas']['ventes']) {
		$res .= association_onglet1(_T('asso:menu2_titre_ventes_asso'), 'ventes', 'Ventes', 'ventes.gif');
	}

	/* onglet de gestion des activites */
	if ($GLOBALS['association_metas']['activites']) {
		$res .= association_onglet1(_T('asso:menu2_titre_gestion_activites'), 'activites', 'Activites', 'activites.gif');
	}

	/* onglet de gestion des prets */
	if ($GLOBALS['association_metas']['prets']) {
		$res .= association_onglet1(_T('asso:menu2_titre_gestion_prets'), 'ressources', 'Prets', 'pret1.gif');
	}

	/* onglet de gestion comptable */
	if ($GLOBALS['association_metas']['comptes']) {
		$res .= association_onglet1(_T('asso:menu2_titre_livres_comptes'), 'comptes', 'Comptes', 'comptes.gif');
	}

	/* Affichage */
	if ($INSERT_HEAD) { // mettre ''|0|FALSE|NULL dans la balise (appel dans une page HTML-SPIPee donc et non PHP) pour eviter l'erreur de "Double occurrence de INSERT_HEAD"
		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page();
	}
	echo '<div class="table_page">';
	// Nom du module
	echo '<h1 class="asso_titre">', $titre?$titre:_T('asso:gestion_de_lassoc', array('nom'=>$GLOBALS['association_metas']['nom']) ), '</h1>'; //  <http://programmer.spip.org/Contenu-d-un-fichier-exec>
	// Onglets actifs
	if ($res)
		echo '<div class="bandeau_actions">', debut_onglet(), $res, fin_onglet(), '</div>';
	echo '</div>';
	if ($INSERT_HEAD) { // Tant qu'a faire, on s'embete pas a le retaper dans toutes les pages...
		echo debut_gauche('',true);
		echo debut_boite_info(true);
	}
}
function association_onglets($titre='', $INSERT_HEAD=TRUE)
{
	onglets_association($titre,$INSERT_HEAD);
}

// dessin d'un onglet seul
function association_onglet1($texte, $objet, $libelle, $image)
{
	if (autoriser('associer', $objet)) {
		return onglet($texte, generer_url_ecrire($objet), '', $libelle, _DIR_PLUGIN_ASSOCIATION_ICONES . $image, 'rien.gif');
	} else
		return '';
}

// cette fonction remplace et personnalise le couplet final <http://programmer.spip.org/Contenu-d-un-fichier-exec> : echo fin_gauche(), fin_page();
function fin_page_association($FIN_CADRE_RELIEF=true)
{
	$copyright = fin_page();
	// Pour eliminer le copyright a l'impression
	$copyright = str_replace("<div class='table_page'>", "<div class='table_page contenu_nom_site'>", $copyright);
	echo ($FIN_CADRE_RELIEF ? fin_cadre_relief() : '') . fin_gauche() . $copyright;
}

//cadre en relief debutant la colonne centrale/principale essentiellement
function debut_cadre_association($icone,$titre,$T_argrs='',$DEBUT_DROITE=true)
{
	if ($DEBUT_DROITE)
		echo debut_droite('',true);
	debut_cadre_relief(_DIR_PLUGIN_ASSOCIATION_ICONES.$icone, false, '', (is_array($T_args)?_T("asso:$titre",$T_args): _T("asso:$titre")." $T_args") );
}

?>