<?php
use Waynestate\Promotions\ParsePromos;

/**
 * Class ParsePromosTest
 */
class ParsePromosTest extends PHPUnit_Framework_TestCase
{
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
     * Setup
     */
    protected function setUp()
    {
        // Create the parser
        $this->parser = new ParsePromos;

        // Stub group names
        $this->groups = array(
            1 => 'one',
            2 => 'two',
        );

        // Stub promotions
        $this->promos = array(
            'promotions' => array(
                1 => array(
                    'promo_item_id' => 1,
                    'promo_group_id' => 1,
                    'page_id' => '1,2,3,4',
                    'display_start_date' => '2014-01-01',
                    'start_date' => '2014-01-01',
                    'title' => 'Zebra',
                    'data' => 'foo',
                    'group' => array(
                        'promo_group_id' => 1,
                        'title' => $this->groups[1],
                    ),
                ),
                2 => array(
                    'promo_item_id' => 2,
                    'promo_group_id' => 1,
                    'page_id' => '2,3,4',
                    'display_start_date' => '2014-01-02',
                    'start_date' => '2014-01-03',
                    'title' => 'Bear',
                    'data' => 'foo',
                    'group' => array(
                        'promo_group_id' => 1,
                        'title' => $this->groups[1],
                    ),
                ),
                3 => array(
                    'promo_item_id' => 3,
                    'promo_group_id' => 1,
                    'page_id' => '3,4',
                    'display_start_date' => '2014-01-03',
                    'start_date' => '2014-01-02',
                    'title' => 'Cat',
                    'data' => 'foo',
                    'group' => array(
                        'promo_group_id' => 1,
                        'title' => $this->groups[1],
                    ),
                ),
                4 => array(
                    'promo_item_id' => 4,
                    'promo_group_id' => 1,
                    'page_id' => '4,5,6',
                    'display_start_date' => '2014-01-03',
                    'start_date' => '2014-01-02',
                    'title' => 'Dog',
                    'data' => 'foo',
                    'group' => array(
                        'promo_group_id' => 1,
                        'title' => $this->groups[1],
                    ),
                ),
                5 => array(
                    'promo_item_id' => 5,
                    'promo_group_id' => 1,
                    'page_id' => '4,6,7',
                    'display_start_date' => '2014-01-03',
                    'start_date' => '2014-01-02',
                    'title' => 'Kitty',
                    'data' => 'foo',
                    'group' => array(
                        'promo_group_id' => 1,
                        'title' => $this->groups[1],
                    ),
                ),
                6 => array(
                    'promo_item_id' => 6,
                    'promo_group_id' => 2,
                    'page_id' => '4,6,7',
                    'display_start_date' => '2014-01-03',
                    'start_date' => '2014-01-02',
                    'title' => 'Red',
                    'data' => 'foo',
                    'group' => array(
                        'promo_group_id' => 2,
                        'title' => $this->groups[2],
                    ),
                ),
                7 => array(
                    'promo_item_id' => 7,
                    'promo_group_id' => 2,
                    'page_id' => '4,6,7',
                    'display_start_date' => '2014-01-03',
                    'start_date' => '2014-01-02',
                    'title' => 'Blue',
                    'data' => 'foo',
                    'group' => array(
                        'promo_group_id' => 2,
                        'title' => $this->groups[2],
                    ),
                ),
                8 => array(
                    'promo_item_id' => 8,
                    'promo_group_id' => 2,
                    'page_id' => '4,6,7',
                    'display_start_date' => '2014-01-03',
                    'start_date' => '2014-01-02',
                    'title' => 'Circle',
                    'data' => 'foo',
                    'group' => array(
                        'promo_group_id' => 99999,
                        'title' => 'Random Group Not In Group Reference Array',
                    ),
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
    public function shouldReturnASingleItem()
    {
        // Group 'one' should only return the first item
        $config = array(
            'one' => 'first',
        );

        // Parse the promotions based on the config
        $parsed = $this->parser->parse($this->promos, $this->groups, $config);

        // Make sure it equals the first item in the first group of the unparsed promos
        $this->assertEquals($this->promos['promotions'][1], $parsed['one']);
    }

    /**
     * @test
     */
    public function shouldLimitOne()
    {
        // Group 'one' should only return the first item
        $config = array(
            'one' => 'limit:1',
        );

        // Parse the promotions based on the config
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

        // Parse the promotions based on the config
        $parsed = $this->parser->parse($this->promos, $this->groups, $config);

        // Verify there is only one element in the 'one' group
        $this->assertCount(2, $parsed['one']);
    }

    /**
     * @test
     */
    public function shouldRandomize()
    {
        // Group 'one' should only return the first item
        $config = array(
            'one' => 'randomize',
        );

        // Parse the promotions based on the config
        $parsed = $this->parser->parse($this->promos, $this->groups, $config);

        // TODO: Figure out how to test a shuffled array
    }

    /**
     * @test
     */
    public function shouldLimitToPageThatExists()
    {
        // Group 'one' should only return the first item
        $config = array(
            'one' => 'page_id:3',
        );

        // Parse the promotions based on the config
        $parsed = $this->parser->parse($this->promos, $this->groups, $config);

        // There should only be three elements with this page_id
        $this->assertCount(3, $parsed['one']);
    }

    /**
     * @test
     */
    public function shouldLimitToPageThatDoesNotExists()
    {
        // Group 'one' should only return the first item
        $config = array(
            'one' => 'page_id:999',
        );

        // Parse the promotions based on the config
        $parsed = $this->parser->parse($this->promos, $this->groups, $config);

        // There should be no elements with this page_id
        $this->assertCount(0, $parsed['one']);
    }

    /**
     * @test
     */
    public function shouldOrderByDisplayDateDesc()
    {
        // Group 'one' should only return the first item
        $config = array(
            'one' => 'order:display_date_desc',
        );

        // Parse the promotions based on the config
        $parsed = $this->parser->parse($this->promos, $this->groups, $config);

        // Loop through each item - 1
        $length = count($parsed['one']);
        for ($i = 0; $i < $length - 1; ++$i) {
            // Compare the current start_date to the next item
            $this->assertGreaterThanOrEqual(strtotime($parsed['one'][$i+1]['display_start_date']), strtotime($parsed['one'][$i]['display_start_date']));
        }
    }

    /**
     * @test
     */
    public function shouldOrderByDisplayDateAsc()
    {
        // Group 'one' should only return the first item
        $config = array(
            'one' => 'order:display_date_asc',
        );

        // Parse the promotions based on the config
        $parsed = $this->parser->parse($this->promos, $this->groups, $config);

        // Loop through each item - 1
        $length = count($parsed['one']);
        for ($i = 0; $i < $length - 1; ++$i) {
            // Compare the current start_date to the next item
            $this->assertLessThanOrEqual(strtotime($parsed['one'][$i+1]['display_start_date']), strtotime($parsed['one'][$i]['display_start_date']));
        }
    }

    /**
     * @test
     */
    public function shouldOrderByStartDateDesc()
    {
        // Group 'one' should only return the first item
        $config = array(
            'one' => 'order:start_date_desc',
        );

        // Parse the promotions based on the config
        $parsed = $this->parser->parse($this->promos, $this->groups, $config);

        // Loop through each item - 1
        $length = count($parsed['one']);
        for ($i = 0; $i < $length - 1; ++$i) {
            // Compare the current start_date to the next item
            $this->assertGreaterThanOrEqual(strtotime($parsed['one'][$i+1]['start_date']), strtotime($parsed['one'][$i]['start_date']));
        }
    }

    /**
     * @test
     */
    public function shouldOrderByStartDateAsc()
    {
        // Group 'one' should only return the first item
        $config = array(
            'one' => 'order:start_date_asc',
        );

        // Parse the promotions based on the config
        $parsed = $this->parser->parse($this->promos, $this->groups, $config);

        // Loop through each item - 1
        $length = count($parsed['one']);
        for ($i = 0; $i < $length - 1; ++$i) {
            // Compare the current start_date to the next item
            $this->assertLessThanOrEqual(strtotime($parsed['one'][$i+1]['start_date']), strtotime($parsed['one'][$i]['start_date']));
        }
    }

    /**
     * @test
     */
    public function shouldOrderByTitleAsc()
    {
        // Group 'one' should only return the first item
        $config = array(
            'one' => 'order:title_asc',
        );

        // Parse the promotions based on the config
        $parsed = $this->parser->parse($this->promos, $this->groups, $config);

        // Loop through each item - 1
        $length = count($parsed['one']);
        for ($i = 0; $i < $length - 1; ++$i) {
            // Compare the current start_date to the next item
            $this->assertGreaterThan(0, strcmp($parsed['one'][$i+1]['title'], $parsed['one'][$i]['title']));
        }
    }

    /**
     * @test
     */
    public function shouldOrderByTitleDesc()
    {
        // Group 'one' should only return the first item
        $config = array(
            'one' => 'order:title_desc',
        );

        // Parse the promotions based on the config
        $parsed = $this->parser->parse($this->promos, $this->groups, $config);

        // Loop through each item - 1
        $length = count($parsed['one']);
        for ($i = 0; $i < $length - 1; ++$i) {
            // Compare the current start_date to the next item
            $this->assertLessThan(0, strcmp($parsed['one'][$i+1]['title'], $parsed['one'][$i]['title']));
        }
    }

    /**
     * @test
     */
    public function shouldChainConfigs()
    {
        // Group 'one' should only return the first item
        $config = array(
            'one' => 'order:start_date_desc|limit:1',
        );

        // Parse the promotions based on the config
        $parsed = $this->parser->parse($this->promos, $this->groups, $config);

        // There should be the same number of elements started with
        $this->assertCount(1, $parsed['one']);

        // Ensure that item has a specific start date
        $first = current($parsed['one']);
        $this->assertEquals('2014-01-03', $first['start_date']);
    }

    /**
     * @test
     */
    public function shouldReturnIntGroupKeys()
    {
        // Parse the promotions
        $parsed = $this->parser->parse($this->promos, $this->groups);

        // Parse for the group names
        $groups = $this->parser->groups($parsed, $this->groups);

        // Assert that the keys are not type INT
        foreach ($groups as $key=>$item) {
            $this->assertNotInternalType('int', $key);
        }
    }

    /**
     * @test
     */
    public function shouldReturnStringGroupKeys()
    {
        // Parse the promotions
        $parsed = $this->parser->parse($this->promos, $this->groups);

        // Parse for the group names
        $groups = $this->parser->groups($parsed);

        // Assert that the keys are not type STRING
        foreach ($groups as $key=>$item) {
            $this->assertNotInternalType('string', $key);
        }
    }
}
