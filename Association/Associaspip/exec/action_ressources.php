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


if (!defined('_ECRIRE_INC_VERSION'))
	return;

include_spip('inc/presentation');
include_spip ('inc/navigation_modules');

function exec_action_ressources()
{
	$id_ressource=intval(_request('id'));
	include_spip('inc/autoriser');
	if (!autoriser('associer', 'ressources') OR !$id_ressource) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('asso:ressources_titre_suppression_ressources')) ;
		association_onglets();
		echo debut_gauche('',true);
		echo debut_boite_info(true);
		$data = sql_fetsel('*', 'spip_asso_ressources', "id_ressource=$id_ressource" ) ;
		$infos['ressources_libelle_code'] = $data['code'];
		if (is_numeric($data['statut'])) { /* utilisation des 3 nouveaux statuts numeriques (gestion de quantites/exemplaires) */
			if ($data['statut']>0) {
				$puce = 'verte';
				$type = 'ok';
			} elseif ($data['statut']<0) {
				$puce = 'orange';
				$type = 'suspendu';
			} else {
				$puce = 'rouge';
				$type = 'reserve';
			}
		} else {
			switch($data['statut']){ /* utilisation des anciens 4+ statuts textuels (etat de reservation) */
				case 'ok':
					$puce = 'verte';
					break;
				case 'reserve':
					$puce = 'rouge';
					break;
				case 'suspendu':
					$puce = 'orange';
					break;
				case 'sorti':
				case '':
				case NULL:
					$puce = 'poubelle';
					break;
			}
			$type = $data['statut'];
		}
		$infos['statut'] =  '<img src="'._DIR_PLUGIN_ASSOCIATION_ICONES.'puce-'.$puce.'.gif" title="'.$data['statut'].'" alt="" /> '. _T("asso:ressource_statut_$type");
		$infos['nombre_prets'] = sql_countsel('spip_asso_prets', "id_ressource=$id_ressource");
		echo totauxinfos_intro($data['intitule'] , 'ressource', $id_ressource, $infos );
		// datation
		echo association_date_du_jour();
		echo fin_boite_info(true);
		echo association_retour();
		echo debut_droite('',true);
		echo debut_cadre_relief('', false, '', $titre = _T('asso:ressources_titre_suppression_ressources'));
		echo '<p><strong>'._T('asso:ressources_danger_suppression', array('id_ressource' => $id_ressource)).'</strong></p>';
		$res = '<div class="formulaire"><form><p class="boutons"><input type="submit" value="'. _T('asso:bouton_confirmer') .'" /></p></form></div>';
		echo redirige_action_post('supprimer_ressources', $id_ressource, 'ressources', '', $res);
		echo fin_cadre_relief(true);
		echo fin_page_association();
	}
}

?>