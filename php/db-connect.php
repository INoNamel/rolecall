<?php

class Dbconnect extends Dbconfig {

    private $connect;
    public $dataSet;
    protected $serverName;
    protected $userName;
    protected $passCode;
    protected $dbName;

    function __construct() {
        parent::__construct();
        $this->connect = NULL;
        $this->dataSet = NULL;

        $dbPara = new Dbconfig();
        $this->serverName = $dbPara->serverName;
        $this->userName = $dbPara->userName;
        $this->passCode = $dbPara->passCode;
        $this->dbName = $dbPara->dbName;
    }

    function close() {
        mysqli_close($this->connect);
        $this->connect = NULL;
        $this->dataSet = NULL;
        $this->protected = NULL;
        $this->protected = NULL;
        $this->protected = NULL;
        $this->protected = NULL;
    }

    function getConnect() {
        return $this->connect;
    }

    function connect() {
        mysqli_report(MYSQLI_REPORT_STRICT);

        try {
            $this->connect = new mysqli($this->serverName, $this->userName, $this->passCode, $this->dbName);
            $this->connect->set_charset('utf8');
        } catch (mysqli_sql_exception $ex) {
            header("HTTP/1.0 500 offline");
            include '500.php';
            exit();
        }
        return $this->connect;
    }

    function executeQuery($query) {
        $this->dataSet = $this->connect->query($query);
        return $this->dataSet;
    }

    function executeMultiQuery($query) {
        $this->dataSet = $this->connect->multi_query($query);
        return $this->dataSet;
    }

    function autocommit($autocommit) {
        $this->connect->autocommit($autocommit);
    }
    
    function beginTransaction() {
        $this->connect->begin_transaction();
    }

    function commit() {
        $this->connect->commit();
    }

    function rollback() {
        $this->connect->rollback();
    }

}
