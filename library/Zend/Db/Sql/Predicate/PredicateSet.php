<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Db\Sql\Predicate;

use Countable;
use Zend\Db\Sql\Exception;

class PredicateSet implements PredicateInterface, Countable
{
    const COMBINED_BY_AND = 'AND';
    const OP_AND          = 'AND';

    const COMBINED_BY_OR  = 'OR';
    const OP_OR           = 'OR';

    protected $defaultCombination = self::COMBINED_BY_AND;
    protected $predicates         = array();

    /**
     * Constructor
     *
     * @param  null|array $predicates
     * @param  string $defaultCombination
     */
    public function __construct(array $predicates = null, $defaultCombination = self::COMBINED_BY_AND)
    {
        $this->defaultCombination = $defaultCombination;
        if ($predicates) {
            foreach ($predicates as $predicate) {
                $this->addPredicate($predicate);
            }
        }
    }

    /**
     * Add predicate to set
     *
     * @param  PredicateInterface $predicate
     * @param  string $combination
     * @return PredicateSet
     */
    public function addPredicate(PredicateInterface $predicate, $combination = null)
    {
        if ($combination === null || !in_array($combination, array(self::OP_AND, self::OP_OR))) {
            $combination = $this->defaultCombination;
        }

        if ($combination == self::OP_OR) {
            $this->orPredicate($predicate);
            return $this;
        }

        $this->andPredicate($predicate);
        return $this;
    }

    public function addPredicates($predicate, $combination = self::OP_AND)
    {
        if ($predicate === null) {
            throw new Exception\InvalidArgumentException('Predicate cannot be null');
        }
        if ($predicate instanceof PredicateInterface) {
            $this->addPredicate($predicate, $combination);
            return $this;
        }
        if ($predicate instanceof \Closure) {
            $predicate($this);
            return $this;
        }
        if (is_string($predicate)) {
            // String $predicate should be passed as an expression
            $predicate = (strpos($predicate, Expression::PLACEHOLDER) !== false)
                ? new Expression($predicate) : new Literal($predicate);
            $this->addPredicate($predicate, $combination);
            return $this;
        }
        if (is_array($predicate)) {
            foreach ($predicate as $pkey => $pvalue) {
                // loop through predicates
                if (is_string($pkey)) {
                    if (strpos($pkey, '?') !== false) {
                        // First, process strings that the abstraction replacement character ?
                        // as an Expression predicate
                        $predicate = new Expression($pkey, $pvalue);
                    } elseif ($pvalue === null) { // Otherwise, if still a string, do something intelligent with the PHP type provided
                        // map PHP null to SQL IS NULL expression
                        $predicate = new IsNull($pkey, $pvalue);
                    } elseif (is_array($pvalue)) {
                        // if the value is an array, assume IN() is desired
                        $predicate = new In($pkey, $pvalue);
                    } elseif ($pvalue instanceof PredicateInterface) {
                        throw new Exception\InvalidArgumentException(
                            'Using Predicate must not use string keys'
                        );
                    } else {
                        // otherwise assume that array('foo' => 'bar') means "foo" = 'bar'
                        $predicate = new Operator($pkey, Operator::OP_EQ, $pvalue);
                    }
                } elseif ($pvalue instanceof PredicateInterface) {
                    // Predicate type is ok
                    $predicate = $pvalue;
                } else {
                    // must be an array of expressions (with int-indexed array)
                    $predicate = (strpos($pvalue, Expression::PLACEHOLDER) !== false)
                        ? new Expression($pvalue) : new Literal($pvalue);
                }
                $this->addPredicate($predicate, $combination);
            }
        }
        return $this;
    }

    /**
     * Return the predicates
     *
     * @return PredicateInterface[]
     */
    public function getPredicates()
    {
        return $this->predicates;
    }

    /**
     * Add predicate using OR operator
     *
     * @param  PredicateInterface $predicate
     * @return PredicateSet
     */
    public function orPredicate(PredicateInterface $predicate)
    {
        $this->predicates[] = array(self::OP_OR, $predicate);
        return $this;
    }

    /**
     * Add predicate using AND operator
     *
     * @param  PredicateInterface $predicate
     * @return PredicateSet
     */
    public function andPredicate(PredicateInterface $predicate)
    {
        $this->predicates[] = array(self::OP_AND, $predicate);
        return $this;
    }

    /**
     * Get predicate parts for where statement
     *
     * @return array
     */
    public function getExpressionData()
    {
        $parts = array();
        for ($i = 0, $count = count($this->predicates); $i < $count; $i++) {

            /** @var $predicate PredicateInterface */
            $predicate = $this->predicates[$i][1];

            if ($predicate instanceof PredicateSet) {
                $parts[] = '(';
            }

            $parts = array_merge($parts, $predicate->getExpressionData());

            if ($predicate instanceof PredicateSet) {
                $parts[] = ')';
            }

            if (isset($this->predicates[$i+1])) {
                $parts[] = sprintf(' %s ', $this->predicates[$i+1][0]);
            }
        }
        return $parts;
    }

    /**
     * Get count of attached predicates
     *
     * @return int
     */
    public function count()
    {
        return count($this->predicates);
    }
}
