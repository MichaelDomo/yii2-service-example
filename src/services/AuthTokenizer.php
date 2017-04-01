<?php

namespace base\services;

use yii\base\Security;
use base\services\interfaces\AuthTokenizerInterface;

/**
 * Class AuthTokenizer
 * @package base\services
 */
class AuthTokenizer implements AuthTokenizerInterface
{
    private $security;
    private $length;

    /**
     * AuthTokenizer constructor.
     * @param Security $security
     * @param $length
     */
    public function __construct(Security $security, $length)
    {
        $this->security = $security;
        $this->length = $length;
    }

    /**
     * @inheritdoc
     */
    public function generate()
    {
        return $this->security->generateRandomString($this->length);
    }

    /**
     * @inheritdoc
     */
    public function validate($token)
    {
        return empty($token) ? false : true;
    }
}
