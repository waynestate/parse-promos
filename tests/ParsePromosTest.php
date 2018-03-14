<?php

use PHPUnit\Framework\TestCase;
use Waynestate\Promotions\ParsePromos;

/**
 * Class ParsePromosTest
 */
class ParsePromosTest extends TestCase
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
        $this->groups = [
            1 => 'one',
            2 => 'two',
            3 => 'three',
        ];

        // Stub promotions
        $this->promos = [
            'promotions' => [
                1 => [
                    'promo_item_id' => 1,
                    'promo_group_id' => 1,
                    'page_id' => '1,2,3,4',
                    'display_start_date' => '2014-01-01',
                    'start_date' => '2014-01-01',
                    'title' => 'Zebra',
                    'link' => '',
                    'data' => 'foo',
                    'group' => [
                        'promo_group_id' => 1,
                        'title' => $this->groups[1],
                    ],
                ],
                2 => [
                    'promo_item_id' => 2,
                    'promo_group_id' => 1,
                    'page_id' => '2,3,4',
                    'display_start_date' => '2014-01-02',
                    'start_date' => '2014-01-03',
                    'title' => 'Bear',
                    'link' => 'https://wayne.edu/',
                    'data' => 'foo',
                    'group' => [
                        'promo_group_id' => 1,
                        'title' => $this->groups[1],
                    ],
                ],
                3 => [
                    'promo_item_id' => 3,
                    'promo_group_id' => 1,
                    'page_id' => '3,4',
                    'display_start_date' => '2014-01-03',
                    'start_date' => '2014-01-02',
                    'title' => 'Cat',
                    'link' => 'https://youtube.com/',
                    'data' => 'foo',
                    'group' => [
                        'promo_group_id' => 1,
                        'title' => $this->groups[1],
                    ],
                ],
                4 => [
                    'promo_item_id' => 4,
                    'promo_group_id' => 1,
                    'page_id' => '4,5,6',
                    'display_start_date' => '2014-01-03',
                    'start_date' => '2014-01-02',
                    'title' => 'Dog',
                    'link' => 'http://www.youtube.com/watch?v=PHqfwq033yQ',
                    'data' => 'foo',
                    'group' => [
                        'promo_group_id' => 1,
                        'title' => $this->groups[1],
                    ],
                ],
                5 => [
                    'promo_item_id' => 5,
                    'promo_group_id' => 1,
                    'page_id' => '4,6,7',
                    'display_start_date' => '2014-01-03',
                    'start_date' => '2014-01-02',
                    'title' => 'Kitty',
                    'link' => 'http://www.youtube.com/v/PHqfwq033yQ?version=3&autohide=1',
                    'data' => 'foo',
                    'group' => [
                        'promo_group_id' => 1,
                        'title' => $this->groups[1],
                    ],
                ],
                6 => [
                    'promo_item_id' => 6,
                    'promo_group_id' => 2,
                    'page_id' => '4,6,7',
                    'display_start_date' => '2014-01-03',
                    'start_date' => '2014-01-02',
                    'title' => 'Red',
                    'link' => 'http://youtu.be/PHqfwq033yQ',
                    'data' => 'foo',
                    'group' => [
                        'promo_group_id' => 2,
                        'title' => $this->groups[2],
                    ],
                ],
                7 => [
                    'promo_item_id' => 7,
                    'promo_group_id' => 2,
                    'page_id' => '4,6,7',
                    'display_start_date' => '2014-01-03',
                    'start_date' => '2014-01-02',
                    'title' => 'Blue',
                    'link' => 'https://www.youtube.com/embed/PHqfwq033yQ',
                    'data' => 'foo',
                    'group' => [
                        'promo_group_id' => 2,
                        'title' => $this->groups[2],
                    ],
                ],
                8 => [
                    'promo_item_id' => 8,
                    'promo_group_id' => 99999,
                    'page_id' => '4,6,7',
                    'display_start_date' => '2014-01-03',
                    'start_date' => '2014-01-02',
                    'title' => 'Circle',
                    'link' => 'https://youtube.com/v/PHqfwq033yQ',
                    'data' => 'foo',
                    'group' => [
                        'promo_group_id' => 99999,
                        'title' => 'Random Group Not In Group Reference Array',
                    ],
                ],
            ],
        ];
    }

    /**
     * @test
     */
    public function accountForMalformedArray()
    {
        $promos = [];

        // Pass in non-ideal $promos array
        $parsed = $this->parser->parse($promos, $this->groups);

        // Verify the array has been reorganized without a warning
        $this->assertArrayHasKey('one', $parsed);
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
    public function returnPromotionsWithoutGroupNames()
    {
        // Basic parse with no groups
        $parsed = $this->parser->parse($this->promos);

        // Assert that all keys are integers
        foreach (array_keys($parsed) as $key) {
            $this->assertInternalType('int', $key);
        }
    }

    /**
     * @test
     */
    public function shouldReturnASingleItem()
    {
        // Group 'one' should only return the first item
        $config = [
            'one' => 'first',
        ];

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
        $config = [
            'one' => 'limit:1',
        ];

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
        $config = [
            'one' => 'limit:2',
        ];

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
        $config = [
            'one' => 'randomize',
        ];

        // Parse the promotions based on the config
        $parsed = $this->parser->parse($this->promos, $this->groups, $config);

        // Verify the same number of elements in array
        $this->assertCount(5, $parsed['one']);
    }

    /**
     * @test
     */
    public function shouldLimitToPageThatExists()
    {
        // Group 'one' should only return the first item
        $config = [
            'one' => 'page_id:3',
        ];

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
        $config = [
            'one' => 'page_id:999',
        ];

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
        $config = [
            'one' => 'order:display_date_desc',
        ];

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
        $config = [
            'one' => 'order:display_date_asc',
        ];

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
        $config = [
            'one' => 'order:start_date_desc',
        ];

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
        $config = [
            'one' => 'order:start_date_asc',
        ];

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
        $config = [
            'one' => 'order:title_asc',
        ];

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
        $config = [
            'one' => 'order:title_desc',
        ];

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
        $config = [
            'one' => 'order:start_date_desc|limit:1',
        ];

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
    public function shouldReturnGroupKeys()
    {
        // Parse the promotions
        $parsed = $this->parser->parse($this->promos, $this->groups);

        // Parse for the group names
        $groups = $this->parser->groups($parsed, $this->groups);

        // Remove any group labels from the parsed group return
        $should_only_be_strings = array_intersect_key(array_keys($groups), array_values($this->groups));

        // Difference in keys should only be integers
        foreach ($should_only_be_strings as $key) {
            $this->assertInternalType('string', $key);
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

        // Remove any group labels from the parsed group return
        $should_only_be_ints = array_diff_key(array_keys($groups), array_values($this->groups));

        // Difference in keys should only be integers
        foreach ($should_only_be_ints as $key) {
            $this->assertInternalType('int', $key);
        }
    }

    /**
     * @test
     */
    public function should_be_blank_if_no_promotions_available()
    {
        $parsed = $this->parser->parse($this->promos, $this->groups);

        // Make sure a blank array is returned for the 3 group
        $this->assertEmpty($parsed['three']);
    }

    /**
     * @test
     */
    public function parsing_null_promos_with_configs_should_return_blank_groups()
    {
        $config = [
            'one' => 'first',
            'two' => 'randomize',
            'three' => 'limit:2|page_id:2|order:start_date_desc',
        ];

        $parsed = $this->parser->parse(null, $this->groups, $config);

        // Make sure all groups are blank
        foreach ($parsed as $group) {
            $this->assertCount(0, $group);
        }
    }

    /**
     * @test
     */
    public function parsing_youtube_should_always_contain_youtube_id_array_key()
    {
        $config = [
            'one' => 'youtube',
        ];

        $parsed = $this->parser->parse($this->promos, $this->groups, $config);

        foreach ($parsed['one'] as $item) {
            $this->assertArrayHasKey('youtube_id', $item);
        }
    }

    /**
     * @test
     */
    public function parsing_youtube_id_should_successfully_parse_all_variants_of_youtube_urls()
    {
        $config = [
            'two' => 'youtube',
        ];

        $parsed = $this->parser->parse($this->promos, $this->groups, $config);

        foreach ($parsed['two'] as $item) {
            $this->assertArrayHasKey('youtube_id', $item);
        }
    }
}
