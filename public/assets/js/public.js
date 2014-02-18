(function ( $ ) {
	"use strict";

	$(function () {

		$('.nervetask-new-task').submit(function (e) {
			nervetaskAjax(this, e, 'nervetask-new-task' );
		});

		$('.nervetask-update-content').submit(function (e) {
			nervetaskAjax(this, e, 'nervetask-update-content' );
		});

		$('.nervetask-update-assignees').submit(function (e) {
			nervetaskAjax(this, e, 'nervetask-update-assignees');
		});

		$('.nervetask-update-status').submit(function (e) {
			nervetaskAjax(this, e, 'nervetask-update-status' );
		});

		$('.nervetask-update-priority').submit(function (e) {
			nervetaskAjax(this, e, 'nervetask-update-priority' );
		});

		$('.nervetask-update-category').submit(function (e) {
			nervetaskAjax(this, e, 'nervetask-update-category' );
		});

		$(document).on('nervetask-update-content', nervetaskUpdateContentHandler );

		function nervetaskAjax(data, event, triggerType) {

			var xhr;

			if ( xhr ) {
				xhr.abort();
			}

			xhr = $.ajax( {
				type: 'POST',
				url: nervetask.ajaxurl,
				dataType: 'json',
				data: $(data).serialize()
			})
			.done(function( response ) {
				if ( response ) {
					try {


					} catch ( err ) {

					}
				}
			})
			.fail(function( response ) {

			})
			.always(function( response ) {
				console.log(response);

				if( triggerType ) {
					$.event.trigger({
						type: triggerType,
						message: response,
						time: new Date()
					});
				}

			});

			event.preventDefault();

		}

		function nervetaskUpdateContentHandler(e) {

			var content = $('textarea[name="nervetask-new-task-content"]').val();
			$('.static-content').empty();
			$('#task-update-content').collapse('hide');
			$('.static-content').html( function() {
				var output = content;
				return output;
			});

			var data = {
				'action': 'nervetask_insert_comment',
				'post_id': e.message.post.ID,
				'nervetask-new-comment-content': 'Task content updated'
			};

			nervetaskCommentAjax( data, e );

		}

		function nervetaskCommentAjax(data, event) {

			console.log(data);

			var xhr;

			if ( xhr ) {
				xhr.abort();
			}

			xhr = $.ajax( {
				type: 'POST',
				url: nervetask.ajaxurl,
				dataType: 'json',
				data: data
			})
			.done(function( response ) {
				if ( response ) {
					try {

						nervetaskComment( response );

					} catch ( err ) {

					}
				}
			})
			.fail(function( response ) {

			})
			.always(function( response ) {
				console.log(response);
			});

			event.preventDefault();

		}

		function nervetaskComment( data ) {
			console.log('comment');

			var output;
			ouput = '<li>comment</li>';
			$('.comment-list').append(output);

		}


	});

}(jQuery));