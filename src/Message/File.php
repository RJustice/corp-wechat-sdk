<?php
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 2016/8/29
 * Time: 9:48
 */

namespace RCorpWechat\Message;

class File extends AbstractMessage
{
    protected $type = 'file';

    protected $safe = 0;

    protected $properties = [
        'media_id',
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