<?php
/**
 * Plugin Simple Calendrier pour Spip 2.1.2
 * Licence GPL (c) 2010-2011 Julien Lanfrey
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

//include_spip('inc/commentaire_utils'); // spip_log
//include_spip('base/abstract_sql');

// Suppression des incohérences dans la base...
// (code inspiré de ecrire/genie/optimiser.php : optimiser_base_disparus)
function genie_simplecal_nettoyer_base_dist($t){
	spip_log("CRON executé", "simplecal");

    # -------------------------------------------------------------------------------
    # Les liens des évènements qui sont liés à un objet inexistant.
    # Ex : evt.id_objet = 123 et evt.type = 'article' Mais article123 inexistant.
    # Dans ce cas, on vide les colonnes 'type' et 'objet' pour l'evenement concerné.
    # -------------------------------------------------------------------------------
	$r = sql_select("DISTINCT type","spip_evenements");
	while ($t = sql_fetch($r)){
		$type = $t['type'];
		$spip_table_objet = table_objet_sql($type);
		$id_table_objet = id_table_objet($type);
		$res = sql_select("e.id_evenement AS id, id_objet",
                        "spip_evenements AS e LEFT JOIN $spip_table_objet AS O ON O.$id_table_objet=e.id_objet AND e.type=".sql_quote($type),
                        "O.$id_table_objet IS NULL");
		
        // sur une cle primaire composee, pas d'autres solutions que de traiter un a un
		while ($row = sql_fetch($sel)){
            $data = array();
            $data['type'] = "";
            $data['objet'] = null;
            sql_updateq('spip_evenements', $data, "id_evenement=".$row['id']);
            spip_log("- Reference '".$type."".$row['id_objet']."' retirée dans la table spip_evenements (id=".$row['id'].")", "simplecal");
        }
	}
	
	
    # -------------------------------------------------------------------------------
    # Les liens des mots qui sont liés à un évènement inexistant.
    # Ex : mots_evenements.id_evenement = 123 Mais evenement123 inexistant.
    # Dans ce cas, on supprime le mots_evenements concerné.
    # -------------------------------------------------------------------------------
	# les liens de mots affectes a des evenements effaces
	$res = sql_select("mots_evenements.id_mot,mots_evenements.id_evenement",
			"spip_mots_evenements AS mots_evenements
			LEFT JOIN spip_evenements AS evenements
			ON mots_evenements.id_evenement=evenements.id_evenement",
			"evenements.id_evenement IS NULL");

	while ($row = sql_fetch($res)) {
		sql_delete("spip_mots_evenements","id_mot=".$row['id_mot']." AND id_evenement=".$row['id_evenement']);
        spip_log("- Reference '"."id_mot=".$row['id_mot']." / id_evenement=".$row['id_evenement']."' retirée dans la table spip_mots_evenements", "simplecal");
    }

	return 1;
}

?>