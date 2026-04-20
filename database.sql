-- =========================================
-- 1. CREATE DATABASE
-- =========================================
CREATE DATABASE IF NOT EXISTS users_db;

-- =========================================
-- 2. SELECT DATABASE
-- =========================================
USE users_db;

-- =========================================
-- 3. CREATE USERS TABLE
-- =========================================
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    -- prevent duplicate accounts
    CONSTRAINT unique_username UNIQUE (username),
    CONSTRAINT unique_email UNIQUE (email)
);

-- =========================================
-- 4. SAMPLE DATA (OPTIONAL TEST USER)
-- =========================================
INSERT INTO users (username, email, password_hash)
VALUES (
    'admin',
    'admin@example.com',
    '$2y$10$examplehashedpasswordreplacewithphp'
);