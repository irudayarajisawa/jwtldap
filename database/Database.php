<?php
#public $conf = parse_ini_file('/etc/projectsconf/jwtldap.ini');

class Database
{
    private $localhost; # = 'localhost';#$conf['mysql_host'];
    private $username; # = $conf['mysql_username'];
    private $password; # = utf8_decode(base64_decode($conf['mysql_password']));
    private $database; # = $conf['mysql_database'];

    private $mysqli = "";
    private $result = array();
    private $conn = false;

    //connect database using consturcted method
    public function __construct($localhost, $username, $password, $database)
    {
	    $this->localhost = $localhost;
	    $this->username = $username;
	    $this->password = $password;
	    $this->database = $database;

        if (!$this->conn) {
            $this->mysqli = new mysqli($this->localhost, $this->username, $this->password, $this->database);
            #$this->mysqli = new mysqli($localhost, $username, $password, $database);
            $this->conn = true;

            if ($this->mysqli->connect_error) {
                array_push($this->result, $this->mysqli_connection_error);
                return false;
            }
        } else {
            return true;
        }
    }

    // insert data
    public function insert($table, $params = array())
    {
        if ($this->tableExist($table)) {
            $table_column = implode(', ', array_keys($params));
            $table_value = implode("', '", array_values($params));

            $sql = "INSERT INTO $table ($table_column) VALUES ('$table_value')";
            if ($this->mysqli->query($sql)) {
                array_push($this->result, true);
                return true;
            } else {
                array_push($this->result, false);
                return false;
            }
        } else {
            return false;
        }
    }

    // get data
    public function select($table, $row = "*", $join = null, $where = null, $order = null, $limit = null)
    {
        if ($this->tableExist($table)) {
            $sql = "SELECT $row FROM $table";
            if ($join != null) {
                $sql .= " JOIN $join";
            }
            if ($where != null) {
                $sql .= " WHERE $where";
            }
            if ($order != null) {
                $sql .= " ORDER BY $order";
            }
            if ($limit != null) {
                $sql .= " LIMIT $limit";
            }
            $query = $this->mysqli->query($sql);
            if ($query) {
                $this->result = $query->fetch_all(MYSQLI_ASSOC);
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    // update data
    public function update($table, $params = array(), $where = null)
    {
        if ($this->tableExist($table)) {
            $arg = array();
            foreach ($params as $key => $val) {
                $arg[] = "$key = '{$val}'";
            }
            $sql = "UPDATE $table SET " . implode(', ', $arg);
            if($where != null){
                $sql .=" WHERE $where";
            }
            if ($this->mysqli->query($sql)) {
                array_push($this->result, true);
                return true;
            } else {
                array_push($this->result, false);
                return false;
            }
        } else {
            return false;
        }
    }
    // delete data
    public function delete($table, $where = null)
    {
        if ($this->tableExist($table)) {
            $sql = "DELETE FROM $table";
            if ($where != null) {
                $sql .= " WHERE $where";
            }
            if ($this->mysqli->query($sql)) {
                array_push($this->result, true);
                return true;
            } else {
                array_push($this->result, false);
                return false;
            }
        } else {
            return false;
        }
    }
    // table exist
    private function tableExist($table)
    {
        $sql = "SHOW TABLES FROM $this->database LIKE '{$table}'";
        $tableInDb = $this->mysqli->query($sql);
        if ($tableInDb) {
            if ($tableInDb->num_rows  == 1) {
                return true;
            } else {
                array_push($this->result, $table . " Does not Exist");
            }
        } else {
            return false;
        }
    }

    // get result
    public function getResult()
    {
        $val = $this->result;
        $this->result = array();
        return $val;
    }

    // close the connection
    public function __destruct()
    {
        if ($this->conn) {
            if ($this->mysqli->close()) {
                $this->conn = false;
                return true;
            }
        } else {
            return false;
        }
    }
}




