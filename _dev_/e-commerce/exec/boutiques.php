<?php

include_spip("inc/presentation");
include_spip('inc/e-commerce_outils');
include_spip("inc/e-commerce_boutique");

//
// boutique
//

function exec_boutiques_dist()
	{
	global $connect_statut;

	debut_page(_T('boutique:boutique'));
	if ($connect_statut == "0minirezo") 
		{
		if (estceque_boutique_editable()) 
			{
			echo "<div align='right'>";
			$link=generer_url_ecrire('boutique_creation', 'new=oui');
			$link=parametre_url($link,'retour',str_replace('&amp;', '&', self()));
			icone(_L("Cr&eacute;er les tables de donnees"), $link, "../"._DIR_PLUGIN_BOUTIQUE. "/img_pack/euro.png", "creer.gif");
			echo "</div>";
			}
		else
			echo "<strong>Vous n'avez pas acc&egrave;s &agrave; cette page.</strong>";
		}
	else 
		echo "<strong>Vous n'avez pas acc&egrave;s &agrave; cette page.</strong>";
	fin_page();
	}
?>
