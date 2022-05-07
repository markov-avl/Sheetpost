CREATE DATABASE sheetpost;
CREATE USER 'admin'@'localhost' IDENTIFIED BY 'password';
GRANT ALL PRIVILEGES ON sheetpost.* TO 'admin'@'localhost';

DROP TABLE IF EXISTS sheets CASCADE;
DROP TABLE IF EXISTS posts  CASCADE;
DROP TABLE IF EXISTS users  CASCADE;

CREATE TABLE users
(
    username VARCHAR(32) NOT NULL,
    password VARCHAR(64) NOT NULL,
    PRIMARY KEY (username)
);

CREATE TABLE posts
(
    id       INT           NOT NULL AUTO_INCREMENT,
    username VARCHAR(32)   NOT NULL,
    date     TIMESTAMP     NOT NULL,
    message  VARCHAR(4096) NOT NULL,
    PRIMARY KEY (id),
    FOREIGN KEY (username) REFERENCES users (username) ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE TABLE sheets
(
    username VARCHAR(32)   NOT NULL,
    post_id  INT           NOT NULL,
    PRIMARY KEY (username, post_id),
    FOREIGN KEY (username) REFERENCES users (username) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (post_id)  REFERENCES posts (id)       ON UPDATE CASCADE ON DELETE CASCADE
);