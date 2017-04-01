<?php

namespace base\services\interfaces;

/**
 * Interface LoggerInterface
 * @package base\services\interfaces
 */
interface LoggerInterface
{
    /**
     * @param $category
     * @param $event
     * @param $data
     * @return mixed
     */
    public function log($category, $event, $data);
}
