<?php

#
# forum-list bridge 
# Copyright (C) 2010 Joel Uckelman
#
# This program is free software: you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation, either version 3 of the License, or
# (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with this program. If not, see <http://www.gnu.org/licenses/>.
#

class PhpBB3ImplTest extends PHPUnit_Framework_TestCase {

  protected function setUp() {
    $this->markTestSkipped();
  }

  /**
   * phpBB3 uses many globals; due to the way tests are run, we cannot
   * easily get these globals into the right scope so that the methods
   * called by our tests can see them. Therefore, we use this function
   * to run tests externally and report back on the results.
   */
  protected function exec_kludge($run) {
    $prog = <<<EOF
try {
  require_once("src/PhpBB3Impl.php");
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
      array(0, false, null),
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
   * @dataProvider providerTopicStatus
   */
  public function testTopicStatus($topic_id, $expected, $ex) {
    if ($ex) $this->setExpectedException($ex);
    $run = 'topicStatus(' . $topic_id . ')';
    $this->assertEquals($expected, $this->exec_kludge($run));
  }

  public function providerTopicStatus() {
    return array(
      array('bogus', null, 'Exception'),
      array(0, false, null),
      array(1, 0, null)
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
      array()
    );
  }

  /**
   * @dataProvider providerGetAttachmentData
   */
  public function testGetAttachmentData($attach_id, $expected, $ex) {
    $this->markTestIncomplete();

    if ($ex) $this->setExpectedException($ex);
    $run = 'getAttachmentData(' . $attach_id . ')';
    $this->assertEquals($expected, $this->exec_kludge($run));
  }

  public function providerGetAttachmentData() {
    return array(
      array()
    );
  }
}
