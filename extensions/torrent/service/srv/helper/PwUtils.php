<?php

defined('WEKIT_VERSION') || exit('Forbidden');

class PwUtils
{
    public static function curl($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }

    public static function readableDataTransfer($byte)
    {
        if ($byte > 1024) {
            $kbytes = round($byte / 1024, 2);

            if ($kbytes > 1024) {
                $mbytes = round($kbytes / 1024, 2);

                if ($mbytes > 1024) {
                    $gbytes = round($mbytes / 1024, 2);

                    if ($gbytes > 1024) {
                        $result = round($gbytes / 1024, 2) . ' TB';
                    } else {
                        $result = $gbytes . ' GB';
                    }
                } else {
                    $result = $mbytes . ' MB';
                }
            } else {
                $result = $kbytes . ' KB';
            }
        } else {
            $result = $byte . ' B';
        }

        return $result;
    }
}
