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


if (!defined("_ECRIRE_INC_VERSION")) return;

function action_exclure_du_groupe_dist() {
		
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	/* cette fonction peut etre appelee selon trois modes:
	1 - soit directement depuis un squelette avec en argument <id_groupe>-<id_auteur>
	2 - soit depuis le traitement d'un formulaire et arg contient alors uniquement <id_groupe>
	3 - soit depuis la page des adherents et la suppression multiple. Les differents id_groupes sont alors dans un tableau id_groupes */
	if (strpos($arg, '-')) { /* mode 1 */
		list($id_groupe,$id_auteur)=explode('-', $arg);
		sql_delete('spip_asso_groupes_liaisons', "id_groupe=".intval($id_groupe)." AND id_auteur=".intval($id_auteur));
	} else { 
		$id_auteurs = _request('id_auteurs');
		$id_auteurs = (isset($id_auteurs)) ? $id_auteurs:array();

		$id_groupes = _request('id_groupes');
		if (is_array($id_groupes)) { /* mode 3 */
			foreach($id_groupes as $id_groupe) {
				exclure_membre($id_groupe, $id_auteurs);
			}
		} else { /* mode 2 */
			exclure_membre($arg, $id_auteurs);
		}

	}
}

function exclure_membre ($id_groupe, $id_auteurs) {
		if (count($id_auteurs)) {
			$in = sql_in('id_auteur', $id_auteurs);
			sql_delete('spip_asso_groupes_liaisons', 'id_groupe='.intval($id_groupe).' AND '.$in);
		}
}
?>
