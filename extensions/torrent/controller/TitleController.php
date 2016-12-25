<?php

defined('WEKIT_VERSION') || exit('Forbidden');

class TitleController extends PwBaseController
{
    public function beforeAction($handlerAdapter)
    {
        parent::beforeAction($handlerAdapter);
    }

    private function send($url)
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
        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }

    public function run()
    {
        if (Wekit::C('site', 'app.torrent.titlegen.enabled') > 0) {
            $wikilink = $this->getInput('wikilink', 'get');

            $url = parse_url($wikilink);

            $url_path = explode('/', $url['path']);
            $wiki_id  = $url_path[2];

            $cache = new WindDbCache(Wind::getComponent('db'), array(
                'table-name'   => 'app_torrent_caches',
                'field-key'    => 'cache_key',
                'field-value'  => 'cache_value',
                'field-expire' => 'cache_expire',
                'expires'      => '259200',
            ));

            $cache->clear(true);

            $result = $cache->get($url['host'] . '_' . $wiki_id);

            if (!$result) {
                switch ($url['host']) {
                    case 'book.douban.com':
                        // Tested with https://book.douban.com/subject/7007241/
                        $api_url = 'https://api.douban.com/v2/book/' . $wiki_id;
                        if (!empty(Wekit::C('site', 'app.torrent.titlegen.douban'))) {
                            $url .= '?apikey=' . Wekit::C('site', 'app.torrent.titlegen.douban');
                        }

                        $api_result = json_decode($this->send($api_url), true);

                        $title = '[' . explode('-', $api_result['pubdate'])[0] . ']';
                        $title .= '[' . $api_result['author'][0] . ']';
                        $title .= '[' . $api_result['title'] . ']';
                        $title .= !empty($api_result['origin_title']) ? '[' . $api_result['origin_title'] . ']' : '';

                        $content = '[img]' . $api_result['image'] . '[/img]<br />' . $api_result['summary'];
                        break;
                    case 'movie.douban.com':
                        // Tested with https://movie.douban.com/subject/1292226/
                        $api_url = 'https://api.douban.com/v2/movie/subject/' . $wiki_id;
                        if (!empty(Wekit::C('site', 'app.torrent.titlegen.douban'))) {
                            $url .= '?apikey=' . Wekit::C('site', 'app.torrent.titlegen.douban');
                        }

                        $api_result = json_decode($this->send($api_url), true);

                        $title = '[' . $api_result['countries'][0] . ']';
                        $title .= '[' . $api_result['year'] . ']';
                        $title .= '[' . $api_result['title'] . ']';
                        $title .= '[' . implode(' / ', $api_result['aka']) . ']';
                        $title .= '[' . implode(' / ', $api_result['genres']) . ']';

                        $content = '[img]' . $api_result['images']['large'] . '[/img]<br />' . $api_result['summary'];
                        break;
                    case 'music.douban.com':
                        // Tested with https://music.douban.com/subject/26767978/
                        $api_url = 'https://api.douban.com/v2/music/' . $wiki_id;
                        if (!empty(Wekit::C('site', 'app.torrent.titlegen.douban'))) {
                            $url .= '?apikey=' . Wekit::C('site', 'app.torrent.titlegen.douban');
                        }

                        $api_result = json_decode($this->send($api_url), true);

                        $title = '[' . explode('-', $api_result['attrs']['pubdate'][0])[0] . ']';
                        $title .= '[' . $api_result['title'] . ']';
                        $title .= '[' . $api_result['alt_title'] . ']';
                        $title .= '[' . $api_result['attrs']['singer'][0] . ']';

                        $content = '[img]' . $api_result['image'] . '[/img]<br />' . $api_result['summary'];
                        break;
                    case 'www.imdb.com':
                        // Tested with http://www.imdb.com/title/tt0062622/
                        $api_url    = 'http://omdbapi.com/?i=' . $wiki_id;
                        $api_result = json_decode($this->send($api_url), true);

                        $title = '[' . explode(',', $api_result['Country'])[0] . ']';
                        $title .= '[' . $api_result['Year'] . ']';
                        $title .= '[' . $api_result['Title'] . ']';
                        $title .= '[' . str_replace(', ', ' / ', $api_result['Genre']) . ']';

                        if (!empty(Wekit::C('site', 'app.torrent.titlegen.omdb'))) {
                            $content = '[img]' . 'http://img.omdbapi.com/?i=' . $wiki_id . '&apikey=' . Wekit::C('site', 'app.torrent.titlegen.omdb') . '&h=640[/img]<br />' . $result->Plot;
                        } else {
                            $content = trim($api_result['Plot']);
                        }
                        break;
                }

                if (!empty($title) && !empty($content) && !strstr($title, '[]')) {
                    $result = json_encode(array('code' => 1, 'result' => array('title' => $title, 'content' => $content)));
                } else {
                    $result = json_encode(array('code' => -1, 'result' => array()));
                }
            }

            $cache->set($url['host'] . '_' . $wiki_id, $result);
        }

        exit($result);
    }

    public function bindAction()
    {
        exit(json_encode(Wekit::C('site', 'app.torrent.typebind')));
    }
}
