CREATE TABLE posts (
  post_id MEDIUMINT UNSIGNED,
  message_id VARCHAR(255) NOT NULL,
  in_reply_to VARCHAR(255),
  edit_id MEDIUMINT NOT NULL AUTO_INCREMENT,
  UNIQUE KEY (message_id),
  PRIMARY KEY (edit_id),
  INDEX (post_id)
);

CREATE TABLE forums (
  list_name VARCHAR(255) NOT NULL,
  forum_id MEDIUMINT UNSIGNED NOT NULL,
  PRIMARY KEY (list_name)
);

CREATE TABLE lists (
  list_name VARCHAR(255) NOT NULL,
  forum_id MEDIUMINT UNSIGNED NOT NULL,
  PRIMARY KEY (forum_id)
);
