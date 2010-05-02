DROP TABLE IF EXISTS `amap_produit_distribution`;
DROP TABLE IF EXISTS `amap_vacance`;
DROP TABLE IF EXISTS `amap_prix`;
DROP TABLE IF EXISTS `amap_reglement`;
DROP TABLE IF EXISTS `amap_panier`;
DROP TABLE IF EXISTS `amap_variete`;
DROP TABLE IF EXISTS `amap_famille_variete`;
DROP TABLE IF EXISTS `amap_participation_sortie`;
DROP TABLE IF EXISTS `amap_sortie`;
DROP TABLE IF EXISTS `amap_contrat`;
DROP TABLE IF EXISTS `amap_evenements`;
DROP TABLE IF EXISTS `amap_produit`;
DROP TABLE IF EXISTS `amap_type_contrat`;
DROP TABLE IF EXISTS `amap_banque`;
DROP TABLE IF EXISTS `amap_personne`;
DROP TABLE IF EXISTS `amap_lieu`;
DROP TABLE IF EXISTS `amap_saison`;

create table amap_lieu
(id_lieu integer not null auto_increment, 
nom_lieu varchar(40) not null,
rue_lieu varchar(40) null,
cp_lieu varchar(5) null,
ville_lieu varchar(30) null,
telephone_lieu varchar(10) null,
constraint pk_amap_lieu primary key(id_lieu))
ENGINE=InnoDB DEFAULT CHARSET=utf8;

create table amap_type_contrat
(id_type integer not null auto_increment,
label_type varchar(20) not null,
constraint pk_amap_type_contrat primary key(id_type))
ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- id_article fait référence à l'article de spip désignant la saison (lié à l'affichage dans l'agenda)
create table amap_saison
(id_saison integer not null auto_increment, 
id_agenda integer default null,
id_contrat integer default null,
id_sortie integer default null,
id_responsable integer default null,
id_vacance integer default null,
constraint pk_amap_saison primary key(id_saison))
ENGINE=InnoDB DEFAULT CHARSET=utf8;

create table amap_personne
(id_personne integer not null auto_increment,
prenom varchar(20) null,
nom varchar(30) not null,
fixe varchar(10) null,
portable varchar(10) null,
adhesion varchar(4) null,
constraint pk_amap_personne primary key(id_personne))
ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- recopie automatique possible de la table spip_evenements via l'id_article de amap_saison
create table amap_evenements
(id_evenement integer not null auto_increment,
date_evenement datetime not null,
id_saison integer not null,
id_lieu integer null,
id_personne1 integer null,
id_personne2 integer null,
id_personne3 integer null,
constraint pk_amap_evenements primary key(id_evenement),
constraint fk1_amap_evenements foreign key(id_saison) references amap_saison(id_saison) ON DELETE CASCADE ON UPDATE CASCADE,
constraint fk2_amap_evenements foreign key(id_lieu) references amap_lieu(id_lieu) ON DELETE SET NULL ON UPDATE CASCADE,
constraint fk3_amap_evenements foreign key(id_personne1) references amap_personne(id_personne) ON DELETE SET NULL ON UPDATE CASCADE,
constraint fk4_amap_evenements foreign key(id_personne2) references amap_personne(id_personne) ON DELETE SET NULL ON UPDATE CASCADE,
constraint fk5_amap_evenements foreign key(id_personne3) references amap_personne(id_personne) ON DELETE SET NULL ON UPDATE CASCADE)
ENGINE=InnoDB DEFAULT CHARSET=utf8;

create table amap_produit
(id_produit integer not null auto_increment,
id_paysan integer default null,
label_produit varchar(20) not null,
constraint pk_amap_produit primary key(id_produit),
constraint fk_amap_produit foreign key(id_paysan) references amap_personne(id_personne) ON DELETE CASCADE ON UPDATE CASCADE)
ENGINE=InnoDB DEFAULT CHARSET=utf8;

create table amap_produit_distribution
(id_evenement integer not null,
id_produit integer not null,
constraint pk_amap_produit_distribution primary key(id_evenement, id_produit),
constraint fk1_amap_produit_distribution foreign key(id_evenement) references amap_evenements(id_evenement) ON DELETE CASCADE ON UPDATE CASCADE,
constraint fk2_amap_produit_distribution foreign key(id_produit) references amap_produit(id_produit) ON DELETE CASCADE ON UPDATE CASCADE)
ENGINE=InnoDB DEFAULT CHARSET=utf8;

create table amap_contrat
(id_contrat integer not null auto_increment, 
id_produit integer not null, 
id_saison integer not null,
id_personne integer not null,
demi_panier bool not null,
id_type integer not null,
debut_contrat integer null,
nb_distribution integer null,
constraint pk_amap_contrat primary key(id_contrat), 
constraint fk1_amap_contrat foreign key(id_produit) references amap_produit(id_produit) ON DELETE CASCADE ON UPDATE CASCADE,
constraint fk2_amap_contrat foreign key(id_saison) references amap_saison(id_saison) ON DELETE CASCADE ON UPDATE CASCADE,
constraint fk3_amap_contrat foreign key(id_personne) references amap_personne(id_personne) ON DELETE CASCADE ON UPDATE CASCADE,
constraint fk4_amap_contrat foreign key(id_type) references amap_type_contrat(id_type) ON DELETE CASCADE ON UPDATE CASCADE,
constraint fk5_amap_contrat foreign key(debut_contrat) references amap_evenements(id_evenement) ON DELETE CASCADE ON UPDATE CASCADE)
ENGINE=InnoDB DEFAULT CHARSET=utf8;

