$(function() {
	$(".tablesorter").tablesorter({
		widgets: ["zebra","stickyHeaders", "filter","print", "columnSelector"],
		widgetOptions: {
			columnSelector_container : $('#columnSelector'),
		}
	});
  $('.print').click(function() {
    $('.tablesorter').trigger('printTable');
  });
});
