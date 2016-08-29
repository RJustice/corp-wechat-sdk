<?php
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 2016/8/29
 * Time: 9:24
 */

namespace RCorpWechat\Message;

class Voice extends AbstractMessage
{
    protected $type = 'voice';
    protected $safe = 0;
    protected $properties = [
        'media_id',
    ];

    public function media($mediaId) {
        $this->setAttribute('media', $mediaId);

        return $this;
    }

    public function setSafe($safe){
        $this->setAttribute('safe', $safe);

        return $this;
    }
}