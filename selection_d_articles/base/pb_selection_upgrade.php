<?php

function pb_selection_install ($action) {
	if ($action == "install") {
		global $tables_principales;

		include_spip("base/pb_selection_install");
		$tables_principales = pb_selection_declarer_tables_principales($tables_principales);
		
		include_spip('base/create');
		include_spip('base/abstract_sql');
		creer_base();
	};
}

?>