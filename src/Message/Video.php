<?php
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 2016/8/29
 * Time: 9:44
 */

namespace RCorpWechat\Message;

class Video extends AbstractMessage
{
    protected $type = 'video';
    protected $safe = 0;
    protected $properties = [
        'title',
        'media_id',
        'description',
    ];

    public function media($mediaId) {
        $this->setAttribute('media_id', $mediaId);

        return $this;
    }

    public function setSafe($safe){
        $this->setAttribute('safe', $safe);

        return $this;
    }
}