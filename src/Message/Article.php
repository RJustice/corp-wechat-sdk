<?php
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 2016/8/26
 * Time: 17:12
 */

namespace RCorpWechat\Message;

class Article extends AbstractMessage
{
    protected $type = 'mpnews';

    protected $property = [
        'title',
        'thumb_media_id',
        'author',
        'source_url',
        'content',
        'digest',
        'show_cover',
    ];

    protected $aliases = [
        'source_url' => 'content_source_url',
        'show_cover' => 'show_cover_pic',
    ];
}