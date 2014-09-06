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
                    'page_id' => '1,2,3,4',
                    'display_start_date' => '2014-01-01',
                    'start_date' => '2014-01-01',
                    'data' => 'foo',
                ),
                '2' => array (
                    'promo_item_id' => '2',
                    'promo_group_id' => '1',
                    'page_id' => '2,3,4',
                    'display_start_date' => '2014-01-02',
                    'start_date' => '2014-01-03',
                    'data' => 'foo',
                ),
                '3' => array (
                    'promo_item_id' => '3',
                    'promo_group_id' => '1',
                    'page_id' => '3,4',
                    'display_start_date' => '2014-01-03',
                    'start_date' => '2014-01-02',
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
    public function shouldShuffle()
    {
        // Group 'one' should only return the first item
        $config = array(
            'one' => 'shuffle',
        );

        // Parse the promotions based on the config
        $parsed = $this->parser->parse($this->promos, $this->groups, $config);

        // Didn't setup a mock yet so just testing there are the same number of elements in returned array
        $this->assertCount(3, $parsed['one']);
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
            'one' => 'page_id:6',
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

        // There should be the same number of elements started with
        $this->assertCount(3, $parsed['one']);

        // The display_date of the first should be greater than the last (quick check)
        $first = current($parsed['one']);
        $last = end($parsed['one']);

        $this->assertGreaterThanOrEqual( strtotime($last['display_start_date']), strtotime($first['display_start_date']) );


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

        // There should be the same number of elements started with
        $this->assertCount(3, $parsed['one']);

        // The display_date of the first should be greater than the last (quick check)
        $first = current($parsed['one']);
        $last = end($parsed['one']);

        $this->assertGreaterThanOrEqual( strtotime($last['start_date']), strtotime($first['start_date']) );


    }
}
