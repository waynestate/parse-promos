<?php namespace Waynestate\Promotions;

use Waynestate\Youtube\ParseId;
use Waynestate\Promotions\ParserInterface;
use Waynestate\Promotions\ParsePromosException;

/**
 * Class ParsePromos
 * @package Waynestate\Promotions
 */
class ParsePromos implements ParserInterface
{
    /**
     * Parse the promotions array
     *
     * @param mixed $promos
     * @param array $group_reference
     * @param array $config
     * @return array
     */
    public function parse($promos, array $group_reference = [], array $config = [])
    {
        $promotions = [];

        // Initialize Promotion Groups
        foreach ((array)$group_reference as $key => $value) {
            $promotions[$value] = [];
        }

        // Re-organize by group id
        if (isset($promos['promotions']) && is_array($promos['promotions']) && array_key_exists('promotions', $promos)) {
            // Loop through each promo item
            foreach ($promos['promotions'] as $item) {
                // Organize them by their reference
                $key = (array_key_exists($item['promo_group_id'], $group_reference))?$group_reference[$item['promo_group_id']]:$item['promo_group_id'];
                $promotions[$key][$item['promo_item_id']] = $item;
            }
        }

        // If there are special config cases
        if (is_array($config) && count($config) > 0) {
            // Key should be the group ID
            foreach ($config as $group_name => $option) {
                // If there is a group with this ID
                if (array_key_exists($group_name, $promotions)) {
                    // Perform the action
                    $promotions[$group_name] = $this->performConfig($promotions[$group_name], $option);
                }
            }
        }

        return $promotions;
    }

    /**
     * Parse the promotions array
     *
     * @param array $promos
     * @param array $group_reference
     * @return array
     */
    public function groups(array $promos, array $group_reference = [])
    {
        $groups = [];

        // Make sure we have promotional items
        if (is_array($promos)) {
            foreach ($promos as $items) {
                // Get the first item in that group
                $first_item = current($items);

                // If group reference is passed, then use that as the key otherwise default to promo_group_id
                $key = isset($group_reference[$first_item['group']['promo_group_id']]) ? $group_reference[$first_item['group']['promo_group_id']] : $first_item['group']['promo_group_id'];

                // Set the name of the promo group
                $groups[$key] = $first_item['group']['title'];
            }
        }

        return $groups;
    }

    /**
     * Take a promotion group and perform an action on it
     *
     * @param array $array
     * @param string $option
     * @return array|mixed
     */
    protected function performConfig(array &$array, $option)
    {
        // Allow for the option to be a pipe delimited list
        foreach (explode('|', $option) as $action) {
            // Check to see if there are options on the action
            $action = explode(':', $action);

            // Perform the action based on the config
            switch (current($action)) {

                // Shuffle the array (looses keys)
                case 'randomize':
                    shuffle($array);
                    break;

                // Limit the number returned
                case 'limit':
                    if (isset($action[1])) {
                        $array = $this->arrayLimit($array, $action[1]);
                    }
                    break;

                // Picks just the first one
                case 'first':
                    if (count($array) > 0) {
                        $array = current($array);
                    }
                    break;

                // Only return the 'per page' associated with a specific page_id
                case 'page_id':
                    if (isset($action[1])) {
                        $array = $this->arrayPage($array, $action[1]);
                    }
                    break;

                // Reorder array by an array key
                case 'order':
                    if (isset($action[1])) {
                        $array = $this->arrayOrder($array, $action[1]);
                    }
                    break;

                // Parse out YouTube ID from link field
                case 'youtube':
                    $array = $this->parseYouTubeID($array);
                    break;
            }
        }

        return $array;
    }

    /**
     * @param array $array
     * @param $count
     * @return array
     */
    protected function arrayLimit(array &$array, $count)
    {
        // Chop off the rest of the array
        return array_slice($array, 0, (int) $count, true);
    }

    /**
     * @param array $array
     * @param $page_id
     * @return array
     */
    protected function arrayPage(array &$array, $page_id)
    {
        // Return only the promotions selected for this page
        $page_array = [];

        foreach ($array as $key => $item) {
            if (strstr(',' . $item['page_id'] . ',', ',' . $page_id . ',')) {
                $page_array[$key] = $item;
            }
        }

        return $page_array;
    }

    /**
     * Take a YouTube URL and parse out the YouTube ID as a separate field
     *
     * @param array $array
     * @return array
     */
    protected function parseYouTubeID(array &$array)
    {
        foreach ($array as $key => $item) {
            $array[$key]['youtube_id'] = ParseId::fromUrl($item['link']);
        }

        return $array;
    }

    /**
     * @param array $array
     * @param $field
     * @return array
     */
    protected function arrayOrder(array &$array, $field)
    {
        switch ($field) {
            case 'start_date_desc':
                usort($array, 'self::sortStartDateDesc');
                break;
            case 'start_date_asc':
                usort($array, 'self::sortStartDateAsc');
                break;
            case 'display_date_desc':
                usort($array, 'self::sortDisplayDateDesc');
                break;
            case 'display_date_asc':
                usort($array, 'self::sortDisplayDateAsc');
                break;
            case 'title_desc':
                usort($array, 'self::sortTitleDesc');
                break;
            case 'title_asc':
                usort($array, 'self::sortTitleAsc');
                break;
        }

        return $array;
    }

    /**
     * @param mixed $first
     * @param mixed $second
     * @return int
     */
    private static function sortDisplayDateAsc($first, $second)
    {
        if ($first['display_start_date'] == $second['display_start_date']) {
            return 0;
        }
        return ($first['display_start_date'] < $second['display_start_date']) ? -1 : 1;
    }

    /**
     * @param mixed $first
     * @param mixed $second
     * @return int
     */
    private static function sortDisplayDateDesc($first, $second)
    {
        if ($first['display_start_date'] == $second['display_start_date']) {
            return 0;
        }
        return ($first['display_start_date'] > $second['display_start_date']) ? -1 : 1;
    }

    /**
     * @param mixed $first
     * @param mixed $second
     * @return int
     */
    private static function sortStartDateDesc($first, $second)
    {
        if ($first['start_date'] == $second['start_date']) {
            return 0;
        }
        return ($first['start_date'] > $second['start_date']) ? -1 : 1;
    }

    /**
     * @param mixed $first
     * @param mixed $second
     * @return int
     */
    private static function sortStartDateAsc($first, $second)
    {
        if ($first['start_date'] == $second['start_date']) {
            return 0;
        }
        return ($first['start_date'] < $second['start_date']) ? -1 : 1;
    }

    /**
     * @param mixed $first
     * @param mixed $second
     * @return int
     */
    private static function sortTitleAsc($first, $second)
    {
        if ($first['title'] == $second['title']) {
            return 0;
        }
        return ($first['title'] < $second['title']) ? -1 : 1;
    }

    /**
     * @param mixed $first
     * @param mixed $second
     * @return int
     */
    private static function sortTitleDesc($first, $second)
    {
        if ($first['title'] == $second['title']) {
            return 0;
        }
        return ($first['title'] > $second['title']) ? -1 : 1;
    }
}
