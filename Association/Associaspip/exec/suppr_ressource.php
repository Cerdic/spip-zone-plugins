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

include_spip ('inc/navigation_modules');

function exec_suppr_ressource()
{
	$id_ressource = association_passeparam_id('ressource');
	if (!autoriser('associer', 'ressources') OR !$id_ressource) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		onglets_association('titre_onglet_prets', 'ressources');
		// INTRO : resume ressource
		$data = sql_fetsel('*', 'spip_asso_ressources', "id_ressource=$id_ressource" ) ;
		$infos['ressources_libelle_code'] = association_formater_code($data['code'], 'spip_asso_ressources');
		$infos['ressources_entete_montant'] = association_formater_prix($data['pu'], 'rent');
		$infos['ressources_libelle_unite'] = association_formater_duree(1, $data['ud']);
		$infos['ressources_entete_caution'] = association_formater_prix($data['prix_caution'], 'guarantee');
		if ( is_numeric($data['statut']) ) { // utilisation des 3 nouveaux statuts numeriques (gestion de quantites/exemplaires)
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
			switch($data['statut']){ // utilisation des anciens 4+ statuts textuels (etat de reservation)
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
		$infos['statut'] = '<span class="'.(is_numeric($data['statut'])?'quanttity':'availability').'">'. association_formater_puce($data['statut'], $puce, "ressources_statut_$type") .'</span>';
		$infos['ressource_pretee'] = _T('asso:nombre_fois', array('nombre'=>sql_countsel('spip_asso_prets', "id_ressource=$id_ressource"), )); // indice de popularite
		echo '<div class="hproduct">'.  association_totauxinfos_intro('<span class="n">'.$data['intitule'].'</span>', 'ressource', $id_ressource, $infos ) .'</div>';
		// STATS sur la duree et le montant des emprunts
		echo association_totauxinfos_stats('prets', 'prets', array('entete_duree'=>'duree','entete_montant'=>'duree*prix_unitaire',), "id_ressource=$id_ressource");
		// datation et raccourcis
		raccourcis_association('');
		debut_cadre_association('pret-24.gif', 'ressources_titre_suppression_ressources');
		echo association_bloc_suppression('ressource', $id_ressource );
		fin_page_association();
	}
}

?>