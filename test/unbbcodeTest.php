<?php

require_once('PHPUnit/Framework.php');

class unbbcodeTest extends PHPUnit_Framework_TestCase {

  /**
   * phpBB3 uses many globals; due to the way tests are run, we cannot
   * easily get these globals into the right scope so that the methods
   * called by our tests can see them. Therefore, we use this function
   * to run tests externally and report back on the results.
   */
  protected function exec_kludge($run) {
    $prog = <<<EOF
try {
  require_once("src/unbbcode.php");
  \$unbbcode = new unbbcode();
  \$result = serialize(\$unbbcode->$run);
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
   * @dataProvider provider_bbcode_second_pass
   */
  public function test_bbcode_second_pass($msg, $uid, $bitfield,
                                          $expected, $ex) {


  }

  public function provider_bbcode_second_pass() {
    return array(
      array('[b]Test[/b]', '', false, '<b>Test</b>', null)
    );
  }
?>
