$(document).ready(function() {
	$("a.spip_out").click(function() {
		window.open(this.href);
		return false;
	});
});