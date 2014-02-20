<form class="nervetask-update-content form-horizontal" role="form" method="post">

	<a type="button" data-toggle="collapse" data-target="#task-update-content" href="#"><i class="glyphicon glyphicon-pencil"></i> Edit</a>

	<div class="static-content">
		<?php the_content(); ?>
	</div>

	<div class="edit-content collapse" id="task-update-content">

		<?php
		$post_id = get_queried_object();
		$post = get_post( $post_id, OBJECT, 'edit' );

		$content = $post->post_content;
		$editor_id = 'editpost';

		wp_editor( $content, $editor_id, array( 'textarea_name' => 'nervetask-new-task-content', 'tinymce' => false ) );
		?>

		<p><button type="submit" class="btn">Update</button></p>

	</div>

	<input type="hidden" name="action" value="nervetask">
	<input type="hidden" name="controller" value="nervetask_update_content">
	<input type="hidden" name="post_id" value="<?php the_ID(); ?>">
	<input type="hidden" name="security" value="<?php echo wp_create_nonce( 'nervetask_update_content' ); ?>">

</form>