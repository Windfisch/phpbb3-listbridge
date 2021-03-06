<?php

require_once(__DIR__ . '/../src/Util.php');

class UtilTest extends PHPUnit_Framework_TestCase {

  public function has_rfc822_specials_provider() {
    return array(
      array('Joel Uckelman', false),
      array('L.Tankersley', true),
      array('()<>@,;:\".[]', true)
    );
  }

  /** @dataProvider has_rfc822_specials_provider */
  public function test_has_rfc822_specials($string, $expected) {
    $this->assertEquals($expected, has_rfc822_specials($string));
  }

  public function rfc822_quote_provider() {
    return array(
      array('Joel Uckelman', '"Joel Uckelman"'),
      array('L.Tankersley',  '"L.Tankersley"'),
      array('"\foo\"',       "\"\\\"\\\\foo\\\\\\\"\"")
    );
  }

  /** @dataProvider rfc822_quote_provider */
  public function test_rfc822_quote($string, $expected) {
    $this->assertEquals($expected, rfc822_quote($string));
  }

  public function is_ascii_provider() {
    return array(
      array('', true),
      array('foo', true),
      array('Heizölrückstoßabdämpfung', false)
    );
  }

  /** @dataProvider is_ascii_provider */
  public function test_is_ascii($string, $expected) {
    $this->assertEquals($expected, is_ascii($string));   
  }

  public function utf8_quote_provider() {
    return array(
      array('', '=?UTF-8?B??='),
      array('foo', '=?UTF-8?B?Zm9v?='),
      array('Heizölrückstoßabdämpfung', '=?UTF-8?B?SGVpesO2bHLDvGNrc3Rvw59hYmTDpG1wZnVuZw==?=')
    );
  }

  /** @dataProvider utf8_quote_provider */
  public function test_utf8_quote($string, $expected) {
    $this->assertEquals($expected, utf8_quote($string));
  }

  public function utf8_quote_non_ascii_provider() {
    return array(
      array('', ''),
      array('foo', 'foo'),
      array('Heizölrückstoßabdämpfung', '=?UTF-8?B?SGVpesO2bHLDvGNrc3Rvw59hYmTDpG1wZnVuZw==?=')
    );
  }

  /** @dataProvider utf8_quote_non_ascii_provider */
  public function test_utf8_quote_non_ascii($string, $expected) {
    $this->assertEquals($expected, utf8_quote_non_ascii($string));
  }
}

?>
