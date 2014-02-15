<form class="nervetask-new-task" method="post">

	<div>
		<label for="nervetask-new-task-title">
			Title
			<input type="text" id="nervetask-new-task-title" name="nervetask-new-task-title" placeholder="Title of this task">
		</label>
	</div>

	<div>
		<label for="nervetask-new-task-content">
			Description
			<textarea id="nervetask-new-task-content" name="nervetask-new-task-content"></textarea>
		</label>
	</div>

	<div>
		<label for="nervetask-new-task-category">
			Categories

		</label>
	</div>

	<div>
		<label for="nervetask-new-task-assignees">
			Assignees

		</label>
	</div>

	<div>
		<button type="submit">Create Task</button>
	</div>

	<input type="hidden" name="action" value="nervetask_new_task">
	<input type="hidden" name="security" value="<?php echo wp_create_nonce( 'nervetask_new_task' ); ?>">

</form>