create table amap_vacance
(id_contrat integer not null,
id_evenement integer not null, 
id_remplacant integer default null,
remplacant_ext varchar(150) default null,
paye bool default null,
constraint pk_amap_vacance primary key(id_contrat, id_evenement), 
constraint fk1_amap_vacance foreign key(id_contrat) references amap_contrat(id_contrat) ON DELETE CASCADE ON UPDATE CASCADE,
constraint fk2_amap_vacance foreign key(id_evenement) references amap_evenements(id_evenement) ON DELETE CASCADE ON UPDATE CASCADE,
constraint fk3_amap_vacance foreign key(id_remplacant) references amap_personne(id_personne) ON DELETE SET NULL ON UPDATE CASCADE)
ENGINE=InnoDB DEFAULT CHARSET=utf8;

create table amap_prix
(id_produit integer not null, 
id_saison integer not null,
id_type integer not null,
prix_distribution integer not null,
constraint pk_amap_prix primary key(id_produit, id_saison, id_type),
constraint fk1_amap_prix foreign key(id_produit) references amap_produit(id_produit) ON DELETE CASCADE ON UPDATE CASCADE,
constraint fk2_amap_prix foreign key(id_saison) references amap_saison(id_saison) ON DELETE CASCADE ON UPDATE CASCADE,
constraint fk3_amap_prix foreign key(id_type) references amap_type_contrat(id_type) ON DELETE CASCADE ON UPDATE CASCADE)
ENGINE=InnoDB DEFAULT CHARSET=utf8;

create table amap_banque
(id_banque integer not null auto_increment, 
label_banque varchar(50) not null,
constraint pk_amap_banque primary key(id_banque))
ENGINE=InnoDB DEFAULT CHARSET=utf8;

create table amap_reglement
(id_cheque integer not null auto_increment,
id_contrat integer not null,
id_banque integer null,
ref_cheque varchar(12) null,
montant_euros varchar(4) not null,
constraint pk_amap_reglement primary key(id_cheque), 
constraint fk1_amap_reglement foreign key(id_contrat) references amap_contrat(id_contrat) ON DELETE CASCADE ON UPDATE CASCADE,
constraint fk2_amap_reglement foreign key(id_banque) references amap_banque(id_banque) ON DELETE SET NULL ON UPDATE CASCADE)
ENGINE=InnoDB DEFAULT CHARSET=utf8;

create table amap_famille_variete
(id_famille integer not null auto_increment,
label_famille varchar(30) not null,
id_produit integer not null,
constraint pk_amap_famille_variete primary key(id_famille), 
constraint fk_amap_famille_variete foreign key(id_produit) references amap_produit(id_produit) ON DELETE CASCADE ON UPDATE CASCADE)
ENGINE=InnoDB DEFAULT CHARSET=utf8;

create table amap_variete
(id_variete integer not null auto_increment,
label_variete varchar(30) not null,
id_famille integer not null,
constraint pk_amap_variete primary key(id_variete), 
constraint fk_amap_variete foreign key(id_famille) references amap_famille_variete(id_famille) ON DELETE CASCADE ON UPDATE CASCADE)
ENGINE=InnoDB DEFAULT CHARSET=utf8;

create table amap_panier
(id_produit integer not null, 
id_evenement integer not null,
id_element integer not null,
id_famille integer not null,
id_variete integer null,
quantite integer null,
poids varchar(6) null,
constraint pk_amap_panier primary key(id_produit, id_evenement, id_element),
constraint fk1_amap_panier foreign key(id_produit) references amap_produit(id_produit) ON DELETE CASCADE ON UPDATE CASCADE,
constraint fk2_amap_panier foreign key(id_evenement) references amap_evenements(id_evenement) ON DELETE CASCADE ON UPDATE CASCADE,
constraint fk3_amap_panier foreign key(id_famille) references amap_famille_variete(id_famille) ON DELETE CASCADE ON UPDATE CASCADE,
constraint fk4_amap_panier foreign key(id_variete) references amap_variete(id_variete) ON DELETE CASCADE ON UPDATE CASCADE)
ENGINE=InnoDB DEFAULT CHARSET=utf8;

create table amap_sortie
(id_sortie integer not null auto_increment,
date_sortie datetime not null,
id_saison integer not null,
id_produit integer not null, 
constraint pk_amap_sortie primary key(id_sortie), 
constraint fk1_amap_sortie foreign key(id_saison) references amap_saison(id_saison) ON DELETE CASCADE ON UPDATE CASCADE,
constraint fk2_amap_sortie foreign key(id_produit) references amap_produit(id_produit) ON DELETE CASCADE ON UPDATE CASCADE)
ENGINE=InnoDB DEFAULT CHARSET=utf8;

create table amap_participation_sortie
(id_sortie integer not null,
id_personne integer not null,
constraint pk_amap_participation_sortie primary key(id_sortie, id_personne),
constraint fk1_amap_participation_sortie foreign key(id_sortie) references amap_sortie(id_sortie) ON DELETE CASCADE ON UPDATE CASCADE,
constraint fk2_amap_participation_sortie foreign key(id_personne) references amap_personne(id_personne) ON DELETE CASCADE ON UPDATE CASCADE)
ENGINE=InnoDB DEFAULT CHARSET=utf8;
