<?php namespace zgldh\Scaffold\Installer\HtmlFields;

/**
 * Created by PhpStorm.
 * User: zhangwb-pc
 * Date: 08/15/2017
 * Time: 18:00
 */
class Radio
{
    private $options = [];

    public function __construct($options = [])
    {
        $this->options = $options;
    }
}