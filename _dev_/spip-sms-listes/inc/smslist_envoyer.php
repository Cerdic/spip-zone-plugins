<?
/*
 * Spip SMS Liste
 * Gestion de liste de diffusion de SMS
 *
 * Auteur :
 * Cedric Morin
 * (c) 2007 - Distribue sous licence GNU/GPL
 *
 */

function smslist_log($texte){
	echo $texte."<br/>";
}

function smslist_declencher_envoi($id_donnee,$listes){
	#$in = calcul_mysql_in('id_donnee',$listes)
	#$res = spip_query("SELECT")
	
	// retirer tous les liens abonnes existants, il proviennent d'un declenchement interrompu
	
}

function smslist_etat_boite_envoi(){
	$now = time();
	$liste = Forms_liste_tables("smslist_boiteenvoi");
	foreach($liste as $id_form){
		smslist_log("scan $id_form");
		$res = spip_query("SELECT * FROM spip_forms_donnees WHERE id_form="._q($id_form)." AND statut='prepa'");
		while ($row = spip_fetch_array($res)){
			$id_donnee = $row['id_donnee'];
			$date = Forms_les_valeurs($id_form, $id_donnee, "date_1", " ",true);
			$heure = Forms_les_valeurs($id_form, $id_donnee, "heure_1", " ",true);
			$message = Forms_les_valeurs($id_form, $id_donnee, "joint_1", ",",true);
			$listes = Forms_les_valeurs($id_form, $id_donnee, "joint_2", ",",true);
			$log = "#$id_donnee:$date:$heure:$message:$listes";
			if (strtotime("$date $heure")<$now){
				$log .= " Top depart";
				smslist_declencher_envoi($id_donnee,$listes);
			}
			smslist_log($log);
		}
	}
}


function inc_smslist_envoyer(){
	include_spip("base/forms_base_api");

	// chercher les envois en attente (statut=prepa)
	smslist_etat_boite_envoi();
}

?>