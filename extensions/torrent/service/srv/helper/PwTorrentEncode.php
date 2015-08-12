<?php

defined('WEKIT_VERSION') || exit('Forbidden');

// We'll protect the namespace of our code
// using a class
class PwTorrentEncode
{
    // Dictionary keys must be sorted. foreach tends to iterate over the order
    // the array was made, so we make a new one in sorted order. :)
    public function makeSorted($array)
    {
        // Shouldn't happen!
        if (empty($array)) {
            return $array;
        }

        $i = 0;
        foreach ($array as $key => $dummy) {
            $keys[$i++] = stripslashes($key);
        }

        sort($keys);
        for ($i = 0;isset($keys[$i]); $i++) {
            $return[addslashes($keys[$i])] = $array[addslashes($keys[$i])];
        }

        return $return;
    }

    // Encodes strings, integers and empty dictionaries.
    // $unstrip is set to true when decoding dictionary keys
    public function encodeEntry($entry, &$fd, $unstrip = false)
    {
        if (is_bool($entry)) {
            $fd .= 'de';
            return;
        }
        if (is_int($entry) || is_float($entry)) {
            $fd .= 'i' . $entry . 'e';
            return;
        }
        if ($unstrip) {
            $myentry = stripslashes($entry);
        } else {
            $myentry = $entry;
        }

        $length = strlen($myentry);
        $fd .= $length . ':' . $myentry;
    }

    // Encodes lists
    public function encodeList($array, &$fd)
    {
        $fd .= 'l';
        // The empty list is defined as array();
        if (empty($array)) {
            $fd .= 'e';
            return;
        }
        for ($i = 0;isset($array[$i]); $i++) {
            $this->decideEncode($array[$i], $fd);
        }

        $fd .= 'e';
    }

    // Passes lists and dictionaries accordingly, and has encodeEntry handle
    // the strings and integers.
    public function decideEncode($unknown, &$fd)
    {
        if (is_array($unknown)) {
            if (isset($unknown[0]) || empty($unknown)) {
                return $this->encodeList($unknown, $fd);
            } else {
                return $this->encodeDict($unknown, $fd);
            }

        }
        $this->encodeEntry($unknown, $fd);
    }

    // Encodes dictionaries
    public function encodeDict($array, &$fd)
    {
        $fd .= 'd';
        if (is_bool($array)) {
            $fd .= 'e';
            return;
        }
        // NEED TO SORT!
        $newarray = $this->makeSorted($array);
        foreach ($newarray as $left => $right) {
            $this->encodeEntry($left, $fd, true);
            $this->decideEncode($right, $fd);
        }
        $fd .= 'e';
    }
} // End of class declaration.
