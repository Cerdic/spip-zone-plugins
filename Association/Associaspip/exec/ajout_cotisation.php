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
	
include_spip('inc/presentation');
include_spip('inc/autoriser');
include_spip ('inc/navigation_modules');

function exec_ajout_cotisation(){
		
	$id_auteur = intval(_request('id'));
	$row = sql_fetsel("nom_famille,prenom,categorie,validite",'spip_asso_membres', "id_auteur=$id_auteur");
	if (!autoriser('associer', 'adherents', $id_auteur) OR !$row) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('asso:ajout_de_cotisation')) ;
		association_onglets();
		echo debut_gauche("",true);

		$nom_famille = $row['nom_famille'];
		$prenom = $row['prenom'];
		$categorie = $row['categorie'];
		$validite = $row['validite'];

		$categorie_libelle = sql_fetsel("libelle", "spip_asso_categories", "id_categorie=" . intval($categorie));

		$h = generer_url_ecrire('voir_adherent', "id=$id_auteur");

		echo debut_boite_info(true);
		echo "<h3><a href='$h'>", $nom_famille.' '.$prenom.'</a></h3>';
		echo $categorie_libelle ? ('<strong>'.$categorie_libelle['libelle'].'</strong>') :'';
		echo association_date_du_jour();	
		echo fin_boite_info(true);

		echo debut_droite("",true);

		echo debut_cadre_relief(  "", false, "", _T('asso:nouvelle_cotisation'));
		echo recuperer_fond("prive/editer/editer_cotisations", array (
			'id_auteur' => $id_auteur,
			'nom_prenom' => $prenom.' '.$nom_famille,
			'categorie' => $categorie,
			'validite' => $validite
		));
		echo fin_cadre_relief(true);  
		echo fin_page_association();
	}
}
?>
