<?php

class BBCodeParser {

  const TEXT = 0;
  const CTAG = 1;
  const OTAG = 2;
  const DONE = 3;



  function parse($in, $uid) {

    $arg_stack = array();
    $contents_stack = array();

    $fn_number = 1;
    $fn = array();

    $i = 0;
    $len = strlen($in);

    $indent = '';
    $list_conter_stack = array();

    $out = '';

    $state = self::TEXT;

    while ($state != self::DONE) {
      switch ($state) {
      case self::TEXT:
        # find next occurance of uid
        $ustart = strpos($in, ":$uid", $i);
        if ($ustart === false) {
          #  no more tags, all done
          $out .= substr($in, $i);
          $state = self::DONE;
        }
        else {
          # locate next tag
          $tstart = strrpos($in, '[', $ustart-$len);
          $tag = substr($in, $tstart+1, $ustart-$tstart-1);

          # determine whether it's an open or close tag
          if ($tag[0] == '/') {
            $state = self::CTAG;
            $tag = substr($tag, 1); 
          }
          else {
            $state = self::OTAG;
          }
         
          # copy leading text to output 
          $out .= substr($in, $i, $tstart-$i);

          # advance past ":$uid]" to next unparsed character
          $i = $ustart + strlen($uid) + 2;
        }
        break;

      case self::OTAG:
        # split tag into tag name and argument, if any
        $arg = false;
        $epos = strpos($tag, '=');
        if ($epos !== false) {
          $tag = substr($tag, 0, $epos);
          $arg = substr($tag, $epos+1);
        }

        $arg_stack[] = $arg;

        switch ($tag) {
        case 'b':
          $out .= '__';
          break;
        case 'u':
        case 'i':
          $out .= '_';
          break;
        case 'url':
        case 'email':
          # nothing to do on opening
          break;
        case 'quote':
          break;
        case 'code':
          break;
        case 'list':
          $out .= "\n";
          $indent .= ' ';
          
          switch ($arg) {
          case '1': $list_counter_stack[] = 1;   break;
          case 'a': $list_counter_stack[] = 'a'; break;
          default:  $list_counter_stack[] = '*'; break; 
          }

          break;
        case '*':
          $out .= "\n" . $indent;

          $c = array_pop($list_counter_stack);
          if ($c == '*') {
          }
          else if (is_int($c)) {
            $out .= $c . '. ';
            $list_counter_stack[] = $c + 1;
          }
          else if ($c == '*') {
            $out .= $c . ' ';
            $list_counter_stack[] = '*';
          }
          else {
            $out .= $c . '. ';
            $list_counter_stack[] = chr(ord($c)+1);
          }
          break;
        case 'img':
          break;
        case 'attachment':
          break;
        case 'color':
        case 'size':
          # ignored
          break;
        default:
          throw new Exception('Unrecognized open tag: ' . $tag);
        }

        $state = self::TEXT;
        break;

      case self::CTAG:
        $arg = array_pop($arg_stack);

        switch ($tag) {
        case 'b':
          $out .= '__';
          break;
        case 'u':
        case 'i':
          $out .= '_';
          break;
        case 'url':
        case 'email':
          if ($arg !== false) {
            # built footnotes for links with text
            $out .= '[' . $fn_number++ .']';
            $fn[] = $arg;
          }
          break;
        case 'quote':
          break;
        case 'code':
          break;
        case 'list':
          $out .= "\n";
          $indent = substr($indent, -1);
          array_pop($list_counter_stack);
          break;
        case '*':
          break;
        case 'img':
          break;
        case 'attachment':
          break;
        case 'color':
        case 'size':
          # ignored
          break;
        default:
          throw new Exception('Unrecognized close tag: ' . $tag);
        }

        $state = self::TEXT;
        break;
      }
    }

    if (!empty($fn)) {
      # build footnotes
      $out .= "\n";

      for ($i = 0; $i < count($fn); ++$i) {
        $out .= "\n[" . ($i+1) . '] ' . $fn[$i];
      }
    }

    return $out;
  }

/*
    $tstack = array();
    $ctag = $otag = '';

    $state = TEXT;

    $i = 0;
    $len = strlen($in);

    while ($i < $len) {
      $c = $in[$i];

      switch ($state) {
      case TEXT:
        switch ($c) {
        case '[':
          # left bracket might start a tag
          $state = LBRAC;
          ++$i;
          break;
        default:
          # otherwise copy this character to the output
          $out .= $c;
          ++$i;
        }
        break;

      case LBRAC:
        switch ($c) {
        case '/':
          # might be a closing tag
          $ctag = '';
          $state = CTAG;
          ++$i;
          break;
        case ']':
          # '[]' is not a tag, copy to output
          $out .= '[]';
          $state = TEXT;
          ++$i;
          break;
        case '[':
          # previous '[' did NOT start a tag, copy it to output
          # this '[' might start a tag, don't change state
          $out .= '[';
          ++$i;
          break;
        default:
          # might be an opening tag
          $otag = '';
          $state = OTAG;
        }
        break;

      case OTAG:
        switch ($c) {
        case '[':
          # '[' cannot appear in tag names, so this is not a tag,
          # but '[' might start a real tag
          $out .= '[' . $otag;
          $otag = '';
          $state = OTAG;
          ++$i;
          break;
        case '=':
          # start of a tag argument
          $targ = '';
          $state = TARG;
          ++$i;
          break;
        case ']':
          # end of tag
          $state = OPEN_TAG;
          break;
        default:
          # continue accumulating tag name
          $otag .= $c;
          ++$i;
        }
        break;

      case TARG:
        switch ($c) {
        case ']':
          # end of tag
          $state = OPEN_TAG;
          break;
        case '[':
          # '[' cannot appear in tag names, so this is not a tag,
          # but '[' might start a real tag
          $out .= '[' . $otag . '=' . $targ;
          $otag = $targ = '';
          $state = OTAG;
          ++$i;
          break;
        default:
          # continue accumulating tag argument
          $targ .= $c;
          ++$i;
        }
        break;

      case CTAG:
        switch ($c) {       
        case '[':
          # '[' cannot appear in tag names, so this is not a tag,
          # but '[' might start a real tag
          $out .= '[/' . $ctag;
          $ctag = '';
          $state = OTAG;
          ++$i;
          break;
        case ']':
          # end of tag
          $state = CLOSE_TAG;
          break;
        default:
          # continue accumulating tag name
          $ctag .= $c;
          ++$i;
        }
        break;

      case OPEN_TAG:
        switch ($otag) {
        case 'b':
        case 'u':
        case 'i':
          $tstack[] = $otag;
          break;
        case 'color':
          break;
        case 'size':
          break;
        case 'quote':
          break;
        case 'code':
          break;
        case 'list':
          break;
        case '*':
          break;
        case 'url':
          break;
        case 'email':
          break;
        case 'img':
          break;
        case 'attachment':
          break;
        default:
          # unrecognized tag, make it text
          $out .= '[' . $otag . ']';
          $state = TEXT; 
        }

        ++$i;
        break;

      case CLOSE_TAG:
        $otag = array_pop($tstack);
        if ($ctag != $otag) {
          # badly nested tags
# TODO 
        }

        switch ($ctag) {
        case 'b':
          break;
        case 'u':
          break;
        case 'i':
          break;
        case 'color':
          break;
        case 'size':
          break;
        case 'quote':
          break;
        case 'code':
          break;
        case 'list':
          break;
        case 'url':
          break;
        case 'email':
          break;
        case 'img':
          break;
        case 'attachment': 
          break;
        default:
          # unrecognized tag, make it text
          $out .= '[/' . $ctag . ']';
          $state = TEXT; 
        }

        ++$i;
        break;
      }
    }
*/


}

?>
