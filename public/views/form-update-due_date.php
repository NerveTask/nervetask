<?php
	global $post;
	$due_date_object = get_post_meta( $post->ID, 'nervetask_due_date', true );
	$due_date_object_decoded = json_decode( $due_date_object );

	if( $due_date_object_decoded ) {
		if( $due_date_object_decoded->due_date ) {
			$due_date = new DateTime($due_date_object_decoded->due_date);
			$due_date = $due_date->format(get_option('date_format')) .' '. $due_date->format(get_option('time_format'));
		} else {
			$due_date = '';
		}
	} else {
		$due_date = '';
	}
?>

<form class="nervetask-update-due-date form-horizontal" role="form" method="post">
	<div>
		<strong><?php _e( 'Due date', 'nervetask' ); ?></strong>:
		<strong><span class="task-due-date">
		<?php if ( $due_date != '' ) { ?>
				<?php if( current_user_can( 'edit_posts' ) ) { ?><a type="button" data-toggle="collapse" data-target="#task-meta-due-date-options" href="#"><?php }?>
				<?php echo $due_date; ?>
				<?php if( current_user_can( 'edit_posts' ) ) { ?></a><?php }?>
		<?php } else { ?>
			<?php if( current_user_can( 'edit_posts' ) ) { ?><a type="button" data-toggle="collapse" data-target="#task-meta-due-date-options" href="#"><?php }?>
			<?php _e( 'None', 'nervetask' ); ?>
			<?php if( current_user_can( 'edit_posts' ) ) { ?></a><?php }?>
		<?php } ?>
		</span></strong>
	</div>

	<div class="collapse" id="task-meta-due-date-options">

		<div class="form-group">

			<div class="control-input">

				<input id="nervetask-update-task-due-date" name="nervetask_due_date" class="form-control" value=""/>

			</div>

		</div>

		<div class="form-group">
			<div class="control-input control-submit">
				<button type="submit" class="btn btn-block">Update</button>
			</div>
		</div>

	</div>

	<input type="hidden" name="action" value="nervetask">
	<input type="hidden" name="controller" value="nervetask_update_due_date">
	<input type="hidden" name="post_id" value="<?php the_ID(); ?>">
	<input type="hidden" name="security" value="<?php echo wp_create_nonce( 'nervetask_update_due_date' ); ?>">

</form>