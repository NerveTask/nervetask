<?php

	$categories = get_terms( 'nervetask_category', array( 'hide_empty' => 0 ) );
	$assigned_categories = wp_get_object_terms( get_the_ID(), 'nervetask_category', array( 'fields' => 'ids' ) );
?>

<form class="nervetask-update-category form-horizontal" role="form" method="post">

	<div>
		<strong><?php _e( 'Category', 'nervetask' ); ?></strong>:
		<strong><span class="task-category">
		<?php if ( ! empty( $assigned_categories ) ) { ?>
			<?php foreach ( $assigned_categories as $category ) { $category = get_term_by( 'id', $category, 'nervetask_category' );  ?>
				<?php if( current_user_can( 'edit_posts' ) ) { ?><a type="button" data-toggle="collapse" data-target="#task-meta-category-options" href="#"><?php } ?>
					<?php echo esc_html( $category->name ); ?>
				<?php if( current_user_can( 'edit_posts' ) ) { ?></a><?php } ?>
			<?php } ?>
			
		<?php } else { ?>
			<?php _e( 'None', 'nervetask' ); ?>
			<?php if( current_user_can( 'edit_posts' ) ) { ?><a type="button" data-toggle="collapse" data-target="#task-meta-category-options" href="#"><?php }?>
			<?php if( current_user_can( 'edit_posts' ) ) { ?></a><?php }?>
		<?php } ?>
		</span></strong>
	</div>

	<div class="collapse" id="task-meta-category-options">

	<?php if ( ! empty( $categories ) ) { ?>

		<div class="form-group">

			<div class="control-input">

				<select size="11" name="category[]" class="chosen-select">

				<?php foreach ( $categories as $category ) { ?>

					<?php
					if ( in_array($category->term_id, $assigned_categories ) ) {
						$selected = ' selected';
					} else {
						$selected = false;
					}
					?>
					<option value="<?php echo $category->term_id; ?>"<?php echo $selected; ?>><?php echo $category->name; ?></option>

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
		<p>There are no categories</p>
	<?php } ?>

	</div>

	<input type="hidden" name="action" value="nervetask">
	<input type="hidden" name="controller" value="nervetask_update_category">
	<input type="hidden" name="post_id" value="<?php the_ID(); ?>">
	<input type="hidden" name="security" value="<?php echo wp_create_nonce( 'nervetask_update_category' ); ?>">

</form>