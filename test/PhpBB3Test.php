<?php

require_once('PHPUnit/Framework.php');

class PhpBB3Test extends PHPUnit_Framework_TestCase {

  /**
   * phpBB3 uses many globals; due to the way tests are run, we cannot
   * easily get these globals into the right scope so that the methods
   * called by our tests can see them. Therefore, we use this function
   * to run tests externally and report back on the results.
   */
  protected function exec_kludge($run) {
    $prog = <<<EOF
try {
  require_once("src/PhpBB3.php");
  \$phpBB = new PhpBB3();
  \$result = serialize(\$phpBB->$run);
}
catch (Exception \$e) {
  \$result = serialize(\$e);
}

print \$result;
EOF;

    $result = unserialize(exec('php -r \'' . $prog . '\''));

    if ($result instanceof Exception) {
      throw $result;
    }

    return $result;    
  }

  /**
   * @dataProvider providerGetUserId
   */
  public function testGetUserId($from, $expected, $ex) {
    if ($ex) $this->setExpectedException($ex);
    $run = 'getUserId("' . $from . '")';
    $this->assertEquals($expected, $this->exec_kludge($run));
  }

  public function providerGetUserId() {
    return array(
      array(null, null, 'Exception'),
      array('bogus', false, null),
      array('uckelman@nomic.net', 2,    null)
    );
  }

  /**
   * @dataProvider providerGetUserName
   */
  public function testGetUserName($id, $expected, $ex) {
    if ($ex) $this->setExpectedException($ex);
    $run = 'getUserName(' . $id . ')';
    $this->assertEquals($expected, $this->exec_kludge($run));
  }

  public function providerGetUserName() {
    return array(
      array(0, null, 'Exception'),
      array(2, 'admin', null                     )
    );
  }

  /**
   * @dataProvider providerGetTopicId
   */
  public function testGetTopicId($post_id, $expected, $ex) {
    if ($ex) $this->setExpectedException($ex);
    $run = 'getTopicId(' . $post_id . ')';
    $this->assertEquals($expected, $this->exec_kludge($run));
  }

  public function providerGetTopicId() {
    return array(
      array(null, null, 'Exception'),
      array('bogus', false, null),
      array(2, 2, null)
    );
  }

  /**
   * @dataProvider providerGetTopicAndForumIds
   */
  public function testGetTopicAndForumIds($post_id, $expected, $ex) {
    if ($ex) $this->setExpectedException($ex);
    $run = 'getTopicAndForumIds(' . $post_id . ')';
    $this->assertEquals($expected, $this->exec_kludge($run));
  }

  public function providerGetTopicAndForumIds() {
    return array(
      array(null, null, 'Exception'),
      array('bogus', false, null),
      array(2, array('topic_id' => 2, 'forum_id' => 2), null)
    );
  }

  /**
   * @dataProvider providerForumExists
   */
  public function testForumExists($forum_id, $expected, $ex) {
    if ($ex) $this->setExpectedException($ex);
    $run = 'forumExists(' . $forum_id . ')';
    $this->assertEquals($expected, $this->exec_kludge($run));
  }

  public function providerForumExists() {
    return array(
      array('bogus', null, 'Exception'),
      array(0, false, null),
      array(2, true, null)
    );
  }

  /**
   * @dataProvider providerTopicExists
   */
  public function testTopicExists($topic_id, $expected, $ex) {
    if ($ex) $this->setExpectedException($ex);
    $run = 'topicExists(' . $topic_id . ')';
    $this->assertEquals($expected, $this->exec_kludge($run));
  }

  public function providerTopicExists() {
    return array(
      array('bogus', null, 'Exception'),
      array(0, false, null),
      array(1, true, null)
    );
  }

  /**
   * @dataProvider providerGetPostTime
   */
  public function testGetPostTime($post_id, $expected, $ex) {
    $this->markTestIncomplete();

    if ($ex) $this->setExpectedException($ex);
    $run = 'getPostTime(' . $post_id . ')';
    $this->assertEquals($expected, $this->exec_kludge($run));
  }

  public function providerGetPostTime() {
    return array(
    );
  }

  /**
   * @dataProvider providerGetFirstPostId
   */
  public function testGetFirstPostId($topic_id, $expected, $ex) {
    $this->markTestIncomplete();

    if ($ex) $this->setExpectedException($ex);
    $run = 'getFirstPostId(' . $topic_id . ')';
    $this->assertEquals($expected, $this->exec_kludge($run));
  }

  public function providerGetFirstPostId() {
    return array(
    );
  }
}
