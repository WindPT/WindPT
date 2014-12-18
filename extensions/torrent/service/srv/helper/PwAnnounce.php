<?php
Wind::import('EXT:torrent.service.PwTorrentAgentAllowedFamily');
Wind::import('EXT:torrent.service.srv.helper.PwBencode');
class PwAnnounce {
    public static function showError($message = "") {
        $bencode = new PwBencode();
        echo "d" . $bencode->doEncodeString("failure reason:") . $bencode->doEncodeString($message) . "e";
        exit(0);
    }
    public static function checkClient() {
        $agent = $_SERVER["HTTP_USER_AGENT"];
        if (!preg_match("/^uTorrent|^μTorrent|^transmission/i", $agent)){
            return false;
        }
        return true;
    }
    public static function checkClientRole($left) {
        if(!$left) {
            return "yes";
        }
        return "no";
    }
    public static function getPeersByTorrentId($torrent_id = 0, $peer_id = "") {
        $peer_list = self::_getTorrentPeerDS()->getTorrentPeerByTorrent($torrent_id);
        if(is_array($peer_list)) {
            foreach($peer_list as $key=>$peer) {
                if($peer_id == $peer['peer_id']) {
                    unset($peer_list[$key]);
                }
            }
        }
        return $peer_list;
    }
    public static function getSelf($peer_list, $peer_id) {
        if(is_array($peer_list)) {
            foreach($peer_list as $peer) {
                if($peer_id == $peer['peer_id']) {
                    return $peer;
                }
            }
        }
        return NULL;
    }
    public static function sendPeerList($peer_string) {
        header("Content-Type: text/plain; charset=utf-8");
        header("Pragma: no-cache");
        /*
        if (stristr($_SERVER["HTTP_ACCEPT_ENCODING"],"gzip") && extension_loaded('zlib') && ini_get("zlib.output_compression") == 0) {
            if (ini_get('output_handler')!='ob_gzhandler') {
                // only for non compact
                ob_start("ob_gzhandler");
            } else {
                ob_start();
            }
        } else {
            ob_start();
        }
        */
        echo $peer_string;
        exit();
    }
    public static function updatePeerCount($torrent, $peer_list) {
        if(!$torrent['leechers'] && !$torrent['seeders'] && is_array($peer_list)) {
            foreach($peer_list as $peer) {
                if($peer['seeder'] == 'yes') {
                    $torrent['seeders'] = $torrent['seeders'] + 1;
                } else {
                    $torrent['leechers'] = $torrent['leechers'] + 1;
                }
            }
        }
        return $torrent;
    }
    public static function buildWaitTime($torrent) {
        $bencode = new PwBencode();
        return "d" . $bencode->doEncodeString("interval") . "i840e" . $bencode->doEncodeString("min interval") . "i30e". $bencode->doEncodeString("complete") . "i" . $torrent['seeders'] ."e" . $bencode->doEncodeString("incomplete") . "i" . $torrent['leechers'] ."e";
    }
    public static function buildPeerList($peer_list, $compact, $no_peer_id, $string) {
        $bencode = new PwBencode();
        $string .= $bencode->doEncodeString("peers");
        $peer_string = "";
        if(is_array($peer_list)) {
            $count = count($peer_list);
            foreach($peer_list as $peer) {
                if($compact) {
                    $peer_string .= str_pad(pack("Nn", ip2long($peer['ip']), $peer['port']), 6);
                } elseif ($no_peer_id == 1) {
                    $peer_string .= "d" . $bencode->doEncodeString("ip") . $bencode->doEncodeString($peer["ip"]) . $bencode->doEncodeString("port") . "i" . $peer["port"] . "e" . "e";
                } else {
                    $peer_string .= "d" . $bencode->doEncodeString("ip") . $bencode->doEncodeString($peer["ip"]) . $bencode->doEncodeString("peer id") . $bencode->doEncodeString($peer["peer_id"]) . $bencode->doEncodeString("port") . "i" . $peer["port"] . "e" . "e";
                }
            }
        }
        if($compact) {
            $string .= $bencode->doEncodeString($peer_string);
        } else {
            $string .= "l" . $peer_string . "e";
        }
        return $string . "e";
    }
    private static function _getTorrentPeerDS() {
        return Wekit::load('EXT:torrent.service.PwTorrentPeer');
    }
}