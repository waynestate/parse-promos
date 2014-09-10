<?php namespace Waynestate\Promotions;

/**
 * Class ParsePromos
 * @package Waynestate
 */
class ParsePromos {
    /**
     * Parse the promotions array
     *
     * @param array $promos
     * @param array $group_reference
     * @param array $config
     * @return array
     */
    function parse(array &$promos, array $group_reference, $config = array())
    {
        $promotions = array();

        // Re-organize by group id
        if ( is_array( $promos['promotions'] ) ) {

            // Loop through each promo item
            foreach ( $promos['promotions'] as $item ) {

                // Organize them by their reference
                $promotions[$group_reference[$item['promo_group_id']]][$item['promo_item_id']] = $item;
            }
        }

        // If there are special config cases
        if ( is_array($config) && count($config) > 0 ) {

            // Key should be the group ID
            foreach ( $config as $group_name => $option ) {

                // If there is a group with this ID
                if ( array_key_exists($group_name, $promotions) ) {

                    // Perform the action
                    $promotions[$group_name] = $this->performConfig($promotions[$group_name], $option);
                }
            }
        }

        return $promotions;
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
        foreach ( explode( '|', $option ) as $action ) {

            // Check to see if there are options on the action
            $action = explode( ':', $action );

            // Perform the action based on the config
            switch (current( $action )) {

                // Shuffle the array (looses keys)
                case 'randomize':
                    shuffle( $array );
                    break;

                // Limit the number returned
                case 'limit':
                    if ( isset($action[1]) ) {
                        $array = $this->arrayLimit( $array, $action[1] );
                    }
                    break;

                // Picks just the first one
                case 'first':
                    $array = $this->arrayLimit( $array, 1 );
                    break;

                // Only return the 'per page' associated with a specific page_id
                case 'page_id':
                    if ( isset($action[1]) ) {
                        $array = $this->arrayPage( $array, $action[1] );
                    }
                    break;

                // Reorder array by an array key
                case 'order':
                    if ( isset($action[1]) ) {
                        $array = $this->arrayOrder( $array, $action[1] );
                    }
                    break;

                // Only return a single item
                case 'first':
                    $array = current($array);
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
    protected function arrayLimit( array &$array, $count )
    {
        // Chop off the rest of the array
        return array_slice( $array, 0, (int) $count, true );
    }

    /**
     * @param array $array
     * @param $page_id
     * @return array
     */
    protected function arrayPage( array &$array, $page_id )
    {
        // Return only the promotions selected for this page
        $page_array = array();

        foreach ( $array as $key => $item ) {
            if ( strstr(',' . $item['page_id'] . ',', ',' . $page_id . ',') ) {
                $page_array[$key] = $item;
            }
        }

        return $page_array;
    }

    /**
     * @param array $array
     * @param $field
     * @return array
     */
    protected function arrayOrder( array &$array, $field )
    {
        switch ($field) {
            case 'start_date_desc':
                usort( $array, 'self::sortStartDateDesc' );
                break;
            case 'start_date_asc':
                usort( $array, 'self::sortStartDateAsc' );
                break;
            case 'display_date_desc':
                usort( $array, 'self::sortDisplayDateDesc' );
                break;
            case 'display_date_asc':
                usort( $array, 'self::sortDisplayDateAsc' );
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
}
