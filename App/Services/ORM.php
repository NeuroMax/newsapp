<?php

namespace App\Services;

use App\InvestedInterface;

/**
 * Class ORM
 * @package App\Services
 */
class ORM implements InvestedInterface
{
    /** @var $db \mysqli */
    private static $db;
    /** @var $stack \SplStack */
    private static $stack;

    /**
     * @param \mysqli $dbi
     * @param string $enc
     * @return bool
     * @throws \Exception
     */
    static public function setup(\mysqli $dbi, $enc = "utf8")
    {
        if (is_object($dbi)) {
            self::$db = $dbi;
            self::$stack = new \SplStack();
            return self::setEncoding($enc);
        }else{
            throw new \Exception("Параметр $dbi не является объектом mysqli", 1);
            return false;
        }
    }

    /**
     * @param $enc
     * @return bool
     */
    static function setEncoding($enc)
    {
        $result = self::$db->query("SET NAMES '$enc'");
        self::$stack->push("SET NAMES '$enc' [".($result ? "TRUE" : self::getError())."]");
        return $result ? true : false;
    }

    /**
     * @return string
     */
    static function getError()
    {
        return self::$db->error." [".self::$db->errno."]";
    }

    /**
     * @param $string
     * @return string
     */
    private static function escape($string)
    {
        return mysqli_real_escape_string(self::$db,$string);
    }

    /**
     * @param $field
     * @return string|string[]|null
     */
    private static function RenderField($field)
    {
        $r = "";
        switch (gettype($field))  {
            case "integer":	case "float":
            $r = $field;
            break;
            case "NULL": 	$r = "NULL";  break;
            case "boolean": $r = ($field) ? "true" : "false"; break;
            case "string":
                $p_function = "/^[a-zA-Z_]+\((.)*\)/";
                preg_match($p_function, $field,$mathes);
                if (isset($mathes[0])){
                    $p_value = "/\((.+)\)/";
                    preg_match($p_value, $field,$mValue);
                    if (isset($mValue[0]) && !empty($mValue[0])){
                        $pv = trim($mValue[0],"()");
                        $pv = "'".self::escape($pv)."'";
                        $r = preg_replace($p_value, "($pv)" , $field);
                    }
                    else $r = $field;
                }
                else $r = "'".self::escape($field)."'";
                break;
            default: $r = "'".self::escape($field)."'";	break;
        }
        return $r;
    }

    /**
     * @return array
     */
    public static function _getVars()
    {
        return array_filter(get_class_vars(get_called_class()), function($elem) {
            if (!is_object($elem)) return true;
        });
    }

    /**
     * @return array
     */
    public function _toArray()
    {
        $arr = [];
        $vars = array_filter(get_class_vars(get_called_class()), function($elem) {
            if (!is_object($elem)) return true;
        });


        foreach ($vars as $key => $value)
        {
            $arr[$key] = $this->$key;
        }

        return $arr;
    }

    /**
     * @param int $limit
     * @param int $offset
     * @param array $where
     * @return array|bool
     * @throws \ReflectionException
     */
    static function find(int $limit, int $offset, array $where = [])
    {
        $table = strtolower((new \ReflectionClass(get_called_class()))->getShortName());
        $query = "SELECT SQL_CALC_FOUND_ROWS * FROM `$table`";

        if (!empty($where)) {
            $query .= " WHERE ";
            $arr = [];
            foreach ($where as $key => $value)
            {
                $arr[] = " `$key` = '$value' ";
            }

            $query .= join(',', $arr);
        }

        $query .= " ORDER BY `createdAt` DESC LIMIT $limit OFFSET $offset";
        $qRows = "SELECT FOUND_ROWS() AS count";
        $result = self::query($query, 'find', $err);
        $rows = mysqli_fetch_array(self::$db->query($qRows))['count'];

        if ($result && $result->num_rows > 0) {

            $rClasses = [];
            $cName = get_called_class();
            while ($row = $result->fetch_object()){
                $rClass = new $cName();
                foreach ($row as $key => $value) {
                    $rClass->$key = $value ?? '';
                }

                $rClasses[] = $rClass;
            }

            return ['count' => $rows, 'data' => $rClasses];
        } else return false;
    }

    /**
     * @param $id
     * @return bool
     * @throws \ReflectionException
     */
    static function findID($id)
    {
        $table = strtolower((new \ReflectionClass(get_called_class()))->getShortName());
        if (is_numeric($id)) {
            $keys = self::_getVars();
            $query = "SELECT * FROM `".$table."` WHERE `".key($keys)."` = $id LIMIT 1";
            $result = self::query($query, 'find');

            if ($result->num_rows == 1) {
                $row = $result->fetch_object();
                $cName = get_called_class();
                $rClass = new $cName();
                foreach ($row as $key => $value) $rClass->$key = $value;
                return $rClass;
            } else return false;
        } else return false;
    }

    /**
     * @return bool|mixed
     * @throws \ReflectionException
     */
    public function Save()
    {
        $table = strtolower((new \ReflectionClass(get_called_class()))->getShortName());
        $id = key(self::_getVars());
        if (!isset($this->$id) || empty($this->$id)) return $this->Create();
        $query = "UPDATE `".$table."` SET ";
        $columns = self::_getVars();
        $Update = array();

        foreach ($columns as $k => $v) {
            if ($id != $k)
                $Update[] = "`".$k."` = ".self::RenderField($this->$k);
        }

        $query .= join(", ",$Update);
        $query .= " WHERE `$id` = ".self::escape($this->$id)." LIMIT 1";
        $result = self::query($query, 'find');
        return ($result) ? true : false;
    }

    /**
     * @param string $err
     * @return bool|mixed
     * @throws \ReflectionException
     */
    public function Create(&$err = null)
    {
        $table = strtolower((new \ReflectionClass(get_called_class()))->getShortName());
        $query = "INSERT INTO `".$table."` (";
        $columns = self::_getVars();
        $q_column = array();
        $q_data = array();

        foreach ($columns as $k => $v){
            if (key($columns) == $k) continue;
            $q_column[] = "`".$k."`";
            if ($k == 'createdAt') $this->$k = (new \DateTime('now'))->format('Y-m-d H:i:s');
            $q_data[] 	= self::RenderField($this->$k);
        }

        $query .= join(", ",$q_column).") VALUES (";
        $query .= join(", ",$q_data).")";
        $result = self::query($query, 'insert', $err);
        $insert_id = self::$db->insert_id;
        return ($result) ? $insert_id : false;
    }

    /**
     * @return bool
     * @throws \ReflectionException
     */
    public function Remove()
    {
        $table = strtolower((new \ReflectionClass(get_called_class()))->getShortName());
        $id = key(self::_getVars());
        if (!empty($this->$id)){
            $qDel = "DELETE FROM `".$table."` WHERE `$id` = ".$this->$id." LIMIT 1";
            $rDel = self::query($qDel, 'delete');
            return $rDel ? true : false;
        } else return false;
    }

    /**
     * @param $query
     * @param $type
     * @param string|null $err
     * @return bool|\mysqli_result
     */
    private static function query ($query, $type, &$err = null)
    {
        $result = self::$db->query($query);
        if (!$result) $err = self::getError();
        if ($type == 'find')
            self::$stack->push($query." [".$result->num_rows."]");
        if ($type == 'insert')
            self::$stack->push($query." [".($result ? self::$db->insert_id : $err)."]");
        if ($type == 'delete')
            self::$stack->push($result." [".($result ? "TRUE" : $err)."]");

        return $result;
    }
}