<?php

class activityProofMySQL
{

    /**
     * mysql Section
     */
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

    function deleteActivity($id)
    {
        $sql = "DELETE FROM activities WHERE activityID=?";
        $params = [$id];

        $this->getQuery($sql, $params);
    }

    /**
     * Insert Activity
     */

    function insertMoneyActivity($uuid, $specialisedType, $date, $value, $screen)
    {
        $memberID = $this->getMemberID($uuid);
        $sql = "INSERT INTO `activities` (`memberID`, `activityType`, `specialisedType`, `value`, `screenshot`, `date`) VALUES (?, 'money', ?, ?, ?, ?);";
        $params = [$memberID, $specialisedType, str_replace("-", "", $value), $screen, $date];

        $this->getQuery($sql, $params);
    }

    function insertDrugActivity($uuid, $drugType, $drugQuality, $drugAmount, $date, $screen)
    {
        $memberID = $this->getMemberID($uuid);
        $sql = "INSERT INTO `activities` (`memberID`, `activityType`, `drugType`, `drugQuality`, `drugAmount`, `screenshot`, `date`) VALUES (?, 'drug', ?, ?, ?, ?, ?);";
        $params = [$memberID, $drugType, $drugQuality, str_replace("-", "", $drugAmount), $screen, $date];

        $this->getQuery($sql, $params);
    }

    function insertRoleplayActivity($uuid, $specialisedType, $date, $screen)
    {
        $memberID = $this->getMemberID($uuid);
        $sql = "INSERT INTO `activities` (`memberID`, `activityType`, `specialisedType`, `screenshot`, `date`) VALUES (?, 'roleplay',  ?, ?, ?);";
        $params = [$memberID, $specialisedType, $screen, $date];

        $this->getQuery($sql, $params);
    }

    /**
     * Num Activity Types
     */
    function numMonetaryIncome($uuid)
    {
        $memberID = $this->getMemberID($uuid);
        $sql = "SELECT * FROM activities WHERE memberID=? AND activityType='money'";
        $params = [$memberID];

        $num = $this->numRows($sql, $params);
        return $num;
    }

    function numDrugIncome($uuid)
    {
        $memberID = $this->getMemberID($uuid);
        $sql = "SELECT * FROM activities WHERE memberID=? AND activityType='drug'";
        $params = [$memberID];

        $num = $this->numRows($sql, $params);
        return $num;
    }

    function numRolePlays($uuid)
    {
        $memberID = $this->getMemberID($uuid);
        $sql = "SELECT * FROM activities WHERE memberID=? AND activityType='roleplay'";
        $params = [$memberID];

        $num = $this->numRows($sql, $params);
        return $num;
    }

    function numAllMonetaryIncome()
    {
        $sql = "SELECT * FROM activities WHERE activityType='money'";

        $num = $this->numRows($sql);
        return $num;
    }

    function numAllDrugIncome()
    {
        $sql = "SELECT * FROM activities WHERE activityType='drug'";

        $num = $this->numRows($sql);
        return $num;
    }

    function numAllRolePlays()
    {
        $sql = "SELECT * FROM activities WHERE activityType='roleplay'";

        $num = $this->numRows($sql);
        return $num;
    }

    /**
     * Select Activitys
     */

    function selectMonetaryIncomes($uuid)
    {
        $memberID = $this->getMemberID($uuid);
        $sql = "SELECT * FROM activities WHERE memberID=? AND activityType='money'";
        $params = [$memberID];

        $query = $this->getQuery($sql, $params);
        return $query;
    }

    function selectDrugIncomes($uuid)
    {
        $memberID = $this->getMemberID($uuid);
        $sql = "SELECT * FROM activities WHERE memberID=? AND activityType='drug'";
        $params = [$memberID];

        $query = $this->getQuery($sql, $params);
        return $query;
    }

