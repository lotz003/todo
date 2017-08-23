<?php

$ajax_server_script = "todo_ajax.php";

?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">

	<title>Nat's To-Do App</title>

	<link rel="stylesheet" type="text/css" href="css/todo.css">

	<!-- Plugins -->
	<script src="plugins/jquery-1.12.0.js" type="text/javascript"></script>
</head>
<body>
	<div class="container">
		<div class="todoContainer">
			<h1 class="appTitle">Nat's To-Do App</h1>
			<div class="errorMessage"></div>
			<div class="inputItemRow">
				<input id="f_listItem" type="text" />
				<button id="addItemButton" class="submitButton">Add Item</button>
			</div>
			<div class="listContainer">

				<table id="todoListTable">
					<thead>
						<tr>
							<th style="display:none;">Item ID</th>
							<th>Item</th>
							<th>Date Added</th>
						</tr>
					</thead>
					<tbody></tbody>
				</table>

			</div>
		</div>
	</div>
</body>

<script src="js/ui_todo.js"></script>

<?php

// Move global PHP variables into global JS namespace
echo <<< EOS
	<script type="text/javascript">
		ajax_server_script = "{$GLOBALS['ajax_server_script']}";
	</script>
EOS;

?>

<script>
	$(function() {
		ui_todo.init();
	});
</script>

</html>
