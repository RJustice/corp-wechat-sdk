<?php

namespace RCorpWechat\Material;

use RCorpWechat\Core\AbstractAPI;
use RCorpWechat\Core\Exceptions\InvalidArgumentException;
use RCorpWechat\Message\Article;

class Material extends AbstractAPI
{
    protected $allowTypes = ['image', 'voice', 'video', 'file', 'news_image'];

    const API_UPLOAD = 'https://qyapi.weixin.qq.com/cgi-bin/material/add_material';
    const API_GET = 'https://qyapi.weixin.qq.com/cgi-bin/material/get';
    const API_DELETE = 'https://qyapi.weixin.qq.com/cgi-bin/material/del';
    const API_STATS = 'https://qyapi.weixin.qq.com/cgi-bin/material/get_count';
    const API_LISTS = 'https://qyapi.weixin.qq.com/cgi-bin/material/batchget';
    const API_MPNEWS_UPLOAD = 'https://qyapi.weixin.qq.com/cgi-bin/material/add_mpnews';
    const API_MPNEWS_UPDATE = 'https://qyapi.weixin.qq.com/cgi-bin/material/update_mpnews';
    const API_NEWS_IMAGE_UPLOAD = 'https://qyapi.weixin.qq.com/cgi-bin/media/uploadimg';

    public function uploadImage($path, $agentId = 0)
    {
        $params = [
            'agentid' => $agentId,
        ];

        return $this->uploadMedia('image', $path, $params);
    }

    public function uploadVoice($path, $agentId = 0)
    {
        $params = [
            'agentid' => $agentId,
        ];

        return $this->uploadMedia('voice', $path, $params);
    }

    public function uploadFile($path, $agentId = 0)
    {
        $params = [
            'agentid' => $agentId,
        ];

        return $this->uploadMedia('file', $path, $params);
    }

    public function uploadVideo($path, $agentId = 0)
    {
        $params = [
            'agentid' => $agentId,
        ];

        return $this->uploadMedia('video', $path, $params);
    }

    public function uploadArticle($articles, $agentId = 0)
    {
        if (!empty($articles['title']) || $articles instanceof Article) {
            $articles = [$articles];
        }

        $params = [
            'agentid' => $agentId,
            'mpnews' => [
                'articles' => array_map(function ($article) {
                    if ($article instanceof Article) {
                        return $article->only([
                                'title', 'thumb_media_id', 'author', 'content_source_url', 'content', 'digest', 'show_cover_pic'
                            ]);
                    }

                    return $article;
                    }, $articles),
                ]
            ];

            return $this->parseJSON('json', [self::API_MPNEWS_UPLOAD, $params]);
    }

    public function updateArticle($articles, $agentId = 0) {
        if (!empty($articles['title']) || $articles instanceof Article) {
            $articles = [$articles];
        }

        $params = [
            'agentid' => $agentId,
            'mpnews' => [
                'articles' => array_map(function ($article) {
                    if ($article instanceof Article) {
                        return $article->only([
                            'title', 'thumb_media_id', 'author', 'content_source_url', 'content', 'digest', 'show_cover_pic'
                        ]);
                    }

                    return $article;
                }, $articles),
            ]
        ];
        return $this->parseJSON('json', [self::API_MPNEWS_UPDATE, $params]);
    }

    public function uploadArticleImage($path)
    {
        return $this->uploadMedia('news_image', $path, []);
    }

    public function get($mediaId)
    {
        $response = $this->getHttp()->json(self::API_GET, ['media_id' => $mediaId]);

        foreach ($response->getHeader('Content-Type') as $mine) {
            if (preg_match('/(image|video|audio)/i', $mine)) {
                return $response->getBody();
            }
        }

        $json = $this->getHttp()->parseJSON($response);

        if (!$json) {
            return $response->getBody();
        }

        $this->checkAndThrow($json);

        return $json;
    }

    public function delete($mediaId)
    {
        return $this->parseJSON('json', [self::API_DELETE, 'media_id' => $mediaId]);
    }

    public function lists($type, $agentId = 0, $offset = 0, $count = 20)
    {
        $params = [
            'type' => $type,
            'agentid' => $agentId,
            'offset' => $offset,
            'count' => $count,
        ];

        return $this->parseJSON('json', [self::API_LISTS, $params]);
    }

    public function stats()
    {
        return $this->parseJSON('get', [self::API_STATS]);
    }

    protected function uploadMedia($type, $path, array $params)
    {
        if (!file_exists($path) || !is_readable($path)) {
            throw new InvalidArgumentException("File does not exist, or the file is unreadable: '$path'");
        }

        $params['type'] = $type;

        return $this->parseJSON('upload', [self::getAPIByType($type), ['media' => $path], $params]);
    }

    public function getAPIByType($type)
    {
        switch ($type) {
            case 'news_image' :
                $api = self::API_NEWS_IMAGE_UPLOAD;
                break;
            default :
                $api = self::API_UPLOAD;
        }

        return $api;
    }
}