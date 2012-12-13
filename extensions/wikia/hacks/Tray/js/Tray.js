var WikiaTray = {

	// Properties
	open: false,
	speed: 250,
	$search: $('#WikiaTrayHeader input[type="search"]'),

	// Methods
	init: function() {
		// Bindings
		this.$search.on( 'input', $.proxy( this.searchChange, this ) );
	},

	searchChange: function() {
		if ( this.$search.val().length == 0 ) {
			this.closeTray();
		} else {
			this.autoComplete();
		}
	},

	autoComplete: function() {
		$.when(
			$.loadMustache()
		).done(function() {

			$.ajax({
				url: wgServer + wgScript + '?action=ajax&rs=getLinkSuggest&format=json',
				data: {
					query: WikiaTray.$search.val()
				},
				success: function( data ) {
					if ( data.suggestions.length ) {
						var list = $('#WikiaTray .wiki-matches');

						WikiaTray.openTray();

						list.empty();
						$.each( data.suggestions, function(index, value) {
							console.log('logging value', value);
							list.append( $('#WikiaTray-wiki-match').mustache({
								name: value,
								href: mw.config.values.wgArticlePath.replace('$1', value)
							}) );
						});
					} else {
						WikiaTray.closeTray();
					}
				}
			});

		});
	},

	openTray: function() {
		// Don't open if already open
		if ( this.open ) {
			return;
		}

		$(window).scrollTop( 0 );

		$('body')
			.addClass('tray-open')
			.stop()
			.clearQueue()
			.animate( {
				'margin-right': 300,
				'margin-left': -300
			}, this.speed );

		$('#WikiaTray')
			.stop()
			.clearQueue()
			.animate( {
				'margin-right': 0
				},
			this.speed );

		this.open = true;
	},

	closeTray: function() {
		if ( !this.open ) {
			return;
		}

		$('body')
			.removeClass('tray-open')
			.stop()
			.clearQueue()
			.animate( {
				'margin-right': 0,
				'margin-left': 0
			}, this.speed );

		$('#WikiaTray')
			.stop()
			.clearQueue()
			.animate( {
				'margin-right': -300
			}, this.speed );

		this.open = false;
	}

};

WikiaTray.init();