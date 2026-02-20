-- ============================================================
-- Portfolio: Full fresh setup (schema + seed in one file)
-- ============================================================
-- Import this in phpMyAdmin to wipe and rebuild from scratch.
-- Steps: phpMyAdmin → portfolio database → Import → choose this file → Go
-- ============================================================

CREATE DATABASE IF NOT EXISTS portfolio
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE portfolio;

-- Drop existing tables (order matters — foreign keys first)
DROP TABLE IF EXISTS skills;
DROP TABLE IF EXISTS skill_categories;
DROP TABLE IF EXISTS projects;
DROP TABLE IF EXISTS project_categories;
DROP TABLE IF EXISTS blog_posts;
DROP TABLE IF EXISTS contact_messages;
DROP TABLE IF EXISTS admin_users;
DROP TABLE IF EXISTS settings;

-- ── Tables ───────────────────────────────────────────────────

CREATE TABLE settings (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100)  NOT NULL,
    value       TEXT,
    updated_at  TIMESTAMP     DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_setting_key (setting_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE skill_categories (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name          VARCHAR(100)  NOT NULL,
    slug          VARCHAR(100)  NOT NULL,
    display_order TINYINT UNSIGNED DEFAULT 0,
    created_at    TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_skill_cat_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE skills (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    category_id   INT UNSIGNED  NOT NULL,
    name          VARCHAR(100)  NOT NULL,
    icon_url      VARCHAR(255),
    display_order TINYINT UNSIGNED DEFAULT 0,
    created_at    TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES skill_categories(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE project_categories (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name          VARCHAR(100)  NOT NULL,
    slug          VARCHAR(100)  NOT NULL,
    display_order TINYINT UNSIGNED DEFAULT 0,
    UNIQUE KEY uq_proj_cat_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE projects (
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

CREATE TABLE blog_posts (
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

CREATE TABLE contact_messages (
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

CREATE TABLE admin_users (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username      VARCHAR(100)  NOT NULL,
    password_hash VARCHAR(255)  NOT NULL,
    last_login    DATETIME,
    created_at    TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_admin_username (username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── Seed Data ────────────────────────────────────────────────

INSERT INTO settings (setting_key, value) VALUES
    ('site_name',         'Matthew McCusker'),
    ('tagline',           'Fullstack Developer & Robotics Engineer'),
    ('hero_summary',      'I build robust web applications and automation systems, with a strong interest in intelligent robotic systems, turning complex problems into actionable, scalable steps.'),
    ('about_bio_1',       'I''m a fullstack developer and robotics engineer with a passion for crafting systems that bridge the digital and physical world. From responsive web interfaces to embedded hardware control, I enjoy working across the full technology stack.'),
    ('about_bio_2',       'When I''m not writing code, I am learning AI, robotic prototyping with microcontrollers, or exploring new frameworks. I believe the best software is built with clarity, purpose, and attention to detail.'),
    ('resume_url',        ''),
    ('profile_image',     '/uploads/profile_matthew.jpeg'),
    ('stat_years',        '4+'),
    ('stat_projects',     '5+'),
    ('stat_technologies', '15+'),
    ('email',             'MatthewMccuskerlv@gmail.com'),
    ('location',          'Calgary, Canada'),
    ('github_url',        'https://github.com/yourusername'),
    ('linkedin_url',      'https://www.linkedin.com/in/matthew-mccusker-908148297/'),
    ('twitter_url',       '');

INSERT INTO skill_categories (name, slug, display_order) VALUES
    ('Frontend',             'frontend',    1),
    ('Backend',              'backend',     2),
    ('Database & DevOps',    'devops',      3),
    ('Robotics & Hardware',  'robotics',    4);

INSERT INTO skills (category_id, name, display_order) VALUES
    (1, 'HTML / CSS',    1),
    (1, 'JavaScript',    2),
    (1, 'React',         3),
    (1, 'Tailwind CSS',  4),
    (2, 'PHP',           1),
    (2, 'Node.js',       2),
    (2, 'Python',        3),
    (2, 'REST APIs',     4),
    (3, 'MySQL',         1),
    (3, 'Git / GitHub',  2),
    (3, 'Linux',         3),
    (3, 'Docker',        4),
    (4, 'Arduino',       1),
    (4, 'Raspberry Pi',  2),
    (4, 'C / C++',       3),
    (4, 'ROS',           4);

INSERT INTO project_categories (name, slug, display_order) VALUES
    ('Fullstack', 'fullstack', 1),
    ('Frontend',  'frontend',  2),
    ('Backend',   'backend',   3),
    ('Robotics',  'robotics',  4);

INSERT INTO projects (category_id, title, slug, short_desc, tech_tags, is_robotics, display_order, status) VALUES
    (1, 'Portfolio CMS',       'portfolio-cms',      'This portfolio itself — fully content-managed with a PHP admin panel.',        'PHP,MySQL,JavaScript,CSS',       0, 1, 'published'),
    (4, 'Autonomous Robot',    'autonomous-robot',   'A differential-drive robot with obstacle avoidance using ultrasonic sensors.', 'Arduino,C++,Ultrasonic,Motors',  1, 2, 'published'),
    (2, 'UI Component Library','ui-component-lib',   'A collection of accessible, reusable UI components built with vanilla JS.',   'HTML,CSS,JavaScript',            0, 3, 'published');

INSERT INTO blog_posts (title, slug, excerpt, body, read_time, tags, status, published_at) VALUES
(
    'AI Agents: From Chatbots to Systems That Actually Do Things',
    'ai-agents-from-chatbots-to-systems-that-act',
    'What separates a chatbot from an AI agent? One responds, the other reasons, plans, and acts. Here is what I have learned about agentic AI — and why it matters for developers and engineers.',
    '<p>A few weeks ago I started digging into agentic AI — not just what it is, but how it actually works under the hood. The distinction hit me pretty quickly: <strong>a chatbot responds</strong>. <strong>An AI agent reasons, plans, and acts</strong>.</p><p>Imagine asking a regular AI chatbot to plan a trip to Japan. It gives you a solid list — but you still have to book flights, find hotels, and coordinate everything yourself. An AI agent would check your calendar, search for flights within budget, book the hotel, build an itinerary, and confirm everything — only checking in when a real decision is needed. That is the difference.</p><h2>The Four Core Components</h2><p>Every AI agent is built from the same four parts working together:</p><ol><li><strong>The LLM (The Brain)</strong> — A large language model acts as the central coordinator. It interprets your request, reasons through what needs to happen, and decides which tools to use and in what order. It is the project manager of the agent.</li><li><strong>Memory Modules</strong> — Agents have <em>short-term memory</em> (tracking current progress) and <em>long-term memory</em> (retaining past interactions and preferences). Without memory, every interaction starts from scratch.</li><li><strong>Planning Modules</strong> — This is what separates agents from chatbots. The agent decomposes complex tasks into steps — either upfront using Chain of Thought reasoning, or iteratively, adjusting based on results and feedback.</li><li><strong>Tools (The Hands)</strong> — APIs, databases, RAG pipelines, even other AI models. Tools are how the agent actually interacts with the outside world. Without tools, an agent can only think — it cannot do.</li></ol><h2>The Perceive → Reason → Act → Learn Loop</h2><p>The way an agent operates follows a four-step loop:</p><ul><li><strong>Perceive</strong> — Take in the task, context, and relevant external data</li><li><strong>Reason</strong> — Break the goal into a plan of action using the LLM</li><li><strong>Act</strong> — Execute each step using tools and APIs</li><li><strong>Learn</strong> — Feed results back into the system to improve future performance</li></ul><p>That last step — <strong>Learn</strong> — is where the data flywheel kicks in. Every real-world interaction generates data. That data gets used to fine-tune the model. The improved model performs better, generates better data, and the cycle accelerates. It is the same compounding logic as any feedback loop, applied to AI.</p><h2>Why This Matters for Developers</h2><p>For fullstack developers and robotics engineers, the implications are concrete:</p><ul><li><strong>Software development agents</strong> like GitHub Copilot already generate code and spot errors — the next generation will coordinate entire features autonomously</li><li><strong>Factory automation</strong> is moving from rigid rule-based systems toward hierarchical agent architectures, with higher-level agents coordinating specialised sub-agents</li><li><strong>IoT and embedded systems</strong> benefit from multi-agent setups where agents monitor sensor data, adjust to real-time conditions, and respond autonomously</li></ul><h2>RAG: How Agents Stay Grounded</h2><p>LLMs have a knowledge cutoff and can hallucinate confidently wrong answers. Retrieval-Augmented Generation (RAG) solves this by letting the agent retrieve real, up-to-date information from external sources before generating a response — grounding its reasoning in actual facts rather than patterns from training data.</p><p>For a robotics application, this means an agent could pull live sensor readings or current documentation before deciding on an action, rather than relying purely on what it learned during training.</p><h2>Key Takeaway</h2><p>AI agents represent the evolution from AI that <em>talks</em> to AI that <em>does</em>. The building blocks — LLMs, memory, planning, and tools — are already mature. What is changing is how developers combine them into systems that can autonomously solve multi-step, real-world problems. For engineers working at the intersection of software and hardware, this is not just a trend to watch — it is a fundamental shift in how intelligent systems get built.</p>',
    6,
    'AI,Agents,LLMs,Automation,Robotics',
    'published',
    NOW()
);

-- Admin login: admin / changeme123
-- Change password immediately after first login!
INSERT INTO admin_users (username, password_hash) VALUES
    ('admin', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');