    function selectRoleplay($uuid)
    {
        $memberID = $this->getMemberID($uuid);
        $sql = "SELECT * FROM activities WHERE memberID=? AND activityType='roleplay'";
        $params = [$memberID];

        $query = $this->getQuery($sql, $params);
        return $query;
    }

    /**
     * Select Special Information
     */

    function getActivityMoneyValue($id)
    {
        $sql = "SELECT * FROM activities WHERE activityId=?";
        $params = [$id];

        $fetch = $this->fetchAssoc($sql, "value", $params);
        return $fetch;
    }

    function getDrugDealValue($id)
    {
        $sql = "SELECT * FROM activities WHERE activityId=?";
        $params = [$id];

        $fetch = $this->fetchAssoc($sql, "value", $params);
        return $fetch;
    }

    function getActivityType($id)
    {
        $sql = "SELECT * FROM activities WHERE activityId=?";
        $params = [$id];

        $fetch = $this->fetchAssoc($sql, "activityType", $params);
        return $fetch;
    }

    function getDrugAmount($id)
    {
        $sql = "SELECT * FROM activities WHERE activityId=?";
        $params = [$id];

        $fetch = $this->fetchAssoc($sql, "drugAmount", $params);
        return $fetch;
    }

    function clearActivityProof()
    {
        $sql = "DELETE FROM `activities`";

        $this->getQuery($sql);
    }


    function getMemberID($uuid)
    {
        $sql = "SELECT * FROM member WHERE UUID=?";
        $params = [$uuid];

        $fetch = $this->fetchAssoc($sql, "memberID", $params);
        return $fetch;
    }

    function getMoneyRevenue($uuid)
    {
        $memberID = $this->getMemberID($uuid);
        $sql = "SELECT 'value' FROM activities WHERE memberID=? AND activityType='money'";
        $params = [$memberID];

        if ($this->numRows($sql, $params) > 0) {
            $sql2 = "SELECT SUM(value) AS moneysum FROM activities WHERE memberID=? AND activityType='money'";
            $params2 = [$memberID];

            $fetch = $this->fetchAssoc($sql2, "moneysum", $params2);
            return $fetch;
        } else {
            return 0.0;
        }
    }

    function getDrugRevenue($uuid)
    {
        $memberID = $this->getMemberID($uuid);
        $sql = "SELECT drugAmount FROM activities WHERE memberID=? AND activityType='drug'";
        $params = [$memberID];

        if ($this->numRows($sql, $params) > 0) {
            $sql2 = "SELECT SUM(drugAmount) AS drugsum FROM activities WHERE memberID=? AND activityType='drug'";
            $params2 = [$memberID];

            $fetch = $this->fetchAssoc($sql2, "drugsum", $params2);
            return $fetch;
        }
        return 0.0;
    }

    function getRoleplayActivity($uuid)
    {
        $memberID = $this->getMemberID($uuid);
        $sql = "SELECT memberID FROM activities WHERE memberID=? AND activityType='roleplay'";
        $params = [$memberID];

        if ($this->numRows($sql, $params) > 0) {
            $sql2 = "SELECT COUNT(memberID) AS rpsum FROM activities WHERE memberID=? AND activityType='roleplay'";
            $params2 = [$memberID];

            $fetch = $this->fetchAssoc($sql2, "rpsum", $params2);
            return $fetch;
        }
        return 0.0;
    }

    /**
     * O M G MEMBER DER WOCHE??!??!?!?!?!!!!
     */

    function getMoneyMVP()
    {
        $sql = "SELECT * FROM activities WHERE activityType='money'";
        if ($this->numRows($sql) > 0) {
            $sql2 = "SELECT memberID, SUM(value) AS total_apples FROM activities WHERE activityType='money' GROUP BY memberID ORDER BY total_apples DESC LIMIT 1";

            $fetch = $this->fetchAssoc($sql2, "memberID");
            return $fetch;
        }
        return "Noch niemand..";
    }

