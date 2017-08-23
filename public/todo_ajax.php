<?php
require "../private/config.php";
require __DIR__ . "/../private/classes/Class_Database.php";
require __DIR__ . "/../private/classes/Class_Todo.php";

// Determine the action to be routed by using the $_GET request variable if it exists.
// If it does exist, strip any whitespace or tags from the string
$action = "";
if ( isset($_GET) && isset($_GET['action']) ) {
  $action = trim(filter_var($_GET['action'], FILTER_SANITIZE_STRING));
}
if ( empty($action) ) {
	if ( isset($_POST) && isset($_POST["action"]) ) {
		$action = trim(filter_var($_POST["action"], FILTER_SANITIZE_STRING));
	}
}

// Connect to DB
$dbObj = new Database();
$dbh = $dbObj->connectToDB(
  $config['hostname'],
  $config['port'],
  $config['dbname'],
  $config['username'],
  $config['password']
);
if ( $dbh == null ) {
	$failArray = array( "responseId" => 100, "responseStatus" => "FAIL", "errorCode" => 10, "errorStr" => 'Error 115: cannot connect to database');
	echo json_encode($failArray);
	die;
}

// Setup todo object
$todo = new Todo();
$todo->log(" AJAX CALL: ACTION = $action");
$todo->setupDB($dbh);

// Route the request
if ( strlen($action) > 0 ) {
  switch ( $action ) {
    case 'todo_create':
      $rc = $todo->todo_create();
      if ( $rc == 0 ) {
        $rcArray = array(
          "responseId" => 100,
          "responseStatus" => "OK"
        );
      }
      else {
        $errorCode = 1000 + $rc;
        $rcArray = array(
          "responseId" => 100,
          "responseStatus" => "FAIL",
          "errorCode" => $errorCode,
          "errorStr" => "Error 200: failed creating new item"
        );
      }
      echo json_encode($rcArray);
      break;
      case 'todo_retrieve':
        $rc = $todo->todo_retrieve();
        if ( $rc == 0 ) {
          $rcArray = array(
            "responseId" => 100,
            "responseStatus" => "OK",
            "itemData" => $todo->itemArray
          );
        }
        else {
          $errorCode = 1000 + $rc;
          $rcArray = array(
            "responseId" => 100,
            "responseStatus" => "FAIL",
            "errorCode" => $errorCode,
            "errorStr" => "Error 205: failed retrieving to-do list of items"
          );
        }
        echo json_encode($rcArray);
        break;
    default: // The action is not recognized
      $failArray = array( "responseId" => 100, "responseStatus" => "FAIL", "errorCode" => 122, "errorStr" => "Error 150: the action type is not recognized" );
      echo json_encode($failArray); // Return the JSON representation of the failure array
      break;
  } // End switch
} // End if ( strlen($action) > 0 )

die;

?>
