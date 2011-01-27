<?php
/***************************************************************************\
 *  SPIPAL, Utilitaire de paiement en ligne pour SPIP                      *
 *                                                                         *
 *  Copyright (c) 2007 Thierry Schmit                                      *
 *  Copyright (c) 2011 Emmanuel Saint-James                                *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('base/abstract_sql');

function formulaires_payer_charger($id_article=0, $validation='valider')
{
	// compatibilite partielle avec l'ancienne version
	if (!$id_article) $id_article = _request('id_article');
	$row = sql_fetsel(
        'id_article, ref_produit,don,prix_unitaire_ht,tva,nom_com',
	'spip_spipal_produits',
	'id_article='. intval($id_article));

	if (!$row) return "Rien a payer";

	$row['quantite'] = _request('quantite');
	if (!is_numeric($row['quantite'])) $row['quantite'] = 1;

	if ($row['don'] <=1) {
		if (!$row['prix_unitaire_ht']) 
			$row['prix_unitaire_ht'] = _request('don');
		$row['taxes'] = 0;
	} else {
	  $row['taxes'] = round($row['prix_unitaire_ht'] * $row['tva'] / 100, 2);
	}
	$row['prix_unitaire_ttc'] = $row['taxes'] + $row['prix_unitaire_ht'];
	$row['total_ttc'] = $row['quantite'] * $row['prix_unitaire_ttc'];
	$row['monnaie'] = 'EUR';

	$id = isset($GLOBALS['auteur_session']['id_auteur'])
	  ? $GLOBALS['auteur_session']['id_auteur']
	  : (0 - intval(_request('id_auteur')));

	$row['custom'] = serialize(array('id_auteur' => $id, 'validation' => $validation));

	return $row;
}
?>
