<?php

namespace Croga\Functions;

use Croga\Functions\Traits\Error;

/**
 * Class Misc
 *
 * @package Croga\Functions
 */
class Misc
{

    use Error;


    /**
     * @param string $email_address
     * @param int    $size
     *
     * @return string
     */
    public static function getGravatarUrl(  string $email_address, int $size = 160 ): string
    {
        return '//www.gravatar.com/avatar/'.md5( strtolower( $email_address ) ) .'?s=' . $size . '&d=mm';
    }
}