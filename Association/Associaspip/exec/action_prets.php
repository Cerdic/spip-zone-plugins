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

function exec_action_prets()
{
	include_spip('inc/autoriser');
	if (!autoriser('associer', 'activites')) {
			include_spip('inc/minipres');
			echo minipres();
	} else {
		$id_pret = intval(_request('id_pret'));

		$id_ressource=$_REQUEST['id_ressource']; // text !
		$id_emprunteur=$_POST['id_emprunteur']; // text !
		$date_sortie=$_POST['date_sortie'];
		$duree=$_POST['duree'];
		$date_retour=$_POST['date_retour'];
		$commentaire_sortie=$_POST['commentaire_sortie'];
		$commentaire_retour=$_POST['commentaire_retour'];
		$statut=$_POST['statut'];
		$montant=$_POST['montant'];
		$journal=$_POST['journal'];

		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('asso:prets_titre_suppression_prets')) ;
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
		echo debut_cadre_relief('', true, '', $titre = _T('asso:prets_titre_suppression_prets'));
		echo '<p><strong>'._T('asso:prets_danger_suppression',array('id_pret' => $id_pret)).'</strong></p>';
		$res = '<p class="boutons"><input type="submit" value="'._T('asso:bouton_confirmer').'" /></p>';
		echo redirige_action_post('supprimer_prets', "$id_pret-$id_ressource", 'prets', '', $res);
		echo fin_cadre_relief(true);
		echo fin_page_association();
	}
}
?>
