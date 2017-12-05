<?php namespace Waynestate\Promotions;

/**
 * Interface ParserInterface
 * @package Waynestate\Promotions
 */
interface ParserInterface
{
    /**
     * @param mixed $promos
     * @param array $group_reference
     * @param array $config
     * @return array
     */
    public function parse($promos, array $group_reference, array $config);
}
