<?php namespace WayneState;

/**
 * Class ParsePromos
 * @package WayneState
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
    function performConfig(array &$array, $option)
    {
        // Check to see if there are options on the option
        $option = explode(':', $option);

        // Perform the action based on the config
        switch (current($option)) {
            // Shuffle the array (looses keys)
            case 'randomize':
                shuffle($array);
                break;

            // Picks just the first one
            case 'first':
                $array = current($array);
                break;

            // Reorder by start_date descending
            case 'order':
                // Require there but a second param for the count
                if ( !isset($option[1]) )
                    break;

                if ( $option[1] == 'start_date_desc') {
                    usort( $array, 'self::sortStartDateDesc' );
                }

                if ( $option[1] == 'display_date_desc') {
                    usort( $array, 'self::sortDisplayDateDesc' );
                }
                break;

            // Limit the number returned
            case 'limit':
                // Require there but a second param for the count
                if ( !isset($option[1]) )
                    break;

                // Chop off the rest of the array
                $array = array_slice($array, 0, (int)$option[1], true);
                break;

            // Only return the 'per page' associated with a specific page_id
            case 'page_id':
                // Require there but a second param for the count
                if ( !isset($option[1]) )
                    break;

                // Return only the promotions selected for this page
                $page_array = array();
                foreach ( $array as $key => $item ) {
                    if ( strstr(',' . $item['page_id'] . ',', ',' . $option[1] . ',') ) {
                        $page_array[$key] = $item;
                    }
                }
                $array = $page_array;
                break;

            // Do nothing to the array
            default:
                break;
        }

        return $array;
    }

    /**
     * @param mixed $a
     * @param mixed $b
     * @return int
     */
    private static function sortDisplayDateDesc($a, $b)
    {
        if ($a['display_start_date'] == $b['display_start_date']) {
            return 0;
        }
        return ($a['display_start_date'] > $b['display_start_date']) ? -1 : 1;
    }

    /**
     * @param mixed $a
     * @param mixed $b
     * @return int
     */
    private static function sortStartDateDesc($a, $b)
    {
        if ($a['start_date'] == $b['start_date']) {
            return 0;
        }
        return ($a['start_date'] > $b['start_date']) ? -1 : 1;
    }
}
