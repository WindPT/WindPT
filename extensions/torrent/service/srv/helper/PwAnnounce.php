<?php

defined('WEKIT_VERSION') || exit('Forbidden');

Wind::import('EXT:torrent.service.srv.helper.PwBencode');

class PwAnnounce
{
    public static function showError($message = '')
    {
        $bencode = new PwBencode();
        exit('d' . $bencode->doEncodeString('failure reason:') . $bencode->doEncodeString($message) . 'e');
    }

    public static function getPeersByTorrentId($torrent_id = 0, $peer_id = '')
    {
        $peer_list = self::_getTorrentPeerDS()->getTorrentPeerByTorrent($torrent_id);
        if (is_array($peer_list)) {
            foreach ($peer_list as $key => $peer) {
                if ($peer_id == $peer['peer_id']) {
                    unset($peer_list[$key]);
                }
            }
        }
        return $peer_list;
    }

    public static function updatePeerCount($torrent, $peer_list)
    {
        $torrent['seeders']  = 0;
        $torrent['leechers'] = 0;

        if (is_array($torrent) && is_array($peer_list)) {
            foreach ($peer_list as $peer) {
                if ($peer['seeder'] == 'yes') {
                    $torrent['seeders']++;
                } else {
                    $torrent['leechers']++;
                }
            }
        }

        return $torrent;
    }

    public static function buildPeerList($torrent, $peer_list, $compact, $no_peer_id)
    {
        $bencode = new PwBencode();

        $string = 'd' . $bencode->doEncodeString('interval') . 'i840e' . $bencode->doEncodeString('min interval') . 'i30e' . $bencode->doEncodeString('complete') . 'i' . $torrent['seeders'] . 'e' . $bencode->doEncodeString('incomplete') . 'i' . $torrent['leechers'] . 'e' . $bencode->doEncodeString('peers');

        $peer_string = '';

        if (is_array($peer_list)) {

            $count = count($peer_list);

            foreach ($peer_list as $peer) {
                if ($compact) {
                    $peer_string .= str_pad(pack('Nn', ip2long($peer['ip']), $peer['port']), 6);
                } elseif ($no_peer_id == 1) {
                    $peer_string .= 'd' . $bencode->doEncodeString('ip') . $bencode->doEncodeString($peer['ip']) . $bencode->doEncodeString('port') . 'i' . $peer['port'] . 'e' . 'e';
                } else {
                    $peer_string .= 'd' . $bencode->doEncodeString('ip') . $bencode->doEncodeString($peer['ip']) . $bencode->doEncodeString('peer id') . $bencode->doEncodeString($peer['peer_id']) . $bencode->doEncodeString('port') . 'i' . $peer['port'] . 'e' . 'e';
                }
            }
        }

        if ($compact) {
            $string .= $bencode->doEncodeString($peer_string);
        } else {
            $string .= 'l' . $peer_string . 'e';
        }

        return $string . 'e';
    }

    private static function _getTorrentPeerDS()
    {
        return Wekit::load('EXT:torrent.service.PwTorrentPeer');
    }
}
