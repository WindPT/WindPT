<?php
defined('WEKIT_VERSION') || exit('Forbidden');
Wind::import('SRV:forum.srv.post.do.PwPostDoBase');
Wind::import('EXT:torrent.service.srv.helper.PwBencode');
Wind::import('EXT:torrent.service.dm.PwTorrentDm');
Wind::import('EXT:torrent.service.dm.PwTorrentFileDm');
Wind::import('EXT:torrent.service.dao.PwTorrentUserDao');
class PwPostDoTorrent extends PwPostDoBase {
    protected $user;
    protected $tid;
    protected $fid;
    protected $wikilink;
    protected $dictionary;
    protected $infohash;
    protected $filename;
    protected $filesavename;
    protected $filelist;
    protected $totalength;
    protected $type;
    protected $action;
    public function __construct(PwPost $pwpost, $tid = null, $wikilink = "") {
        $this->user = $pwpost->user;
        $this->special = $pwpost->special;
        $this->tid = $tid ? intval($tid) : null;
        $this->fid = intval($pwpost->forum->fid);
        $this->wikilink = $wikilink;
        $this->action = $this->tid ? 'modify' : 'add';
    }
    public function createHtmlBeforeContent() {
        $torrentUser = $this->_getTorrentUserDS()->getTorrentUserByUid($this->user->uid);
        $this->passkey = $torrentUser['passkey'];
        
        $uid = $this->user->uid;
        if(!$this->passkey) {
            Wind::import('EXT:torrent.service.dm.PwTorrentUserDm');
            $dm = new PwTorrentUserDm();
            $dm->setUid($uid);
            $dm->setPassKey($this->makePassKey());
            $this->_getTorrentUserDS()->addTorrentUser($dm);
            $this->getUser($uid);
        }
        PwHook::template('displayPostTorrentHtml', 'EXT:torrent.template.post_injector_before_torrent', true, $this);
    }
    public function dataProcessing($postDm) {
        $postDm->setSpecial('torrent');
        return $postDm;
    }
    public function addThread($tid) {
        return $this->addTorrentt($tid);
    }
    public function check($postDm) {
        $bencode = new PwBencode();
        if(isset($_FILES['torrent'])) {
            $file = pathinfo($_FILES['torrent']['name']);
            if($file['extension'] != "torrent") {
                return new PwError('只允许上传后缀名为.torrent的文件！');
            }
            if($_FILES['torrent']['size'] < 1) {
                return new PwError("上传文件大小有问题，为空！");
            }
            $filename = $_FILES['torrent']['name'];
            $dictionary = $bencode->doDecodeFile($_FILES['torrent']['tmp_name']);
            if(!isset($dictionary)) {
                return new PwError("种子读取错误，请检查种子是否正确！");
            }
            list($announce, $info) = $bencode->doDictionaryCheck($dictionary, "announce(string):info");
            list($dictionaryName, $pieceLength, $pieces) = $bencode->doDictionaryCheck($info, "name(string):piece length(integer):pieces(string)");
            if(strlen($pieces) % 20 != 0) {
                return new PwError("无效的文件块，请检查种子是否正确！");
            }
            $fileList = array();
            $totalLength = $bencode->doDictionaryGet($info, "length", "integer");
            if (isset($totalLength)) {
                $fileList[] = array($dictionaryName, $totalLength);
                $type = "single";
            } else {
                $flist = $bencode->doDictionaryGet($info, "files", "list");
                if(!isset($flist)) {
                    return new PwError('种子缺少长度和文件，请检查种子是否正确！');
                }
                if (!count($flist)) {
                    return new PwError('种子不存在任何文件，请检查种子是否正确！');
                }
                $totalLength = 0;
                foreach ($flist as $fn) {
                    list($ll, $ff) = $bencode->doDictionaryCheck($fn, "length(integer):path(list)");
                    $totalLength += $ll;
                    $ffa = array();
                    foreach ($ff as $ffe) {
                        if ($ffe["type"] != "string") {
                            return new PwError('种子存在文件名错误，请检查种子是否正确！');
                        }
                        $ffa[] = $ffe["value"];
                    }
                    if (!count($ffa)) {
                        return new PwError('种子存在文件名错误，请检查种子是否正确！');
                    }
                    $ffe = implode("/", $ffa);
                    $fileList[] = array($ffe, $ll);
                }
                $type = "multi";
            }
            $dictionary['value']['announce'] = $bencode->doDecode($bencode->doEncodeString(Wekit::C("site", "info.url") . "/announce.php"));
            $dictionary['value']['info']['value']['private'] = $bencode->doDecode('i1e');
            //$dictionary['value']['info']['value']['source'] = $bencode->doDecode($bencode->doEncodeString(Wekit::C("site", "info.name")));
            unset($dictionary['value']['announce-list']);
            unset($dictionary['value']['nodes']);
            $dictionary = $bencode->doDecode($bencode->doEncode($dictionary));
            list($announce, $info) = $bencode->doDictionaryCheck($dictionary, "announce(string):info");
            $infohash = pack("H*", sha1($info["string"]));
            $check = $this->_getTorrentDS()->checkTorrentExist($infohash);
            if($check) {
                return new PwError("不能发布重复种子资源");
            }
            $this->dictionary = $dictionary;
            $this->infohash = $infohash;
            $this->filename = $filename;
            $this->filesavename = $dictionaryName;
            $this->filelist = $fileList;
            $this->totalength = $totalLength;
            $this->type = $type;
            //return new PwError($infohash);
            //return new PwError('检测check成功！');
        } else {
            return new PwError('必须上传一个种子文件！');
        }
        return true;
    }
    public function addTorrentt($tid) {
        $dm = new PwTorrentDm();
        $dm->setTid($tid);
        $dm->setInfoHash($this->infohash);
        $dm->setOwner($this->user->uid);
        $dm->setVisible("yes");
        $dm->setAnonymous("yes");
        $dm->setSize($this->totalength);
        $dm->setNumfiles(count($this->filelist));
        $dm->setType($this->type);
        $dm->setWikilink($this->wikilink);
        $dm->setFileName($this->filename);
        $dm->setSaveAs($this->filesavename);
        $dm->setSpState(1);
        $dm->setAdded(date("Y-m-d H:i:s"));
        $dm->setLastAction(date("Y-m-d H:i:s"));
        $result = $this->_getTorrentDS()->addTorrent($dm);
        if($result instanceof PwError) {
            return $result;
        }
        $filedm = new PwTorrentFileDm();
        if(is_array($this->filelist)) {
            foreach($this->filelist as $file) {
                $filedm->setTottent($result);
                $filedm->setFileName($file[0]);
                $filedm->setSize($file[1]);
                $this->_getTorrentFileDS()->addTorrentFile($filedm);
            }
        }
        $bencode = new PwBencode();
        $fp = fopen("./torrent/$result.torrent", "w");
        if ($fp)
        {
            @fwrite($fp, $bencode->doEncode($this->dictionary));
            fclose($fp);
        }
        return true;
    }
    
    public function getUser($uid) {
        $user = new PwUserBo($uid, true);
        $torrentUser = $this->_getTorrentUserDS()->getTorrentUserByUid($uid);
        $user->passkey = $torrentUser['passkey'];
        $this->user = $user;
    }
    
    public function makePassKey() {
        return md5($this->loginUser->username.Pw::time2str(Pw::getTime(), "Y-m-d H:i:s").$this->loginUser->info['password']);
    }
    
    private function _getTorrentUserDS() {
        return Wekit::load('EXT:torrent.service.PwTorrentUser');
    }
    
    private function _checkHash($hash) {
        return true;
    }
    private function _getTorrentDS() {
        return Wekit::load('EXT:torrent.service.PwTorrent');
    }
    private function _getTorrentFileDS() {
        return Wekit::load('EXT:torrent.service.PwTorrentFile');
    }
}