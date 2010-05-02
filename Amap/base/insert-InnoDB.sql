-- les lieux
insert into amap_lieu values ('1', 'Maison des &eacute;l&eacute;phants', null, null, null, null);
insert into amap_lieu values ('2', 'salle Louise Michel', '15 rue Aragon', '99000', 'Mutins', '09xxxxxxxx');

-- les types de contrat
insert into amap_type_contrat values ('1', 'normal');
insert into amap_type_contrat values ('2', 'solidaire');

-- les saisons
insert into amap_saison values ('1', '15', '16', '12', '11', '17');

-- les personnes (paysans et consom'acteurs)
insert into amap_personne values ('1', 'St&eacute;phane', 'Moulinet', '09xxxxxxxx', null, '2008');
insert into amap_personne values ('2', 'Michel', 'Sardon', null, null, null);
insert into amap_personne values ('3', 'Kim', 'Deal', null, null, null);
insert into amap_personne values ('4', 'S&eacute;bastien', 'Faure', '04xxxxxxxx', '06xxxxxxxx', '2008');
insert into amap_personne values ('5', 'Ren&eacute;', 'Binam&eacute;', '01xxxxxxxx', null, null);


-- les produits et les paysans associés
insert into amap_produit values ('1', '3', 'l&eacute;gume');
insert into amap_produit values ('2', null, 'pain');

-- les évènements
insert into amap_evenements values ('48', '2008-11-17 19:00:00', '1', '1', '1', null, null);
insert into amap_evenements values ('50', '2008-11-24 19:00:00', '1', '1', '2', null, null);
insert into amap_evenements values ('11', '2008-12-01 19:00:00', '1', '1', '4', null, null);
insert into amap_evenements values ('13', '2008-12-08 19:00:00', '1', '1', '1', null, null);

-- les produits aux distributions
insert into amap_produit_distribution values ('48', '1');
insert into amap_produit_distribution values ('50', '1');
insert into amap_produit_distribution values ('50', '2');
insert into amap_produit_distribution values ('11', '1');
insert into amap_produit_distribution values ('13', '1');
insert into amap_produit_distribution values ('13', '2');

-- les contrats
insert into amap_contrat values ('1', '1', '1', '1', '0', '1', '48', '59');
insert into amap_contrat values ('2', '1', '1', '4', '0', '1', '50', '58');
insert into amap_contrat values ('3', '2', '1', '1', '1', '1', '50', '24');
insert into amap_contrat values ('4', '2', '1', '5', '0', '1', '11', '23');

-- les vacances
insert into amap_vacance values ('1', '11', '2', null, null);

-- les prix hebdomadaires
insert into amap_prix values ('1', '1', '1', '20');
insert into amap_prix values ('2', '1', '1', '5');

-- les banques
insert into amap_banque values ('1', 'La Nef');
insert into amap_banque values ('2', 'Cr&eacute;dit Coop&eacute;ratif');
insert into amap_banque values ('3', 'La Banque Postale');
insert into amap_banque values ('4', 'BNP Paribas');
insert into amap_banque values ('5', 'Soci&eacute;t&eacute; G&eacute;n&eacute;rale');
insert into amap_banque values ('6', 'Cr&eacute;dit Agricole');
insert into amap_banque values ('7', 'Le Cr&eacute;dit Lyonnais');

-- les réglements
insert into amap_reglement values ('1', '1', '1', null, '500');
insert into amap_reglement values ('2', '1', '1', null, '300');
insert into amap_reglement values ('3', '2', '3', null, '500');
insert into amap_reglement values ('4', '3', '2', null, '60');
insert into amap_reglement values ('5', '4', '4', null, '60');

-- les familles de variétés
insert into amap_famille_variete values ('1', 'Courge', '1');
insert into amap_famille_variete values ('2', 'Chou', '1');
insert into amap_famille_variete values ('3', 'Navet', '1');
insert into amap_famille_variete values ('4', 'Oignon', '1');
insert into amap_famille_variete values ('5', 'Persil', '1');
insert into amap_famille_variete values ('6', 'Pomme de terre', '1');
insert into amap_famille_variete values ('7', 'Radis', '1');
insert into amap_famille_variete values ('8', 'Salade', '1');
insert into amap_famille_variete values ('9', 'Tomate', '1');

insert into amap_famille_variete values ('10', 'pain complet', '2');
insert into amap_famille_variete values ('11', 'pain aux noix', '2');
insert into amap_famille_variete values ('12', 'pain aux figues', '2');

-- les variétés
insert into amap_variete values ('1', 'butternut', '1');
insert into amap_variete values ('2', 'citrouille', '1');
insert into amap_variete values ('3', 'longue de Nice', '1');
insert into amap_variete values ('4', 'musqu&eacute;e de Provence', '1');
insert into amap_variete values ('5', 'patidou', '1');
insert into amap_variete values ('6', 'patisson', '1');
insert into amap_variete values ('7', 'potimarrons', '1');
insert into amap_variete values ('8', 'potiron', '1');
insert into amap_variete values ('9', 'spaghetti', '1');
insert into amap_variete values ('10', 'brocoli', '2');
insert into amap_variete values ('11', 'bruxelles', '2');
insert into amap_variete values ('12', 'cabus', '2');
insert into amap_variete values ('13', 'chine', '2');
insert into amap_variete values ('14', 'fleur', '2');
insert into amap_variete values ('15', 'milan', '2');
insert into amap_variete values ('16', 'pain de sucre', '2');
insert into amap_variete values ('17', 'pomm&eacute;', '2');
insert into amap_variete values ('18', 'rave', '2');
insert into amap_variete values ('19', 'blanc', '3');
insert into amap_variete values ('20', 'boule d\'or', '3');
insert into amap_variete values ('21', 'tokyo', '3');
insert into amap_variete values ('22', 'blanc', '4');
insert into amap_variete values ('23', 'rouge', '4');
insert into amap_variete values ('24', 'fris&eacute;', '5');
insert into amap_variete values ('25', 'plat', '5');
insert into amap_variete values ('26', 'belle de fontenay', '6');
insert into amap_variete values ('27', 'charlotte', '6');
insert into amap_variete values ('28', 'nicolas', '6');
insert into amap_variete values ('29', 'ratte', '6');
insert into amap_variete values ('30', 'blanc', '7');
insert into amap_variete values ('31', 'noir', '7');
insert into amap_variete values ('32', 'rose', '7');
insert into amap_variete values ('33', 'batavia', '8');
insert into amap_variete values ('34', 'feuille de ch&ecirc;ne', '8');
insert into amap_variete values ('35', 'laitue', '8');
insert into amap_variete values ('36', 'm&ecirc;che', '8');
insert into amap_variete values ('37', 'scarolle', '8');
insert into amap_variete values ('38', 'cerise', '9');
insert into amap_variete values ('39', 'c&oelig;ur de b&oelig;uf', '9');
insert into amap_variete values ('40', 'cornue des Andes', '9');
insert into amap_variete values ('41', 'noire de Crim&eacute;', '9');
insert into amap_variete values ('42', 'ronde rouge', '9');
insert into amap_variete values ('43', 'rose de Berne', '9');

-- composition des paniers
insert into amap_panier values ('1', '48', '1',  '2', '13', '1', null);
insert into amap_panier values ('1', '48', '2',  '6', '28', null, '1.5 kg');

insert into amap_panier values ('1', '50', '1',  '1', null, null, null);
insert into amap_panier values ('1', '50', '2',  '5', '25', '1', null);

insert into amap_panier values ('1', '11', '1',  '1', null, null, null);
insert into amap_panier values ('1', '11', '2',  '2', '13', '1', null);
insert into amap_panier values ('1', '11', '3',  '5', '25', '1', null);
insert into amap_panier values ('1', '11', '4',  '6', '28', null, '1.5 kg');

insert into amap_panier values ('1', '13', '1',  '2', '13', '1', null);
insert into amap_panier values ('1', '13', '2',  '6', '28', null, '1.5 kg');
insert into amap_panier values ('1', '13', '3',  '5', '25', '1', null);

insert into amap_panier values ('2', '50', '1',  '10', null, '1', null);
insert into amap_panier values ('2', '50', '2',  '11', null, '1', null);

insert into amap_panier values ('2', '13', '1',  '10', null, '1', null);
insert into amap_panier values ('2', '13', '2',  '12', null, '1', null);

-- Les dates des sorties à la ferme
insert into amap_sortie values ('1', '2008-10-15', '1', '1');
insert into amap_sortie values ('2', '2008-11-22', '1', '1');
insert into amap_sortie values ('3', '2008-12-17', '1', '1');

-- La participation aux sorties à la ferme
insert into amap_participation_sortie values ('1', '1');
insert into amap_participation_sortie values ('1', '4');
