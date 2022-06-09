<?php

namespace CG\Functions;

use CG\Functions\Traits\Error;

/**
 * Class Json
 *
 * @package CG\Functions
 */
class Json
{

    use Error;

    /**
     * Safely json decode string
     *
     * @param $string
     *
     * @return array
     */
    public static function decodeToArray( $string ): array
    {
        $array = [];

        try
        {
            $array = ! empty( $string ) ? json_decode( $string, TRUE, 512, JSON_THROW_ON_ERROR ) : [];
            if ( ! is_array( $array ) || json_last_error() !== JSON_ERROR_NONE )
            {
                $array = [];
            }
        }
        catch ( \JsonException | \Exception $e )
        {
            self::throwError( $e );
        }

        return $array;
    }


    /**
     * Safely json decode string | can be array of objects
     *
     * @param $string
     *
     * @return object|array
     */
    public static function decodeToObject( $string ): object|array
    {
        $object = new \stdClass();

        try
        {
            $object = ! empty( $string ) ? json_decode( $string, FALSE, 512, JSON_THROW_ON_ERROR ) : new \stdClass();

            if ( json_last_error() !== JSON_ERROR_NONE )
            {
                $object = new \stdClass();
            }

        }
        catch ( \JsonException $e )
        {
            self::throwError($e);
        }

        return $object;
    }

    /**
     * Safely json encode array
     *
     * @param string|object|array $array $array
     *
     * @return string
     */
    public static function encode( string|object|array $array ): string
    {
        try
        {
            return json_encode($array, JSON_THROW_ON_ERROR );
        }
        catch ( \JsonException $e )
        {
            self::throwError( $e );
        }
    }



    /**
     * Safely json decode string | can be array of objects
     *
     * @param object|array $data
     *
     * @return object|array
     */
    public static function castToObject( object|array $data ): object|array
    {
        return self::decodeToObject( self::encode( $data ) );
    }



    /**
     * Safely json decode string | can be array of objects
     *
     * @param object|array $data
     *
     * @return array
     */
    public static function castToArray( object|array $data ): array
    {
        return self::decodeToArray( self::encode( $data ) );
    }
}