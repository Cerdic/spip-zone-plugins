function initialize_rssjs(flux, entrees, recept, mode) {
	/* spip remplace & par &amp; et google feed api n'aime pas */
	var flux = flux.replace(/&amp;/g, '&');
	var feed = new google.feeds.Feed(flux);
	feed.setNumEntries(entrees);
	feed.load(function(result) {
		if (!result.error) {
			var container = $('#'+recept);
			for (var i = 0; i < result.feed.entries.length; i++) {
				var entry = result.feed.entries[i];
				/* dt par defaut */
				var element = '<dt><a rel="external nofollow" class="spip_out" href="'+entry.link+'">'+entry.title+'</a></dt>';

				container.append(element);
				/* dd */
				if (mode != 'no_content'){
					if (mode == 'snippet')
						var descr = entry.contentSnippet;
					else
						var descr = entry.content;
						
					var element = '<dd>'+descr+'</dd>'
					container.append(element);
				}
			}
		}
	});
}