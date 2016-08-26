<?php
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 2016/8/26
 * Time: 16:39
 */

namespace RCorpWechat\Message;

class News extends AbstractMessage
{
    protected $type = 'news';

    protected $properties = [
        'title',
        'description',
        'url',
        'image',
    ];

    protected  $aliases = [
        'image' => 'picurl',
    ];
}