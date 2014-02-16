<form class="nervetask-new-task form-horizontal" role="form" method="post">

	<?php if( !isset( $atts['title'] ) || ( $atts['title'] == 'true' ) ) { ?>

	<div class="control-group">
		<label class="control-label" for="nervetask-new-task-title">Title</label>
		<div class="controls">
			<input type="text" id="nervetask-new-task-title" name="nervetask-new-task-title" placeholder="Title of this task">
		</div>
	</div>

	<?php } ?>

	<?php if( !isset( $atts['content'] ) || ( $atts['title'] == 'true' ) ) { ?>

	<div class="control-group">
		<label class="control-label" for="nervetask-new-task-content">Content</label>
		<div class="controls">
			<textarea id="nervetask-new-task-content" name="nervetask-new-task-content"></textarea>
		</div>
	</div>

	<?php } ?>

	<div class="control-group">
		<div class="controls">
			<button type="submit" class="btn">Create Task</button>
		</div>
	</div>

	<input type="hidden" name="action" value="nervetask_new_task">
	<input type="hidden" name="security" value="<?php echo wp_create_nonce( 'nervetask_new_task' ); ?>">

</form>