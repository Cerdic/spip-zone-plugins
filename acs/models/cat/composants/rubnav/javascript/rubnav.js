/* todo : debug ! ;-) */
if(document.all) {
	jQuery(document).ready(
		function() {
			setHoverForDamnedBuggyIE();
		  onAjaxLoad(setHoverForDamnedBuggyIE);
		}
	);
	
	function setHoverForDamnedBuggyIE() {
		jQuery(".cRubnav li.menu-item>a").hover(function(){
		  jQuery(this).parent().find("ul.hidden:first").show("slow");
		},function(){
		  jQuery(this).parent().find("ul.hidden:first").hide("slow");
		});
	}
}
