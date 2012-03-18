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

  function test_strip_list_footer() {
    $exp = "Thus spake uckelman:
> The changes appear to have munged SELinux permissions for the list
> bridge. Trying again...
> 

And checking that the bridge works in the other direction...

-- 
J.
";
   
    $fpat = "/^_______________________________________________\nmessages mailing list\nmessages@vassalengine.org\nhttp:\/\/www.vassalengine.org\/mailman\/listinfo\/messages.*/ms";

    $this->assertEquals($exp, strip_list_footer("Thus spake uckelman:
> The changes appear to have munged SELinux permissions for the list
> bridge. Trying again...
> 

And checking that the bridge works in the other direction...

-- 
J.
_______________________________________________
messages mailing list
messages@vassalengine.org
http://www.vassalengine.org/mailman/listinfo/messages
", $fpat));

    $msg = new MailmanMessage(file_get_contents(__DIR__ . '/326'));
    list($text, ) = $msg->getFlattenedParts();

    $this->assertEquals($exp, strip_list_footer($text, $fpat));
  }
}

?>
