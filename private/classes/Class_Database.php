<?php
class Database {
  private $dbh = null;

  //----------------------------------------------------------------------------------------------------
  // __construct():
  //----------------------------------------------------------------------------------------------------
  public function __construct() {

  }

  //----------------------------------------------------------------------------------------------------
  // connectToDB(): connects to database
  //----------------------------------------------------------------------------------------------------
  public function connectToDB($host, $port, $dbName, $user, $pw) {
    $this->dbh = null;

    try {
      $this->dbh = new PDO("mysql:host=$host;port=$port;dbname=$dbName", $user, $pw);

      // Set the error reporting to throw exceptions
      $this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    catch(PDOException $e) {
      echo $e->getMessage();
    }

    return $this->dbh;
  }
}
?>
