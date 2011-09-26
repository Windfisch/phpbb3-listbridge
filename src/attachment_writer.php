<?php

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

$password = '';
$attach_dir = '/var/www/forum/files';

# All requests should be local, since they come from the list post script.
if ($_SERVER['SERVER_ADDR'] != $_SERVER['REMOTE_ADDR']) {
  die("Client address is not local\n");
} 

# Check the password
if (!isset($_POST['password'])) {
  die("No password given\n");
}

if ($_POST['password'] != $password) {
  die("Incorrect password\n");
}

# Process each attachment
foreach ($_FILES as $file) {
  # Check for errors
  switch ($file['error']) {
  case UPLOAD_ERR_OK:
    break;
  case UPLOAD_ERR_INI_SIZE:
    die('Error UPLOAD_ERR_INI_SIZE: ' . $file['name'] . "\n");
  case UPLOAD_ERR_FORM_SIZE:
    die('Error UPLOAD_ERR_FORM_SIZE: ' . $file['name'] . "\n");
  case UPLOAD_ERR_PARTIAL:
    die('Error UPLOAD_ERR_PARTIAL: ' . $file['name'] . "\n");
  case UPLOAD_ERR_NO_FILE:
    die('Error UPLOAD_ERR_NO_FILE: ' . $file['name'] . "\n");
  case UPLOAD_ERR_NO_TMP_DIR:
    die('Error UPLOAD_ERR_NO_TMP_DIR: ' . $file['name'] . "\n");
  case UPLOAD_ERR_CANT_WRITE:
    die('Error UPLOAD_ERR_CANT_WRITE: ' . $file['name'] . "\n");
  case UPLOAD_ERR_EXTENSION:
    die('Error UPLOAD_ERR_EXTENSION: ' . $file['name'] . "\n");
  default:
    die('Unrecognized error code ' . $file['error'] . ': '
                                   . $file['name'] . "\n");
  }

  # Don't continue if the name isn't what phpBB expects
  if (preg_match('/^\d+_[0-9a-f]{32}$/', $file['name']) != 1) {
    die('Bad destination filename: ' . $file['name'] . "\n");
  }

  $src = $file['tmp_name'];
  $dst = $attach_dir . '/' . $file['name'];

  # Destination file should not exist, don't overwrite
  if (file_exists($dst)) {
    die('Destination file already exists: ' . $dst . "\n");
  }

  # Move temp file to attachments dir
  if (!move_uploaded_file($src, $dst)) {
    die("Failed to move $src to $dst.\n");
  }
}

# Success!
print 1;

?>
