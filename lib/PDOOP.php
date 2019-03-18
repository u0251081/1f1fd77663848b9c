<?php
/**
 * Created by PhpStorm.
 * User: Andy
 * Date: 2018/10/1
 * Time: 上午 10:57
 */

// PDOOP 意思是 PDO Operator
class PDOOP
{
    private $dbh;
    private $select;
    private $set;
    private $where;
    private $table;
    private $join = '';
    private $Parameter;
    private $Extra;

    public function __construct($config = array())
    {
        // $this->dbh = db_connect();
        /*
         * Create PDO connection
         */
        // drivers: mysql, pgsql, sqlite, mssql, odbc, firebird
        if (!isset($config['driver'])) exit('PDO\'s driver not been sat');
        if (!isset($config['host'])) exit('PDO\'s host not been sat');
        if (!isset($config['database'])) exit('PDO\'s database not been sat');
        if (!isset($config['username'])) exit('PDO\'s username not been sat');
        if (!isset($config['password'])) exit('PDO\'s password not been sat');
        try {
            // $dsn = 'mysql:host=localhost;dbname=database;charset=utf8;'; # for PHP version > 5.3.6
            // $dns = 'mysql:host=localhost;dbname=database;'; // for PHP version <= 5.3.6
            // $PDOObj = new PDO($dns,$user,$password);        // for PHP version <= 5.3.6
            // $PDOOjb->exec('set names utf8');                // for PHP version <= 5.3.6
            $driver = $config['driver'];
            $host = $config['host'];
            $database = $config['database'];
            $username = $config['username'];
            $passname = $config['password'];
            $dsn =
                $driver . ':' .
                'host=' . $host . ';' .
                'dbname=' . $database . ';';
            $dbh = new PDO ($dsn, $username, $passname); // mysql 連線
            $dbh->exec("set names utf8");
            $dbh->exec("SET SESSION sql_mode=NO_ZERO_IN_DATE");
            $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $dbh->setAttribute(PDO::ATTR_PERSISTENT, true);
            $dbh->beginTransaction();
            $this->dbh = $dbh;
        } catch (PDOException $e) {
            print "Error: " . $e->getMessage() . "<br/>";
            die();

        }
    }

    public
    function select($columns = [])
    {
        if (!isset($this->select)) $this->select = '';
        else $this->select .= ', ';
        if (gettype($columns) === 'string') {
            $this->select .= $columns;
            return $this;
        }
        if (gettype($columns) === 'array') {
            $tmp = [];
            foreach ($columns as $key => $value) {
                if (gettype($key) === 'integer') $tmp[] = $value;
                if (gettype($key) === 'string') $tmp[] = $key;
            }
            $this->select .= implode(', ', $tmp);
            return $this;
        }
        unset($this->select);
        return false;
    }

    public
    function set($columns = [])
    {
        if (is_string($columns)) {
            $this->set = $columns;
            return $this;
        }
        if (is_array($columns)) {
            $tmp = [];
            foreach ($columns as $key => $value) {
                $tmp[] = $key . ' = :' . $key;
                $this->Parameter[$key] = $value;
            }
            $this->set = implode(', ', $tmp);
            return $this;
        }
        return false;
    }

    public
    function table($table = '')
    {
        if (gettype($table) === 'string') $this->table = $table;
        else return false;
        return $this;
    }

    public
    function join($table = '', $column1 = '', $column2 = '', $type = 'left')
    {
        if ($column1 === '') $SQL = '';
        if ($column2 === '') $SQL = $type . ' join ' . $table . ' using(' . $column1 . ')';
        else $SQL = $type . ' join ' . $table . ' on ' . $column1 . ' = ' . $column2;
        $this->join .= ' ' . $SQL . ' ';
        return $this;
    }

    public
    function where($columns = [], $Glue = 'and')
    {
        if (!isset($this->where)) $this->where = '';
        if (is_array($columns)) {
            $tmp = [];
            foreach ($columns as $key => $value) {
                if (is_string($key)) {
                    switch (gettype($value)) {
                        case 'string':
                        case 'integer':
                            $tmp[] = $key . ' = :' . $key;
                            break;
                        case 'NULL':
                            $tmp[] = $key . ' is :' . $key;
                            break;
                    }
                    $this->Parameter[$key] = $value;
                }
            }
            $this->where .= ' ' . implode(' ' . $Glue . ' ', $tmp);
            return $this;
        }
        if (is_string($columns)) {
            $this->where .= ' ' . $columns;
            return $this;
        }
        unset($this->where);
        return false;
    }

    public
    function Extra($SQL = '')
    {
        if (gettype($SQL) === 'string') {
            if (isset($this->Extra)) $this->Extra .= ' ';
            else $this->Extra = '';
            $this->Extra .= $SQL;
        }
        return $this;
    }

