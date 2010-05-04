<?php
	/**
	* Plugin Association
	*
	* Copyright (c) 2007
	* Bernard Blazin & François de Montlivault
	* http://www.plugandspip.com 
	* Ce programme est un logiciel libre distribue sous licence GNU/GPL.
	* Pour plus de details voir le fichier COPYING.txt.
	*  
	**/
if (!defined("_ECRIRE_INC_VERSION")) return;


	//juste pour essai
	//sql_alter("TABLE spip_auteurs_elargis ADD statut_interne TEXT  NOT NULL");
	//spip_query("UPDATE spip_auteurs_elargis SET statut_interne='ok'");
	//sql_alter("TABLE spip_auteurs_elargis ADD categorie TEXT  NOT NULL");
	//sql_alter("TABLE spip_auteurs_elargis ADD validite DATE DEFAULT '2008-12-15' NOT NULL");
	//fin essai

	include_spip('inc/presentation');
	include_spip ('inc/navigation_modules');
	
	function exec_association() {
		
		include_spip('inc/autoriser');
		if (!autoriser('configurer')) {
			include_spip('inc/minipres');
			echo minipres();
			exit;
		}	
		
		$url_edit_adherent = generer_url_ecrire('edit_adherent');
		  $commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('asso:association')) ;
		//debut_page(_T('asso:association'), "naviguer", "association");
		
		association_onglets();
		
		echo debut_gauche("",true);
		
		echo debut_boite_info(true);
		echo propre(_T('asso:info_doc'));  	
		echo fin_boite_info(true);
		
		$res=association_icone(_T('asso:profil_de_lassociation'),  '?exec=cfg&cfg=association', 'assoc_qui.png');
			$res.=association_icone(_T('asso:categories_de_cotisations'),  generer_url_ecrire("categories"), 'cotisation.png',  '');
	$res.=association_icone(_T('asso:plan_comptable'),  generer_url_ecrire("plan"), 'plan_compte.png',  '');	
	echo bloc_des_raccourcis($res);
		
		echo debut_droite("",true);	
		
		echo debut_cadre_formulaire("",true);
		echo gros_titre(_T('asso:votre_asso'),'',false);
		echo '<br>';		
		echo '<strong>'.lire_config('association/nom').'</strong><br>';
		echo lire_config('association/rue').'<br>';
		echo lire_config('association/cp').'&nbsp;';
		echo lire_config('association/ville').'<br>';
		echo lire_config('association/telephone').'<br>';
		echo lire_config('association/email').'<br>';
		echo lire_config('association/siret').'<br>';
		echo lire_config('association/declaration').'<br>';
		echo lire_config('association/prefet').'<br>';
		fin_cadre_formulaire(true);
		
		/* Provisoirement supprimé en attendant 1.9.3*/
		
		echo '<br />';
		echo gros_titre(_T('asso:votre_equipe'),'',false);		
		echo '<br />';	
		
		echo debut_cadre_relief(true);
		
		echo '<table border=0 cellpadding=2 cellspacing=0 width="100%" class="arial2" style="border: 1px solid #aaaaaa;">';
		echo '<tr bgcolor="#DBE1C5">';
		echo '<td><strong>Nom</strong></td>';
		echo '<td><strong>Email</strong></td>';
		echo '<td><strong>Fonction</strong></td>';
		echo '<td><strong>Portable</strong></td>';
		echo '<td><strong>T&eacute;l&eacute;phone</strong></td>';
		echo '</tr>';
		$query = sql_select("*",_ASSOCIATION_AUTEURS_ELARGIS .  " a INNER JOIN spip_auteurs AS b ON a.id_auteur=b.id_auteur", "fonction !='' AND statut_interne != 'sorti'", '',  "a.nom_famille");
		while ($data = sql_fetch($query))
    {	
			$id_auteur=$data['id_auteur'];
			echo '<tr style="background-color: #EEEEEE;">';
			echo '<td class="arial11 border1"><a href="'.generer_url_ecrire('auteur_infos',"id_auteur=$id_auteur").'" title="Modifier l\'administrateur">'.$data['nom'].' </a></td>';
			echo '<td class="arial1 border1"><a href="mailto:'.$data['email'].'"title="Envoyer un email">email</a></td>';
			echo '<td class="arial1 border1">'.$data['fonction'].'</td>';
			echo '<td class="arial1 border1">'.$data['mobile'].'</td>';
			echo '<td class="arial1 border1">'.$data['telephone'].'</td>';
			echo '</tr>';
		}				
		echo '</table>';
		
		fin_cadre_relief();	
		
		
		
		 echo fin_gauche(), fin_page();
		
		//Petite routine pour mettre à jour les statuts de cotisation "échu"
		 sql_updateq(_ASSOCIATION_AUTEURS_ELARGIS, 
			array("statut_interne"=> 'echu'),
			"statut_interne = 'ok' AND validite < CURRENT_DATE() ");
	}
?>
