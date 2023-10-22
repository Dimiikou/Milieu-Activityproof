<?php
class loginManager
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

    function loginPost($uuid, $password)
    {
        if ($this->numMembers() < 1) {
            $this->createUser($uuid, $password, 0, 6);
            $_SESSION['logged'] = true;
            $_SESSION['uuid'] = $uuid;
            $_SESSION['rank'] = 6;
            header('Location: /index?login=true');
        } else if ($this->checkLogin($uuid, $password) > 0) {
            $_SESSION['logged'] = true;
            $_SESSION['uuid'] = $uuid;
            $_SESSION['rank'] = $this->getRank($uuid);
            header('Location: /index?login=true');
        } else {
            header('Location: /login?errorlogin=true');
        }
    }

    function getRank($uuid)
    {
        $sql = "SELECT * FROM member WHERE UUID= ?";
        $params = [$uuid];

        $fetch = $this->fetchAssoc($sql, "rank", $params);
        return $fetch;
    }

    function checkLogin($uuid, $password)
    {
        $sql = "SELECT * FROM member WHERE UUID=? AND password=?";
        $params = [$uuid, md5($password)];

        $num = $this->numRows($sql, $params);
        return $num;
    }

    function createUser($uuid, $password, $date, $rank)
    {
        $sql = "INSERT INTO member (`UUID`,`rank`,`password`,`inviteDate`) VALUES (?, ?, ?, ?)";
        $params = [$this->minify_uuid(htmlspecialchars($uuid)), $rank, md5($password), $date];

        $this->getQuery($sql, $params);
    }

    function minify_uuid($uuid)
    {
        if (is_string($uuid)) {
            $minified = str_replace('-', '', $uuid);
            if (strlen($minified) === 32) {
                return $minified;
            }
        }
        return false;
    }

    function removeUser($uuid)
    {
        $sql = "DELETE FROM `member` WHERE `UUID`= ?";
        $params = [$uuid];

        $this->getQuery($sql, $params);
    }

    function getFreeKevs($uuid)
    {
        $sql = "SELECT * FROM `member` WHERE `UUID`= ?";
        $params = [$uuid];
        $fetch = $this->fetchAssoc($sql, "freeKevs", $params);

        return $fetch;
    }

    function addFreeKev($uuid)
    {
        $freeKevs = ($this->getFreeKevs($uuid)) + 1;

        $sql = "UPDATE `member` SET `freeKevs`= ? WHERE `UUID`= ?";
        $params = [$freeKevs, $uuid];

        $this->getQuery($sql, $params);
    }

    function useFreeKev($uuid, $equipID)
    {
        $freeKevs = ($this->getFreeKevs($uuid)) - 1;

        $sql = "UPDATE `member` SET `freeKevs`= ? WHERE `UUID`= ?";
        $params = [$freeKevs, $uuid];
        $this->getQuery($sql, $params);

        $sql2 = "UPDATE `equip` SET `payed`='1' WHERE id= ?";
        $params2 = [$equipID];
        $this->getQuery($sql2, $params2);
    }

    function changeUserRank($uuid, $rank)
    {
        $sql = "UPDATE member SET rank=? WHERE UUID=?";
        $params = [$rank, $uuid];

        $this->getQuery($sql, $params);
    }

    function getMembers()
    {
        $sql = "SELECT UUID FROM member";

        $query = $this->getQuery($sql);
        return $query;
    }

    function getUUID($memberID)
    {
        $sql = "SELECT * FROM member WHERE memberID=?";
        $params = [$memberID];

        $fetch = $this->fetchAssoc($sql, "UUID", $params);
        return $fetch;
    }

    function getName($memberID)
    {
        $sql = "SELECT * FROM member WHERE memberID=?";
        $params = [$memberID];

        $fetch = $this->fetchAssoc($sql, "name", $params);
        return $fetch;
    }

    function setName($name, $uuid)
    {
        $sql = "UPDATE `member` SET `name`=? WHERE `UUID`=?";
        $params = [$name, $uuid];

        $this->getQuery($sql, $params);
    }

    function numMembers()
    {
        $sql = "SELECT * FROM member";

        $num = $this->numRows($sql);
        return $num;
    }

    function checkUserExist($uuid)
    {
        $sql = "SELECT * FROM member WHERE `UUID`=?";
        $params = [htmlspecialchars($uuid)];

        $num = $this->numRows($sql, $params);
        return $num;
    }

    function resetPassword($uuid, $password)
    {
        $sql = "UPDATE member SET `password`=? WHERE `UUID`=?";
        $params = [md5($password), $uuid];

        $this->getQuery($sql, $params);
    }

    function resetBanner()
    {
        $members = $this->getMembers();
        while ($row = mysqli_fetch_assoc($members)) {
            $sql = "UPDATE `member` SET `banner`='0' WHERE `UUID`=?";
            $params = [$row['UUID']];

            $this->getQuery($sql, $params);
        }
    }
}
