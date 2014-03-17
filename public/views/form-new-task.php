<?php if( is_user_logged_in() ) { ?>

<form class="nervetask-new-task" role="form" method="post">

	<?php if( !isset( $atts['title'] ) || ( $atts['title'] == true ) ) { ?>

	<div class="form-group">
		<label class="control-label" for="nervetask-new-task-title">Title</label>
		<div class="control-input">
			<input type="text" class="form-control" id="nervetask-new-task-title" name="nervetask-new-task-title" placeholder="New task title...">
		</div>
	</div>

	<?php } ?>

	<?php if( !isset( $atts['content'] ) || ( $atts['content'] == true ) ) { ?>

	<div class="form-group">
		<label class="control-label" for="nervetask-new-task-content">Content</label>
		<div class="control-input">
			<textarea class="form-control" id="nervetask-new-task-content" name="nervetask-new-task-content"></textarea>
		</div>
	</div>

	<?php } ?>

	<?php if( isset( $atts['category']) && $atts['category'] == true ) { ?>

	<div class="form-group">
		<label class="control-label" for="nervetask-new-task-category">Categories</label>
		<div class="control-input">
			<select multiple="multiple" size="11" name="nervetask_category[]" class="form-control chosen-select">

			<?php
				$categories = get_terms( 'nervetask_category', array( 'hide_empty' => 0, 'orderby' => 'slug' ) );
				foreach ( $categories as $category ) { ?>
				<option value ="<?php echo $category->term_id; ?>"><?php echo $category->name; ?></option>
			<?php } ?>
			</select>
		</div>
	</div>

	<?php } ?>

	<?php if( isset( $atts['priority']) &&  $atts['priority'] == true ) { ?>

	<div class="form-group">
		<label class="control-label" for="nervetask-new-task-priority">Priority</label>
		<div class="control-input">
			<select size="11" name="nervetask_priority[]" class="form-control chosen-select">

			<?php
				$priorities = get_terms( 'nervetask_priority', array( 'hide_empty' => 0, 'orderby' => 'slug' ) );
				foreach ( $priorities as $priority ) { ?>
				<option value ="<?php echo $priority->term_id; ?>"><?php echo $priority->name; ?></option>
			<?php } ?>
			</select>
		</div>
	</div>

	<?php } ?>

	<?php if( isset( $atts['status']) &&  $atts['status'] == true ) { ?>

	<div class="form-group">
		<label class="control-label" for="nervetask-new-task-status">Status</label>
		<div class="control-input">
			<select size="11" name="nervetask_status[]" class="form-control chosen-select">

			<?php
				$statuses = get_terms( 'nervetask_status', array( 'hide_empty' => 0, 'orderby' => 'slug' ) );
				foreach ( $statuses as $status ) { ?>
				<option value ="<?php echo $status->term_id; ?>"><?php echo $status->name; ?></option>
			<?php } ?>
			</select>
		</div>
	</div>

	<?php } ?>

	<?php if( !isset( $atts['due_date'] ) || ( $atts['due_date'] == true ) ) { ?>

	<div class="form-group">
		<label class="control-label" for="nervetask-new-task-due-date">Due Date</label>
		<div class="control-input">
			<input type="text" class="form-control" id="nervetask-new-task-due-date-visible" name="nervetask-new-task-due-date-visible"></input>
			<input type="hidden" id="nervetask-new-task-due-date" name="nervetask_due_date"></input>
			<p id="nervetask-new-task-due-date-feedback" class="display-date"></p>
		</div>
	</div>

	<?php } ?>


	<div class="form-group">
		<div class="col-sm-offset-2">
			<button type="submit" class="btn">Create Task</button>
		</div>
	</div>

	<input type="hidden" name="action" value="nervetask">
	<input type="hidden" name="controller" value="nervetask_new_task">
	<input type="hidden" name="security" value="<?php echo wp_create_nonce( 'nervetask_new_task' ); ?>">

</form>

<?php } else { ?>

<div class="alert alert-warning">

	You must be logged in to create new tasks.
	
</div>

<?php } ?>