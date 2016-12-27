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
            if ($url['host'] == 'anidb.net') {
                $url_query = $url['query'];
                parse_str($url_query, $url_query);

                $wiki_id = $url_query['aid'];
            } else {
                $wiki_id = $url_path[2];
            }

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

                        $attrs['year']   = explode('-', $api_result['pubdate'])[0];
                        $attrs['author'] = implode(', ', $api_result['author']);
                        $attrs['title']  = $api_result['title'];
                        if (!empty($api_result['origin_title'])) {
                            $attrs['aka'] = $api_result['origin_title'];
                        }

                        $content = '[img]' . $api_result['image'] . '[/img]<br />' . $api_result['summary'];
                        break;
                    case 'movie.douban.com':
                        // Tested with https://movie.douban.com/subject/1292226/
                        $api_url = 'https://api.douban.com/v2/movie/subject/' . $wiki_id;
                        if (!empty(Wekit::C('site', 'app.torrent.titlegen.douban'))) {
                            $url .= '?apikey=' . Wekit::C('site', 'app.torrent.titlegen.douban');
                        }

                        $api_result = json_decode($this->send($api_url), true);

                        $attrs['country'] = $api_result['countries'][0];
                        $attrs['year']    = $api_result['year'];
                        $attrs['title']   = $api_result['title'];
                        $attrs['aka']     = implode(' / ', $api_result['aka']);
                        $attrs['genres']  = implode(' / ', $api_result['genres']);

                        $content = '[img]' . $api_result['images']['large'] . '[/img]<br />' . $api_result['summary'];
                        break;
                    case 'music.douban.com':
                        // Tested with https://music.douban.com/subject/26767978/
                        $api_url = 'https://api.douban.com/v2/music/' . $wiki_id;
                        if (!empty(Wekit::C('site', 'app.torrent.titlegen.douban'))) {
                            $url .= '?apikey=' . Wekit::C('site', 'app.torrent.titlegen.douban');
                        }

                        $api_result = json_decode($this->send($api_url), true);

                        $attrs['year']   = explode('-', $api_result['attrs']['pubdate'][0])[0];
                        $attrs['title']  = $api_result['title'];
                        $attrs['aka']    = $api_result['alt_title'];
                        $attrs['singer'] = implode(', ', $api_result['attrs']['singer']);

                        $content = '[img]' . $api_result['image'] . '[/img]<br />' . $api_result['summary'];
                        break;
                    case 'www.imdb.com':
                        // Tested with http://www.imdb.com/title/tt0062622/
                        $api_url    = 'http://omdbapi.com/?i=' . $wiki_id;
                        $api_result = json_decode($this->send($api_url), true);

                        $attrs['country'] = explode(',', $api_result['Country'])[0];
                        $attrs['year']    = $api_result['Year'];
                        $attrs['title']   = $api_result['Title'];
                        $attrs['aka']     = '';
                        $attrs['genres']  = str_replace(', ', ' / ', $api_result['Genre']);

                        if (!empty(Wekit::C('site', 'app.torrent.titlegen.omdb'))) {
                            $content = '[img]' . 'http://img.omdbapi.com/?i=' . $wiki_id . '&apikey=' . Wekit::C('site', 'app.torrent.titlegen.omdb') . '&h=640[/img]<br />' . $result->Plot;
                        } else {
                            $content = trim($api_result['Plot']);
                        }
                        break;
                    case 'bgm.tv':
                    case 'bangumi.tv':
                        // Tested with http://bgm.tv/subject/265
                        $api_url    = 'https://api.bgm.tv/subject/' . $wiki_id;
                        $api_result = json_decode($this->send($api_url), true);

                        $attrs['year']  = explode('-', $api_result['air_date'])[0];
                        $attrs['title'] = $api_result['name_cn'];
                        $attrs['aka']   = $api_result['name'];

                        $content = '[img]' . $api_result['images']['large'] . '[/img]<br />' . $api_result['summary'];
                        break;
                    case 'anidb.net':
                        // Tested wit http://anidb.net/perl-bin/animedb.pl?show=anime&aid=22
                        if (!empty(Wekit::C('site', 'app.torrent.titlegen.anidb'))) {
                            $client = Wekit::C('site', 'app.torrent.titlegen.anidb');

                            $api_url = 'http://api.anidb.net:9001/httpapi?client=' . $client . '&clientver=1&protover=1&request=anime&aid=' . $wiki_id;

                            $api_result = $this->send($api_url);
                            $api_result = gzinflate(substr($api_result, 10));
                            $api_result = simplexml_load_string($api_result);

                            $ns = $api_result->getNamespaces(true);

                            foreach ($api_result->titles->title as $title) {
                                $type = trim($title->attributes()['type']);
                                $lang = $title->attributes($ns['xml'])['lang'];
                                switch ($lang) {
                                    case 'zh-Hans':
                                        $title_cn[$type] = $title;
                                        break;
                                    case 'en':
                                        $title_en[$type] = $title;
                                        break;
                                    case 'ja':
                                        $title_ja[$type] = $title;
                                        break;
                                    case 'x-jat':
                                        $title_jat[$type] = $title;
                                        break;
                                    default:
                                        continue;
                                        break;
                                }
                            }

                            $title_cn  = !empty($title_cn['official']) ? $title_cn['official'] : $title_cn['synonym'];
                            $title_en  = !empty($title_en['official']) ? $title_en['official'] : $title_en['synonym'];
                            $title_ja  = !empty($title_ja['official']) ? $title_ja['official'] : $title_ja['synonym'];
                            $title_jat = !empty($title_jat['main']) ? $title_jat['main'] : $title_jat['synonym'];

                            $attrs['year']  = explode('-', $api_result->startdate)[0];
                            $attrs['title'] = (string) ($title_cn ?: $title_ja);
                            $attrs['aka']   = $title_cn ? $title_ja . ' / ' . $title_jat . ' / ' . $title_en : $title_jat . ' / ' . $title_en;

                            $content = '[img]http://img7.anidb.net/pics/anime/' . $api_result->picture . '[/img]<br />' . $api_result->description;
                        }
                        break;
                }

                if (!empty($attrs) && !empty($content) && !in_array('', $attrs)) {
                    $result = json_encode(array('code' => 1, 'result' => array('attrs' => $attrs, 'content' => $content)));
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

    public function htmlAction()
    {
        // Output the generator html
    }
}
