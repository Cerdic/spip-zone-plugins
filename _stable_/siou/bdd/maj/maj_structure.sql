-- ================= Modif0
ALTER TABLE `odb_candidats` ADD `login` VARCHAR( 31 ) default NULL ;
ALTER TABLE `odb_candidats` ADD `pdn` INT NOT NULL AFTER `nationalite` ;
ALTER TABLE `odb_histo_candidats` ADD `pdn` INT NOT NULL AFTER `nationalite` ;

-- ================= Modif1
-- odb_ref_salle
ALTER TABLE `odb_ref_salle` ADD `annee` YEAR NOT NULL AFTER `id` ;
ALTER TABLE `odb_ref_salle` DROP PRIMARY KEY ,ADD PRIMARY KEY ( `id` , `annee` );
UPDATE odb_ref_salle SET annee=2007;

-- odb_ref_examen
ALTER TABLE `odb_ref_examen` ADD `annee` YEAR NOT NULL AFTER `id` ;
ALTER TABLE `odb_ref_examen` DROP PRIMARY KEY ,ADD PRIMARY KEY ( `id` , `annee` ) ;
ALTER TABLE `odb_ref_examen` DROP INDEX `id_serie` ,
ADD INDEX `id_serie` ( `id_serie` , `id_matiere` , `annee` ) ;
UPDATE odb_ref_examen SET annee=2007;

-- odb_candidats
ALTER TABLE `odb_candidats` CHANGE `id_saisie` `id_saisie` INT( 11 ) NOT NULL AUTO_INCREMENT ;

-- ================== Modif2
-- odb_ref_operateur
ALTER TABLE `odb_ref_operateur` ADD `annee` YEAR NOT NULL AFTER `id` ;
ALTER TABLE `odb_ref_operateur`
  DROP PRIMARY KEY,
   ADD PRIMARY KEY(
     `id`,
     `annee`);
UPDATE odb_ref_operateur SET annee =2007 ;

-- odb_ref_examen
ALTER TABLE `odb_ref_examen` ADD `type` ENUM( '', 'Pratique', 'Ecrit', 'Oral', 'Divers' ) NOT NULL AFTER `examen`;
UPDATE odb_ref_examen exa SET TYPE = if( exa.duree =0 AND EXTRACT( DAY FROM exa.examen ) =0, 'Oral', if( exa.duree >0, 'Ecrit', 'Pratique' ) ) ;

-- odb_param
UPDATE odb_param SET param='_delib1_2007' where param='_delib1';

-- ================== Modif3
-- odb_notes
ALTER TABLE `odb_notes` ADD `id_serie` SMALLINT NOT NULL AFTER `annee` ,
ADD `jury` SMALLINT NOT NULL AFTER `id_serie` ;

-- ==================== Modif4
-- odb_decisions
 ALTER TABLE `odb_decisions` CHANGE `delib2` `delib2` ENUM( '', 'Oral', 'Refuse', 'Reserve', 'Passable', 'Abien', 'Bien', 'TBien' ) 
 CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL  
 
-- ==================== Modif5
-- odb_ref_ecole : ajout du coefficient du bac pour chaque ecole/serie
 --ALTER TABLE `odb_ref_ecole` ADD `coeff_bac` TINYINT NOT NULL DEFAULT '0' COMMENT 'Coefficient du bac';
 --Remplacé par
  ALTER TABLE `odb_ref_ecole` CHANGE `coeff_bac` `coeff_bac` DECIMAL( 3, 2 ) NOT NULL DEFAULT '0' COMMENT 'Coefficient du bac';
