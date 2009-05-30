/*
+--------------------------------------------+
| DW2 2.13 (02/2007) - SPIP 1.9.1
+--------------------------------------------+
| H. AROUX . Scoty . koakidi.com
| Script certifié KOAK2.0 strict, mais si !
+--------------------------------------------+
| fermer div alerte (zone public)
+--------------------------------------------+
*/

function fermepop() {
	var pop = document.getElementById('dw2_alerte');
	if (pop) { pop.style.visibility = 'hidden'; }
}
