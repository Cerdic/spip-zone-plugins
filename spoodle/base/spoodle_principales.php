<?php

function spoodle_declarer_tables_principales($tables_principales){
$spoodle_sondage=array(
												"id_sondage"=>"BIGINT NOT NULL",
												"id_auteur"=>"BIGINT NOT NULL",
												"nom"=>"VARCHAR(128) NOT NULL",
												"e-mail"=>"TINYTEXT NOT NULL",
												"titre"=>"VARCHAR(128) NOT NULL",
												"descriptif"=>"TEXT NOT NULL",
												"status"=>"VARCHAR(10) NOT NULL",
												"date_creation"=>"DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP",
												"date_maj"=>"TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP",
												"date_fin"=>"DATETIME NOT NULL DEFAULT 0000-00-00 00:00:00",
												"notification"=>"BOOL NOT NULL",
												"simple_complique"=>"BOOL NOT NULL",
												"cacher_resultats"=>"BOOL NOT NULL",
												"max_un_choix"=>"BOOL NOT NULL",
												"max_autant_par_choix"=>"INT NOT NULL",
												"heure_precisee"=>"BOOL NOT NULL",
												"privee_publique"=>"BOOL NOT NULL"
)

$spoodle_sondage_key=array(
													"primary key"=>"id_sondage",
													"key id_auteur"=>"id_auteur",
													"key status"=>"status"
)

$spoodle_sondage_join=array(
													"id_sondage"=>"id_sondage",
													"id_auteur"=>"id_auteur"
)
$tables_principales["spip_spoodle_sondage"]=array(
																									"field"=>$spoodle_sondage,
																									"key"=>$spoodle_sondage_key,
																									"join"=>$spoodle_sondage_join
)
return $tables_principales;

}

?>