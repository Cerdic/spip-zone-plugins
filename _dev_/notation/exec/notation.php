<?php
/**
* Plugin Notation
* par JEM (jean-marc.viglino@ign.fr)
* 
* Copyright (c) 2007
* Logiciel libre distribue sous licence GNU/GPL.
*  
* Affichage de la page principale
*  
**/

include_spip('inc/presentation');
include_spip('inc/notation_menu');

function petit_titre($titre)
{	global $couleur_foncee;
	echo "<div class='verdana3' style='color:$couleur_foncee; font-weight:bold'>".$titre."</div><hr color=$couleur_foncee style='height:1px'/>\n";
}

function exec_notation()
{	// Afficher les menus
	if (ecrire_menu())
	{	global $couleur_foncee, $couleur_claire;
		//
		// Le code de la page
		//

		// Classement par note
    global $auteur_session;
    $auteur		= $auteur_session ? intval($auteur_session['id_auteur']) : 0;

		if ($auteur!=0) {
		debut_cadre_relief(_DIR_PLUGIN_NOTATION."img_pack/notation.gif");
		petit_titre(_T('notation:vos_notes'));
		echo "<table cellpadding=2 cellspacing=1 width=100%><tr bgcolor='$couleur_foncee'>".
			"<td style='font-weight:bold;' colspan=2>"._T('notation:articles').
			"</td><td width=10% style='text-align:center;'>"._T('notation:notesp').
			"</td><td width=10% style='text-align:center;'>"._T('notation:notes').
			"</td><td width=10%>"._T('notation:nbvotes')."</td></tr>" ;
		// La requete :
	    $q = "SELECT sna.id_article, sna.note, sna.note_ponderee, sna.nb, sa.id_article, sa.titre, saa.id_auteur FROM spip_notations_articles sna ".
			" LEFT JOIN spip_auteurs_articles saa ON sna.id_article = saa.id_article ".
			" LEFT JOIN spip_articles sa ON sna.id_article = sa.id_article ".
			" WHERE saa.id_auteur = ".$auteur.
			" ORDER BY sna.note_ponderee DESC LIMIT 0,5";
		$res = spip_query($q);
		$i=0;
		// Affichage
		while ($row=spip_fetch_array($res))
		{	if ($i++ % 2) echo "<tr>";
			else echo "<tr bgcolor='$couleur_claire'>";
			echo "<td width=5% style='text-align:right'>".
            $row['id_article']."</td><td>".
            $row['titre']."</td><td style='text-align:center;'>".
            $row['note_ponderee']."</td><td style='text-align:center;'>".
            $row['note']."</td><td style='text-align:center;'>".
            $row['nb']."</td<tr>";
		}

		echo ("</table>");
		fin_cadre_relief();
		}

		// Calcul de la moyenne des votants
	    $q = "SELECT nb FROM spip_notations_articles";
		$res = spip_query($q);
		$i=0; $nb=0; $vmax=0;
		// Affichage
		while ($row=spip_fetch_array($res)) 
		{	$nb++; 
			$i+=$row['nb']; 
		}
		
		// Nb articles publies
	    $q = "SELECT id_article FROM spip_articles WHERE statut = 'publie'";
		$res = spip_query($q);
		$nb_articles = spip_num_rows($res);

		// Nb articles publies
	    $q = "SELECT id_article FROM spip_notations";
		$res = spip_query($q);
		$nb_votes = spip_num_rows($res);

		echo "<br/>";
		debut_cadre_trait_couleur();
		if ($nb!=0) petit_titre (_T('notation:nbvotes_moyen').round($i/$nb*100)/100);
		petit_titre (_T('notation:nbvotes_moyen').'0');
		petit_titre (_T('notation:nbarticle_note').$nb."/".$nb_articles);
		petit_titre (_T('notation:nbvotes_total').$nb_votes);
		fin_cadre_trait_couleur();
		
		// Classement par note ponderee
		debut_cadre_relief(_DIR_PLUGIN_NOTATION."img_pack/notation.gif");
		petit_titre (_T('notation:toptenp'));
		echo "<table cellpadding=2 cellspacing=1 width=100%><tr bgcolor='$couleur_foncee'>".
			"<td style='font-weight:bold;' colspan=2>"._T('notation:articles').
			"</td><td width=10% style='text-align:center;'>"._T('notation:notesp').
			"</td><td width=10% style='text-align:center;'>"._T('notation:notes').
			"</td><td width=10%>"._T('notation:nbvotes')."</td></tr>" ;
		// La requete :
	    $q = "SELECT sna.id_article, sna.note, sna.note_ponderee, sna.nb, sa.id_article, sa.titre FROM spip_notations_articles sna ".
			"LEFT JOIN spip_articles sa ON sna.id_article = sa.id_article ".
			"ORDER BY sna.note_ponderee DESC LIMIT 0,10";
		$res = spip_query($q);
		$i=0;
		// Affichage
		while ($row=spip_fetch_array($res))
		{	if ($i++ % 2) echo "<tr>";
			else echo "<tr bgcolor='$couleur_claire'>";
			echo "<td width=5% style='text-align:right'>".
            $row['id_article']."</td><td>".
            $row['titre']."</td><td style='text-align:center;'>".
            $row['note_ponderee']."</td><td style='text-align:center;'>".
            $row['note']."</td><td style='text-align:center;'>".
            $row['nb']."</td<tr>";
		}

		echo ("</table>");
		fin_cadre_relief();
		
		// Classement par nb vote
		debut_cadre_relief(_DIR_PLUGIN_NOTATION."img_pack/notation.gif");
		petit_titre (_T('notation:topnb'));
		echo "<table cellpadding=2 cellspacing=1 width=100%><tr bgcolor='$couleur_foncee'>".
			"<td style='font-weight:bold;' colspan=2>"._T('notation:articles').
			"</td><td width=10% style='text-align:center;'>"._T('notation:notes').
			"</td><td width=10%>"._T('notation:nbvotes')."</td></tr>" ;
		// La requete :
	    $q = "SELECT sna.id_article, sna.note, sna.nb, sa.id_article, sa.titre FROM spip_notations_articles sna ".
			"LEFT JOIN spip_articles sa ON sna.id_article = sa.id_article ".
			"ORDER BY sna.nb DESC LIMIT 0,10";
		$res = spip_query($q);
		$i=0;
		// Affichage
		while ($row=spip_fetch_array($res))
		{	if ($i++ % 2) echo "<tr>";
			else echo "<tr bgcolor='$couleur_claire'>";
			echo "<td width=5% style='text-align:right'>".
            $row['id_article']."</td><td>".
            $row['titre']."</td><td style='text-align:center;'>".
            $row['note']."</td><td style='text-align:center;'>".
            $row['nb']."</td<tr>";
		}

		echo ("</table>");
		fin_cadre_relief();
		
		// Fin de la page
 		fin_page();
	}
}

?>