if (window.navigator.standalone) {
	$("a:not([onclick])").live("click", function() {
		var lien = $(this).attr("href");
		if (lien != "#") {
			document.location = lien;
			return false;
		}
	});
}
