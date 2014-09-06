<?php
use Waynestate\Promotions\ParsePromos;

/**
 * Class ParsePromosTest
 */
class ParsePromosTest extends PHPUnit_Framework_TestCase {

    /**
     * @var
     */
    protected $promos;

    /**
     * @var
     */
    protected $parser;

    /**
     * @var
     */
    protected $groups;

    /**
     *
     */
    protected function setUp()
    {
        // Create the parser
        $this->parser = new ParsePromos;

        // Stub group names
        $this->groups = array(
            '1' => 'one',
        );

        // Stub promotions
        $this->promos = array(
            'promotions' => array(
                '1' => array (
                    'promo_item_id' => '1',
                    'promo_group_id' => '1',
                    'data' => 'foo',
                ),
                '2' => array (
                    'promo_item_id' => '2',
                    'promo_group_id' => '1',
                    'data' => 'foo',
                ),
                '3' => array (
                    'promo_item_id' => '3',
                    'promo_group_id' => '1',
                    'data' => 'foo',
                ),
            )
        );
    }

    /**
     * @test
     */
    public function changeKeysToGroupName()
    {
        // Basic parse with no config
        $parsed = $this->parser->parse($this->promos, $this->groups);

        // Verify the array has been reorganized
        $this->assertArrayHasKey('one', $parsed);
    }

    /**
     * @test
     */
    public function shouldLimitOne()
    {
        // Group 'one' should only return the first item
        $config = array(
            'one' => 'first',
        );

        // Basic parse with no config
        $parsed = $this->parser->parse($this->promos, $this->groups, $config);

        // Verify there is only one element in the 'one' group
        $this->assertCount(1, $parsed['one']);
    }

    /**
     * @test
     */
    public function shouldLimitMultiple()
    {
        // Group 'one' should only return the first item
        $config = array(
            'one' => 'limit:2',
        );

        // Basic parse with no config
        $parsed = $this->parser->parse($this->promos, $this->groups, $config);

        // Verify there is only one element in the 'one' group
        $this->assertCount(2, $parsed['one']);
    }
}