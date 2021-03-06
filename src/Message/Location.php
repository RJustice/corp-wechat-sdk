<?php
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 2016/8/29
 * Time: 10:15
 */

namespace RCorpWechat\Message;

class Location extends AbstractMessage
{
    protected $type = 'location';

    protected $properties = [
        'latitude',
        'longitude',
        'scale',
        'label',
        'percision',
    ];
}