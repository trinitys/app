$(function(){
	require('querystring', function(qs){
		var links = Array.prototype.slice.call(document.getElementsByTagName('a')),
			l = links.length,
			link,
			q;

		while(l--){
			link = links[l];
			q = new qs(link.href);
			var path = q.getPath();
			if(q.getPath().indexOf('/wiki/') > -1 && path.indexOf(':') == -1) {
				link.href = wgServer + '/wikia.php?controller=GameGuidesController&method=renderPage&title=' + path.slice(6);
				link.className += ' validLink';
			} else if(link.className.indexOf('image') > -1) {
				link.className += ' validLink';
			}
		}
	});
});