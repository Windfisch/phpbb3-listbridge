<?php

require_once(__DIR__ . '/../src/build_post.php');

class build_post_test extends PHPUnit_Framework_TestCase {
  
  /** @dataProvider build_post_subject_data */
  public function test_build_post_subject($ltag, $ftag, $subject, $expected) {
    $this->assertEquals(
      $expected,
      build_post_subject($ltag, $ftag, $subject)
    );
  }

  public function build_post_subject_data() {
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
      array('[l]', '[f]', 'Re: Subject [l][f] Subject', 'Subject Subject'),
      array('[l]', '[f]', 'Edit:', '(no subject)'),
      array('[l]', '[f]', 'Edit: Re:', '(no subject)'),
      array('[l]', '[f]', 'Edit: Subject', 'Subject'),
      array('[l]', '[f]', 'Edit: Re: Subject', 'Subject'),
      array('[l]', '[f]', 'Edit: Re: Re: Re: Subject', 'Subject'),
      array('[l]', '[f]', 'Edit: [f] Subject', 'Subject'),
      array('[l]', '[f]', 'Edit: [f] [f] Subject', 'Subject'),
      array('[l]', '[f]', 'Edit: [f] [f] Subject [f]', 'Subject'),
      array('[l]', '[f]', '[l] [f] Edit: Re: Subject', 'Subject'),
      array('[l]', '[f]', 'Edit: Re: [l] [f] Subject', 'Subject'),
      array('[l]', '[f]', 'Edit: Re: Subject [l][f] Subject', 'Subject Subject')
    );
  }
}

?>
