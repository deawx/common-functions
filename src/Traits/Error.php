<?php

namespace CG\Functions\Traits;

use JetBrains\PhpStorm\NoReturn;

/**
 * Trigger error
 */
trait Error
{

    /**
     * Die on error
     *
     * @param \Exception | \JsonException $e
     */
    #[NoReturn] public static function throwError( \Exception|\JsonException $e ):void
    {
        echo $e->getMessage();
        die();
    }
}