<?php

namespace Zend\Authentication\Adapter\Exception;

use Zend\Authentication\Exception;

class UnexpectedValueException
    extends Exception\UnexpectedValueException
    implements ExceptionInterface
{
}