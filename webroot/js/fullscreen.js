$(function() {

	$( '.icon-fullscreen' ).bind( 'click', function() {

		$.ajax({
				url: '/autotask/dashboards/toggleFullscreen/' + $( '.fullscreen' ).attr( 'rel' )
		}).done(function( response ) {
			
			if( 'disabled' == response ) {

				$( '.navbar' ).show();
				$( 'body' ).css( 'padding-top', '60px' );

			} else {

				$( '.navbar' ).hide();
				$( 'body' ).css( 'padding-top', '0' );

			}

		});

	});

});