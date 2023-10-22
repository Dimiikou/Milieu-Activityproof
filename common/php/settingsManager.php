<?php
class settingsManager
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

    function getPrefix($setting)
    {
        $sql = "SELECT * FROM prefixes";

        $fetch = $this->fetchAssoc($sql, $setting);
        return $fetch;
    }

    function getAllianceSetting($setting)
    {
        $sql = "SELECT * FROM alliance";

        $fetch = $this->fetchAssoc($sql, $setting);
        return $fetch;
    }

    function changeAlliance($setting)
    {
        $sql = "UPDATE `alliance` SET `allianceFaction`=?";
        $params = [$setting];

        $result = $this->getQuery($sql, $params);
        return $result;
    }

    function changeAllianceLeader($setting)
    {
        $sql = "UPDATE `alliance` SET `allianceLeader`=?";
        $params = [$setting];

        $result = $this->getQuery($sql, $params);
        return $result;
    }

    function changeAllianceFoundDate($setting)
    {
        $sql = "UPDATE `alliance` SET `foundDate`=?";
        $params = [$setting];

        $result = $this->getQuery($sql, $params);
        return $result;
    }

    function getMinimumMoney($rank)
    {
        $sql = "SELECT * FROM minimumactivities WHERE rank=?";
        $params = [$rank];

        $result = $this->fetchAssoc($sql, "money", $params);
        return $result;
    }

    function getMinimumDrugs($rank)
    {
        $sql = "SELECT * FROM minimumactivities WHERE rank=?";
        $params = [$rank];

        $result = $this->fetchAssoc($sql, "drugincome", $params);
        return $result;
    }

    function getMinimumRoleplay($rank)
    {
        $sql = "SELECT * FROM minimumactivities WHERE rank=?";
        $params = [$rank];

        $result = $this->fetchAssoc($sql, "roleplay", $params);
        return $result;
    }

    function changeMoneyActivity($rank, $value)
    {
        $sql = "UPDATE `minimumactivities` SET `money`=? WHERE rank=?";
        $params = [$value, $rank];

        $this->getQuery($sql, $params);
    }

    function changeDrugActivity($rank, $value)
    {
        $sql = "UPDATE `minimumactivities` SET `drugincome`=? WHERE rank=?";
        $params = [$value, $rank];

        $this->getQuery($sql, $params);
    }

    function changeRolePlayActivity($rank, $value)
    {
        $sql = "UPDATE `minimumactivities` SET `roleplay`=? WHERE rank=?";
        $params = [$value, $rank];

        $this->getQuery($sql, $params);
    }

    function changePhase($value)
    {
        $sql = "UPDATE `appliance` SET `applyPhase`=?";
        $params = [$value];

        $this->getQuery($sql, $params);
    }

    function getAlliance()
    {
        $sql = "SELECT * FROM alliance";

        $fetch = $this->fetchAssoc($sql, "allianceFaction");
        return $fetch;
    }

    function getAllianceLeader()
    {
        $sql = "SELECT * FROM alliance";

        $fetch = $this->fetchAssoc($sql, "allianceLeader");
        return $fetch;
    }

    function getAllianceFoundDate()
    {
        $sql = "SELECT * FROM alliance";

        $fetch = $this->fetchAssoc($sql, "foundDate");
        return $fetch;
    }

    function getAppliancePhase()
    {
        $sql = "SELECT * FROM appliance";

        $fetch = $this->fetchAssoc($sql, "applyPhase");
        return $fetch;
    }
}
