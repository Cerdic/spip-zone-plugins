CREATE TABLE projects(
-- Attributes --
project_id bigint,
title tinytext,
description text,
sys_crea datetime,
sys_modif datetime);

CREATE TABLE users(
-- Attributes --
user_id bigint,
login varchar(30),
password varchar(255));

