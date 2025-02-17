-- Users table for authentication
CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT NOT NULL UNIQUE,
    password TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Favorite articles table
CREATE TABLE IF NOT EXISTS favorite_articles (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER,
    article_id TEXT NOT NULL,
    title TEXT NOT NULL,
    url TEXT NOT NULL,
    published_date TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE(user_id, article_id)
);
