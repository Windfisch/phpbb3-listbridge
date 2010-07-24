<?php

class BBCodeParser {

  const TEXT = 0;
  const WHSP = 1;
  const CTAG = 2;
  const OTAG = 3;
  const DONE = 4;

  function parse($in, $uid) {
    # decode HTML entities before parsing
    $in = html_entity_decode($in, ENT_QUOTES, 'UTF-8');

    # convert smilies, which aren't in BBCode (ack!)
    $in = preg_replace('/<!-- s(.*?) --><img src="\{SMILIES_PATH\}\/.*? \/><!-- s\1 -->/', '\1', $in);

    $text_stack = array();
    $arg_stack = array();

    $fn_number = 1;
    $fn = array();

    $i = 0;
    $len = strlen($in);

    $list_counter_stack = array();

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

      case self::WHSP:
        while ($in[$i] == "\n" || $in[$i] == "\t" || $in[$i] == ' ') ++$i;
        $state = self::TEXT;
        break;

      case self::OTAG:
        # split tag into tag name and argument, if any
        if (strpos($tag, '=') !== false) {
          list($tag, $arg) = explode('=', $tag, 2);
        }
        else {
          $arg = false;
        }

        $arg_stack[] = $arg;

        switch ($tag) {
        case 'b':
          $out .= '__';
          $state = self::TEXT;
          break;
        case 'u':
        case 'i':
          $out .= '_';
          $state = self::TEXT;
          break;
        case 'url':
        case 'email':
          # nothing to do on opening
          $state = self::TEXT;
          break;
        case 'quote':
          if ($arg !== false) {
            $text_stack[] = $out . "\n$arg wrote:\n";
          }
          else {
            $text_stack[] = $out . "\n";
          }
          $out = '';
          $state = self::TEXT;
          break;
        case 'code':
          $out .= "\nCode:\n";
          $state = self::TEXT;
          break;
        case 'list':
#          if ($out[strlen($out)-1] != "\n") $out .= "\n";

          switch ($arg) {
          case '1': $list_counter_stack[] = 1;   break;
          case 'a': $list_counter_stack[] = 'a'; break;
          default:  $list_counter_stack[] = '*'; break; 
          }

          $state = self::TEXT;
#          $state = self::WHSP;
          break;
        case '*':
#          if ($out[strlen($out)-1] != "\n") $out .= "\n";
          $out .= str_repeat(' ', 2*count($list_counter_stack));

          $c = array_pop($list_counter_stack);
          if (is_int($c)) {
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

          $state = self::TEXT;
#          $state = self::WHSP;
          break;
        case 'img':
          $text_stack[] = $out;
          $out = '';
          $state = self::TEXT;
          break;
        case 'attachment':
# TODO: unimplemented
          $state = self::TEXT;
          break;
        case 'color':
        case 'size':
          # ignored
          $state = self::TEXT;
          break;
        default:
          throw new Exception('Unrecognized open tag: ' . $tag);
        }

        break;

      case self::CTAG:
        $arg = array_pop($arg_stack);

        switch ($tag) {
        case 'b':
          $out .= '__';
          $state = self::TEXT;
          break;
        case 'u':
        case 'i':
          $out .= '_';
          $state = self::TEXT;
          break;
        case 'url':
        case 'email':
# TODO: untested
          if ($arg !== false) {
            # built footnotes for links with text
            $out .= '[' . $fn_number++ .']';
            $fn[] = $arg;
          }
          $state = self::TEXT;
          break;
        case 'quote':
          $level = count($text_stack);
          $out = wordwrap($out, 72 - 2*$level);
          $out = str_replace("\n", "\n> ", $out);
          $out = '> ' . $out;
          $out = array_pop($text_stack) . $out . "\n";
          $state = self::TEXT;
          break;
        case 'code':
# TODO: untested
# FIXME: don't wordwrap code!
          $out .= "\n\n";
          $state = self::TEXT;
          break;
        case 'list':
        case 'list:o':
        case 'list:u':
          array_pop($list_counter_stack);
          $out .= "\n\n";
#          $state = self::WHSP;
          $state = self::TEXT;
          break;
        case '*':
        case '*:m':
#          if ($out[strlen($out)-1] == "\n") $out = substr($out, 0, -1);
#          $state = self::WHSP;
          $state = self::TEXT;
          break;
        case 'img':
# TODO: untested
          $fn[] = $out; 
          $out = array_pop($text_stack) . '[' . $fn_number++ . ']';
          $state = self::TEXT;
          break;
        case 'attachment':
          $state = self::TEXT;
          break;
        case 'color':
        case 'size':
          # ignored
          $state = self::TEXT;
          break;
        default:
          throw new Exception('Unrecognized close tag: ' . $tag);
        }

        break;
      }
    }

    $out = wordwrap($out, 72);

    if (!empty($fn)) {
      # build footnotes
      $out .= "\n";

      for ($i = 0; $i < count($fn); ++$i) {
        $out .= "\n[" . ($i+1) . '] ' . $fn[$i];
      }

      $out .= "\n";
    }

    return $out;
  }
}

?>
