function initNavgroups() {
	var ng = jQuery(".cNavGroup");
	if (ng.accordion) {
		ng.accordion({
			event: "mouseover",
			header: "h3"
		});
	}
}

jQuery(document).ready(
  function() {
  	initNavgroups();
    onAjaxLoad(initNavgroups);
  }
);
