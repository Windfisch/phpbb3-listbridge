<?php

require_once('PHPUnit/Framework.php');

require_once(__DIR__ . '/../src/build_post.php');

class build_post_test extends PHPUnit_Framework_TestCase {
  
  /** @dataProvider buildPostSubjectProvider */
  public function testBuildPostSubject($ltag, $ftag, $subject, $expected) {
    $this->assertEquals(
      $expected,
      build_post_subject($ltag, $ftag, $subject)
    );
  }

  public function buildPostSubjectProvider() {
    return array(
      array('[l]', '[f]', '', '(no subject)'),
      array('[l]', '[f]', 'Re:', '(no subject)'),
      array('[l]', '[f]', 'Subject', 'Subject'),
      array('[l]', '[f]', 'Re: Subject', 'Subject'),
      array('[l]', '[f]', 'Re: Re: Re: Subject', 'Subject'),
      array('[l]', '[f]', '[f] Subject', 'Subject'),
      array('[l]', '[f]', '[f] [f] Subject', 'Subject'),
      array('[l]', '[f]', '[f] [f] Subject [f]', 'Subject'),
      array('[l]', '[f]', '[l] [f] Re: Subject', 'Subject'),
      array('[l]', '[f]', 'Re: [l] [f] Subject', 'Subject'),
      array('[l]', '[f]', 'Re: Subject [l][f] Subject', 'Subject Subject')
    );
  }
}

?>
