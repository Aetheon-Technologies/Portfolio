-- Portfolio Database Schema
-- Run: mysql -u root -p portfolio < schema.sql

CREATE DATABASE IF NOT EXISTS portfolio
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE portfolio;

-- --------------------------------------------------------
-- Settings: key-value store for all editable site content
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS settings (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100)  NOT NULL,
    value       TEXT,
    updated_at  TIMESTAMP     DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_setting_key (setting_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Skill Categories: Frontend, Backend, DevOps, Robotics
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS skill_categories (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name          VARCHAR(100)  NOT NULL,
    slug          VARCHAR(100)  NOT NULL,
    display_order TINYINT UNSIGNED DEFAULT 0,
    created_at    TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_skill_cat_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Skills
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS skills (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    category_id   INT UNSIGNED  NOT NULL,
    name          VARCHAR(100)  NOT NULL,
    icon_url      VARCHAR(255),
    display_order TINYINT UNSIGNED DEFAULT 0,
    created_at    TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES skill_categories(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Project Categories: Fullstack, Frontend, Backend, Robotics
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS project_categories (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name          VARCHAR(100)  NOT NULL,
    slug          VARCHAR(100)  NOT NULL,
    display_order TINYINT UNSIGNED DEFAULT 0,
    UNIQUE KEY uq_proj_cat_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Projects
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS projects (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    category_id   INT UNSIGNED  NOT NULL,
    title         VARCHAR(200)  NOT NULL,
    slug          VARCHAR(200)  NOT NULL,
    short_desc    VARCHAR(500),
    thumbnail_url VARCHAR(255),
    live_url      VARCHAR(255),
    github_url    VARCHAR(255),
    tech_tags     VARCHAR(500),
    is_robotics   TINYINT(1)    NOT NULL DEFAULT 0,
    display_order TINYINT UNSIGNED DEFAULT 0,
    status        ENUM('published','draft') NOT NULL DEFAULT 'draft',
    created_at    TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
    updated_at    TIMESTAMP     DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_project_slug (slug),
    FOREIGN KEY (category_id) REFERENCES project_categories(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Blog Posts
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS blog_posts (
    id             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title          VARCHAR(300)  NOT NULL,
    slug           VARCHAR(300)  NOT NULL,
    excerpt        VARCHAR(600),
    body           LONGTEXT,
    featured_image VARCHAR(255),
    read_time      TINYINT UNSIGNED DEFAULT 5,
    tags           VARCHAR(500),
    status         ENUM('published','draft') NOT NULL DEFAULT 'draft',
    published_at   DATETIME,
    created_at     TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
    updated_at     TIMESTAMP     DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_blog_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Contact Messages
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS contact_messages (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(150)  NOT NULL,
    email       VARCHAR(254)  NOT NULL,
    subject     VARCHAR(300),
    message     TEXT          NOT NULL,
    ip_address  VARCHAR(45),
    is_read     TINYINT(1)    NOT NULL DEFAULT 0,
    is_archived TINYINT(1)    NOT NULL DEFAULT 0,
    created_at  TIMESTAMP     DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Admin Users
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS admin_users (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username      VARCHAR(100)  NOT NULL,
    password_hash VARCHAR(255)  NOT NULL,
    last_login    DATETIME,
    created_at    TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_admin_username (username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
