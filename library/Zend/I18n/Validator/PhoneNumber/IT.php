<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

return array(
    'code' => '39',
    'patterns' => array(
        'national' => array(
            'general' => '/^[01589]\\d{5,10}|3(?:[12457-9]\\d{8}|[36]\\d{7,9})$/',
            'fixed' => '/^0(?:[26]\\d{4,9}|(?:1(?:[0159]\\d|[27][1-5]|31|4[1-4]|6[1356]|8[2-57])|3(?:[0159]\\d|2[1-4]|3[12]|[48][1-6]|6[2-59]|7[1-7])|4(?:[0159]\\d|[23][1-9]|4[245]|6[1-5]|7[1-4]|81)|5(?:[0159]\\d|2[1-5]|3[2-6]|4[1-79]|6[4-6]|7[1-578]|8[3-8])|7(?:[0159]\\d|2[12]|3[1-7]|4[2346]|6[13569]|7[13-6]|8[1-59])|8(?:[0159]\\d|2[34578]|3[1-356]|[6-8][1-5])|9(?:[0159]\\d|[238][1-5]|4[12]|6[1-8]|7[1-6]))\\d{2,7})$/',
            'mobile' => '/^3(?:[12457-9]\\d{8}|6\\d{7,8}|3\\d{7,9})$/',
            'tollfree' => '/^80(?:0\\d{6}|3\\d{3})$/',
            'premium' => '/^0878\\d{5}|1(?:44|6[346])\\d{6}|89(?:2\\d{3}|4(?:[0-4]\\d{2}|[5-9]\\d{4})|5(?:[0-4]\\d{2}|[5-9]\\d{6})|9\\d{6})$/',
            'shared' => '/^84(?:[08]\\d{6}|[17]\\d{3})$/',
            'personal' => '/^1(?:78\\d|99)\\d{6}$/',
            'voip' => '/^55\\d{8}$/',
            'shortcode' => '/^1(?:1(?:[47]|6\\d{3})|2\\d{2}|4(?:82|9\\d{1,3})|5(?:00|1[58]|2[25]|3[03]|44)|86|9(?:2(?:[01]\\d{2}|[2-9]\\d)|4\\d|696))$/',
            'emergency' => '/^11[2358]$/',
        ),
        'possible' => array(
            'general' => '/^\\d{6,11}$/',
            'fixed' => '/^\\d{6,11}$/',
            'mobile' => '/^\\d{9,11}$/',
            'tollfree' => '/^\\d{6,9}$/',
            'premium' => '/^\\d{6,10}$/',
            'shared' => '/^\\d{6,9}$/',
            'personal' => '/^\\d{9,10}$/',
            'voip' => '/^\\d{10}$/',
            'shortcode' => '/^\\d{3,6}$/',
            'emergency' => '/^\\d{3}$/',
        ),
    ),
);
