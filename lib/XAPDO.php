<?php
/**
 * Created by PhpStorm.
 * User: Xin-an
 * Date: 2019/1/23
 * Time: 下午 06:52
 */

namespace XTool;

use PDO, PDOException;

class XAPDO
{
    protected static $DB = false;
    private $select, $set, $where, $table, $join, $parameter, $Extra;

    public function __construct($configs = array(), $debug = false)
    {
        $importIndex = array('driver', 'host', 'port', 'username', 'password', 'database');
        foreach ($importIndex as $value) {
            if (!isset($configs[$value])) {
                if ($debug) print $value . ' has no been set';
                return false;
            }
            if (!is_string($configs[$value])) {
                if ($debug) print $value . ' is not a string';
                return false;
            }
        }
        $driver = isset($configs['driver']) ? $configs['driver'] : false;
        $host = isset($configs['host']) ? $configs['host'] : false;
        $port = isset($configs['port']) ? $configs['port'] : false;
        $username = isset($configs['username']) ? $configs['username'] : false;
        $password = isset($configs['password']) ? $configs['password'] : false;
        $database = isset($configs['database']) ? $configs['database'] : false;


        $dsn = $driver . ':';
        $dsn .= 'host=' . $host . ';';
        $dsn .= 'port=' . $port . ';';
        $dsn .= 'dbname=' . $database . ';';
        $dsn .= isset($configs['character']) ? 'charset=' . $configs['character'] . ';' : '';
        try {
            $connection = new PDO($dsn, $username, $password/*, [PDO::ATTR_PERSISTENT => true]*/);
            $this::$DB = $connection;
        } catch (PDOException $e) {
            print 'PDO connect to database fail:' . "\n" . $e->getMessage();
            exit();
        }
    }

    public function select($columns = [])
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

    public function set($columns = [])
    {
        if (is_string($columns)) {
            $this->set = $columns;
            return $this;
        }
        if (is_array($columns)) {
            $tmp = [];
            foreach ($columns as $key => $value) {
                $tmp[] = $key . ' = :' . $key;
                $this->parameter[$key] = $value;
            }
            $this->set = implode(', ', $tmp);
            return $this;
        }
        return false;
    }

    public function table($table = '')
    {
        if (gettype($table) === 'string') $this->table = $table;
        else return false;
        return $this;
    }

    public function where($columns = [], $Glue = 'and')
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
                    $this->parameter[$key] = $value;
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

    public function Extra($SQL = '')
    {
        if (gettype($SQL) === 'string') {
            if (isset($this->Extra)) $this->Extra .= ' ';
            else $this->Extra = '';
            $this->Extra .= $SQL;
        }
        return $this;
    }

    public function execute($command = 'select', $Debug = false)
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
            if ($this::$DB->inTransaction() === true) $this::$DB->rollBack();
            $this::$DB->beginTransaction();
            $sth = $this::$DB->prepare($SQL, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
            if (isset($this->Parameter)) $this->bindParameter($sth, $this->Parameter, $Debug);
            $sth->execute();
            $this::$DB->commit();
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

    public function setParameter($Parameter = [])
    {
        if (!is_array($Parameter)) return false;
        foreach ($Parameter as $key => $value) {
            $this->parameter[$key] = $value;
        }
        return $this;
    }

    public function bindParameter($sth, $Parameter, $Debug = false)
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

    public function GenSetColumns($columns = [])
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

    public function GenSelectColumns($columns = [])
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

    public function GenWhereColumns($columns = [], $Glue = 'and')
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
                $this->parameter[$key] = $value;
            }
            return implode(' ' . $Glue . ' ', $tmp);
        } else {
            return false;
        }
    }
}