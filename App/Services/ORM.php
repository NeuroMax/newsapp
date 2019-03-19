<?php

namespace App\Services;

use App\InvestedInterface;
use App\Service;

class ORM extends Service implements InvestedInterface
{
    /** @var $db \mysqli */
    private static $db;
    /** @var $stack \SplStack */
    private static $stack;

    static public function setup(\mysqli $dbi,$enc = "utf8")
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

    static function setEncoding($enc)
    {
        $result = self::$db->query("SET NAMES '$enc'");
        self::$stack->push("SET NAMES '$enc' [".($result ? "TRUE" : self::getError())."]");
        return $result ? true : false;
    }

    static function getError()
    {
        return self::$db->error." [".self::$db->errno."]";
    }

    private static function escape($string)
    {
        return mysqli_real_escape_string(self::$db,$string);
    }

    private static function RenderField($field)
    {
        $r = "";															//Строка для возвращения
        switch (gettype($field)) {											//Селектор типа передаваемого поля
            case "integer":	case "float":									//Тип int или float
            $r = $field;
            break;
            case "NULL": 	$r = "NULL";  break;							//Тип NULL
            case "boolean": $r = ($field) ? "true" : "false"; break;		//Тип boolean
            case "string":													//если тип строковой
                $p_function = "/^[a-zA-Z_]+\((.)*\)/";						//Шаблон на функцию
                preg_match($p_function, $field,$mathes);			//Поиск соврадений на функцию
                if (isset($mathes[0])){										//Совпадения есть, это функция
                    $p_value = "/\((.+)\)/";								//Шаблон для выборки значения функции
                    preg_match($p_value, $field,$mValue);			//Выборка значений
                    if (isset($mValue[0]) && !empty($mValue[0])){			//Если данные между скобок существуют и не пустые
                        $pv = trim($mValue[0],"()");				//Убираем скобки по концам
                        $pv = "'".self::escape($pv)."'";					//Экранируем то что в скобках
                        $r = preg_replace($p_value, "($pv)" , $field);	//Меняем под функцию
                    }
                    else $r = $field;										//Возвращаем функцию без параметров
                }
                else $r = "'".self::escape($field)."'";						//Если просто строка экранируем
                break;
            default: $r = "'".self::escape($field)."'";	break;				//По умолчанию экранируем
        }
        return $r;															//Возвращаем результат
    }

    public static function _getVars()
    {
        return array_filter(get_class_vars(get_called_class()), function($elem) {
            if (!is_object($elem)) return true;
        });
    }

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

    static function find(int $limit, int $offset)
    {
        $table = strtolower((new \ReflectionClass(get_called_class()))->getShortName());
        $query = "SELECT SQL_CALC_FOUND_ROWS * FROM `".$table."` ORDER BY `createdAt` DESC LIMIT $limit OFFSET $offset";
        $qRows = "SELECT FOUND_ROWS() AS count";
        $result = self::query($query, 'find');
        $rows = mysqli_fetch_array(self::$db->query($qRows))['count'];

        if ($result->num_rows > 0) {

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

    static function findID($id)
    {
        $table = strtolower((new \ReflectionClass(get_called_class()))->getShortName());
        if (is_numeric($id)) {												//Если число, то ищем по идентификатору
            $keys = self::_getVars();
            $query = "SELECT * FROM `".$table."` WHERE `".key($keys)."` = $id LIMIT 1";

            $result = self::query($query, 'find');					    //Отправляем запрос

            if ($result->num_rows == 1) {									//Если запрос вернул строку
                $row = $result->fetch_object();								//Строку запроса в класс
                $cName = get_called_class();	                            //Получем название класса
                $rClass = new $cName();										//Создаем экземпляр класса
                foreach ($row as $key => $value) $rClass->$key = $value;	//Переносим свойства класса
                return $rClass;												//Возвращаем класс
            } else return false;											//Если строка не найдена, то ложь
        } else return false;												//Если не число возвращаем ложь
    }

    public function Save()
    {									                                    //Сохраняем объект - UPDATE
        $table = strtolower((new \ReflectionClass(get_called_class()))->getShortName());
        $id = key(self::_getVars());						                //Получаем идентификатор
        if (!isset($this->$id) || empty($this->$id)) return $this->Create();	//Если пусто, добавляем
        $query = "UPDATE `".$table."` SET ";	                            //Формируем запрос
        $columns = self::_getVars();						                //Получем колонки таблицы
        $Update = array();									                //Массив обновления

        foreach ($columns as $k => $v) {					                //перебираем все колонки
            if ($id != $k)                                                  //Убираем идентификатор из запроса
                $Update[] = "`".$k."` = ".self::RenderField($this->$k);	    //Оборачиваем в оболочки
        }

        $query .= join(", ",$Update);					                //Дополняем запрос данными
        $query .= " WHERE `$id` = ".self::escape($this->$id)." LIMIT 1";    //Дополняем запрос уточнениями
        $result = self::query($query, 'find');
        return ($result) ? true : false;					                //Возвращаем ответ
    }

    public function Create()
    {									                                    //Добавляем объект - INSERT
        $table = strtolower((new \ReflectionClass(get_called_class()))->getShortName());
        $query = "INSERT INTO `".$table."` (";	                            //Подготавливаем запрос
        $columns = self::_getVars();                                        //Получем колонки
        $q_column = array();								                //Массив полей для вставки
        $q_data = array();									                //Массив данных для вставки

        foreach ($columns as $k => $v){						                //Пробегаемся по столбцам
            if (key($columns) == $k) continue;
            $q_column[] = "`".$k."`";					                    //Обертываем в кавычки
            if ($k == 'createdAt') $this->$k = (new \DateTime('now'))->format('Y-m-d H:i:s');
            $q_data[] 	= self::RenderField($this->$k);		                //Рендерим обертку для данных
        }

        $query .= join(", ",$q_column).") VALUES (";	                //Дополняем запрос столбцами
        $query .= join(", ",$q_data).")";				                //Дополняем запрос данными
        $result = self::query($query, 'insert');			            //Делаем запрос
        $insert_id = self::$db->insert_id;					                //Получаем идентификатор вставки

        return ($result) ? $insert_id : false;				                //Возвращаем ответ
    }

    public function Remove()
    {								                                        //Удаляем объект - DELETE
        $table = strtolower((new \ReflectionClass(get_called_class()))->getShortName());
        $id = key(self::_getVars());						                //Выбираем идентификатор
        if (!empty($this->$id)){							                //Если идентификатор не пустой
            $qDel = "DELETE FROM `".$table."` WHERE `$id` = ".$this->$id." LIMIT 1";
            $rDel = self::query($qDel, 'delete');			            //Запрос на удаление
            return $rDel ? true : false;						            //Возвращаем ответ
        } else return false;								                //Отрицательный ответ
    }

    private static function query ($query, $type)
    {
        $result = self::$db->query($query);
        if ($type == 'find')
            self::$stack->push($query." [".$result->num_rows."]");
        if ($type == 'insert')
            self::$stack->push($query." [".($result ? self::$db->insert_id : self::getError())."]");
        if ($type == 'delete')
            self::$stack->push($result." [".($result ? "TRUE" : self::getError())."]");
        return $result;
    }
}