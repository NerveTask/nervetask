<?php
	global $post;
	$due_date = get_post_meta( $post->ID, 'nervetask_due_date', TRUE );
?>

<form class="nervetask-update-due_date form-horizontal" role="form" method="post">
	<div>
		<?php if( current_user_can( 'edit_posts' ) ) { ?>
			<a type="button" data-toggle="collapse" data-target="#task-meta-due_date-options" href="#"><i class="glyphicon glyphicon-pencil"></i></a>
		<?php } ?>

		<?php if ( $due_date ) { ?>
			<strong>Due Date:
			<span class="task-due_date">
				<?php echo $due_date->format(get_option('date_format')); ?> at <?php echo $due_date->format(get_option('time_format')); ?>

			</span>
			</strong>
		<?php } else { ?>
			<span class="task-due_date">There is no assigned due date</span>
		<?php } ?>
	</div>

	<div class="collapse" id="task-meta-due_date-options">

	<?php if ( $due_date ) { ?>

		<div class="form-group">

			<div class="control-input">

				<input id="nervetask-update-task-due-date" name="nervetask_due_date" class="form-control" value="<?php echo $due_date->format('Y-m-d H:i:s'); ?>"/>

			</div>

		</div>

		<div class="form-group">
			<div class="control-input control-submit">
				<button type="submit" class="btn btn-block">Update</button>
			</div>
		</div>

	<?php } ?>

	</div>

	<input type="hidden" name="action" value="nervetask">
	<input type="hidden" name="controller" value="nervetask_update_due_date">
	<input type="hidden" name="post_id" value="<?php the_ID(); ?>">
	<input type="hidden" name="security" value="<?php echo wp_create_nonce( 'nervetask_update_due_date' ); ?>">

</form>