<?php

	if (!defined("_ECRIRE_INC_VERSION")) return;

	function action_spipimmo_supprimer_annonce()
	{
		//Suppression de l'annonce dans la BDD
		$resSuppr=sql_delete("spip_annonces", "`id_annonce`=" . _request('arg'));
		if($resSuppr)
		{
			//Suppression image taille normal
			$handle=opendir(_DIR_IMG);
			while ($fichier = readdir($handle))
			{
				if(ereg("^immo" . _request('arg') . "-" . "[0-9]*.[a-zA-Z]*$", $fichier))
				{
					unlink(_DIR_IMG . $fichier);
				}
			}
			closedir($handle);

			//Suppression  vignette
			$handle=opendir(_SPIPIMMO_REP_VIGNETTES);
			while ($fichier = readdir($handle))
			{
				if(ereg("^immo" . _request('arg') . "-" . "[0-9]*.[a-zA-Z]*$", $fichier))
				{
					unlink(_SPIPIMMO_REP_VIGNETTES . $fichier);
				}
			}
			closedir($handle);

			//Suppression des documents dans la BDD
			sql_delete("spip_documents_annonces", "`numero_dossier`=" . _request('arg'));

			redirige_par_entete($_SERVER["HTTP_REFERER"] . '&suppr=1');
		}
		else
		{
			redirige_par_entete($_SERVER["HTTP_REFERER"] . '&suppr=0');
		}
	}
?>
