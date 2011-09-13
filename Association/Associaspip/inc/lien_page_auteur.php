<?php
/***************************************************************************
 *  Associaspip, extension de SPIP pour gestion d'associations             *
 *                                                                         *
 *  Copyright (c) 2007 Bernard Blazin & Francois de Montlivault (V1)       *
 *  Copyright (c) 2010-2011 Emmanuel Saint-James & Jeannot Lapin (V2)       *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

/**
 * Ajout d'un lien vers la page de membre sur la page d'auteur
**/
function association_affiche_gauche($flux) {
	if ($flux['args']['exec'] == 'auteur_infos') {
		$id_auteur = $flux['args']['id_auteur'];
		/* verifier que l'auteur est bien membre de l'asso */
		if (sql_countsel('spip_asso_membres', "id_auteur=$id_auteur")) {
			$flux['data'] .= '<div class="iconifier"><div class="cadre cadre-r"><img class="cadre-icone" src="'._DIR_PLUGIN_ASSOCIATION_ICONES.'annonce.gif"><a href="'.generer_url_ecrire('voir_adherent', "id=$id_auteur").'"><div class="titrem">'._T('asso:adherent_label_page_du_membre').'</div></a></div></div>';
		}
		
		//recuperer_fond(prive/boite/lien_page_auteur,array ('id_auteur' => $id_auteur));
	}
	return $flux;
}?>
