<?php
/**
 * Copyright © MageKey. All rights reserved.
 */
namespace MageKey\CustomerRestriction\Model\Restriction;

use MageKey\CustomerRestriction\Helper\Data as RestrictionHelper;
use Magento\Framework\Exception\LocalizedException;

class Validation
{
    /**
     * @var RestrictionHelper
     */
    protected $restrictionHelper;
    
    /**
     * @var Pool
     */
    protected $restrictionPool;

    /**
     * @param RestrictionHelper $restrictionHelper
     * @param Pool $restrictionPool
     */
    public function __construct(
        RestrictionHelper $restrictionHelper,
        Pool $restrictionPool
    ) {
        $this->restrictionHelper = $restrictionHelper;
        $this->restrictionPool = $restrictionPool;
    }
    
    /**
     * Validate restriction
     *
     * @param string $restriction
     * @param mixed $value
     * @param bool $throwException
     * @return bool
     */
    public function validate($restriction, $value, $throwException = true)
    {
        if ($this->restrictionHelper->isRestrictionEnabled($restriction)) {
            $observers = $this->restrictionPool->get($restriction);
            if (!empty($observers)) {
                foreach ($observers as $observer) {
                    if (!$observer->isValid($value)) {
                        if ($throwException) {
                            $this->throwException($observer->getErrorMessage());
                        }
                        return false;
                    }
                }
            }
        }
        
        return true;
    }
    
    /**
     * Throw exception
     *
     * @param string $message
     */
    public function throwException($message = null)
    {
        throw new LocalizedException($message instanceof \Magento\Framework\Phrase ? $message : __($message));
    }
}