    function getDrugMVP()
    {
        $sql = "SELECT * FROM activities WHERE activityType='drug'";
        if ($this->numRows($sql) > 0) {
            $sql2 = "SELECT memberID, SUM(drugAmount) AS totalDrugs FROM activities WHERE activityType='drug' GROUP BY memberID ORDER BY totalDrugs DESC LIMIT 1";

            $fetch = $this->fetchAssoc($sql2, "memberID");
            return $fetch;
        }
        return "Noch niemand..";
    }

    function getRoleplayMVP()
    {
        $sql = "SELECT * FROM activities WHERE activityType='roleplay'";
        if ($this->numRows($sql) > 0) {
            $sql2 = "SELECT memberID, COUNT(memberID) AS totalRoleplay FROM activities WHERE activityType='roleplay' GROUP BY memberID ORDER BY totalRoleplay DESC LIMIT 1";

            $fetch = $this->fetchAssoc($sql2, "memberID");
            return $fetch;
        }
        return "Noch niemand..";
    }


    function getMoneyActivities()
    {
        $sql = "SELECT * FROM activities WHERE activityType='money'";
        if ($this->numRows($sql) > 0) {
            $sql2 = "SELECT memberID, SUM(value) AS moneysum FROM activities WHERE activityType = 'money' GROUP BY memberID;";

            $fetch = $this->getQuery($sql2);
            return $fetch;
        } else {
            return 0;
        }
    }

    function getDrugActivities()
    {
        $sql = "SELECT * FROM activities WHERE activityType='drug'";
        if ($this->numRows($sql) > 0) {
            $sql2 = "SELECT memberID, SUM(drugAmount) AS drugAmount FROM activities WHERE activityType = 'drug' GROUP BY memberID;";

            $fetch = $this->getQuery($sql2);
            return $fetch;
        }
        return 0;
    }

    function getRoleplayActivities()
    {
        $sql = "SELECT * FROM activities WHERE activityType='roleplay'";
        if ($this->numRows($sql) > 0) {
            $sql2 = "SELECT memberID, COUNT(memberID) AS roleplayAmount FROM activities WHERE activityType = 'roleplay' GROUP BY memberID;";

            $fetch = $this->getQuery($sql2);
            return $fetch;
        }
        return 0;
    }

    /**
     * other shit
     */

    function getActivityProofs($rank)
    {
        $sql = "SELECT * FROM member WHERE rank=?";
        $params = [$rank];

        $query = $this->getQuery($sql, $params);
        return $query;
    }

    function numActivityProofs($rank)
    {
        $sql = "SELECT * FROM member WHERE rank=?";
        $params = [$rank];

        $query = $this->numRows($sql, $params);
        return $query;
    }

    function insertDrugsUsed($uuid, $drugType, $drugPurity, $drugAmount)
    {
        $memberID = $this->getMemberID($uuid);
        $sql = "INSERT INTO `drugusage`(`memberID`, `drugType`, `drugPurity`, `drugAmount`) VALUES (?,?,?,?)";
        $params = [$memberID, $drugType, $drugPurity, $drugAmount];

        $this->getQuery($sql, $params);
    }

    function geteffectiveDrugRevenue()
    {
        $sql = "SELECT memberID, SUM(drugAmount) AS drugRevenue FROM drugusage GROUP BY memberID";

        $query = $this->getQuery($sql);
        return $query;
    }

    function numEffectiveDrugRevenue()
    {
        $sql = "SELECT * FROM drugusage";

        $query = $this->numRows($sql);
        return $query;
    }

    function getBanner($uuid)
    {
        $sql = "SELECT * FROM member WHERE UUID=?";
        $params = [$uuid];

        $query = $this->fetchAssoc($sql, "banner", $params);
        return $query;
    }

    function addBanner($uuid)
    {
        $bannerAmount = $this->getBanner($uuid) + 1;
        $sql = "UPDATE `member` SET `banner`=? WHERE `UUID`=?";
        $params = [$bannerAmount, $uuid];

        $this->getQuery($sql, $params);
    }

}
