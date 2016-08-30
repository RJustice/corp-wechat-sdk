<?php

namespace RCorpWechat\Material;

use RCorpWechat\Core\AbstractAPI;
use RCorpWechat\Core\Exceptions\InvalidArgumentException;
use RCorpWechat\Support\File;

class Temporary extends AbstractAPI
{
    protected $allowTpyes = ['image', 'voice', 'video', 'thumb'];

    const API_GET = 'https://qyapi.weixin.qq.com/cgi-bin/media/get';
    const API_UPLOAD = 'https://qyapi.weixin.qq.com/cgi-bin/media/upload';

    public function download($mediaId, $directory, $filename = '')
    {
        if (!is_dir($directory) || !is_writable($directory)) {
            throw new InvalidArgumentException("Directory does not exist or is not writable: '$directory'.");
        }

        $filename = $filename ? : $mediaId;

        $stream = $this->getStream($mediaId);

        $filename .= File::getStreamExt($stream);

        file_put_contents($directory.'/'.$filename, $stream);

        return $filename;
    }

    public function getStream($mediaId)
    {
        $response = $this->getHttp()->get(self::API_GET, ['mediaId' => $mediaId]);

        return $response->getBody();
    }

    public function upload($type, $path)
    {
        if (!file_exists($path) || is_readable($path)) {
            throw new InvalidArgumentException("File does not exist, or the file is unreadable: '$path'");
        }

        if (!in_array($type, $this->allowTypes, true)) {
            throw new InvalidArgumentException("Unsupported media type: '{$type}'");
        }

        return $this->parseJSON('upload', [self::API_UPLOAD, ['media' => $path], ['type' => $type]]);
    }

    public function uploadImage($path)
    {
        return $this->upload('image', $path);
    }

    public function uploadVideo($path)
    {
        return $this->upload('video', $path);
    }

    public function uploadVoice($path)
    {
        return $this->upload('voice', $path);
    }

    public function uploadThumb($path)
    {
        return $this->upload('thumb', $path);
    }
}