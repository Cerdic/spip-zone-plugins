<?php

function tickets_install ($action) {
	if ($action == "install") {
		global $tables_principales;

		include_spip("base/tickets_install");
		$tables_principales = tickets_declarer_tables_principales($tables_principales);
		
		include_spip('base/create');
		include_spip('base/abstract_sql');
		creer_base();



	};
}

?>