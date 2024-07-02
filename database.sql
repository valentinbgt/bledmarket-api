DROP TABLE IF EXISTS files ;
CREATE TABLE files (file_id INT AUTO_INCREMENT NOT NULL,
file_public_id TEXT,
file_db_id TEXT,
file_cypher_key TEXT,
file_token TEXT,
file_is_uploaded BOOLEAN,
file_repertory TEXT,
file_name TEXT,
file_path MEDIUMTEXT,
file_size BIGINT,
file_date BIGINT,
file_is_locked BOOLEAN,
file_is_folder BOOLEAN,
file_is_shared BOOLEAN,
file_shared_id TEXT,
file_is_trashed BOOLEAN,
file_trash_date BIGINT,
file_type TINYTEXT,
file_hash TEXT,
user_id BIGINT,
PRIMARY KEY (file_id)) ENGINE=InnoDB;

DROP TABLE IF EXISTS users ;
CREATE TABLE users (user_id BIGINT AUTO_INCREMENT NOT NULL,
user_type TINYTEXT,
user_name TINYTEXT,
user_display_name TINYTEXT,
user_email TEXT,
user_pwd TEXT,
user_about TEXT,
user_google_link BIGINT,
PRIMARY KEY (user_id)) ENGINE=InnoDB;

DROP TABLE IF EXISTS pictusers ;
CREATE TABLE pictusers (user_id BIGINT,
pictuser_profile_content LONGTEXT,
pictuser_profile_etag TEXT,
pictuser_banner_content LONGTEXT,
pictuser_banner_etag TEXT,
PRIMARY KEY (user_id)) ENGINE=InnoDB;

ALTER TABLE files ADD CONSTRAINT FK_files_user_id FOREIGN KEY (user_id) REFERENCES users (user_id);
ALTER TABLE pictusers ADD CONSTRAINT FK_pictusers_user_id FOREIGN KEY (user_id) REFERENCES users (user_id);