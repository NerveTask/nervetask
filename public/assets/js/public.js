(function ($) {
	"use strict";

	$(function () {

		function nervetaskAjax(data, event, triggerType) {

			var xhr;

			if (xhr) {
				xhr.abort();
			}

			xhr = $.ajax({
				type: 'POST',
				url: nervetask.ajaxurl,
				dataType: 'json',
				data: $(data).serialize()
			})
			.done(function (response) {
				if (response) {
					try {
						resetForm($(data));
					} catch (err) {

					}
				}
			})
			.fail(function (response) {

			})
			.always(function (response) {

				if (triggerType) {
					$.event.trigger({
						type: triggerType,
						message: response,
						time: new Date()
					});
				}

			});

		event.preventDefault();

		}

		$('.nervetask-new-task').submit(function (e) {
			nervetaskAjax(this, e, 'nervetask-new-task');
		});

		$('.nervetask-update-content').submit(function (e) {
			nervetaskAjax(this, e, 'nervetask-update-content');
		});

		$('.nervetask-update-assignees').submit(function (e) {
			nervetaskAjax(this, e, 'nervetask-update-assignees');
		});

		$('.nervetask-update-status').submit(function (e) {
			nervetaskAjax(this, e, 'nervetask-update-status');
		});

		$('.nervetask-update-priority').submit(function (e) {
			nervetaskAjax(this, e, 'nervetask-update-priority');
		});

		$('.nervetask-update-category').submit(function (e) {
			nervetaskAjax(this, e, 'nervetask-update-category');
		});
		
		$('.nervetask-update-tags').submit(function (e) {
			nervetaskAjax(this, e, 'nervetask-update-tags');
		});
		
		$('.nervetask-update-due-date').submit(function (e) {
			nervetaskAjax(this, e, 'nervetask-update-due-date');
		});

		function nervetaskUpdateContentHandler(e) {

			var content = $('textarea[name="nervetask-new-task-content"]').val();
			$('.static-content').empty();
			$('#task-update-content').collapse('hide');
			$('.static-content').html(function () {
				var output = e.message.post.post_content;
				return output;
			});

			var data = {
				'action': 'nervetask_insert_comment',
				'post_id': e.message.post.ID,
				'nervetask-new-comment-content': 'Task content updated'
			};

		}

		function nervetaskComment(data) {

			var output;
			output = '<li>comment</li>';
			$('.comment-list').append(output);

		}

		function resetForm($form) {
			$form.find('input:text, input:password, input:file, select, textarea').val('');
			$form.find('input:radio, input:checkbox').removeAttr('checked').removeAttr('selected');
		}

		$(document).on('nervetask-update-content', nervetaskUpdateContentHandler);

	});

}(jQuery));