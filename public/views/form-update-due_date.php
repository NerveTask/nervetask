<?php
	
	$due_date = get_post_meta( $post->ID(), 'nervetask_due_date' );

	// $priorities = get_terms( 'nervetask_due_date', array( 'hide_empty' => 0, 'orderby' => 'slug' ) );
	// $assigned_priorities = wp_get_object_terms( get_the_ID(), 'nervetask_due_date', array( 'fields' => 'ids' ) );
?>

<form class="nervetask-update-due_date form-horizontal" role="form" method="post">

	<div>
		<?php if( current_user_can( 'edit_posts' ) ) { ?>
			<a type="button" data-toggle="collapse" data-target="#task-meta-due_date-options" href="#"><i class="glyphicon glyphicon-pencil"></i></a>
		<?php } ?>

		<pre><?php print the_ID(); ?></pre>
		<pre><?php print get_the_ID(); ?></pre>
		<?php foreach ( $due_date as $meta) { ?> 
		<pre><?php print_r( $meta ); ?></pre>
		<?php } ?>
		<?php if ( ! $due_date ) { ?>
			<strong>Due Date:
			<span class="task-due_date">
			</span>
			</strong>
		<?php } else { ?>
			<span class="task-due_date">There is no assigned due_date</span>
		<?php } ?>
	</div>

	<div class="collapse" id="task-meta-due_date-options">

	<?php if ( ! $due_date ) { ?>

		<div class="form-group">

			<div class="control-input col-sm-offset-2 col-sm-10">

				<input size="11" name="due_date[]" class="" value="<?php echo $due_date; ?>"/>

			</div>

		</div>

		<div class="form-group">
			<div class="col-sm-offset-2 col-sm-10">
				<button type="submit" class="btn">Update</button>
			</div>
		</div>

	<?php } else { ?>
		<p>There is no due date</p>
	<?php } ?>

	</div>

	<input type="hidden" name="action" value="nervetask">
	<input type="hidden" name="controller" value="nervetask_update_due_date">
	<input type="hidden" name="post_id" value="<?php the_ID(); ?>">
	<input type="hidden" name="security" value="<?php echo wp_create_nonce( 'nervetask_update_get_post_meta' ); ?>">

</form>