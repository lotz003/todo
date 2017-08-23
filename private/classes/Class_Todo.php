<?php
require __DIR__ . "/Class_AppBase.php";
class Todo extends AppBase {
	private    $dbh;						                   // Database handler
	private    $user				     = "natuser";	     // User
	private    $isFirstLogCall   = true;           // Inicates if log method has been called

  //----------------------------------------------------------------------------------------------------
	// __construct():
	//----------------------------------------------------------------------------------------------------
  public function __construct() {
    parent::__construct();
  }

  //----------------------------------------------------------------------------------------------------
	// log(): sends an error message to a file
	//----------------------------------------------------------------------------------------------------
	public function log($data) {
		global $config;

    // Determine file destination if this is the first log method-call
		if ( $this->isFirstLogCall == true ) {
			$logFilename = "./todo.log"; // Default

      // Use file name value stored in configuration file if it exists
			if ( isset($config['logFilename']) && strlen($config['logFilename']) > 0 ) {
				$logFilename = $config['logFilename'];
			}

			$this->logFilename = $logFilename;
		}

    // Build error message string
		$errorMessage = "TODO:USER={$this->user}::DT=" . date("Y_m_d_H_i_s") . $data;

    // Send the error message by appending to the file destination
		error_log("\n\n" . $errorMessage, 3, $this->logFilename);

		if ($this->isFirstLogCall == true) {
			$this->isFirstLogCall = false;

			// Change file mode to read and write for everyone
			chmod($this->logFilename, 0666);
		}
	}

  //----------------------------------------------------------------------------------------------------
	// setupDB(): sets up database handler
	//----------------------------------------------------------------------------------------------------
  public function setupDB($dbh) {
    $this->dbh = $dbh;
  }

  //----------------------------------------------------------------------------------------------------
	// todo_create(): creates new item to add to the to-do list
	//----------------------------------------------------------------------------------------------------
  public function todo_create() {
    $this->log(" todo_create(): start");

    $user = $this->user;
    $item = trim(filter_var($_POST['item'], FILTER_SANITIZE_STRING));

    return $this->todo_create1($user, $item);
  }

  //----------------------------------------------------------------------------------------------------
	// todo_create1():
	//----------------------------------------------------------------------------------------------------
  public function todo_create1($user, $item) {
    $this->log(" todo_create1(): start");

    $sql = <<< EOS
      INSERT INTO TD_Item (Name, UpdatedAt, UpdatedBy)
      VALUES (?, CURRENT_TIMESTAMP, ?);
EOS;
    $sqlVars = array($item, $user);
    $this->log(" todo_create1(): sql = $sql, vars = " . print_r($sqlVars, true));

    try {
      $sth = $this->dbh->prepare($sql);
      $sth->execute($sqlVars);

      $this->lastItemId = $this->dbh->lastInsertId();
      $this->log(" todo_create1(): last item ID inserted = {$this->lastItemId}");
    }
    catch(PDOException $e) {
      $this->log(" todo_create1(): EXCEPTION - " . print_r($e, true));
      return -1;
    }

    return 0;
  }

  //----------------------------------------------------------------------------------------------------
	// todo_retrieve():
	//----------------------------------------------------------------------------------------------------
  public function todo_retrieve() {
    $this->log(" todo_retrieve(): start");

    $dataArray = array();

    $sql = <<< EOS
      SELECT ItemID, Name, UpdatedAt
        FROM TD_Item
       WHERE IsActiveFlag = 1;
EOS;
    $sqlVars = array();
    $this->log(" todo_retrieve(): sql = $sql, vars = " . print_r($sqlVars, true));

    try {
      $sth = $this->dbh->prepare($sql);
      $sth->execute($sqlVars);

      // Return the result set of rows as an associative array
      $dataArray = $sth->fetchAll(PDO::FETCH_ASSOC);
    }
    catch(PDOException $e) {
      $this->log(" todo_create1(): EXCEPTION - " . print_r($e, true));
      return -1;
    }

    $this->itemArray = $dataArray;
    $this->log(" todo_retrieve(): item data = " . print_r($dataArray, true));

    return 0;
  }
}
?>
