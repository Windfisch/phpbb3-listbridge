<?php

#
# $Id$
#
# forum-list bridge 
# Copyright (C) 2010 Joel Uckelman
#
# This program is free software: you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation, either version 3 of the License, or
# (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with this program. If not, see <http://www.gnu.org/licenses/>.
#

require_once('PHPUnit/Framework.php');
require_once('src/MailmanLib.php');

class MailmanLibTest extends PHPUnit_Framework_TestCase {
  
  /**
   * @dataProvider provider_read_raw_message
   */
  public function test_read_raw_message($url, $expected, $ex) {
    if ($ex) $this->setExpectedException($ex);
    $this->assertEquals($expected, read_raw_message($url));
  }

  public function provider_read_raw_message() {
    return array(
      array(__DIR__ . '/empty', null, 'Exception'),
      array(__DIR__ . '/bougs', null, 'Exception'),
      array(__DIR__ . '/1',     file_get_contents(__DIR__ . '/1'), null),
    );
  }
}

?>
