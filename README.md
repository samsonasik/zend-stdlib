### Welcome to the *Zend Framework 2.0* Release!

Master: [![Build Status](https://secure.travis-ci.org/zendframework/zf2.png?branch=master)](http://travis-ci.org/zendframework/zf2)
Develop: [![Build Status](https://secure.travis-ci.org/zendframework/zf2.png?branch=develop)](http://travis-ci.org/zendframework/zf2)

## RELEASE INFORMATION

*Zend Framework 2.0.8*

This is the eigth maintenance release for the 2.0 series.

DD MMM YYYY

### UPDATES IN 2.0.8

#### Security fix: Query route

The query route was deprecated, as a replacement exists within the HTTP router
itself. You can pass a "query" option to the assemble method containing either
the query string or an array of key-value pairs:

```php
$url = $router->assemble(array(
    'name' => 'foo',
), array(
    'query' => array(
        'page' => 3,
        'sort' => 'DESC',
    ), 
    // or: 'query' => 'page=3&sort=DESC'
));

// via URL helper/plugin:
$rendererOrController->url('foo', array(), array('query' => $request->getQuery()));
```

Additionally, the merging of query parameters into the route match was removed
to avoid potential security issues. Please use the query container of the
request object instead.

For more information on the security vector, please see
[ZF2013-01](http://framework.zend.com/security/ZF2013-01).

#### Security fix: DB platform quoting

Altered `Zend\Db` to throw notices when insecure usage of the following methods
is called: 

- `Zend\Db\Adapter\Platform\*::quoteValue*()`
- `Zend\Db\Sql\*::getSqlString*()`

Fixed `Zend\Db` Platform objects to use driver level quoting when provided, and
throw `E_USER_NOTICE` when not provided.  Added `quoteTrustedValue()` API for
notice-free value quoting.  Fixed all userland quoting in Platform objects to
handle a wider array of escapable characters.

For more information on this security vector, please see
[ZF2013-03](http://framework.zend.com/security/ZF2013-03).

Please see CHANGELOG.md.

### SYSTEM REQUIREMENTS

Zend Framework 2 requires PHP 5.3.3 or later; we recommend using the
latest PHP version whenever possible.

### INSTALLATION

Please see INSTALL.md.

### CONTRIBUTING

If you wish to contribute to Zend Framework 2.0, please read both the
CONTRIBUTING.md and README-GIT.md file.

### QUESTIONS AND FEEDBACK

Online documentation can be found at http://framework.zend.com/manual.
Questions that are not addressed in the manual should be directed to the
appropriate mailing list:

http://framework.zend.com/archives/subscribe/

If you find code in this release behaving in an unexpected manner or
contrary to its documented behavior, please create an issue in our GitHub
issue tracker:

https://github.com/zendframework/zf2/issues

If you would like to be notified of new releases, you can subscribe to
the fw-announce mailing list by sending a blank message to
<fw-announce-subscribe@lists.zend.com>.

### LICENSE

The files in this archive are released under the Zend Framework license.
You can find a copy of this license in LICENSE.txt.

### ACKNOWLEDGEMENTS

The Zend Framework team would like to thank all the [contributors](https://github.com/zendframework/zf2/contributors) to the Zend
Framework project, our corporate sponsor, and you, the Zend Framework user.
Please visit us sometime soon at http://framework.zend.com.
