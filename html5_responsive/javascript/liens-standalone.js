if (window.navigator.standalone) {
	$(document).on("click", "a:not([onclick])", function() {
		var lien = $(this).attr("href");
		if (lien != "#") {
			document.location = lien;
			return false;
		}
	});
}
