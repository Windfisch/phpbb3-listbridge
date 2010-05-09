<?php

define('EOL', "\r\n");

class HTTP_POST_multipart {

  protected $_parts = array();

  public function addData($name, $bytes) {
    $this->_parts[] = array(
      'name' => $name,
      'data' => $bytes
    );
  }

  public function addFile($name, $filename, $mimetype,
                          $charset, $encoding, $data) {
    $this->_parts[] = array(
      'name'     => $name,
      'filename' => $filename,
      'mimetype' => $mimetype,
      'charset'  => $charset,
      'encoding' => $encoding,
      'data'     => $data
    );
  }

  protected static buildDataPart($part) {
    return 'Content-Disposition: form-data; name="' . $part['name'] . '"' .
           EOL . EOL . $part['data'] . EOL;
  }

  protected static buildFilePart($part) {
    # build Content-Disposition
    $p = 'Content-Disposition: form-data; name="' . $part['name'] . '"; ' .
           'filename="' . $part['filename'] . '"' . EOL;

    # build Content-Type
    $p .= 'Content-Type: ' . $part['mimetype'];
    if ($part['charset'] !== null) {
      $p .= '; charset="' . $part['charset'] . '"';
    }
    $p .= EOL;
   
    # build Content-Transfer-Encoding
    $data = null;

    if ($part['encoding'] !== null) {
      $p .= 'Content-Transfer-Encoding: ' . $part['encoding'] . EOL;

      switch ($part['encoding']) {
      case 'binary':
        $data = $part['data'];
        break;
      case 'base64':
        $data = chunk_split(base64_encode($part['data']);
        break;
      default:
        throw new Exception('unrecognized encoding: ' . $part['encoding']);
      }
    }
    else {
      $data = $part['data'];
    }

    # build data
    $p .= EOL . $data . EOL;

    return $p;
  }

  protected static buildBoundary($postParts) {
    # This isn't guaranteed to terminate, but it's unlikely
    # to need more than one iteration on any real input.
    while (1) {
      $boundary = "---------------------------" .
                  base_convert(mt_rand(), 10, 36) .
                  base_convert(mt_rand(), 10, 36) .
                  base_convert(mt_rand(), 10, 36);

      foreach ($postParts as $part) {
        if (strpos($part, $boundary) !== false) {
          # the boundary already occurs in this part, try a new boundary
          continue 2;
        }
      }

      # boundary occurs in no part, use it
      return $boundary;
    }
  }

  protected static buildPost($parts) {
    $postParts[] = array();

    foreach ($parts as $part) {
      if (array_key_exists('filename')) {
        # this is a file part
        $postParts[] = self::buildFilePart($part);
      }
      else {
        # this is a simple data part
        $postParts[] = self::buildDataPart($part); 
      }
    }

    $boundary = self::buildBoundary($postParts);
   
    $bd = '--' . $boundary . EOL;  
    $final_bd = '--' . $boundary . '--' . EOL;

    return array($boundary, $bd . implode($bd, $postParts) . $final_bd);
  }

  public function post($url) {

    list($boundary, $content) = buildPost($this->_parts);
    $ctype = 'Content-Type: multipart/form-data; boundary="' . $boundary . '"';

    $ctx = stream_context_create(array(
      'http' => array(
        'method'  => 'POST',
        'header'  => $ctype,
        'content' => $content
      )
    );

    return file_get_contents($url, false, $ctx);
  }
}

?>
