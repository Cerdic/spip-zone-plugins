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
(5, 2, 'Mlle', 'Hélène', 'MARCHAIS', NULL, NULL, NULL, 1, NULL);
INSERT INTO `spipmine_types_actions` (`id_type_action`, `nom_type_action`, `commentaires`) VALUES
(1, '10. ** à  définir ! **', NULL),
(2, '20. graphisme', 'recherches graphiques hors web (identité visuelle, logo...)'),
(3, '30. créa web', 'recherches graphiques et/ou ergonomiques spécifiquement web'),
(4, '40. print', 'production print (par opposition à "intégration html" pour le web)'),
(5, '50. intégration (html +CSS)', NULL),
(6, '90. développement / programmation', NULL),
(7, '102. support technique', 'aide / support apportée au client (par téléphone ou mail)'),
(8, '130. assistance maitrise d''ouvrage', 'définition du périmètre d''un projet, prise de notes, interview'),
(9, '110. gestion de projet (avant projet)', 'validation, livraison...'),
(10, '140. hébergements / domaines', NULL),
(11, '150. maintenance web', 'retouches sur un projet web déjà livré'),
(12, '160. maintenance informatique', NULL),
(13, '170. formation', NULL),
(14, '60. intégration (boucles + include)', NULL),
(15, '70. intégration (finitions)', NULL),
(16, '80. intégration (débuggage)', NULL),
(17, '100. saisie / ordonnancement de contenus SPIP', NULL),
(18, '120. gestion de projet (fin de projet)', 'documentation, listage des bugs avec le client...'),
(19, '165. messagerie électronique', 'configuration des comptes, maintenance serveur'),
(20, '92. Développement javascript / jQuery', 'Développement javascript / jQuery');
INSERT INTO `spipmine_types_documents` (`id_type_document`, `nom_type_document`) VALUES
(1, 'Facture'),
(2, 'Devis'),
(3, 'Avoir'),
(4, 'Facture proforma');
INSERT INTO `spipmine_types_facturation` (`id_type_facturation`, `nom_type_facturation`, `commentaires`) VALUES
(1, '20. sur devis', 'utilisé pour des projets chiffrés pour lesquels un devis a été réalisé; on reste systématiquement dans le cadre du devis => si le nombre d''heures comptées dépasse le nombre d''heures estimées dans le devis, c''est pour nous'),
(2, '50. en régie', 'utilisé sur des projets non chiffrés (pas encore chiffrés ou non chiffrables, p.ex. aide à la maitrise d''ouvrage); l''estimation n''est pas possible; le devis n''est pas possible; chaque heure passée est décomptée au tarif "forfait" applicable (4h, 8h, 16h ou 24h)'),
(3, '40. forfait', 'utilisé pour des projets dont on peut difficilement estimer le nombre d''heures à l''avance (p. ex. maintenance);'),
(4, '60. autre', 'Autre type de règlement'),
(5, '10. ** non défini ! **', '** A définir impérativement ! **'),
(0, '30. sur estimation', 'on chiffre avec le client un temps à passer sur un {{projet}} et à décompter d''un {{forfait}}; il n''y a pas de devis écrit; la fiche projet sert de référence (objectif, estimation); en cas de dépassement : soit il s''agit de nouvelles prestations qui n''étaient pas demandées au départ => nouvelle estimation (cout pour le client); soit il s''agit des prestations demandées mais qui demandent plus de temps => pas de nouvelle facture (cout pour nous)');
INSERT INTO `spipmine_types_livrables` (`id_type_livrable`, `nom_type_livrable`) VALUES
(1, 'fichier PDF'),
(2, 'document imprimé'),
(3, 'à préciser'),
(4, NULL);
INSERT INTO `spipmine_types_prestations` (`id_type_presta`, `nom_type_presta`, `commentaires`) VALUES
(1, '35. print', NULL),
(2, '30. créa web', NULL),
(3, '70. hébergements / domaines', NULL),
(4, '80. maintenance web', NULL),
(5, '90. maintenance informatique', NULL),
(6, '100. formation', NULL),
(7, '50. développement / programmation', NULL),
(8, '60. assistance maitrise d''ouvrage', NULL),
(9, '20. graphisme', 'Recherches graphiques, identité visuelle'),
(10, '10. ** à définir ! **', NULL),
(11, '40. intégration html', NULL),
(12, '55. support téléphonique', 'aide apportée au client par téléphone'),
(13, '92. Admin reseaux / serveur', NULL);
INSERT INTO `spipmine_types_reglements` (`id_type_reglement`, `nom_type_reglement`, `commentaires`) VALUES
(1, 'Chèque', NULL),
(2, 'Virement bancairre', NULL),
(3, 'Espèces', NULL),
(4, 'Compensation', 'Facture réglée par l''émission d''une autre facture de la part du client');
INSERT INTO `spipmine_types_status` (`id_type_status`, `nom_type_status`, `commentaires`) VALUES
(1, '10. devis en cours', 'le projet a été discuté avec le client, la fiche projet est rédigée; un devis est en cours d''élaboration'),
(2, '20. attente accord client', 'la fiche projet est saisie, un devis a été fait mais pas encore accepté; la prod n''a pas démarré'),
(3, '30. accepté', 'Le projet est accepté par le client, mais la production n''a pas encore démarré'),
(4, '40. en cours de fabrication', 'Le projet est démarré, nous sommes en train de le fabriquer; il n''est pas encore terminé ni livré.'),
(5, '50. livré, en test', 'Le projet est livré; le client le teste avant de l''accepter définitivement'),
(6, '60. livré, en production', 'Le projet est recetté définitivement par le client; pendant la période de garantie nous pouvons corriger certains dysfonctionnements'),
(7, '70. teminé, en production', 'Projet terminé et hors garantie. Toute nouvelle intervention nécessite l''ouverture d''un nouveau projet (maintenance, améliorations).'),
(8, '80. arrêté', 'Projet démarré puis arrêté pour diverses raisons; facturation des heures passées'),
(9, '90. abandonné', 'Le projet n''a jamais été démarré et n''a pas franchi le stade de la fiche de projet');