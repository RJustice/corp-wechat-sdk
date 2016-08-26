<?php
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 2016/8/26
 * Time: 17:17
 */

namespace RCorpWechat\Message;

class Image extends AbstractMessage
{
    protected $type = 'image';

    protected $porperties = [
        'media_id',
    ];

    public function media($mediaId) {
        $this->setAttribute('media_id', $mediaId);

        return $this;
    }
}