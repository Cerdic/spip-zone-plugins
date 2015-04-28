if (window.navigator.standalone) {
	$("a:not([onclick])").on("click", function() {
		var lien = $(this).attr("href");
		if (lien != "#") {
			document.location = lien;
			return false;
		}
	});
}
