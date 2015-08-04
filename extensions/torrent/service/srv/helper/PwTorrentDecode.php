<?php
/*
    Programming info

All functions output a small array, which we'll call $return for now.

$return[0] is the data expected of the function
$return[1] is the offset over the whole bencoded data of the next
           piece of data.

numberdecode returns [0] as the integer read, and [1]-1 points to the
symbol that was interprented as the end of the interger (either "e" or
":"). 
numberdecode is used for integer decodes both for i11e and 11:hello there
so it is tolerant of the ending symbol.

decodelist returns $return[0] as an integer indexed array like you would use in C
for all the entries. $return[1]-1 is the "e" that ends the list, so [1] is the next
useful byte.

decodeDict returns $return[0] as an array of text-indexed entries. For example,
$return[0]["announce"] = "http://www.whatever.com:6969/announce";
$return[1]-1 again points to the "e" that ends the dictionary.

decodeEntry returns [0] as an integer in the case $offset points to
i12345e or a string if $offset points to 11:hello there style strings.
It also calls decodeDict or decodeList if it encounters a d or an l.

Known bugs:
- The program doesn't pay attention to the string it's working on.
  A zero-sized or truncated data block will cause string offset errors
  before they get rejected by the decoder. This is worked around by
  suppressing errors.
*/

// Protect our namespace using a class
class PwTorrentDecode {
    function numberdecode($wholefile, $offset) {
        // Funky handling of negative numbers and zero
        $negative = false;
        if ($wholefile[$offset] == '-') {
            $negative = true;
            $offset++;
        }
        if ($wholefile[$offset] == '0') {
            $offset++;
            if ($negative)
                return array(false);
            if ($wholefile[$offset] == ':' || $wholefile[$offset] == 'e')
                return array(0, ++$offset);
            return array(false);
        }
        $ret[0] = 0;
        for(;;) {
            if ($wholefile[$offset] >= '0' && $wholefile[$offset] <= '9') {
                $ret[0] *= 10;
                //Added 2005.02.21 - VisiGod
           //Changing the type of variable from integer to double to prevent a numeric overflow   
                settype($ret[0],'double');
                //Added 2005.02.21 - VisiGod
                $ret[0] += ord($wholefile[$offset]) - ord('0');
                $offset++;
            }    else if ($wholefile[$offset] == 'e' || $wholefile[$offset] == ':') {
                // Tolerate : or e because this is a multiuse function
                $ret[1] = $offset+1;
                if ($negative) {
                    if ($ret[0] == 0)
                        return array(false);
                    $ret[0] = - $ret[0];
                }
                return $ret;
            } else return array(false);
        }
    }

    function decodeEntry($wholefile, $offset=0) {
        if ($wholefile[$offset] == 'd')
            return $this->decodeDict($wholefile, $offset);
        if ($wholefile[$offset] == 'l')
            return $this->decodelist($wholefile, $offset);
        if ($wholefile[$offset] == 'i')
            return $this->numberdecode($wholefile, ++$offset);
        // String value: decode number, then grab substring

        $info = $this->numberdecode($wholefile, $offset);
        if ($info[0] === false)
            return array(false);
        $ret[0] = substr($wholefile, $info[1], $info[0]);
        $ret[1] = $info[1]+strlen($ret[0]);
        return $ret;
    }

    function decodeList($wholefile, $offset) {
        if ($wholefile[$offset] != 'l')
            return array(false);
        $offset++;
        $ret = array();
        for ($i=0;;$i++) {
            if ($wholefile[$offset] == 'e')
                break;
            $value = $this->decodeEntry($wholefile, $offset);
            if ($value[0] === false)
                return array(false);
            $ret[$i] = $value[0];
            $offset = $value[1];
        }
        // The empty list is an empty array. Seems fine.
        return array(0=>$ret, 1=>++$offset);
    }

    // Tries to construct an array
    function decodeDict($wholefile, $offset=0) {
        if ($wholefile[$offset] == 'l')
            return $this->decodeList($wholefile, $offset);
        if ($wholefile[$offset] != 'd')
            return false;
        $ret=array();
        $offset++;
        for (;;) {    
            if ($wholefile[$offset] == 'e')    {
                $offset++;
                break;
            }
            $left = $this->decodeEntry($wholefile, $offset);
            if (!$left[0])
                return false;
            $offset = $left[1];
            if ($wholefile[$offset] == 'd') {
                // Recurse
                $value = $this->decodedict($wholefile, $offset);
                if (!$value[0])
                    return false;
                $ret[addslashes($left[0])] = $value[0];
                $offset= $value[1];
                continue;
            }
            if ($wholefile[$offset] == 'l') {
                $value = $this->decodeList($wholefile, $offset);
                if (!$value[0] && is_bool($value[0]))
                    return false;
                $ret[addslashes($left[0])] = $value[0];
                $offset = $value[1];
                continue;
            }
            $value = $this->decodeEntry($wholefile, $offset);
            if ($value[0] === false)
                return false;
            $ret[addslashes($left[0])] = $value[0];
            $offset = $value[1];
        }
        return array(0=>(empty($ret)?true:$ret), 1=>$offset);
    }
}