<?php
// ONE-TIME data sync from local export — DELETE IMMEDIATELY AFTER USE
// Access: /sync_data_7x9k.php?t=mccusker2026

if (($_GET['t'] ?? '') !== 'mccusker2026') {
    http_response_code(403);
    exit('Forbidden');
}

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';

$pdo = DB::getConnection();
$errors = [];

function run(PDO $pdo, string $sql, array $params = [], array &$errors = []): void {
    try {
        $pdo->prepare($sql)->execute($params);
    } catch (Throwable $e) {
        $errors[] = $e->getMessage();
    }
}

// ── Settings ──────────────────────────────────────────────────────────────────
$settings = [
    'site_name'         => 'Matthew McCusker',
    'tagline'           => 'Fullstack Developer & Robotics Engineer',
    'hero_summary'      => 'I build robust web applications and automation systems, with a strong interest in intelligent robotic systems, turning complex problems into actionable, scalable steps.',
    'about_bio_1'       => "I'm a fullstack developer and robotics enthusiast with a passion for crafting systems that bridge the digital and physical world. From responsive web interfaces to embedded hardware control, I enjoy working across the full technology stack.",
    'about_bio_2'       => "When I'm not writing code, I am learning AI, robotic prototyping with microcontrollers, or exploring new frameworks. I believe the best software is built with clarity, purpose, and attention to detail.",
    'resume_url'        => '',   // re-upload via admin panel
    'profile_image'     => '',   // re-upload via admin panel
    'stat_years'        => '4+',
    'stat_projects'     => '5+',
    'stat_technologies' => '15+',
    'email'             => 'MatthewMccuskerlv@gmail.com',
    'location'          => 'Calgary, Canada',
    'github_url'        => 'https://github.com/Aetheon-Technologies',
    'linkedin_url'      => 'https://www.linkedin.com/in/matthew-mccusker-908148297/',
    'twitter_url'       => '',
];

foreach ($settings as $key => $value) {
    run($pdo, 'INSERT INTO settings (setting_key, value) VALUES (?, ?) ON DUPLICATE KEY UPDATE value = VALUES(value)',
        [$key, $value], $errors);
}

// ── Skills — replace all with full local set ──────────────────────────────────
run($pdo, 'DELETE FROM skills', [], $errors);

$skills = [
    // [category_id, name, icon_url, display_order]
    [1,'HTML / CSS','https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/html5/html5-original.svg',1],
    [1,'JavaScript','https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/javascript/javascript-original.svg',2],
    [1,'React','https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/react/react-original.svg',3],
    [1,'Tailwind CSS',null,4],
    [1,'TypeScript','',5],
    [1,'Figma','',6],
    [2,'PHP','https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/php/php-original.svg',1],
    [2,'Node.js','https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/nodejs/nodejs-original.svg',2],
    [2,'Python','https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/python/python-original.svg',3],
    [2,'REST APIs','https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/fastapi/fastapi-original.svg',4],
    [2,'ASP.NET / C#','',5],
    [2,'JSON','',6],
    [2,'C++','',7],
    [3,'MySQL','https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/mysql/mysql-original.svg',1],
    [3,'Git / GitHub','https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/github/github-original.svg',2],
    [3,'Linux','https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/linux/linux-original.svg',3],
    [3,'Docker','https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/docker/docker-original.svg',4],
    [3,'Netlify','',5],
    [4,'Arduino','https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/arduino/arduino-original.svg',1],
    [4,'Raspberry Pi','https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/raspberrypi/raspberrypi-original.svg',2],
    [4,'C / C++','https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/cplusplus/cplusplus-original.svg',3],
    [4,'ROS','https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/ros/ros-original.svg',4],
];

foreach ($skills as [$cat, $name, $icon, $order]) {
    run($pdo, 'INSERT INTO skills (category_id, name, icon_url, display_order) VALUES (?,?,?,?)',
        [$cat, $name, $icon, $order], $errors);
}

// ── Projects ──────────────────────────────────────────────────────────────────
$projects = [
    [1,'Portfolio CMS','portfolio-cms','This portfolio itself — fully content-managed with a PHP admin panel.',null,null,null,'PHP,MySQL,JavaScript,CSS',0,1],
    [4,'Autonomous Robot','autonomous-robot','A differential-drive robot with obstacle avoidance using ultrasonic sensors.','','','','Embedded Systems,Arduino,C++,Ultrasonic,Motors',1,2],
    [2,'UI Component Library','ui-component-lib','A collection of accessible, reusable UI components built with vanilla JS.',null,null,null,'HTML,CSS,JavaScript',0,3],
];

