<?php

defined('WEKIT_VERSION') || exit('Forbidden');

class PwBencode
{
    public function doEncode($obj)
    {
        if (!is_array($obj) || !isset($obj["type"]) || !isset($obj["value"])) {
            return;
        }

        $c = $obj["value"];
        switch ($obj["type"]) {
            case "string":
                return $this->doEncodeString($c);
            case "integer":
                return $this->doEncodeInt($c);
            case "list":
                return $this->doEncodeList($c);
            case "dictionary":
                return $this->doEncodeDictionary($c);
            default:
                return;
        }
    }

    public function doEncodeString($s)
    {
        return strlen($s) . ":$s";
    }

    public function doEncodeInt($i)
    {
        return "i" . $i . "e";
    }

    public function doEncodeList($a)
    {
        $s = "l";
        foreach ($a as $e) {
            $s .= $this->doEncode($e);
        }
        $s .= "e";
        return $s;
    }

    public function doEncodeDictionary($d)
    {
        $s = "d";
        $keys = array_keys($d);
        sort($keys);
        foreach ($keys as $k) {
            $v = $d[$k];
            $s .= $this->doEncodeString($k);
            $s .= $this->doEncode($v);
        }
        $s .= "e";
        return $s;
    }

    public function doDecodeFile($file, $max_size = 290000)
    {
        $open = fopen($file, "rb");
        if (!$open) {
            return;
        }

        $string = fread($open, $max_size);
        fclose($open);
        return $this->doDecode($string);
    }

    public function doDecode($s)
    {
        if (preg_match('/^(\d+):/', $s, $m)) {
            $l = $m[1];
            $pl = strlen($l) + 1;
            $v = substr($s, $pl, $l);
            $ss = substr($s, 0, $pl + $l);
            if (strlen($v) != $l) {
                return;
            }

            return array('type' => "string", 'value' => $v, 'strlen' => strlen($ss), 'string' => $ss);
        }
        if (preg_match('/^i(-{0,1}\d+)e/', $s, $m)) {
            $v = $m[1];
            $ss = "i" . $v . "e";
            if ($v === "-0") {
                return;
            }

            if ($v[0] == "0" && strlen($v) != 1) {
                return;
            }

            return array('type' => "integer", 'value' => $v, 'strlen' => strlen($ss), 'string' => $ss);
        }
        switch ($s[0]) {
            case "l":
                return $this->doDecodeList($s);
            case "d":
                return $this->doDecodeDictionary($s);
            default:
                return;
        }
    }

    public function doDecodeList($s)
    {
        if ($s[0] != "l") {
            return;
        }

        $sl = strlen($s);
        $i = 1;
        $v = array();
        $ss = "l";
        for (;;) {
            if ($i >= $sl) {
                return;
            }

            if ($s[$i] == "e") {
                break;
            }

            $ret = $this->doDecode(substr($s, $i));
            if (!isset($ret) || !is_array($ret)) {
                return;
            }

            $v[] = $ret;
            $i += $ret["strlen"];
            $ss .= $ret["string"];
        }
        $ss .= "e";
        return array('type' => "list", 'value' => $v, 'strlen' => strlen($ss), 'string' => $ss);
    }

    public function doDecodeDictionary($s)
    {
        if ($s[0] != "d") {
            return;
        }

        $sl = strlen($s);
        $i = 1;
        $v = array();
        $ss = "d";
        for (;;) {
            if ($i >= $sl) {
                return;
            }

            if ($s[$i] == "e") {
                break;
            }

            $ret = $this->doDecode(substr($s, $i));
            if (!isset($ret) || !is_array($ret) || $ret["type"] != "string") {
                return;
            }

            $k = $ret["value"];
            $i += $ret["strlen"];
            $ss .= $ret["string"];
            if ($i >= $sl) {
                return;
            }

            $ret = $this->doDecode(substr($s, $i));
            if (!isset($ret) || !is_array($ret)) {
                return;
            }

            $v[$k] = $ret;
            $i += $ret["strlen"];
            $ss .= $ret["string"];
        }
        $ss .= "e";
        return array('type' => "dictionary", 'value' => $v, 'strlen' => strlen($ss), 'string' => $ss);
    }

    public function doDictionaryCheck($d, $s)
    {
        if ($d["type"] != "dictionary") {
            return;
        }

        $a = explode(":", $s);
        $dd = $d["value"];
        $ret = array();
        foreach ($a as $k) {
            unset($t);
            if (preg_match('/^(.*)\((.*)\)$/', $k, $m)) {
                $k = $m[1];
                $t = $m[2];
            }
            if (!isset($dd[$k])) {
                bark($lang_takeupload['std_dictionary_is_missing_key']);
            }

            if (isset($t)) {
                if ($dd[$k]["type"] != $t) {
                    bark($lang_takeupload['std_invalid_entry_in_dictionary']);
                }

                $ret[] = $dd[$k]["value"];
            } else {
                $ret[] = $dd[$k];
            }

        }
        return $ret;
    }

    public function doDictionaryGet($d, $k, $t)
    {
        if ($d["type"] != "dictionary") {
            return;
        }

        $dd = $d["value"];
        if (!isset($dd[$k])) {
            return;
        }

        $v = $dd[$k];
        if ($v["type"] != $t) {
            bark($lang_takeupload['std_invalid_dictionary_entry_type']);
        }

        return $v["value"];
    }
}
