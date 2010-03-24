<?php

require_once('PHPUnit/Framework.php');

class PhpBB3LibTest extends PHPUnit_Framework_TestCase {

  /**
   * phpBB3 uses many globals; due to the way tests are run, we cannot
   * easily get these globals into the right scope so that the methods
   * called by our tests can see them. Therefore, we use this function
   * to run tests externally and report back on the results.
   */
  protected function exec_kludge($run) {
    $prog = <<<EOF
try {
  require_once("src/PhpBB3Lib.php");
  \$result = serialize($run);
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
   * @dataProvider provider_get_user_id
   */
  public function test_get_user_id($from, $expected, $ex) {
    if ($ex) $this->setExpectedException($ex);
    $run = 'get_user_id("' . $from . '")';
    $this->assertEquals($expected, $this->exec_kludge($run));
  }

  public function provider_get_user_id() {
    return array(
      array('bogus',              null, 'PHPUnit_Framework_Error'),
      array('uckelman@nomic.net', 2,    null)
    );
  }

  /**
   * @dataProvider provider_get_user_name
   */
  public function test_get_user_name($id, $expected, $ex) {
    if ($ex) $this->setExpectedException($ex);
    $run = 'get_user_name(' . $id . ')';
    $this->assertEquals($expected, $this->exec_kludge($run));
  }

  public function provider_get_user_name() {
    return array(
      array(0, null,    'PHPUnit_Framework_Error'),
      array(2, 'admin', null                     )
    );
  }

  /**
   * @dataProvider provider_get_topic_id
   */
  public function test_get_topic_id($post_id, $expected, $ex) {
    if ($ex) $this->setExpectedException($ex);
    $run = 'get_topic_id(' . $post_id . ')';
    $this->assertEquals($expected, $this->exec_kludge($run));
  }

  public function provider_get_topic_id() {
    return array(
      array(0, null,    'PHPUnit_Framework_Error'),
      array(2, 2,       null                     )
    );
  }
}
