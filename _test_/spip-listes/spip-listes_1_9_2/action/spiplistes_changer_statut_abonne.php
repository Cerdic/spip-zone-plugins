<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/extra_plus');
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
			$extras = get_extra($id_auteur,"auteur");
			$extras["abo"] = $statut;
			set_extra($id_auteur,$extras,"auteur");
		}
	}
	if ($action=='listeabo'){
		//changer de statut
		$id_auteur = _request('id_auteur');
		if ($id_auteur && ($id_liste = $arg[2]) 
			&& autoriser('abonnerauteur','liste',$id_liste,NULL,array('id_auteur'=>$id_auteur))) {
			$result=spip_query("DELETE FROM spip_abonnes_listes WHERE id_auteur="._q($id_auteur)." AND id_liste="._q($id_liste));
			$result=spip_query("INSERT INTO spip_abonnes_listes (id_auteur,id_liste) VALUES ("._q($id_auteur).","._q($id_liste).")");
			//attribuer un format de reception si besoin (ancien auteur)
			$extra_format=get_extra($id_auteur,"auteur");
			if(!$extra_format["abo"]){
				$extra_format["abo"] = "html";
				set_extra($id_auteur,$extra,'auteur');
			}
		}
	}
	if ($action=='listedesabo'){
		if ($id_liste = $arg[2])
			//if (autoriser())
			if (autoriser('desabonnerauteur','liste',$id_liste,NULL,array('id_auteur'=>$id_auteur)))
				spip_query("DELETE FROM spip_abonnes_listes WHERE id_auteur="._q($id_auteur)." AND id_liste="._q($id_liste));
	}
	
	if ($redirect){
		redirige_par_entete(str_replace("&amp;","&",$redirect));
	}
}

?>