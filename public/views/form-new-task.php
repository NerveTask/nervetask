<form class="nervetask-new-task form-horizontal" role="form" method="post">

	<?php if( !isset( $atts['title'] ) || ( $atts['title'] == 'true' ) ) { ?>

	<div class="form-group">
		<label class="control-label col-sm-2" for="nervetask-new-task-title">Title</label>
		<div class="control-input col-sm-10">
			<input type="text" class="form-control" id="nervetask-new-task-title" name="nervetask-new-task-title" placeholder="New task title...">
		</div>
	</div>

	<?php } ?>

	<?php if( !isset( $atts['content'] ) || ( $atts['content'] == 'true' ) ) { ?>

	<div class="form-group">
		<label class="control-label col-sm-2" for="nervetask-new-task-content">Content</label>
		<div class="control-input col-sm-10">
			<textarea class="form-control" id="nervetask-new-task-content" name="nervetask-new-task-content"></textarea>
		</div>
	</div>

	<?php } ?>

	<div class="form-group">
		<div class="col-sm-offset-2 col-sm-10">
			<button type="submit" class="btn">Create Task</button>
		</div>
	</div>

	<input type="hidden" name="action" value="nervetask_new_task">
	<input type="hidden" name="security" value="<?php echo wp_create_nonce( 'nervetask_new_task' ); ?>">

</form>