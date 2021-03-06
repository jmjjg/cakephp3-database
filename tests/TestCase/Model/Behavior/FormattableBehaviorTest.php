<?php

/**
 * Source code for the Database.FormattableBehavior unit test class.
 *
 */
namespace Database\Test\TestCase\Model\Behavior;

use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

require_once Plugin::classPath('Database') . DS . '..' . DS . 'tests' . DS . 'Fixture' . DS . 'items_table.php';

/**
 * The class Database.FormattableBehaviorTest is responsible for testing the
 * Database.FormattableBehavior class.
 *
 * @fixme: be sure to remove App global config for the tests
 */
class FormattableBehaviorTest extends TestCase
{

    /**
     * fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.Database.Items',
    ];

    /**
     * Original intl.default_locale.
     *
     * @var string
     */
    public $default_locale = null;

    /**
     * setUp() method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->default_locale = ini_get('intl.default_locale');
        $this->Items = TableRegistry::get('Items');
    }

    /**
     * tearDown() method
     *
     * @return void
     */
    public function tearDown()
    {
        parent::tearDown();
        ini_set('intl.default_locale', $this->default_locale);
        unset($this->Items);
        TableRegistry::remove('Items');
    }

    /**
     * Test default configuration
     */
    public function testDefaultConfig()
    {
		if(true === version_compare(Configure::version(), '3.1.0', '<')) {
			$this->markTestSkipped('CakePHP version < 3.1.0');
		}

        ini_set('intl.default_locale', 'fr_FR');
        $this->Items->addBehavior(
            'DatabaseFormattable',
            [
                'className' => 'Database.Formattable'
            ]
        );

        $data = [
            'id' => ' ',
            'name' => ' ',
            'parent_id' => ' 7_5 ',
            'price' => '3,65',
            'weight' => '1 000,66',
        ];
        $item = $this->Items->newEntity($data);

        $this->assertEquals(null, $item->id);
        $this->assertEquals(null, $item->name);
        $this->assertEquals(5, $item->parent_id);
        $this->assertEquals(3.65, $item->price);
        $this->assertEquals(1000.66, $item->weight);
    }

    /**
     * Test alternate configuration
     */
    public function testAlternateConfig()
    {
		if(true === version_compare(Configure::version(), '3.1.0', '<')) {
			$this->markTestSkipped('CakePHP version < 3.1.0');
		}

        ini_set('intl.default_locale', 'fr_FR');
        $this->Items->addBehavior(
            'DatabaseFormattable',
            [
                'className' => 'Database.Formattable',
                'cache' => false,
                'formatters' => [
                    '\\Database\\Utility\\Formatter::formatTrim' => false
                ]
            ]
        );

        $data = [
            'id' => ' ',
            'name' => ' ',
            'parent_id' => ' 7_5 ',
            'price' => '3,65',
            'weight' => '1 000,66',
        ];
        $item = $this->Items->newEntity($data);

        $this->assertEquals(null, $item->id);
        $this->assertEquals(' ', $item->name);
        $this->assertEquals(5, $item->parent_id);
        $this->assertEquals(3.65, $item->price);
        $this->assertEquals(1000.66, $item->weight);
    }
}
