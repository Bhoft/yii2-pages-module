<?php

namespace dmstr\modules\pages\tests\unit;

use Codeception\Util\Debug;
use dmstr\modules\pages\models\Tree;

class ModelTestCase extends \Codeception\Test\Unit
{
    public $appConfig = '/app/src/config/main.php';

    // tests
    public function testRootNode()
    {
        Debug::debug('root-'.md5($_SERVER['REQUEST_TIME']));
        $root = new Tree;
        #$root->id = ran;
        #$root->domain_id = 'test-root-node';
        $root->domain_id = 'root-'.md5($_SERVER['REQUEST_TIME']);
        $root->name = 'I am a Root-Node';
        $root->makeRoot();
        $root->save();
        $this->assertSame($root->errors, [], 'Root node has errors');
    }

    public function testMenuItems()
    {
        Debug::debug('root-'.md5($_SERVER['REQUEST_TIME']));
        $tree = Tree::getMenuItems('root-'.md5($_SERVER['REQUEST_TIME']));
        Debug::debug($tree);
    }

    /**
     * Test the virtual name_id attribute setter and getter for 'de' and 'en' root pages
     * @return mixed
     */
    public function testNameId()
    {
        $pages = Tree::findAll(
            [
                Tree::ATTR_DOMAIN_ID => 'root-'.md5($_SERVER['REQUEST_TIME']),
                Tree::ATTR_ACTIVE => Tree::ACTIVE,
                Tree::ATTR_VISIBLE => Tree::VISIBLE,
            ]
        );
        if ($pages) {
            foreach ($pages as $page) {
                $buildNameId = $page->domain_id.'_'.$page->access_domain;
                $this->assertSame($buildNameId, $page->name_id, 'NameID was not set properly');
            }
        } else {
            return $this->assertNotEmpty($pages, 'No Pages found!');
        }
    }

    public function testRemoveRootNode()
    {
        $root = Tree::findOne(['domain_id' => 'root-'.md5($_SERVER['REQUEST_TIME'])]);
        $root->removeNode(false);
        $this->assertSame($root->errors, [], 'Root node has errors');

        $root = Tree::findOne(['domain_id' => 'root-'.md5($_SERVER['REQUEST_TIME'])]);
        $this->assertNull($root);

    }

}
