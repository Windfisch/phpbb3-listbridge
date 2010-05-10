<?php

define('EOL', "\r\n");

class HTTP_POST_multipart {

  protected $_parts = array();

  public function addData($name, $data) {
    if ($name === null) throw new Exception('name is null');
    if ($data === null) throw new Exception('data is null');

    $this->_parts[] = array(
      'name' => $name,
      'data' => $data
    );
  }

  public function addFile($name, $filename, $mimetype,
                          $charset, $encoding, $data) {
    if ($name     === null) throw new Exception('name is null');
    if ($filename === null) throw new Exception('filename is null');
    if ($mimetype === null) throw new Exception('mimetype is null');
    if ($data     === null) throw new Exception('data is null');

    $this->_parts[] = array(
      'name'     => $name,
      'filename' => $filename,
      'mimetype' => $mimetype,
      'charset'  => $charset,
      'encoding' => $encoding,
      'data'     => $data
    );
  }

  protected static function buildDataPart($part) {
    return 'Content-Disposition: form-data; name="' . $part['name'] . '"' .
           EOL . EOL . $part['data'] . EOL;
  }

  protected static function buildFilePart($part) {
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
        $data = chunk_split(base64_encode($part['data']));
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

  protected static function buildBoundary($postParts) {
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

  protected static function buildPost($parts) {
    $postParts = array();

    foreach ($parts as $part) {
      if (array_key_exists('filename', $part)) {
        # this is a file part
        $postParts[] = self::buildFilePart($part);
      }
      else {
        # this is a simple data part
        $postParts[] = self::buildDataPart($part); 
      }
    }

    $boundary = self::buildBoundary($postParts);
  
    # put it all together 
    $bd = '--' . $boundary . EOL;  
    $final_bd = '--' . $boundary . '--' . EOL;

    return array($boundary, $bd . implode($bd, $postParts) . $final_bd);
  }

  public function dump() {
    list(/* skip */, $content) = self::buildPost($this->_parts);
    return $content;
  }

  public function post($url, $headers = array()) {
    list($boundary, $content) = self::buildPost($this->_parts);
    $ctype = 'Content-Type: multipart/form-data; boundary="' . $boundary . '"';

    $header = implode($headers, EOL) . $ctype . EOL;

    $ctx = stream_context_create(
      array('http' => 
        array(
          'method'  => 'POST',
          'header'  => $header,
          'content' => $content
        )
      )
    );

    return file_get_contents($url, false, $ctx);
  }
}

?>