    public
    function execute($command = 'select', $Debug = false)
    {
        $SQL = '';
        switch ($command) {
            case 'SELECT':
            case 'select':
                if (isset($this->select)) $SQL .= 'select ' . $this->select;
                else return false;
                if (isset($this->table)) $SQL .= ' from ' . $this->table;
                else return false;
                $SQL .= $this->join;
                if (isset($this->where)) $SQL .= ' where ' . $this->where;
                break;
            case 'UPDATE':
            case 'update':
                if (isset($this->table)) $SQL .= 'update ' . $this->table;
                else return false;
                if (isset($this->set)) $SQL .= ' set ' . $this->set;
                else return false;
                if (isset($this->where)) $SQL .= ' where ' . $this->where;
                break;
            case 'insert':
            case 'INSERT':
                if (isset($this->table)) $SQL .= 'insert into ' . $this->table;
                else return false;
                if (isset($this->set)) $SQL .= ' set ' . $this->set;
                else return false;
                break;
            case 'DELETE':
            case 'delete':
                if (isset($this->table)) $SQL .= 'delete from ' . $this->table;
                else return false;
                if (isset($this->where)) $SQL .= ' where ' . $this->where;
                break;
            default:
                $SQL = $command;
        }
        if (isset($this->Extra)) $SQL .= ' ' . $this->Extra;
        if ($Debug === true) print $SQL . "\n";
        if ($Debug === true) print 'isset($Parameter): ' . (isset($this->Parameter) ? 'true' : 'false') . "\n";
        if ($Debug === true && isset($this->Parameter)) print '$Parameter: ' . print_r($this->Parameter, true) . "\n";
        try {
            if ($this->dbh->inTransaction() === true) $this->dbh->rollBack();
            $this->dbh->beginTransaction();
            $sth = $this->dbh->prepare($SQL, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
            if (isset($this->Parameter)) $this->bindParameter($sth, $this->Parameter, $Debug);
            $sth->execute();
            $this->dbh->commit();
            $advance = false;// advance debug
            foreach ($this as $Parameter => $value) {
                if (is_object($value) === false) {
                    if ($advance && $Debug === true) print '$Parameter: ' . $Parameter . "\n";
                    if ($advance && $Debug === true) print 'isset(' . $Parameter . '): ' . (isset($this->$Parameter) ? 'true' : 'false') . "\n";
                    unset($this->$Parameter);
                    if ($advance && $Debug === true) print '$Parameter: ' . $Parameter . "\n";
                    if ($advance && $Debug === true) print 'isset(' . $Parameter . '): ' . (isset($this->$Parameter) ? 'true' : 'false') . "\n";
                }
            }
            if ($Debug) $sth->debugDumpParams();
            try {
                return $sth->fetchall(PDO::FETCH_ASSOC);
            } catch (PDOException $exception) {
                return true;
            }
        } catch (PDOException $e) {
            print "Error of " . $command . ': ' . $e->getMessage() . "<br/>";
            die();
        }
    }

    public
    function setParameter($Parameter = [])
    {
        if (!is_array($Parameter)) return false;
        foreach ($Parameter as $key => $value) {
            $this->Parameter[$key] = $value;
        }
        return $this;
    }

    public
    function bindParameter($sth, $Parameter, $Debug = false)
    {
        if (method_exists($sth, 'bindParam')) {
            foreach ($Parameter as $key => $value) {
                if ($Debug === true) {
                    print '$key: ' . $key . "<br>\n";
                    print '$value: ' . $value . "<br>\n";
                    print 'is_int($key): ' . (is_int($key) ? 'true' : 'false') . "<br>\n";
                    print 'is_int($value): ' . (is_int($value) ? 'true' : 'false') . "<br>\n";
                }
                if (is_int($key)) return false;
                if (is_int($value)) $sth->bindParam(':' . $key, $Parameter[$key], PDO::PARAM_INT);
                else $sth->bindParam(':' . $key, $Parameter[$key], PDO::PARAM_STR);
            }
            return $this;
        } else {
            return false;
        }
    }

    public
    function GenSetColumns($columns = [])
    {
        if (gettype($columns) !== 'array') {
            return false;
        } else {
            foreach ($columns as $key => $value) {
                if (gettype($value) !== 'string') return false;
                else $columns[$key] = $key . ' = :' . $key;
            }
            return implode(', ', $columns);
        }
    }

    public
    function GenSelectColumns($columns = [])
    {
        if (gettype($columns) !== 'array') {
            return false;
        } else {
            foreach ($columns as $key => $value) {
                if (gettype($value) !== 'string') return false;
            }
            return implode(', ', $columns);
        }
    }

    public
    function GenWhereColumns($columns = [], $Glue = 'and')
    {
        if (is_array($columns)) {
            $tmp = [];
            foreach ($columns as $key => $value) {
                $type = gettype($value);
                switch ($type) {
                    case 'string':
                        $tmp[] = '(' . $key . ' = :' . $key . ')';
                        break;
                    case 'NULL':
                    case 'integer':
                        if ($value === null)
                            $tmp[] = '(' . $key . ' is :' . $key . ')';
                        else
                            $tmp[] = '(' . $key . ' = :' . $key . ')';
                        break;
                    default:
                        return false;
                }
                $this->Parameter[$key] = $value;
            }
            return implode(' ' . $Glue . ' ', $tmp);
        } else {
            return false;
        }
    }
}