<?php
use Waynestate\Promotions\ParsePromos;

/**
 * Class ParsePromosTest
 */
class ParsePromosTest extends PHPUnit_Framework_TestCase {

    /**
     * @test
     */
    public function changeToGroupName()
    {
        // Create the parser
        $parser = new ParsePromos;

        // Stub Data
        $promos = array('promotions' => array(
            '1' => array (
                'promo_item_id' => '1',
                'promo_group_id' => '1',
                'data' => 'foo',
            ),
        ));
        $group_names = array(
            '1' => 'first',
        );

        // To the parse
        $parsed = $parser->parse($promos, $group_names);

        // Verify the array has been reorganized
        $this->assertArrayHasKey('first', $parsed);
    }

}