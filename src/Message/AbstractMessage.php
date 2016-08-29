<?php
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 2016/8/26
 * Time: 16:27
 */

namespace RCorpWechat\Message;

use RCorpWechat\Support\Attribute;

abstract class AbstractMessage extends Attribute
{
    protected $type;
    protected $id;
    protected $touser;
    protected $toparty;
    protected $totag;
    protected $agentid;
    protected $from;
    protected $safe;
    protected $properties = [];

    public function getType() {
        return $this->type;
    }

    public function __get($property) {
        if (property_exists($this, $property)) {
            return $this->$property;
        }

        return $parent::__get($property);
    }

    public function __set($property, $value) {
        if (property_exists($this, $property)) {
            $this->$property = $value;
         } else {
             parent::__set($property, $value);
        }

        return $this;
    }
}