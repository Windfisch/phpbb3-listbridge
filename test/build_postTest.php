<?php

require_once(__DIR__ . '/../src/build_post.php');

class build_post_test extends PHPUnit_Framework_TestCase {
  
  /** @dataProvider build_post_subject_data */
  public function test_build_post_subject($ltag, $ftag, $subject, $reply, $expected) {
    $this->assertEquals(
      $expected,
      build_post_subject($ltag, $ftag, $subject, $reply)
    );
  }

  public function build_post_subject_data() {
    return array(
      array('[l]', '[f]', '', false, '(no subject)'),
      array('[l]', '[f]', 'Re:', false, '(no subject)'),
      array('[l]', '[f]', 'Subject', false, 'Subject'),
      array('[l]', '[f]', 'Re: Subject', false, 'Subject'),
      array('[l]', '[f]', 'Re: Re: Re: Subject', false, 'Subject'),
      array('[l]', '[f]', '[f] Subject', false, 'Subject'),
      array('[l]', '[f]', '[f] [f] Subject', false, 'Subject'),
      array('[l]', '[f]', '[f] [f] Subject [f]', false, 'Subject'),
      array('[l]', '[f]', '[l] [f] Re: Subject', false, 'Subject'),
      array('[l]', '[f]', 'Re: [l] [f] Subject', false, 'Subject'),
      array('[l]', '[f]', 'Re: Subject [l][f] Subject', false, 'Subject Subject'),
      array('[l]', '[f]', 'Edit:', false, '(no subject)'),
      array('[l]', '[f]', 'Edit: Re:', false, '(no subject)'),
      array('[l]', '[f]', 'Edit: Subject', false, 'Subject'),
      array('[l]', '[f]', 'Edit: Re: Subject', false, 'Subject'),
      array('[l]', '[f]', 'Edit: Re: Re: Re: Subject', false, 'Subject'),
      array('[l]', '[f]', 'Edit: [f] Subject', false, 'Subject'),
      array('[l]', '[f]', 'Edit: [f] [f] Subject', false, 'Subject'),
      array('[l]', '[f]', 'Edit: [f] [f] Subject [f]', false, 'Subject'),
      array('[l]', '[f]', '[l] [f] Edit: Re: Subject', false, 'Subject'),
      array('[l]', '[f]', 'Edit: Re: [l] [f] Subject', false, 'Subject'),
      array('[l]', '[f]', 'Edit: Re: Subject [l][f] Subject', false, 'Subject Subject'),
      array('[l]', '[f]', '', true, 'Re: (no subject)'),
      array('[l]', '[f]', 'Re:', true, 'Re: (no subject)'),
      array('[l]', '[f]', 'Subject', true, 'Re: Subject'),
      array('[l]', '[f]', 'Re: Subject', true, 'Re: Subject'),
      array('[l]', '[f]', 'Re: Re: Re: Subject', true, 'Re: Subject'),
      array('[l]', '[f]', '[f] Subject', true, 'Re: Subject'),
      array('[l]', '[f]', '[f] [f] Subject', true, 'Re: Subject'),
      array('[l]', '[f]', '[f] [f] Subject [f]', true, 'Re: Subject'),
      array('[l]', '[f]', '[l] [f] Re: Subject', true, 'Re: Subject'),
      array('[l]', '[f]', 'Re: [l] [f] Subject', true, 'Re: Subject'),
      array('[l]', '[f]', 'Re: Subject [l][f] Subject', true, 'Re: Subject Subject'),
      array('[l]', '[f]', 'Edit:', true, 'Re: (no subject)'),
      array('[l]', '[f]', 'Edit: Re:', true, 'Re: (no subject)'),
      array('[l]', '[f]', 'Edit: Subject', true, 'Re: Subject'),
      array('[l]', '[f]', 'Edit: Re: Subject', true, 'Re: Subject'),
      array('[l]', '[f]', 'Edit: Re: Re: Re: Subject', true, 'Re: Subject'),
      array('[l]', '[f]', 'Edit: [f] Subject', true, 'Re: Subject'),
      array('[l]', '[f]', 'Edit: [f] [f] Subject', true, 'Re: Subject'),
      array('[l]', '[f]', 'Edit: [f] [f] Subject [f]', true, 'Re: Subject'),
      array('[l]', '[f]', '[l] [f] Edit: Re: Subject', true, 'Re: Subject'),
      array('[l]', '[f]', 'Edit: Re: [l] [f] Subject', true, 'Re: Subject'),
      array('[l]', '[f]', 'Edit: Re: Subject [l][f] Subject', true, 'Re: Subject Subject')
    );
  }
}

?>
