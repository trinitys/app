var WikiaTray = {

	// Properties
	open: false,
	selected: null,
	speed: 250,
	xhr: null,
	$search: $('#WikiaTrayHeader input[type="search"]'),
	$avatar: $('#WikiaTrayHeader .avatar'),

	// Methods
	init: function() {
		// Bindings
		this.$search.on( 'input', $.throttle( 100, $.proxy( this.searchChange, this ) ) );
		this.$avatar.on( 'click', $.proxy( this.avatarClick, this ) );
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
			if (WikiaTray.xhr && WikiaTray.xhr.readyState != 4) {
				WikiaTray.xhr.abort();
			}

			WikiaTray.xhr = $.ajax({
				url: wgServer + '/wikia.php?controller=WikiaSearch&method=getTray',
				data: {
					q: WikiaTray.$search.val()
				},
				success: function( data ) {
					var list;

					WikiaTray.openTray('search');

					// This wiki
					list = $('#WikiaTray .wiki-matches').empty();

					$.each( data['default'], function( index, value ) {
						list.append( $('#WikiaTray-wiki-match').mustache({
							name: value.title_en,
							href: value.url
						}) );
					});

					// All the Wikia
					list = $('#WikiaTray .wikia-matches').empty();

					$.each( data['cross-wiki'], function( index, value ) {
						list.append( $('#WikiaTray-wikia-match').mustache({
							name: value.title_en,
							href: value.url,
							wiki: value.wikititle
						}) );
					});

					// Photo slider
					list = $('#WikiaTray .carousel').empty();

					$.each( data['images'], function( index, value ) {
						var url = value.thumbnail.src;
						list.append( $('#WikiaTray-photos').mustache({
							url: url
						}) );
					});
					
					$('#WikiaTray .photo-carousel').carousel();

				}
			});

		});
	},

	avatarClick: function() {
		if (this.selected == 'user') {
			this.closeTray();
		} else {
			this.openTray( 'user' );
		}
	},

	selectTab: function( name ) {
		this.deselectTabs();
		$('#WikiaTrayHeader .' + name + '-tab').addClass('selected');
		$('#WikiaTray').find('.user, .search').hide();
		$('#WikiaTray .' + name).show();
		this.selected = name;
	},

	deselectTabs: function() {
		$('#WikiaTrayHeader .tray-header-tab').removeClass('selected');
		this.selected = null;
	},

	openTray: function( tab ) {
		// Don't open if already open
		if ( this.open ) {
			return;
		}

		// Scroll to the top of the page
		$(window).scrollTop( 0 );

		// Highlight tab
		this.selectTab( tab );

		// Animate
		$('body')
			.addClass('tray-open')
			.stop()
			.clearQueue()
			.animate( {
				'margin-right': 300,
				'margin-left': -300
			}, this.speed );

		$('#WikiaTray')
			.show()
			.stop()
			.clearQueue()
			.animate( {
				'margin-right': 0
				},
			this.speed );

		// Update property
		this.open = true;
	},

	closeTray: function() {
		if (WikiaTray.xhr && WikiaTray.xhr.readyState != 4) {
			WikiaTray.xhr.abort();
		}

		if ( !this.open ) {
			return;
		}
		
		this.deselectTabs();

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
			}, this.speed, function() {
				$(this).hide();
			} );

		this.open = false;
	}

};

WikiaTray.init();