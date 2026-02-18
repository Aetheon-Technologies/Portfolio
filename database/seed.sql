-- Portfolio Seed Data
-- Run AFTER schema.sql: mysql -u root -p portfolio < seed.sql

USE portfolio;

-- --------------------------------------------------------
-- Default site settings
-- --------------------------------------------------------
INSERT INTO settings (setting_key, value) VALUES
    ('site_name',         'Matthew McCusker'),
    ('tagline',           'Fullstack Developer & Robotics Engineer'),
    ('hero_summary',      'I build robust web applications and automation systems, with a strong interest in intelligent robotic systems, turning complex problems into actionable, scalable steps.'),
    ('about_bio_1',       'I''m a fullstack developer and robotics engineer with a passion for crafting systems that bridge the digital and physical world. From responsive web interfaces to embedded hardware control, I enjoy working across the full technology stack.'),
    ('about_bio_2',       'When I''m not writing code, I am learning AI, robotic prototyping with microcontrollers, or exploring new frameworks. I believe the best software is built with clarity, purpose, and attention to detail.'),
    ('resume_url',        ''),
    ('profile_image',     ''),
    ('stat_years',        '4+'),
    ('stat_projects',     '5+'),
    ('stat_technologies', '15+'),
    ('email',             'MatthewMccuskerlv@gmail.com'),
    ('location',          'Calgary, Canada'),
    ('github_url',        'https://github.com/yourusername'),
    ('linkedin_url',      'https://www.linkedin.com/in/matthew-mccusker-908148297/'),
    ('twitter_url',       '')
ON DUPLICATE KEY UPDATE setting_key = setting_key;

-- --------------------------------------------------------
-- Skill categories
-- --------------------------------------------------------
INSERT INTO skill_categories (name, slug, display_order) VALUES
    ('Frontend',             'frontend',    1),
    ('Backend',              'backend',     2),
    ('Database & DevOps',    'devops',      3),
    ('Robotics & Hardware',  'robotics',    4)
ON DUPLICATE KEY UPDATE name = name;

-- --------------------------------------------------------
-- Sample skills
-- --------------------------------------------------------
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

-- --------------------------------------------------------
-- Project categories
-- --------------------------------------------------------
INSERT INTO project_categories (name, slug, display_order) VALUES
    ('Fullstack', 'fullstack', 1),
    ('Frontend',  'frontend',  2),
    ('Backend',   'backend',   3),
    ('Robotics',  'robotics',  4)
ON DUPLICATE KEY UPDATE name = name;

-- --------------------------------------------------------
-- Sample projects
-- --------------------------------------------------------
INSERT INTO projects (category_id, title, slug, short_desc, tech_tags, is_robotics, display_order, status) VALUES
    (1, 'Portfolio CMS',      'portfolio-cms',     'This portfolio itself — fully content-managed with a PHP admin panel.',        'PHP,MySQL,JavaScript,CSS',       0, 1, 'published'),
    (4, 'Autonomous Robot',   'autonomous-robot',  'A differential-drive robot with obstacle avoidance using ultrasonic sensors.', 'Arduino,C++,Ultrasonic,Motors',  1, 2, 'published'),
    (2, 'UI Component Library','ui-component-lib', 'A collection of accessible, reusable UI components built with vanilla JS.',   'HTML,CSS,JavaScript',            0, 3, 'published');

-- --------------------------------------------------------
-- Sample blog post
-- --------------------------------------------------------
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

-- --------------------------------------------------------
-- Default admin user: admin / changeme123
-- Change this password immediately via admin panel or re-run with your own hash.
-- Generate a new hash: php -r "echo password_hash('yourpassword', PASSWORD_BCRYPT);"
-- --------------------------------------------------------
INSERT INTO admin_users (username, password_hash) VALUES
    ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi')
ON DUPLICATE KEY UPDATE username = username;
-- Note: default password above is 'password' — change it immediately via admin panel!
