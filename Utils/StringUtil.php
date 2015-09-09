<?php

namespace DWD\UtilsBundle\Utils;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;

class StringUtil
{
    public function __construct( Container $container )
    {
        $this->container = $container;
    }

    public function startWith( $haystack, $needle )
    {
        return !strncmp($haystack, $needle, strlen($needle));
    }

    /*
     * format byte number to B,KB, MB,GB,TB,PB,EB,ZB,YB
     */
    public function byteFormat( $bytes, $unit = "MB", $decimals = 2, $withUnit = false )
    {

        $units = array(
            'B' => 0,
            'KB' => 1,
            'MB' => 2,
            'GB' => 3,
            'TB' => 4,
            'PB' => 5,
            'EB' => 6,
            'ZB' => 7,
            'YB' => 8,
        );

        $value = 0;

        if ($bytes > 0) {
            /*
             * Generate automatic prefix by bytes
             * If wrong prefix given
             */
            if (!array_key_exists($unit, $units)) {
                $pow = floor(log($bytes)/log(1024));
                $unit = array_search($pow, $units);
            }

            /*
             * Calculate byte value by prefix
             */
            $value = ($bytes/pow(1024,floor($units[$unit])));
        } else {
            return 0;
        }

        /*
         * If decimals is not numeric or decimals is less than 0
         * then set default value
         */
        if (!is_numeric($decimals) || $decimals < 0) {
            $decimals = 2;
        }

        /*
         * Format output
         */
        if( $withUnit ) {
            return sprintf('%.' . $decimals . 'f '.$unit, $value);
        } else {
            return sprintf('%.' . $decimals . 'f', $value);
        }
    }

    /**
     * generate a random string include uppercase and lowercase letters and numbers
     */
    public function randomStr( $length = 16 )
    {
        $arr = array_merge(range(0, 9), range('a', 'z'), range('A', 'Z'));

        $str = '';
        $arr_len = count($arr);
        for ($i = 0; $i < $length; $i++)
        {
            $rand = mt_rand(0, $arr_len-1);
            $str.=$arr[$rand];
        }

        return $str;
    }
}
