<?php namespace Waynestate\Promotions;

/**
 * Interface ParserInterface
 * @package Waynestate\Promotions
 */
interface ParserInterface
{
    /**
     * @param array $promos
     * @param array $group_reference
     * @param array $config
     * @return array
     */
    public function parse(array &$promos, array $group_reference, array $config);
}