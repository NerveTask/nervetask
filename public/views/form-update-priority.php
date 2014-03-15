<?php

	$priorities = get_terms( 'nervetask_priority', array( 'hide_empty' => 0, 'orderby' => 'slug' ) );
	$assigned_priorities = wp_get_object_terms( get_the_ID(), 'nervetask_priority', array( 'fields' => 'ids' ) );
?>

<form class="nervetask-update-priority form-horizontal" role="form" method="post">

	<div>
		<?php if( current_user_can( 'edit_posts' ) ) { ?>
			<a type="button" data-toggle="collapse" data-target="#task-meta-priority-options" href="#"><i class="glyphicon glyphicon-pencil"></i></a>
		<?php } ?>
		<?php if ( ! empty( $assigned_priorities ) ) { ?>
			<strong>Priority:
			<span class="task-priority">
			<?php foreach ( $assigned_priorities as $priority ) { $priority = get_term_by( 'id', $priority, 'nervetask_priority' );  ?>
				<a href="<?php echo home_url( '/?nervetask_priority='. $priority->slug ); ?>"><?php echo esc_html( $priority->name ); ?></a>
			<?php } ?>
			</span>
			</strong>
		<?php } else { ?>
			<span class="task-priority">There is no assigned priority</span>
		<?php } ?>
	</div>

	<div class="collapse" id="task-meta-priority-options">

	<?php if ( ! empty( $priorities ) ) { ?>

		<div class="form-group">

			<div class="control-input">

				<select size="11" name="priority[]" class="chosen-select">

				<?php foreach ( $priorities as $priority ) { ?>

					<?php
					if ( in_array($priority->term_id, $assigned_priorities ) ) {
						$selected = ' selected';
					} else {
						$selected = false;
					}
					?>
					<option value ="<?php echo $priority->term_id; ?>"<?php echo $selected; ?>><?php echo $priority->name; ?></option>

				<?php } ?>
				</select>

			</div>

		</div>

		<div class="form-group">
			<div class="control-input control-submit">
				<button type="submit" class="btn">Update</button>
			</div>
		</div>

	<?php } else { ?>
		<p>There are no priorities</p>
	<?php } ?>

	</div>

	<input type="hidden" name="action" value="nervetask">
	<input type="hidden" name="controller" value="nervetask_update_priority">
	<input type="hidden" name="post_id" value="<?php the_ID(); ?>">
	<input type="hidden" name="security" value="<?php echo wp_create_nonce( 'nervetask_update_priority' ); ?>">

</form>