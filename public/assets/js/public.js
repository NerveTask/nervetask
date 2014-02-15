(function ( $ ) {
	"use strict";

	$(function () {

		var xhr;

		$('.nervetask-new-task').submit(function (e) {

			if ( xhr ) {
				xhr.abort();
			}

			var data = '';
			data = {
				action: 'nervetask_new_task',
				form_data: $(this)
			};

			xhr = $.ajax( {
				type: 'POST',
				action: 'nervetask_new_task',
				url: nervetask.ajaxurl,
				beforeSend: function() {
					console.log( data );
				},
				data: $(this).serialize()
			})
			.done(function( response ) {
				console.log( response );
				if ( response ) {
					try {

						console.log( 'try' );

					} catch ( err ) {
						console.log( err );
					}
				}
			})
			.fail(function( response ) {
				console.log( response );
			})
			.always(function( response ) {
				console.log( 'complete' );
			});

			e.preventDefault();

		});

	});

}(jQuery));