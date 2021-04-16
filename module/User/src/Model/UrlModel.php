<?php

declare(strict_types=1);

namespace User\Model;

class UrlModel
{
    /**
     * @param string $url
     *
     * @return mixed
     */
    public static function encode(string $url)
    {
        return str_replace(['+','/','='], ['-','_',''], base64_encode($url));
    }

    /**
     * @param string $url
     *
     * @return mixed
     */
    public static function decode(string $url)
    {
        return base64_decode(str_replace(['-','_'], ['+','/'], $url));
    }
}
