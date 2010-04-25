<?php

class BBCodeParser {

  const TEXT = 0;
  const CTAG = 1;
  const OTAG = 2;
  const DONE = 3;

  function parse($in, $uid) {
    # decode HTML entities before parsing
    $in = html_entity_decode($in, ENT_QUOTES, 'UTF-8');

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
          $text_stack[] = $out . "\n";
          $out = '';
          break;
        case 'code':
          $out .= "\n";
          break;
        case 'list':
          $out .= "\n";
          
          switch ($arg) {
          case '1': $list_counter_stack[] = 1;   break;
          case 'a': $list_counter_stack[] = 'a'; break;
          default:  $list_counter_stack[] = '*'; break; 
          }

          break;
        case '*':
          $out .= "\n" . str_repeat(' ', count($list_counter_stack));

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
          break;
        case 'img':
          $text_stack[] = $out;
          $out = '';
          break;
        case 'attachment':
# TODO: unimplemented
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
# TODO: untested
          if ($arg !== false) {
            # built footnotes for links with text
            $out .= '[' . $fn_number++ .']';
            $fn[] = $arg;
          }
          break;
        case 'quote':
          $level = count($text_stack);
          $out = wordwrap($out, 72 - 2*$level);
          $out = str_replace("\n", "\n> ", $out);
          $out = '> ' . $out;
          $out = array_pop($text_stack) . $out . "\n";
          break;
        case 'code':
# TODO: untested
# FIXME: don't wordwrap code!
          $out .= "\n";
          break;
        case 'list':
        case 'list:o':
        case 'list:u':
# TODO: untested
          $out .= "\n";
          array_pop($list_counter_stack);
          break;
        case '*':
        case '*:m':
# TODO: untested
          if ($in[$i] != "\n") {
            $out .= "\n";
          }
          break;
        case 'img':
# TODO: untested
          $fn[] = $out; 
          $out = array_pop($text_stack) . '[' . $fn_number++ . ']';
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

    $out = wordwrap($out, 72);

    if (!empty($fn)) {
      # build footnotes
      $out .= "\n";

      for ($i = 0; $i < count($fn); ++$i) {
        $out .= "\n[" . ($i+1) . '] ' . $fn[$i];
      }
    }

    return $out;
  }
}

?>
