/*
+--------------------------------------------+
| DW2 2.14 (03/2007) - SPIP 1.9.2
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
