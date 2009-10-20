INSERT INTO `spipmine_clients` (`id_client`, `nom_client`, `nom_court`, `adresse_1`, `adresse_2`, `boite_postale`, `code_postal`, `ville`, `commentaires`) VALUES
(1, 'BANQUE B.DUSH &amp; J.MOLL', 'DUSHMOLL', '22 rue du Quatre Septembre', NULL, NULL, '75002', 'PARIS', NULL),
(2, 'AGENCE TOP MODEL', 'TOPMODEL', '3 rue Fernet-Branca', NULL, NULL, '75018', 'PARIS', NULL),
(3, 'HOPITAL BOURICAULT', 'BOURIQUE', '101 avenue Parmentier', NULL, NULL, '91000', 'MARCOUSSIS', NULL),
(4, 'PELOTON CRS 19', 'CRS19', 'Autoroute du Nord', NULL, NULL, '60210', 'SENLIS', NULL);
INSERT INTO `spipmine_contacts` (`id_contact`, `id_compte`, `titre`, `prenom`, `nom`, `fonction`, `telephone`, `email`, `facture`, `commentaire`) VALUES
(1, 1, 'M.', 'Jean-Yves', 'DUC', NULL, NULL, NULL, 1, NULL),
(2, 2, 'Mme', 'Fernande', 'BEAUREGARD', NULL, NULL, NULL, 1, NULL),
(3, 3, 'Dr.', 'Nicolas', 'LAU', NULL, NULL, NULL, 1, NULL),
(4, 4, 'M.', 'Francis', 'BLANCHE', NULL, NULL, NULL, 1, NULL),
(5, 2, 'Mlle', 'H�l�ne', 'MARCHAIS', NULL, NULL, NULL, 1, NULL);
INSERT INTO `spipmine_types_actions` (`id_type_action`, `nom_type_action`, `commentaires`) VALUES
(1, '10. ** �  d�finir ! **', NULL),
(2, '20. graphisme', 'recherches graphiques hors web (identit� visuelle, logo...)'),
(3, '30. cr�a web', 'recherches graphiques et/ou ergonomiques sp�cifiquement web'),
(4, '40. print', 'production print (par opposition � "int�gration html" pour le web)'),
(5, '50. int�gration (html +CSS)', NULL),
(6, '90. d�veloppement / programmation', NULL),
(7, '102. support technique', 'aide / support apport�e au client (par t�l�phone ou mail)'),
(8, '130. assistance maitrise d''ouvrage', 'd�finition du p�rim�tre d''un projet, prise de notes, interview'),
(9, '110. gestion de projet (avant projet)', 'validation, livraison...'),
(10, '140. h�bergements / domaines', NULL),
(11, '150. maintenance web', 'retouches sur un projet web d�j� livr�'),
(12, '160. maintenance informatique', NULL),
(13, '170. formation', NULL),
(14, '60. int�gration (boucles + include)', NULL),
(15, '70. int�gration (finitions)', NULL),
(16, '80. int�gration (d�buggage)', NULL),
(17, '100. saisie / ordonnancement de contenus SPIP', NULL),
(18, '120. gestion de projet (fin de projet)', 'documentation, listage des bugs avec le client...'),
(19, '165. messagerie �lectronique', 'configuration des comptes, maintenance serveur'),
(20, '92. D�veloppement javascript / jQuery', 'D�veloppement javascript / jQuery');
INSERT INTO `spipmine_types_documents` (`id_type_document`, `nom_type_document`) VALUES
(1, 'Facture'),
(2, 'Devis'),
(3, 'Avoir'),
(4, 'Facture proforma');
INSERT INTO `spipmine_types_facturation` (`id_type_facturation`, `nom_type_facturation`, `commentaires`) VALUES
(1, '20. sur devis', 'utilis� pour des projets chiffr�s pour lesquels un devis a �t� r�alis�; on reste syst�matiquement dans le cadre du devis => si le nombre d''heures compt�es d�passe le nombre d''heures estim�es dans le devis, c''est pour nous'),
(2, '50. en r�gie', 'utilis� sur des projets non chiffr�s (pas encore chiffr�s ou non chiffrables, p.ex. aide � la maitrise d''ouvrage); l''estimation n''est pas possible; le devis n''est pas possible; chaque heure pass�e est d�compt�e au tarif "forfait" applicable (4h, 8h, 16h ou 24h)'),
(3, '40. forfait', 'utilis� pour des projets dont on peut difficilement estimer le nombre d''heures � l''avance (p. ex. maintenance);'),
(4, '60. autre', 'Autre type de r�glement'),
(5, '10. ** non d�fini ! **', '** A d�finir imp�rativement ! **'),
(0, '30. sur estimation', 'on chiffre avec le client un temps � passer sur un {{projet}} et � d�compter d''un {{forfait}}; il n''y a pas de devis �crit; la fiche projet sert de r�f�rence (objectif, estimation); en cas de d�passement : soit il s''agit de nouvelles prestations qui n''�taient pas demand�es au d�part => nouvelle estimation (cout pour le client); soit il s''agit des prestations demand�es mais qui demandent plus de temps => pas de nouvelle facture (cout pour nous)');
INSERT INTO `spipmine_types_livrables` (`id_type_livrable`, `nom_type_livrable`) VALUES
(1, 'fichier PDF'),
(2, 'document imprim�'),
(3, '� pr�ciser'),
(4, NULL);
INSERT INTO `spipmine_types_prestations` (`id_type_presta`, `nom_type_presta`, `commentaires`) VALUES
(1, '35. print', NULL),
(2, '30. cr�a web', NULL),
(3, '70. h�bergements / domaines', NULL),
(4, '80. maintenance web', NULL),
(5, '90. maintenance informatique', NULL),
(6, '100. formation', NULL),
(7, '50. d�veloppement / programmation', NULL),
(8, '60. assistance maitrise d''ouvrage', NULL),
(9, '20. graphisme', 'Recherches graphiques, identit� visuelle'),
(10, '10. ** � d�finir ! **', NULL),
(11, '40. int�gration html', NULL),
(12, '55. support t�l�phonique', 'aide apport�e au client par t�l�phone'),
(13, '92. Admin reseaux / serveur', NULL);
INSERT INTO `spipmine_types_reglements` (`id_type_reglement`, `nom_type_reglement`, `commentaires`) VALUES
(1, 'Ch�que', NULL),
(2, 'Virement bancairre', NULL),
(3, 'Esp�ces', NULL),
(4, 'Compensation', 'Facture r�gl�e par l''�mission d''une autre facture de la part du client');
INSERT INTO `spipmine_types_status` (`id_type_status`, `nom_type_status`, `commentaires`) VALUES
(1, '10. devis en cours', 'le projet a �t� discut� avec le client, la fiche projet est r�dig�e; un devis est en cours d''�laboration'),
(2, '20. attente accord client', 'la fiche projet est saisie, un devis a �t� fait mais pas encore accept�; la prod n''a pas d�marr�'),
(3, '30. accept�', 'Le projet est accept� par le client, mais la production n''a pas encore d�marr�'),
(4, '40. en cours de fabrication', 'Le projet est d�marr�, nous sommes en train de le fabriquer; il n''est pas encore termin� ni livr�.'),
(5, '50. livr�, en test', 'Le projet est livr�; le client le teste avant de l''accepter d�finitivement'),
(6, '60. livr�, en production', 'Le projet est recett� d�finitivement par le client; pendant la p�riode de garantie nous pouvons corriger certains dysfonctionnements'),
(7, '70. temin�, en production', 'Projet termin� et hors garantie. Toute nouvelle intervention n�cessite l''ouverture d''un nouveau projet (maintenance, am�liorations).'),
(8, '80. arr�t�', 'Projet d�marr� puis arr�t� pour diverses raisons; facturation des heures pass�es'),
(9, '90. abandonn�', 'Le projet n''a jamais �t� d�marr� et n''a pas franchi le stade de la fiche de projet');