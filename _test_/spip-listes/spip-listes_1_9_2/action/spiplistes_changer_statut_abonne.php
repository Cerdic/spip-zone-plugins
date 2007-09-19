<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/autoriser');

function action_spiplistes_changer_statut_abonne_dist()
{
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	$redirect = urldecode(_request('redirect'));

	$arg = explode('-',$arg);
	$id_auteur = $arg[0];
	$action = $arg[1];
	
	if ($action=='format'){
		//changer de statut
		$statut = _request('statut');
		if (autoriser('statutabonement','auteur',$id_auteur))
		if(($statut=='html') OR ($statut=='texte') OR ($statut=='non')){
			$res = spip_query("SELECT id_auteur FROM spip_auteurs_elargis WHERE id_auteur="._q($id_auteur));
			if (spip_num_rows($res))
				spip_query("UPDATE spip_auteurs_elargis SET `spip_listes_format`="._q($statut)." WHERE id_auteur="._q($id_auteur));
			else
				spip_query("INSERT INTO spip_auteurs_elargis (id_auteur,`spip_listes_format`) VALUES ("._q($id_auteur).","._q($statut).")");		
		}
	}
	if ($action=='listeabo'){
		//changer de statut
		$id_auteur = _request('id_auteur');
		if ($id_auteur && ($id_liste = $arg[2]) 
			&& autoriser('abonnerauteur','liste',$id_liste,NULL,array('id_auteur'=>$id_auteur))) {
			$result=spip_query("DELETE FROM spip_auteurs_listes WHERE id_auteur="._q($id_auteur)." AND id_liste="._q($id_liste));
			$result=spip_query("INSERT INTO spip_auteurs_listes (id_auteur,id_liste) VALUES ("._q($id_auteur).","._q($id_liste).")");
			//attribuer un format de reception si besoin (ancien auteur)
			$abo = spip_fetch_array(spip_query("SELECT `spip_listes_format` FROM `spip_auteurs_elargis` WHERE `id_auteur`='$id_auteur'")) ;		
			if(!$abo){
			$ok = spip_query("UPDATE `spip_auteurs_elargis` SET `spip_listes_format`='html' WHERE id_auteur="._q($id_auteur));
			}
									
		}
	}
	if ($action=='listedesabo'){
		if ($id_liste = $arg[2])
			//if (autoriser())
			if (autoriser('desabonnerauteur','liste',$id_liste,NULL,array('id_auteur'=>$id_auteur)))
				spip_query("DELETE FROM spip_auteurs_listes WHERE id_auteur="._q($id_auteur)." AND id_liste="._q($id_liste));
	}
	
	if ($redirect){
		redirige_par_entete(str_replace("&amp;","&",$redirect)."#abo$id_auteur");
	}
}

?>
