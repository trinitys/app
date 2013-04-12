jQuery(function($) {
	$('#WikiaArticle p').click(function(event) {
		if (event.altKey && event.ctrlKey) {
			var editUrl = $('#ca-edit').attr('href');
			if (editUrl) {
				var headersCount = $(this).prevAll(':header').length;
				if (headersCount) {
					editUrl += '&section=' + headersCount;
				}
				window.location = editUrl;
			}
			return false;
		}
	})
});