foreach ($projects as [$cat,$title,$slug,$desc,$thumb,$live,$github,$tags,$robotics,$order]) {
    run($pdo, 'INSERT INTO projects (category_id,title,slug,short_desc,thumbnail_url,live_url,github_url,tech_tags,is_robotics,display_order,status)
               VALUES (?,?,?,?,?,?,?,?,?,?,\'published\')
               ON DUPLICATE KEY UPDATE
                   short_desc=VALUES(short_desc), tech_tags=VALUES(tech_tags),
                   is_robotics=VALUES(is_robotics), status=\'published\'',
        [$cat,$title,$slug,$desc,$thumb,$live,$github,$tags,$robotics,$order], $errors);
}

// ── Blog post ─────────────────────────────────────────────────────────────────
$blogBody = '<p>A few weeks ago I started digging into agentic AI, not just what it is, but how it actually works under the hood. The distinction hit me pretty quickly: <strong>a chatbot responds</strong>. <strong>An AI agent reasons, plans, and acts</strong>.</p> <p>Imagine asking a regular AI chatbot to plan a trip to Japan. It gives you a solid list, but you still have to book flights, find hotels, and coordinate everything yourself. An AI agent would check your calendar, search for flights within budget, book the hotel, build an itinerary, and confirm everything, only checking in when a real decision is needed. That is the difference.</p> <h2>The Four Core Components</h2> <p>Every AI agent is built from the same four parts working together:</p> <ol> <li><strong>The LLM (The Brain)</strong>. A large language model acts as the central coordinator. It interprets your request, reasons through what needs to happen, and decides which tools to use and in what order. It is the project manager of the agent.</li> <li><strong>Memory Modules</strong>. Agents have <em>short-term memory</em> (tracking current progress) and <em>long-term memory</em> (retaining past interactions and preferences). Without memory, every interaction starts from scratch.</li> <li><strong>Planning Modules</strong>. This is what separates agents from chatbots. The agent decomposes complex tasks into steps, either upfront using Chain of Thought reasoning, or iteratively, adjusting based on results and feedback.</li> <li><strong>Tools (The Hands)</strong>. APIs, databases, RAG pipelines, even other AI models. Tools are how the agent actually interacts with the outside world. Without tools, an agent can only think. It cannot act.</li> </ol> <h2>The Perceive → Reason → Act → Learn Loop</h2> <p>The way an agent operates follows a four-step loop:</p> <ul> <li><strong>Perceive</strong>. Take in the task, context, and relevant external data</li> <li><strong>Reason</strong>. Break the goal into a plan of action using the LLM</li> <li><strong>Act</strong>. Execute each step using tools and APIs</li> <li><strong>Learn</strong>. Feed results back into the system to improve future performance</li> </ul> <p>That last step, <strong>Learn</strong>, is where the data flywheel kicks in. Every real-world interaction generates data. That data gets used to fine-tune the model. The improved model performs better, generates better data, and the cycle accelerates.</p> <h2>RAG: How Agents Stay Grounded</h2> <p>LLMs have a knowledge cutoff and can hallucinate confidently wrong answers. Retrieval-Augmented Generation (RAG) solves this by letting the agent retrieve real, up-to-date information from external sources before generating a response, grounding its reasoning in actual facts rather than patterns from training data.</p> <h2>Key Takeaway</h2> <p>AI agents represent the evolution from AI that <em>talks</em> to AI that <em>does</em>. The building blocks, LLMs, memory, planning, and tools, are already mature. What is changing is how developers combine them into systems that can autonomously solve multi-step, real-world problems.</p>';

run($pdo, 'INSERT INTO blog_posts (title,slug,excerpt,body,featured_image,read_time,tags,status,published_at)
           VALUES (?,?,?,?,NULL,6,\'AI,Agents,LLMs,Automation,Robotics\',\'published\',NOW())
           ON DUPLICATE KEY UPDATE body=VALUES(body), excerpt=VALUES(excerpt), featured_image=NULL, status=\'published\'',
    [
        'AI Agents: From Chatbots to Systems That Actually Do Things',
        'ai-agents-from-chatbots-to-systems-that-act',
        'What separates a chatbot from an AI agent? One responds, the other reasons, plans, and acts. Here is what I have learned about agentic AI — and why it matters for developers and engineers.',
        $blogBody,
    ], $errors);

// ── Done ──────────────────────────────────────────────────────────────────────
echo '<pre>';
if ($errors) {
    echo "Completed with errors:\n";
    foreach ($errors as $e) echo "  - $e\n";
} else {
    echo "All data synced successfully!\n\n";
    echo "Settings, skills, projects, and blog post updated.\n";
    echo "NOTE: profile_image and resume_url were cleared — re-upload via /admin/settings.\n\n";
    echo "DELETE THIS FILE: /sync_data_7x9k.php\n";
}
echo '</pre>';
