<?php
class logoutManager
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

    function getMemberID($uuid)
    {
        $sql = "SELECT * FROM member WHERE UUID=?";
        $params = [$uuid];

        $fetch = $this->fetchAssoc($sql, "memberID", $params);
        return $fetch;
    }

    function numLogouts()
    {
        $sql = "SELECT * FROM logouts";

        $num = $this->numRows($sql);
        return $num;
    }

    function getLogouts()
    {
        if ($this->numLogouts() > 0) {
            $sql = "SELECT * FROM logouts";

            $query = $this->getQuery($sql);
            return $query;
        } else {
            return "0";
        }
    }

    function addLogout($uuid, $startDate, $endDate, $reason)
    {
        $memberID = $this->getMemberID($uuid);
        $sql = "INSERT INTO `logouts`(`reason`, `startDate`, `endDate`, `memberID`) VALUES (?,?,?,?)";
        $params = [$reason, $startDate, $endDate, $memberID];

        $this->getQuery($sql, $params);
    }

    function deleteLogout($logoutID)
    {
        $sql = "DELETE FROM `logouts` WHERE `logoutID`=?";
        $params = [$logoutID];

        $this->getQuery($sql, $params);
    }
}
