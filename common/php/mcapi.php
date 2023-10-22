<?php
class mcapi {
    function getUUIDFromName($username) {
        if ($this->is_valid_username($username)) {
            $json_data = json_decode(file_get_contents('http://rettichlp.de:8888/unicacityaddon/v1/TOKEN/statistic/' . $username));
            return $uuid = $json_data->uuid;
        }
    }

    function getNameFromUUID($uuid) {
        $json_data = json_decode(file_get_contents('http://rettichlp.de:8888/unicacityaddon/v1/TOKEN/statistic/' . $uuid));
        return $name = $json_data->name;
    }

    function is_valid_username($string) {
        return is_string($string) and strlen($string) >= 2 and strlen($string) <= 16 and ctype_alnum(str_replace('_', '', $string));
    }

    function minify_uuid($uuid) {
        if (is_string($uuid)) {
            $minified = str_replace('-', '', $uuid);
            if (strlen($minified) === 32) {
                return $minified;
            }
        }
        return false;
    }

    function format_uuid($uuid) {
        $uuid = $this->minify_uuid($uuid);
        if (is_string($uuid)) {
            return substr($uuid, 0, 8) . '-' . substr($uuid, 8, 4) . '-' . substr($uuid, 12, 4) . '-' . substr($uuid, 16, 4) . '-' . substr($uuid, 20, 12);
        }
        return false;
    }
}
?>