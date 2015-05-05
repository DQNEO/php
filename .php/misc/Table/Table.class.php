<?
class Table
{
    public static function getCol($rows, $colname)
    {
        if(empty($rows) || empty($colname) || !is_array($rows)){
            return false;
        }

        if( !is_array($rows[0]) && !is_object($rows[0])){
            return false;
        }

        if (is_array($rows[0])) {
            return self::_getColFromArrays($rows, $colname);
        } elseif (is_object($rows[0])) {
            return self::_getColFromObjects($rows, $colname);
        }

    }

    private static function _getColFromArrays($rows, $colname)
    {
        if (!isset($rows[0][$colname])) {
            return false;
        }

        /*
          ２次元配列を１次元配列に変換(map)する。
        */
        return  array_map(
            create_function(
                '$row',
                'if(isset($row["'.$colname.'"])) return $row["'.$colname.'"];'
                ),
            $rows
            );
    }
    
    private static function _getColFromObjects($rows, $colname)
    {
        if (!isset($rows[0]->$colname)) {
            return false;
        }
        
        /*
          ２次元配列を１次元配列に変換(map)する。
        */
        return  array_map(
            create_function(
                '$obj',
                'if(isset($obj->'.$colname.')) return $obj->'.$colname.';'
                ),
            $rows
            );

    }

    public static function max($rows, $colname)
    {
        $list = self::getCol($rows, $colname);
        if (empty($list)) {
            return false;
        }

        return max($list);
    }
    
}