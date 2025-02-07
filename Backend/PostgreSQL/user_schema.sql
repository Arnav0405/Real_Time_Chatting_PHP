CREATE TABLE users (
    id 			SERIAL NOT NULL PRIMARY KEY,
    username 	VARCHAR(50) NOT NULL UNIQUE,
    password 	VARCHAR(255) NOT NULL,
    created_at	TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE messages (
    id 			SERIAL PRIMARY KEY,
    sender_id 	INT NOT NULL,
    message 	TEXT NOT NULL,
    created_at 	TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	
    FOREIGN KEY (sender_id) REFERENCES users(id)
); 

