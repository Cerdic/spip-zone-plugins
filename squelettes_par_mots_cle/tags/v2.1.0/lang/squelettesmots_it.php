<?php

	$GLOBALS[$GLOBALS['idx_lang']] = array(
	
	'titre_page' => 'Configurazione della scelta di modelli da parole chiave.',
	'gros_titre' => 'Creare regole per scegliere i modelli con una parola chiave.',
	'help' => 'Solamente gli amministratori possono utilizzare questa pagina. Potete creare regole per la scelta di modelli di pagina con una parola chiave.

Una regola specifica:
-# uno sfondo di base,
-# il gruppo di parole chiave che contiene le parole che specificano il modello,
-# il tipo di elementi visualizzati sulla pagina.

I modelli saranno allora chiamati {{sfondo-parola.html}}. Il plugin cercher&agrave; prima un modello che corrisponda a una delle parole chiave associate all\'elemento. Se non lo trova, cercher&agrave; allora un modello che corrisponda a una parola chiave di una delle rubriche che contengono questo elemento.

Gli autori devono allora solamente associare la parola chiave corretta all\'elemento per scegliere il suo modello.',
	
	'reglei' => 'Regola @id@',
	'nouvelle_regle' => 'Nuova regola',
	'fond' => 'Sfondo:',
	'groupe' => 'Gruppo:',
	'type' => 'Tipo:',
	'possibilites' => '@total_actif@ modello(i).',
	'utiliserasquelette' => 'Questo articolo utilizzer&agrave; il modello @squelette@'
);
?>
