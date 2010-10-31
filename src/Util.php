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

function throw_if_null($arg) {
  if ($arg === null) throw new Exception('argument is null');
}

function is_ascii($str) {
  return !preg_match('/[^[:ascii:]]/', $str);
}

function utf8_quote($str) {
  return '=?UTF-8?B?' . base64_encode($str) . '?=';
}

function utf8_quote_non_ascii($str) {
  return is_ascii($str) ? $str : utf8_quote($str);
}

?>
