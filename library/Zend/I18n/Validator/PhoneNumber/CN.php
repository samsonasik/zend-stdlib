<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

return array(
    'code' => '86',
    'patterns' => array(
        'national' => array(
            'general' => '/^[1-7]\\d{7,11}|8[0-357-9]\\d{6,9}|9(?:5\\d{3}|\\d{9})$/',
            'fixed' => '/^21\\d{8,10}|(?:10|2[02-57-9]|3(?:11|7[179])|4(?:[15]1|3[12])|5(?:1\\d|2[37]|3[12]|51|7[13-79]|9[15])|7(?:31|5[457]|6[09]|91)|898)\\d{8}|(?:3(?:1[02-9]|35|49|5\\d|7[02-68]|9[1-68])|4(?:1[02-9]|2[179]|3[3-9]|5[2-9]|6[4789]|7\\d|8[23])|5(?:3[03-9]|4[36]|5[02-9]|6[1-46]|7[028]|80|9[2-46-9])|6(?:3[1-5]|6[0238]|9[12])|7(?:01|[17]\\d|2[248]|3[04-9]|4[3-6]|5[0-3689]|6[2368]|9[02-9])|8(?:1[236-8]|2[5-7]|[37]\\d|5[1-9]|8[3678]|9[1-7])|9(?:0[1-3689]|1[1-79]|[379]\\d|4[13]|5[1-5]))\\d{7}|80(?:29|6[03578]|7[018]|81)\\d{4}$/',
            'mobile' => '/^1(?:3\\d|4[57]|[58][0-35-9])\\d{8}$/',
            'tollfree' => '/^(?:10)?800\\d{7}$/',
            'premium' => '/^16[08]\\d{5}$/',
            'shared' => '/^400\\d{7}|95\\d{3}$/',
            'emergency' => '/^1(?:1[09]|20)$/',
        ),
        'possible' => array(
            'general' => '/^\\d{4,12}$/',
            'mobile' => '/^\\d{11}$/',
            'tollfree' => '/^\\d{10,12}$/',
            'premium' => '/^\\d{8}$/',
            'shared' => '/^\\d{5}(?:\\d{5})?$/',
            'emergency' => '/^\\d{3}$/',
        ),
    ),
);
