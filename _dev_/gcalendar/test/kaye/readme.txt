/**
	 * Kay�
	 * Le cahier de texte �lectronique spip sp�cial primaire
	 * Copyright (c) 2007
	 * C�dric Couvrat
	 * http://alecole.ac-poitiers.fr/sites/ecole-test
**/


Dans l'espace priv�
	- gestion des classes utilisant le cahier de texte
	- gestion du cahier de texte de la classe (pour l'auteur-administrateur r�f�rent)
	- r�daction des devoirs (pour les auteurs)

Sur le site
	- S�lection d'une classe:
	 avec la boucle <BOUCLE_n(CLASSEKAYE)> #TITRE </B_n> 
	- Affichage des devoirs:
	 avec la boucle 	<BOUCLE_n(KAYE){par date_jour}>
				#ID_CLASSE
				#ID_AUTEUR
				#DISCIPLINE
				[(#DATE_JOUR|affdate{'d-m-Y'})]
				[(#DATE_ECHEANCE|affdate{'d-m-Y'})]
				#DESCRIPTIF
			</BOUCLE_n>

Le plugin est livr� avec un squelette cahier_de_texte.html compatible avec les squelettes alecole (fork de sarkaspip)
http://alecole.ac-poitiers.fr/sites/ecole-test/spip.php?article31
Ce squelette n�cessite le plugin onglets_pagines

A faire:
- une doc
- l'affichage profiliser des devoirs pour le visiteur connect� (en utilisant par exemple le plugin acces_restreints)
- donner un statut aux devoirs (propos�, publi�, � la poubelle)
- ajouter le possibilit� d'uploader des fichiers lorsqu'on r�dige les devoirs (la table spip_document_kaye existe)

Les tables:
spip_kaye
spip_documents_kaye
spip_classekaye