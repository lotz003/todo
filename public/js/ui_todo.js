// ui_todo.js

ui_todo = {
	logLevel: 4,

	//----------------------------------------------------------------------------------------------------
	// init(): sets up initial processes or functions once the app has been loaded
	//----------------------------------------------------------------------------------------------------
	init: function() {
		ui_todo.todo_setUpEvents();		// Set up events for to-do app
		ui_todo.todo_reset();					// Reset to-do app
		ui_todo.todo_retrieve();			// Retrieves list of current to-do list items
	},

	//----------------------------------------------------------------------------------------------------
	// todo_setUpEvents(): event handler for to-do app
	//----------------------------------------------------------------------------------------------------
	todo_setUpEvents: function() {
		ui_todo.log("todo_setUpEvents: start");

		// When user clicks Add Item button
		$(document).delegate("#addItemButton", "click", function() {
			var newItem = $("#f_listItem").val();

			// Validation

			var errorFlag = false;
			var errorMessage = "";

			$(".errorMessage").html(errorMessage);

			if ( newItem.length == 0 ) {
				errorMessage = "Please enter an item before adding to the list";
				focusInput = "#f_listItem";
				errorFlag = true;
			}

			if ( errorFlag == true ) {
				$(".errorMessage").html(errorMessage);
				$(focusInput).focus();

				return false;
			}

			// Add new item to the list
			ui_todo.todo_create(newItem);
		});

		// When user presses enter key after typing a value in the item input field
		$("#f_listItem").bind('keyup', function(e) {
			if (e.keyCode == 13) {
				$('#addItemButton').trigger('click');
			}
		});
	},

	//----------------------------------------------------------------------------------------------------
	// todo_reset(): resets to-app to it's original state
	//----------------------------------------------------------------------------------------------------
	todo_reset: function(item) {
		ui_todo.log("todo_reset(): start");

		$(".errorMessage").html("");
		$("#f_listItem").val("");

		$("#f_listItem").focus();
	},

	//----------------------------------------------------------------------------------------------------
	// todo_create(): creates new item to add to the to-do list
	//----------------------------------------------------------------------------------------------------
	todo_create: function(item) {
		ui_todo.log("todo_create(): start");

		ui_todo.newestItem = item;

		// Process request
		ui_todo.ajaxpost(
			ajax_server_script,
			{
				action: 	"todo_create",
				item:			item
			},
			ui_todo.todo_createCallback
		);
	},

	//----------------------------------------------------------------------------------------------------
	// todo_createCallback():
	//----------------------------------------------------------------------------------------------------
	todo_createCallback: function(jsonDataStr) {
		ui_todo.log("todo_createCallback(): start");

		if (ui_todo.logLevel > 4) {
			ui_todo.log("todo_createCallback(): data = " + jsonDataStr);
		}

		var jsonData = ui_todo.jsonStrToObj(jsonDataStr);

		// No data returned
		if (jsonData == null) {
			return;
		}
		if (ui_todo.logLevel > 3) {
			ui_todo.log("todo_createCallback(): data count = " + jsonDataStr.length);
		}

		// Success
		if (jsonData.responseStatus == "OK") {
			ui_todo.todo_reset();

			// Build item string to append to the html unordered list element
			var str = '';
			str += '<li class="todoItem">' + ui_todo.newestItem + '</li>';

			// Append item to list
			$("#todoList").append(str);
		}
	},

	//----------------------------------------------------------------------------------------------------
	// todo_retrieve(): retrieves list of current to-do list items
	//----------------------------------------------------------------------------------------------------
	todo_retrieve: function(item) {
		ui_todo.log("todo_retrieve(): start");

		// Process request
		ui_todo.ajaxget(
			ajax_server_script,
			{
				action: "todo_retrieve"
			},
			ui_todo.todo_retrieveCallback
		);
	},

	//----------------------------------------------------------------------------------------------------
	// todo_retrieveCallback():
	//----------------------------------------------------------------------------------------------------
	todo_retrieveCallback: function(jsonDataStr) {
		ui_todo.log("todo_retrieveCallback(): start");

		if (ui_todo.logLevel > 4) {
			ui_todo.log("todo_retrieveCallback(): data = " + jsonDataStr);
		}

		var jsonData = ui_todo.jsonStrToObj(jsonDataStr);

		// No data returned
		if (jsonData == null) {
			return;
		}
		if (ui_todo.logLevel > 3) {
			ui_todo.log("todo_retrieveCallback(): data count = " + jsonDataStr.length);
		}

		// Success
		if (jsonData.responseStatus == "OK") {
			var itemArray = jsonData.itemData;
			ui_todo.todo_retrieveProcessDBData(itemArray);
		}
	},

	//----------------------------------------------------------------------------------------------------
	// todo_retrieveProcessDBData(): processes the data and appends to the DOM
	//----------------------------------------------------------------------------------------------------
	todo_retrieveProcessDBData: function(dataArray) {
		ui_todo.log("todo_retrieveProcessDBData(): start");

		if ( typeof dataArray === 'undefined' ) {
			var dataArray = [];
		}

		$("#todoListTable tbody").remove();

		var dataArrayLength = dataArray.length;
		var str = '';
		var oneItem;

		str += '<tbody>';
		for (var i = 0; i < dataArrayLength; i++) {
			oneItem = dataArray[i];

			str += '<tr>';

			str += '<td style="display:none;">' + oneItem.ItemID + '</td>';
			str += '<td>' + oneItem.Name + '</td>';
			str += '<td>' + oneItem.UpdatedAt + '</td>';

			str += '</tr>'
		}

		str += '</tbody>';

		$("#todoListTable").append(str);
	},

	//----------------------------------------------------------------------------------------------------
	// ajaxget(): loads data from the server using a HTTP GET request
	//----------------------------------------------------------------------------------------------------
	ajaxget: function(ajaxserver, ajaxdata, ajaxcallback) {
		ui_todo.log("ajaxget(): start");

		if ( ui_todo.logLevel > 3 ) {
			ui_todo.log("ajaxget(): calling server with - " + JSON.stringify(ajaxdata) + ", callback - " + ajaxcallback);
		}

		var rc = $.get(ajaxserver, ajaxdata, ajaxcallback);

		return rc;
	},

	//----------------------------------------------------------------------------------------------------
	// ajaxpost(): loads data from the server using a HTTP POST request
	//----------------------------------------------------------------------------------------------------
	ajaxpost: function(ajaxserver, ajaxdata, ajaxcallback) {
		ui_todo.log("ajaxpost(): start");

		if ( ui_todo.logLevel > 3 ) {
			ui_todo.log("ajaxpost(): calling server with - " + JSON.stringify(ajaxdata) + ", callback - " + ajaxcallback);
		}

		var rc = $.post(ajaxserver, ajaxdata, ajaxcallback);

		return rc;
	},

	//----------------------------------------------------------------------------------------------------
	// jsonStrToObj(): converts JSON string from the server into a JavaScript object
	//----------------------------------------------------------------------------------------------------
	jsonStrToObj: function(jsonDataStr) {
		ui_todo.log("jsonStrToObj(): start");

		ui_todo.log("jsonStrToObj(): jsonDataStr = " + jsonDataStr);

		var jsonData;
		try {
			jsonData = JSON.parse(jsonDataStr);

			if ( typeof jsonData === 'null' ) {
				alert("Server Error E20: Please contact support");
				return null;
			}
		} catch ( exception ) {
			alert("Server Error E21: Please contact support: " + JSON.stringify(exception));
			return null;
		}
		return jsonData;
	},

	//----------------------------------------------------------------------------------------------------
	// log(): logs message to the console
	//----------------------------------------------------------------------------------------------------
	log: function(message) {
		console.log(message);
	}
}
