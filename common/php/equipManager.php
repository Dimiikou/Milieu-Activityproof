<?php
class equipManager
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

    function getEquip($uuid)
    {
        $memberID = $this->getMemberID($uuid);
        $sql = "SELECT * FROM equip WHERE memberID=?";
        $params = [$memberID];

        $query = $this->getQuery($sql, $params);
        return $query;
    }

    function getEquipAmount($uuid)
    {
        $memberID = $this->getMemberID($uuid);
        $sql = "SELECT * FROM equip WHERE memberID=?";
        $params = [$memberID];

        $num = $this->numRows($sql, $params);
        return $num;
    }

    function numEquipCosts($uuid)
    {
        $memberID = $this->getMemberID($uuid);
        $sql = "SELECT * FROM equip WHERE memberID=?";
        $params = [$memberID];

        return $this->numRows($sql, $params);
    }

    function selectEquipCosts($uuid)
    {
        $memberID = $this->getMemberID($uuid);
        $sql = "SELECT * FROM equip WHERE memberID=?";
        $params = [$memberID];

        $query = $this->getQuery($sql, $params);
        return $query;
    }

    function getEquipCosts($uuid)
    {
        $memberID = $this->getMemberID($uuid);
        $sql = "SELECT 'price' FROM equip WHERE memberID=?";
        $params = [$memberID];

        if ($this->numRows($sql, $params) > 0) {
            $sql2 = "SELECT memberID, SUM(price) AS equipcosts FROM equip WHERE memberID=? GROUP BY memberID;";
            $params2 = [$memberID];

            $fetch = $this->fetchAssoc($sql2, "equipcosts", $params2);
            return $fetch;
        } else {
            return 0.0;
        }
    }

    function deleteEquip($id)
    {
        $sql = "DELETE FROM equip WHERE id=?";
        $params = [$id];

        $this->getQuery($sql, $params);
    }

    function insertEquip($uuid, $item, $price)
    {
        $memberID = $this->getMemberID($uuid);
        $sql = "INSERT INTO `equip` (`memberID`, `item`, `price`, `payed`) VALUES (?, ?, ?, 'false');";
        $params = [$memberID, $item, $price];

        $this->getQuery($sql, $params);
    }

    function clearEquip()
    {
        $sql = "SELECT * FROM equip";

        if ($this->numRows($sql) > 0) {
            $sql2 = "SELECT memberID, SUM(price) AS equipcosts FROM equip WHERE payed='false' GROUP BY memberID;";
            $query = $this->getQuery($sql2);
            $costs = array();
            while ($row = mysqli_fetch_assoc($query)) {
                $totalCosts = $row['equipcosts'];
                $memberID = $row['memberID'];

                $sql3 = "INSERT INTO `equipAdditional`(`memberID`, `price`, `payed`) VALUES (?,?,'false')";
                $params = [$memberID, $totalCosts];

                $this->getQuery($sql3, $params);
            }

            $sql4 = "DELETE FROM `equip`";
            $this->getQuery($sql4);
        } else {
            return 0.0;
        }
    }

    function getEquipAdditionalCosts()
    {
        $sql = "SELECT * FROM `equipAdditional`";

        $query = $this->getQuery($sql);
        return $query;
    }

    function deleteAdditionalCost($id)
    {
        $sql = "DELETE FROM `equipAdditional` WHERE `id`=?";
        $params = [$id];

        $this->getQuery($sql, $params);
    }

    function deleteAdditionalCostViaUUID($uuid)
    {
        $sql = "DELETE FROM `equipAdditional` WHERE `memberID`=?";
        $id = $this->getMemberID($uuid);
        $params = [$id];

        $this->getQuery($sql, $params);
    }

    function changeEquip($id)
    {
        $sql = "UPDATE `equip` SET `payed`='1' WHERE `id`=?";
        $params = [$id];

        $this->getQuery($sql, $params);
    }
}
