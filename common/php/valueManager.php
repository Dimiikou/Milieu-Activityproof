<?php
class valueManager
{
    function getConnection()
    {
        $connection = mysqli_connect(config::$MYSQL_HOST, config::$MYSQL_USER, config::$MYSQL_PASSWORD, config::$MYSQL_DATABASE)
            or die(mysqli_error($connection));

        return $connection;
    }

    function bindParams($stmt, $params)
    {
        $paramTypes = '';
        $boundParams = array();

        foreach ($params as &$param) { // Ampersand hinzugefügt, um Referenzen zu übergeben
            if (is_int($param)) {
                $paramTypes .= 'i'; // Integer
            } elseif (is_float($param)) {
                $paramTypes .= 'd'; // Double
            } elseif (is_string($param)) {
                $paramTypes .= 's'; // String
            } elseif (is_bool($param)) {
                $paramTypes .= 'i'; // Boolean (treated as integer)
                $param = (int) $param;
            } elseif (is_null($param)) {
                $paramTypes .= 's'; // Null (treated as string)
                $param = '';
            }

            $boundParams[] = &$param; // Ampersand hinzugefügt, um Referenz in Array zu speichern
        }

        array_unshift($boundParams, $stmt, $paramTypes);

        call_user_func_array('mysqli_stmt_bind_param', $boundParams); // Array direkt übergeben
    }


    private function getQuery($sql, $params = [])
    {
        $connection = $this->getConnection();
        $stmt = mysqli_prepare($connection, $sql);

        if (!empty($params)) {
            $this->bindParams($stmt, $params);
        }

        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        mysqli_stmt_close($stmt);

        return $result;
    }

    function numRows($sql, $params = [])
    {
        $result = $this->getQuery($sql, $params);
        $rows = mysqli_num_rows($result);
        return $rows;
    }

    function fetchAssoc($sql, $value, $params = [])
    {
        $result = $this->getQuery($sql, $params);
        $fetch = mysqli_fetch_assoc($result);

        return $fetch[$value];
    }

    function getFactionBank()
    {
        $sql = "SELECT * FROM factionBank ORDER BY factionBankID DESC LIMIT 10;";

        $query = $this->getQuery($sql);
        return $query;
    }


    function getCocaine()
    {
        $sql = "SELECT * FROM `drugBank` WHERE `drugType`='Pulver'";

        $query = $this->getQuery($sql);
        return $query;
    }

    function getWeed()
    {
        $sql = "SELECT * FROM `drugBank` WHERE `drugType`='Kräuter'";

        $query = $this->getQuery($sql);
        return $query;
    }

    function getMeth()
    {
        $sql = "SELECT * FROM `drugBank` WHERE `drugType`='Meth'";

        $query = $this->getQuery($sql);
        return $query;
    }

    function getLSD()
    {
        $sql = "SELECT * FROM `drugBank` WHERE `drugType`='LSD' AND `drugQuality`='0'";

        $query = $this->getQuery($sql);
        return $query;
    }
}
