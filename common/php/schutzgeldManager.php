<?php
class protecionMoneyManager
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

    function getProtectionMoney()
    {
        $sql = "SELECT * FROM protectionMoney";
        $sql2 = "UPDATE `protectionMoney` SET `payed`=? WHERE `protectionID`=?";

        $tz = new DateTimeZone('Europe/Berlin');
        $dt = new DateTime('now', $tz);
        $day = $dt->format('w'); // 0 Sonntag 6 Samstag

        $protectionMoney = $this->getQuery($sql);
        while ($row = mysqli_fetch_assoc($protectionMoney)) {
            $lastPayed = $row['lastPayed'];

            $letztesZahlungsdatum = date('Y-m-d', strtotime($lastPayed));

            $params = [$this->zahlungsstatus($letztesZahlungsdatum), $row['protectionID']];

            $this->getQuery($sql2, $params);
        }

        $query = $this->getQuery($sql);
        return $query;
    }


    function zahlungsstatus($letztesZahlungsdatum)
    {
        $aktuellesDatum = date('Y-m-d');

        $diff = abs(strtotime($aktuellesDatum) - strtotime($letztesZahlungsdatum));
        $tageSeitLetzterZahlung = floor($diff / (60 * 60 * 24));

        if ($tageSeitLetzterZahlung == 7) {
            return "ausstehend";
        } elseif ($tageSeitLetzterZahlung < 7) {
            return "bezahlt";
        } else {
            return "nicht gezahlt";
        }
    }

    function getProtectionMoneyExist($uuid)
    {
        $sql = "SELECT * FROM `protectionMoney` WHERE `UUID`=?";
        $params = [$uuid];

        $num = $this->numRows($sql, $params);
        return $num;
    }

    function refreshProtectionMoney($uuid, $name, $date, $member, $screen)
    {
        $sql = "UPDATE `protectionMoney` SET `lastPayed`=?,`lastMember`=?,`lastScreen`=? WHERE `UUID`=?";
        $params = [$date, $member, $screen, $uuid];

        $this->getQuery($sql, $params);

        $this->updateName($uuid, $name);
    }

    function updateName($uuid, $name)
    {
        $sql = "UPDATE `protectionMoney` SET `name`=? WHERE `UUID`=?";
        $params = [$name, $uuid];

        $this->getQuery($sql, $params);
    }

    function getProtectionMoneyPrice($uuid)
    {
        $sql = "SELECT * FROM `protectionMoney` WHERE `UUID`=?";
        $params = [$uuid];

        $fetch = $this->fetchAssoc($sql, "price", $params);
        return $fetch;
    }

    function createSchutzgeld($uuid, $name, $price, $date, $member, $screen)
    {
        $sql = "INSERT INTO `protectionMoney`(`UUID`, `name`, `price`, `lastPayed`, `lastMember`, `lastScreen`) VALUES (?,?,?,?,?,?)";
        $params = [$uuid, $name, $price, $date, $member, $screen];

        $this->getQuery($sql, $params);
    }

    function getProtectionMoneyAmount()
    {
        $sql = "SELECT * FROM protectionMoney";

        $num = $this->numRows($sql);
        return $num;
    }
}
