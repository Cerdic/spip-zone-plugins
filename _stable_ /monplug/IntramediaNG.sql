-- ================================================================================
--   mysql SQL DDL Script File
-- ================================================================================


-- ===============================================================================
-- 
--   Generated by:      tedia2sql -- v1.2.12
--                      See http://tedia2sql.tigris.org/AUTHORS.html for tedia2sql author information
-- 
--   Target Database:   mysql
--   Generated at:      Mon Jul 28 16:01:47 2008
--   Input Files:       IntramediaNG.dia
-- 
-- ================================================================================



-- Generated SQL Constraints Drop statements
-- --------------------------------------------------------------------
--     Target Database:   mysql
--     SQL Generator:     tedia2sql -- v1.2.12
--     Generated at:      Mon Jul 28 16:01:47 2008
--     Input Files:       IntramediaNG.dia



-- Generated Permissions Drops
-- --------------------------------------------------------------------
--     Target Database:   mysql
--     SQL Generator:     tedia2sql -- v1.2.12
--     Generated at:      Mon Jul 28 16:01:47 2008
--     Input Files:       IntramediaNG.dia




-- Generated SQL View Drop Statements
-- --------------------------------------------------------------------
--     Target Database:   mysql
--     SQL Generator:     tedia2sql -- v1.2.12
--     Generated at:      Mon Jul 28 16:01:47 2008
--     Input Files:       IntramediaNG.dia



-- Generated SQL Schema Drop statements
-- --------------------------------------------------------------------
--     Target Database:   mysql
--     SQL Generator:     tedia2sql -- v1.2.12
--     Generated at:      Mon Jul 28 16:01:47 2008
--     Input Files:       IntramediaNG.dia

 drop table if exists projects ;
 drop table if exists users ;


-- Generated SQL Schema
-- --------------------------------------------------------------------
--     Target Database:   mysql
--     SQL Generator:     tedia2sql -- v1.2.12
--     Generated at:      Mon Jul 28 16:01:47 2008
--     Input Files:       IntramediaNG.dia


-- projects
create table projects (
  project_id                bigint,
  title                     tinytext,
  description               text,
  sys_crea                  datetime,
  sys_modif                 datetime
) ;

-- users
create table users (
  user_id                   bigint,
  login                     varchar(30),
  password                  varchar(255)
) ;




-- Generated SQL Views
-- --------------------------------------------------------------------
--     Target Database:   mysql
--     SQL Generator:     tedia2sql -- v1.2.12
--     Generated at:      Mon Jul 28 16:01:47 2008
--     Input Files:       IntramediaNG.dia




-- Generated Permissions
-- --------------------------------------------------------------------
--     Target Database:   mysql
--     SQL Generator:     tedia2sql -- v1.2.12
--     Generated at:      Mon Jul 28 16:01:47 2008
--     Input Files:       IntramediaNG.dia



-- Generated SQL Insert statements
-- --------------------------------------------------------------------
--     Target Database:   mysql
--     SQL Generator:     tedia2sql -- v1.2.12
--     Generated at:      Mon Jul 28 16:01:47 2008
--     Input Files:       IntramediaNG.dia



-- Generated SQL Constraints
-- --------------------------------------------------------------------
--     Target Database:   mysql
--     SQL Generator:     tedia2sql -- v1.2.12
--     Generated at:      Mon Jul 28 16:01:47 2008
--     Input Files:       IntramediaNG.dia


