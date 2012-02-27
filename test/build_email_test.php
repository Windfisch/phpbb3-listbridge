<?php

require_once(__DIR__ . '/../src/build_email.php');

class build_email_test extends PHPUnit_Framework_TestCase {
  /** @dataProvider build_email_text_data */
  public function test_build_email_text($text, $edit, $expected) {
    $this->assertEquals($expected, build_email_text($text, $edit));
  }

  public function build_email_text_data() {
    return array(
      array('foo bar', false, 'foo bar'),
      array('foo bar', true, "[This message has been edited.]

foo bar")
    );
  }
 
  public function test_build_email_footer() {
    $this->assertEquals(
      "
_______________________________________________
Read this topic online here:
http://www.example.com/viewtopic.php?p=42#p42",
      build_email_footer(42, 'http://www.example.com')
    );
  }

  /** @dataProvider build_email_from_data */
  public function test_build_email_from($name, $email, $expected) {
    $this->assertEquals($expected, build_email_from($name, $email));
  }

  public function build_email_from_data() {
    return array(
      array('Heizölrückstoßabdämpfung', 'foo@example.com', '=?UTF-8?B?SGVpesO2bHLDvGNrc3Rvw59hYmTDpG1wZnVuZw==?= <foo@example.com>'),
      array('Joel Uckelman', 'uckelman@nomic.net', 'Joel Uckelman <uckelman@nomic.net>'),
      array('L.Tankersley', 'leland53@comcast.net', '"L.Tankersley" <leland53@comcast.net>')
    );
  }

  /** @dataProvider build_email_subject_data */
  public function test_build_email_subject($ftag, $re, $subject, $expected) {
    $this->assertEquals(
      $expected,
      build_email_subject($ftag, $re, $subject)
    );
  }

  public function build_email_subject_data() {
    return array(
      array('[f]', false, '', '[f] (no subject)'),
      array('[f]', true, '', 'Re: [f] (no subject)'),
      array('[f]', false, 'Subject', '[f] Subject'),
      array('[f]', true, 'Subject', 'Re: [f] Subject'),
    );
  }

  protected $default_headers = array( 
    'To'          => 'messages@vassalengine.org',
    'From'        => 'Joel Uckelman <uckelman@nomic.net>',
    'Sender'      => 'forum-bridge@vassalengine.org',
    'Subject'     => 'Test message',
    'Date'        => 'Sun, 31 Oct 2010 08:46:00 -0700', 
    'Message-ID'  => '<20100302094228.33F0310091@charybdis.ellipsis.cx>',
    'X-BeenThere' => 'http://www.example.com',
    'In-Reply-To' => '<1267473003.m2f.17543@www.vassalengine.org>',
    'References'  => '<1267171317.m2f.17507@www.vassalengine.org> <1267473003.m2f.17543@www.vassalengine.org>'
  );

  protected $default_headers_params = array(
    'Joel Uckelman',
    'uckelman@nomic.net',
    'messages@vassalengine.org',
    'forum-bridge@vassalengine.org',
    'Test message',
    false,
    1288539960,
    '<20100302094228.33F0310091@charybdis.ellipsis.cx>',
    'http://www.example.com',
    '<1267473003.m2f.17543@www.vassalengine.org>',
    '<1267171317.m2f.17507@www.vassalengine.org> <1267473003.m2f.17543@www.vassalengine.org>'
  );

  protected function call_build_email_headers(array $headers, array $params) {
    date_default_timezone_set('America/Phoenix');
    $this->assertEquals(
      $headers,
      build_email_headers(
        $params[0],
        $params[1],
        $params[2],
        $params[3],
        $params[4],
        $params[5],
        $params[6],
        $params[7],
        $params[8],
        $params[9],
        $params[10]
      )
    );
  }

  public function test_build_email_headers() {
    $headers = $this->default_headers;
    $headers_params = $this->default_headers_params;
    $this->call_build_email_headers($headers, $headers_params);
  }

  public function test_build_email_headers_no_in_reply_to() {
    $headers = $this->default_headers;
    $headers_params = $this->default_headers_params;

    unset($headers['In-Reply-To']);
    $headers_params[9] = null;

    $this->call_build_email_headers($headers, $headers_params);
  }

  public function test_build_email_headers_no_references() {
    $headers = $this->default_headers;
    $headers_params = $this->default_headers_params;

    unset($headers['References']);
    $headers_params[10] = null;

    $this->call_build_email_headers($headers, $headers_params);
  }

  public function test_build_email_headers_utf8_subject() {
    $headers = $this->default_headers;
    $headers_params = $this->default_headers_params;
    
    $headers['Subject'] = '=?UTF-8?B?SGVpesO2bHLDvGNrc3Rvw59hYmTDpG1wZnVuZw==?=';
    $headers_params[4] = 'Heizölrückstoßabdämpfung';

    $this->call_build_email_headers($headers, $headers_params);
  }

  public function test_build_email_headers_utf8_username() {
    $headers = $this->default_headers;
    $headers_params = $this->default_headers_params;
    
    $headers['From'] = '=?UTF-8?B?SGVpesO2bHLDvGNrc3Rvw59hYmTDpG1wZnVuZw==?= <uckelman@nomic.net>'; 
    $headers_params[0] = 'Heizölrückstoßabdämpfung';

    $this->call_build_email_headers($headers, $headers_params);
  }

  public function test_build_email_body_no_attachments() {
    $headers = array();
    $text = 'This is some test text.';
    $footer = "
_______________________________________________
Read this topic online here:
http://www.example.com/viewtopic.php?p=42#p42";
    $attachments = null;

    $body = build_email_body($headers, $text, $attachments, $footer);


    $this->assertEquals('text/plain; charset=UTF-8; format=flowed', $headers['Content-Type']);
    $this->assertEquals('8bit', $headers['Content-Transfer-Encoding']);
    $this->assertEquals("$text\n$footer", $body);
  }

  public function test_build_email_body_attachments() {
    // FIXME: This is kind of a complex test to write, because the result
    // is a Mail_mimePart object.
    $this->markTestIncomplete();
  }
}

?>
