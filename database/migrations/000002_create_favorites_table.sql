-- Favorite articles table
CREATE TABLE IF NOT EXISTS favorites (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER,
    article_id TEXT NOT NULL,
    web_url TEXT NOT NULL,
    headline TEXT NOT NULL,
    snippet TEXT,
    pub_date DATETIME,
    source TEXT,
    image_url TEXT,
    author TEXT,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE(user_id, article_id)
);

-- Add index for faster lookups
CREATE INDEX idx_favorites_user_id ON favorites(user_id);
CREATE INDEX idx_favorites_article_id ON favorites(article_id);