-- Uživatelé
CREATE TABLE users (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  username TEXT NOT NULL UNIQUE,
  password_hash TEXT NOT NULL
);

-- Sledované odkazy
CREATE TABLE watchlist (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  user_id INTEGER NOT NULL,
  url TEXT NOT NULL,
  last_hash TEXT,
  last_checked DATETIME,
  FOREIGN KEY(user_id) REFERENCES users(id)
);

-- Záznam změn (jen timestamp)
CREATE TABLE changes (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  watch_id INTEGER NOT NULL,
  timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY(watch_id) REFERENCES watchlist(id)
);
 
