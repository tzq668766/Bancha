<?php
/**
 * BanchaFormTest file.
 *
 * Bancha Project : Combining Ext JS and CakePHP (http://banchaproject.org)
 * Copyright 2011, Roland Schuetz, Kung Wong, Andreas Kern, Florian Eckerstorfer
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2011 Roland Schuetz, Kung Wong, Andreas Kern, Florian Eckerstorfer
 * @link          http://banchaproject.org Bancha Project
 * @since         Bancha v1.0
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @author        Florian Eckerstorfer <f.eckerstorfer@gmail.com>
 */

App::uses('BanchaDispatcher', 'Bancha.Bancha/Routing');
App::uses('BanchaRequestCollection', 'Bancha.Bancha/Network');

require_once dirname(__FILE__) . '/../../../Model/Behavior/BanchaBehavior.php';

// TODO: refactor to use real test models.
require_once dirname(__FILE__) . '/ArticlesController.php';

/**
 * BanchaFormTest
 *
 * @package bancha.libs
 */
class BanchaFormTest extends CakeTestCase {
	
	public function setUp() {
		parent::setUp();
	}
	
	function tearDown() {
		parent::tearDown();
		ClassRegistry::flush();
	}
	
/**
 * Tests the 'add' CRUD operation using the full CakePHP stack.
 *
 */
	public function testAdd() {
		$postData = array(
			'extAction'		=> 'Articles',
			'extMethod'		=> 'create',
			'extTID'			=> 1,
			'title'			=> 'Hello World',
			'body'			=> 'foobar',
			'published'		=> false,
			'user_id'		=> 1,
		);
		$dispatcher = new BanchaDispatcher();
		$responses = json_decode($dispatcher->dispatch(
			new BanchaRequestCollection('', $postData), array('return' => true)
		));

		$this->assertNotNull($responses[0]->data->id);
		$this->assertEquals('Hello World', $responses[0]->data->title);
		$this->assertEquals(false, $responses[0]->data->published);
		$this->assertEquals(1, $responses[0]->data->user_id);
		
		// Clean up operations: delete article
		$article = new Article();
		$article->id = $responses[0]->data->id;
		$article->delete();
	}
	
	public function testEdit() {
		// Preparation: create article
		$article = new Article();
		$article->create();
		$article->save(array('title' => 'foo'));
		
		$postData = array(
			'extAction'		=> 'Articles',
			'extMethod'		=> 'update',
			'extTID'			=> 1,
			'id'			=> $article->id,
			'title'			=> 'foobar',
			'published'		=> true,
		);
		$dispatcher = new BanchaDispatcher();
		$responses = json_decode($dispatcher->dispatch(
			new BanchaRequestCollection('', $postData), array('return' => true)
		));
		
		$this->assertEquals($article->id, $responses[0]->data->id);
		$this->assertEquals('foobar', $responses[0]->data->title);
		$this->assertEquals(true, $responses[0]->data->published);
		
		// Clean up operations: delete article
		$article->delete();
	}
	
	public function testDelete() {
		// Preparation: create article
		$article = new Article();
		$article->create();
		$article->save(array('title' => 'foo'));
		
		// Let's begin with the real test.
		$postData = array(
			'extAction'		=> 'Articles',
			'extMethod'		=> 'destroy',
			'extTID'		=> 1,
			'id' 			=> $article->id,
		);
		$dispatcher = new BanchaDispatcher();
		$responses = json_decode($dispatcher->dispatch(
			new BanchaRequestCollection('', $postData), array('return' => true)
		));
		
		$this->assertEquals(array(), $responses[0]->data);
	}
	
}
