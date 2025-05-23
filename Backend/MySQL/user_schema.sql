CREATE DATABASE chat_app;

USE chat_app;

CREATE TABLE users (
    id 			BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username 	VARCHAR(50) NOT NULL UNIQUE,
    email       VARCHAR(132) NOT NULL UNIQUE,
    password 	VARCHAR(255) NOT NULL,
    bio         TEXT DEFAULT '',
    created_at	TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_seen   TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE messages (
    msg_id 	    BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    sender_id 	BIGINT UNSIGNED NOT NULL,
    reciever_id BIGINT UNSIGNED NOT NULL,
    message 	TEXT NOT NULL,
    file_url    VARCHAR(225) DEFAULT NULL,
    status      ENUM('sent', 'delivered', 'read') DEFAULT 'sent',
    sent_at 	TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	
    FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (reciever_id) REFERENCES users(id) ON DELETE CASCADE
); 

CREATE TABLE statuses (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    file_url VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);