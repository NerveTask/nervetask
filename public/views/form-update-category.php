<?php

	$categories = get_terms( 'nervetask_category', array( 'hide_empty' => 0, 'orderby' => 'slug' ) );
	$assigned_categories = wp_get_object_terms( get_the_ID(), 'nervetask_category', array( 'fields' => 'ids' ) );
?>

<form class="nervetask-update-category form-horizontal" role="form" method="post">

	<div>
		<?php if ( ! empty( $assigned_categories ) ) { ?>
			<?php if( current_user_can( 'edit_posts' ) ) { ?>
				<a type="button" data-toggle="collapse" data-target="#task-meta-category-options" href="#"><i class="glyphicon glyphicon-pencil"></i></a>
			<?php } ?>
			<strong>Category:
			<span class="task-category">
			<?php foreach ( $assigned_categories as $category ) { $category = get_term_by( 'id', $category, 'nervetask_category' );  ?>
				<a href="<?php echo home_url( '/?nervetask_category='. $category->slug ); ?>"><?php echo esc_html( $category->name ); ?></a>
			<?php } ?>
			</span>
			</strong>
		<?php } else { ?>
			<p>There is no assigned category</p>
		<?php } ?>
	</div>

	<div class="collapse" id="task-meta-category-options">

	<?php if ( ! empty( $categories ) ) { ?>

		<div class="form-group">

			<div class="control-input col-sm-offset-2 col-sm-10">

				<select multiple="multiple" size="11" name="category[]" class="">

				<?php foreach ( $categories as $category ) { ?>

					<?php
					if ( in_array($category->term_id, $assigned_categories ) ) {
						$selected = ' selected';
					} else {
						$selected = false;
					}
					?>
					<option value ="<?php echo $category->term_id; ?>"<?php echo $selected; ?>><?php echo $category->name; ?></option>

				<?php } ?>
				</select>

			</div>

		</div>

		<div class="form-group">
			<div class="col-sm-offset-2 col-sm-10">
				<button type="submit" class="btn">Update</button>
			</div>
		</div>

	<?php } else { ?>
		<p>There are no categories</p>
	<?php } ?>

	</div>

	<input type="hidden" name="action" value="nervetask_update_category">
	<input type="hidden" name="post_id" value="<?php the_ID(); ?>">
	<input type="hidden" name="security" value="<?php echo wp_create_nonce( 'nervetask_update_category' ); ?>">

</form>