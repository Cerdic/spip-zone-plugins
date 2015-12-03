<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(
// E
	'editer_liens_simples_nom' => 'Éditer Liens Simples',
	'editer_liens_simples_description' => 'Fourni un formulaire et une API pour lier les objets sur les tables spip_xxx_yyy (et non spip_xxx_liens), directement inspiré de ce qui est fourni par SPIP pour ses tables de liens.
		
		Les tables de liens spip_xxx_yyy servent à lier 2 types d\'objets prédéfinis, à la différence des tables de liens spip_xxx_liens qui permettent de lier n\'importe quels types d\'objets.
		Exemple :
		<code>spip_organisations_contacts</code> permet de lier des contacts à des organisations.
		<code>spip_organisations_liens</code> permet de lier n\'importe quel type d\'objet à des organisations.
		
		L\'API d\'edition de liens simples s\'utilise comme l\'API traditionnelle, en suffixant les fonctions avec «_simples» :
		<code>include_spip(\'action/editer_liens_simples\')</code>
		<code>objet_associer_simples()</code>
		<code>objet_trouver_liens_simples()</code>
		etc.',
	'editer_liens_simples_slogan' => 'API de liens pour les tables spip_xxx_yyy',
);

?>
