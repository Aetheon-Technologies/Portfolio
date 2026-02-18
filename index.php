<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$pdo = DB::getConnection();

// Fetch skills grouped by category
$catStmt = $pdo->query('SELECT * FROM skill_categories ORDER BY display_order ASC');
$skillCategories = $catStmt->fetchAll();

$skillsByCategory = [];
if ($skillCategories) {
    $ids = array_column($skillCategories, 'id');
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $skillStmt = $pdo->prepare(
        "SELECT * FROM skills WHERE category_id IN ($placeholders) ORDER BY category_id, display_order ASC"
    );
    $skillStmt->execute($ids);
    foreach ($skillStmt->fetchAll() as $skill) {
        $skillsByCategory[$skill['category_id']][] = $skill;
    }
}

// Fetch published projects with their category slug
$projStmt = $pdo->query(
    'SELECT p.*, pc.name AS cat_name, pc.slug AS cat_slug
     FROM projects p
     JOIN project_categories pc ON p.category_id = pc.id
     WHERE p.status = "published"
     ORDER BY p.display_order ASC, p.created_at DESC'
);
$projects = $projStmt->fetchAll();

// Fetch robotics-flagged projects
$roboticsProjects = array_filter($projects, fn($p) => (bool)$p['is_robotics']);

// Fetch 3 latest blog posts
$blogStmt = $pdo->prepare(
    'SELECT * FROM blog_posts
     WHERE status = "published" AND published_at <= NOW()
     ORDER BY published_at DESC
     LIMIT 3'
);
$blogStmt->execute();
$blogPosts = $blogStmt->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<!-- ═══════════════════════════════════════════════════════
     HERO
═══════════════════════════════════════════════════════ -->
<section class="hero bg-grid" id="hero" aria-label="Introduction">
    <!-- Atmospheric blobs -->
    <div class="hero__bg-accent hero__bg-accent--cyan" aria-hidden="true"></div>
    <div class="hero__bg-accent hero__bg-accent--violet" aria-hidden="true"></div>

    <div class="container">
        <div class="hero__content">
            <div class="hero__eyebrow" data-reveal>
                <span class="hero__eyebrow-line" aria-hidden="true"></span>
                <span class="hero__eyebrow-text">Available for opportunities</span>
            </div>

            <h1 class="hero__name" data-reveal>
                <?= sanitize(get_setting('site_name', 'Your Name')) ?><span class="hero__name-accent">.</span>
            </h1>

            <div class="hero__role" data-reveal aria-live="polite">
                <span class="hero__role-prefix text-mono">&gt;_&nbsp;</span>
                <span class="hero__role-text" id="hero-role"
                      data-roles='["Full Stack Developer", "Robotics Enthusiast", "Problem Solver"]'></span>
                <noscript><span class="hero__role-text"><?= sanitize(get_setting('tagline', 'Fullstack Developer &amp; Robotics Engineer')) ?></span></noscript>
                <span class="hero__cursor" aria-hidden="true"></span>
            </div>

            <p class="hero__summary" data-reveal>
                <?= sanitize(get_setting('hero_summary', 'I build robust web applications and intelligent robotic systems.')) ?>
            </p>

            <div class="hero__ctas" data-reveal>
                <a href="#projects" class="btn btn--primary">View Projects</a>
                <a href="#contact" class="btn btn--outline">Get in Touch</a>
            </div>
        </div>
    </div>
</section>


<!-- ═══════════════════════════════════════════════════════
     ABOUT
═══════════════════════════════════════════════════════ -->
<section class="section section-alt" id="about" aria-label="About me">
    <div class="container">
        <div class="about__grid">
            <!-- Photo -->
            <div data-reveal>
                <div class="about__photo-wrap">
                    <?php $profileImg = get_setting('profile_image'); ?>
                    <?php if ($profileImg): ?>
                        <img src="<?= sanitize($profileImg) ?>"
                             alt="<?= sanitize(get_setting('site_name')) ?> — profile photo"
                             class="about__photo"
                             width="280" height="280"
                             loading="lazy">
                    <?php else: ?>
                        <div class="about__photo-placeholder">
                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
                            </svg>
                            <span>Add photo in admin</span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Bio -->
            <div>
                <div class="section-header" style="text-align:left; margin-bottom:var(--sp-lg);" data-reveal>
                    <span class="section-label">About Me</span>
                    <h2 class="section-title">Building at the <span>intersection</span> of software & hardware</h2>
                </div>

                <div data-reveal>
                    <?php if (get_setting('about_bio_1')): ?>
                    <p><?= sanitize(get_setting('about_bio_1')) ?></p>
                    <?php endif; ?>
                    <?php if (get_setting('about_bio_2')): ?>
                    <p><?= sanitize(get_setting('about_bio_2')) ?></p>
                    <?php endif; ?>
                </div>

                <!-- Stats -->
                <div class="about__stats" data-reveal>
                    <div>
                        <div class="about__stat-value"><?= sanitize(get_setting('stat_years', '3+')) ?></div>
                        <div class="about__stat-label">Years Experience</div>
                    </div>
                    <div>
                        <div class="about__stat-value"><?= sanitize(get_setting('stat_projects', '20+')) ?></div>
                        <div class="about__stat-label">Projects Completed</div>
                    </div>
                    <div>
                        <div class="about__stat-value"><?= sanitize(get_setting('stat_technologies', '15+')) ?></div>
                        <div class="about__stat-label">Technologies</div>
                    </div>
                </div>

                <?php if (get_setting('resume_url')): ?>
                <div data-reveal>
                    <a href="<?= htmlspecialchars(get_setting('resume_url')) ?>"
                       class="btn btn--outline"
                       download
                       rel="noopener">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                            <polyline points="7 10 12 15 17 10"/>
                            <line x1="12" y1="15" x2="12" y2="3"/>
                        </svg>
                        Download Resume
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>


<!-- ═══════════════════════════════════════════════════════
     SKILLS
═══════════════════════════════════════════════════════ -->
<section class="section" id="skills" aria-label="Skills and technologies">
    <div class="container">
        <div class="section-header" data-reveal>
            <span class="section-label">Tech Stack</span>
            <h2 class="section-title">Skills &amp; <span>Technologies</span></h2>
            <p class="section-desc">Tools and technologies I work with across fullstack development and robotics.</p>
        </div>

        <?php if ($skillCategories): ?>
        <div class="skills__grid">
            <?php foreach ($skillCategories as $i => $cat): ?>
            <div class="skills__category" data-reveal>
                <h3 class="skills__category-title"><?= sanitize($cat['name']) ?></h3>
                <div class="skills__tags">
                    <?php if (!empty($skillsByCategory[$cat['id']])): ?>
                        <?php foreach ($skillsByCategory[$cat['id']] as $skill): ?>
                        <span class="skill-tag">
                            <?php if ($skill['icon_url']): ?>
                                <img src="<?= sanitize($skill['icon_url']) ?>" alt="" width="20" height="20" loading="lazy" style="vertical-align:middle;flex-shrink:0;">
                            <?php endif; ?>
                            <?= sanitize($skill['name']) ?>
                        </span>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <span class="text-muted" style="font-size:var(--text-sm)">No skills added yet</span>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="empty-state"><p>Skills will appear here once added via the admin panel.</p></div>
        <?php endif; ?>
    </div>
</section>


<!-- ═══════════════════════════════════════════════════════
     PROJECTS
═══════════════════════════════════════════════════════ -->
<section class="section section-alt" id="projects" aria-label="Projects">
    <div class="container">
        <div class="section-header" data-reveal>
            <span class="section-label">Work</span>
            <h2 class="section-title">Featured <span>Projects</span></h2>
            <p class="section-desc">A selection of things I've built — from web apps to embedded systems.</p>
        </div>

        <!-- Filter Bar -->
        <div class="filter-bar" role="group" aria-label="Filter projects" data-reveal>
            <button class="filter-btn is-active" data-filter="all">All</button>
            <button class="filter-btn" data-filter="fullstack">Fullstack</button>
            <button class="filter-btn" data-filter="frontend">Frontend</button>
            <button class="filter-btn" data-filter="backend">Backend</button>
            <button class="filter-btn" data-filter="robotics">Robotics</button>
        </div>

        <?php if ($projects): ?>
        <div class="projects__grid" id="projects-grid">
            <?php foreach ($projects as $p): ?>
            <?php $tags = parse_tags($p['tech_tags'] ?? ''); ?>
            <article class="project-card" data-category="<?= sanitize($p['cat_slug']) ?>" data-reveal>
                <div class="project-card__image">
                    <?php if ($p['thumbnail_url']): ?>
                        <img src="<?= sanitize($p['thumbnail_url']) ?>"
                             alt="<?= sanitize($p['title']) ?>"
                             loading="lazy">
                    <?php else: ?>
                        <div class="project-card__image-placeholder">&lt;/&gt;</div>
                    <?php endif; ?>
                </div>

                <div class="project-card__body">
                    <span class="project-card__category"><?= sanitize($p['cat_name']) ?></span>
                    <h3 class="project-card__title"><?= sanitize($p['title']) ?></h3>
                    <?php if ($p['short_desc']): ?>
                    <p class="project-card__desc"><?= sanitize($p['short_desc']) ?></p>
                    <?php endif; ?>

                    <?php if ($tags): ?>
                    <div class="project-card__tags">
                        <?php foreach ($tags as $tag): ?>
                        <span class="tech-pill"><?= sanitize($tag) ?></span>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>

                    <div class="project-card__links">
                        <?php if ($p['live_url']): ?>
                        <a href="<?= sanitize_url($p['live_url']) ?>" target="_blank" rel="noopener noreferrer">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                            Live Demo
                        </a>
                        <?php endif; ?>
                        <?php if ($p['github_url']): ?>
                        <a href="<?= sanitize_url($p['github_url']) ?>" target="_blank" rel="noopener noreferrer">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 0C5.37 0 0 5.37 0 12c0 5.31 3.435 9.795 8.205 11.385.6.105.825-.255.825-.57 0-.285-.015-1.23-.015-2.235-3.015.555-3.795-.735-4.035-1.41-.135-.345-.72-1.41-1.23-1.695-.42-.225-1.02-.78-.015-.795.945-.015 1.62.87 1.845 1.23 1.08 1.815 2.805 1.305 3.495.99.105-.78.42-1.305.765-1.605-2.67-.3-5.46-1.335-5.46-5.925 0-1.305.465-2.385 1.23-3.225-.12-.3-.54-1.53.12-3.18 0 0 1.005-.315 3.3 1.23.96-.27 1.98-.405 3-.405s2.04.135 3 .405c2.295-1.56 3.3-1.23 3.3-1.23.66 1.65.24 2.88.12 3.18.765.84 1.23 1.905 1.23 3.225 0 4.605-2.805 5.625-5.475 5.925.435.375.81 1.095.81 2.22 0 1.605-.015 2.895-.015 3.3 0 .315.225.69.825.57A12.02 12.02 0 0024 12c0-6.63-5.37-12-12-12z"/></svg>
                            GitHub
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="empty-state">
            <p>Projects will appear here once published via the admin panel.</p>
        </div>
        <?php endif; ?>
    </div>
</section>


<!-- ═══════════════════════════════════════════════════════
     ROBOTICS
═══════════════════════════════════════════════════════ -->
<section class="section robotics" id="robotics" aria-label="Robotics and hardware projects">
    <div class="container">
        <div class="section-header" data-reveal>
            <span class="section-label">Hardware</span>
            <h2 class="section-title">Robotics &amp; <span>Embedded Systems</span></h2>
            <p class="section-desc">Where software meets the physical world — microcontrollers, sensors, and autonomous systems.</p>
        </div>

        <?php if ($roboticsProjects): ?>
        <div class="robotics__grid">
            <?php foreach ($roboticsProjects as $p): ?>
            <?php $tags = parse_tags($p['tech_tags'] ?? ''); ?>
            <article class="robotics-card" data-reveal>
                <div class="robotics-card__header">
                    <div class="robotics-card__icon" aria-hidden="true">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <rect x="2" y="7" width="20" height="14" rx="2"/>
                            <path d="M8 7V5a2 2 0 012-2h4a2 2 0 012 2v2"/>
                            <line x1="12" y1="12" x2="12" y2="16"/>
                            <line x1="10" y1="14" x2="14" y2="14"/>
                        </svg>
                    </div>
                    <h3 class="robotics-card__title"><?= sanitize($p['title']) ?></h3>
                </div>

                <?php if ($p['short_desc']): ?>
                <p class="robotics-card__desc"><?= sanitize($p['short_desc']) ?></p>
                <?php endif; ?>

                <?php if ($tags): ?>
                <div class="robotics-card__meta">
                    <?php foreach ($tags as $tag): ?>
                    <span class="tech-pill"><?= sanitize($tag) ?></span>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <div class="robotics-card__links">
                    <?php if ($p['github_url']): ?>
                    <a href="<?= sanitize_url($p['github_url']) ?>" target="_blank" rel="noopener noreferrer">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 0C5.37 0 0 5.37 0 12c0 5.31 3.435 9.795 8.205 11.385.6.105.825-.255.825-.57 0-.285-.015-1.23-.015-2.235-3.015.555-3.795-.735-4.035-1.41-.135-.345-.72-1.41-1.23-1.695-.42-.225-1.02-.78-.015-.795.945-.015 1.62.87 1.845 1.23 1.08 1.815 2.805 1.305 3.495.99.105-.78.42-1.305.765-1.605-2.67-.3-5.46-1.335-5.46-5.925 0-1.305.465-2.385 1.23-3.225-.12-.3-.54-1.53.12-3.18 0 0 1.005-.315 3.3 1.23.96-.27 1.98-.405 3-.405s2.04.135 3 .405c2.295-1.56 3.3-1.23 3.3-1.23.66 1.65.24 2.88.12 3.18.765.84 1.23 1.905 1.23 3.225 0 4.605-2.805 5.625-5.475 5.925.435.375.81 1.095.81 2.22 0 1.605-.015 2.895-.015 3.3 0 .315.225.69.825.57A12.02 12.02 0 0024 12c0-6.63-5.37-12-12-12z"/></svg>
                        View Code
                    </a>
                    <?php endif; ?>
                    <?php if ($p['live_url']): ?>
                    <a href="<?= sanitize_url($p['live_url']) ?>" target="_blank" rel="noopener noreferrer">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                        Demo / Docs
                    </a>
                    <?php endif; ?>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="empty-state">
            <p>Robotics projects will appear here. Mark a project as "Robotics" in the admin panel.</p>
        </div>
        <?php endif; ?>
    </div>
</section>


<!-- ═══════════════════════════════════════════════════════
     BLOG
═══════════════════════════════════════════════════════ -->
<section class="section" id="blog" aria-label="Blog">
    <div class="container">
        <div class="section-header" data-reveal>
            <span class="section-label">Writing</span>
            <h2 class="section-title">Latest <span>Posts</span></h2>
            <p class="section-desc">Thoughts on development, hardware, and the things I'm learning.</p>
        </div>

        <?php if ($blogPosts): ?>
        <div class="blog__grid">
            <?php foreach ($blogPosts as $post): ?>
            <?php $tags = parse_tags($post['tags'] ?? ''); ?>
            <article class="blog-card" data-reveal>
                <?php if ($post['featured_image']): ?>
                <div class="blog-card__image">
                    <img src="<?= sanitize($post['featured_image']) ?>"
                         alt="<?= sanitize($post['title']) ?>"
                         loading="lazy">
                </div>
                <?php endif; ?>

                <div class="blog-card__body">
                    <div class="blog-card__meta">
                        <time datetime="<?= htmlspecialchars($post['published_at']) ?>">
                            <?= format_date($post['published_at']) ?>
                        </time>
                        <span class="dot">•</span>
                        <span><?= (int)$post['read_time'] ?> min read</span>
                    </div>

                    <a href="<?= BASE_URL ?>/blog-post.php?slug=<?= urlencode($post['slug']) ?>"
                       class="blog-card__title">
                        <?= sanitize($post['title']) ?>
                    </a>

                    <?php if ($post['excerpt']): ?>
                    <p class="blog-card__excerpt"><?= sanitize($post['excerpt']) ?></p>
                    <?php endif; ?>

                    <a href="<?= BASE_URL ?>/blog-post.php?slug=<?= urlencode($post['slug']) ?>"
                       class="blog-card__read-more">
                        Read more
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                    </a>
                </div>
            </article>
            <?php endforeach; ?>
        </div>

        <div class="blog__view-all" data-reveal>
            <a href="<?= BASE_URL ?>/blog.php" class="btn btn--outline">View All Posts</a>
        </div>
        <?php else: ?>
        <div class="empty-state">
            <p>Blog posts will appear here once published via the admin panel.</p>
        </div>
        <?php endif; ?>
    </div>
</section>


<!-- ═══════════════════════════════════════════════════════
     CONTACT
═══════════════════════════════════════════════════════ -->
<section class="section section-alt" id="contact" aria-label="Contact">
    <div class="container">
        <div class="section-header" data-reveal>
            <span class="section-label">Contact</span>
            <h2 class="section-title">Let's <span>Work Together</span></h2>
        </div>

        <div class="contact__grid">
            <!-- Contact Info -->
            <div data-reveal>
                <h3 class="contact__info-title">Have a project in mind?</h3>
                <p class="contact__info-desc">
                    I'm open to freelance projects, collaborations, and full-time opportunities.
                    Drop me a message and I'll get back to you as soon as possible.
                </p>

                <div class="contact__details">
                    <?php if (get_setting('email')): ?>
                    <div class="contact__detail">
                        <div class="contact__detail-icon" aria-hidden="true">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                        </div>
                        <a href="mailto:<?= sanitize(get_setting('email')) ?>"><?= sanitize(get_setting('email')) ?></a>
                    </div>
                    <?php endif; ?>
                    <?php if (get_setting('location')): ?>
                    <div class="contact__detail">
                        <div class="contact__detail-icon" aria-hidden="true">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                        </div>
                        <span><?= sanitize(get_setting('location')) ?></span>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="contact__socials">
                    <?php if (get_setting('github_url')): ?>
                    <a href="<?= sanitize_url(get_setting('github_url')) ?>" class="social-link" target="_blank" rel="noopener noreferrer" aria-label="GitHub">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M12 0C5.37 0 0 5.37 0 12c0 5.31 3.435 9.795 8.205 11.385.6.105.825-.255.825-.57 0-.285-.015-1.23-.015-2.235-3.015.555-3.795-.735-4.035-1.41-.135-.345-.72-1.41-1.23-1.695-.42-.225-1.02-.78-.015-.795.945-.015 1.62.87 1.845 1.23 1.08 1.815 2.805 1.305 3.495.99.105-.78.42-1.305.765-1.605-2.67-.3-5.46-1.335-5.46-5.925 0-1.305.465-2.385 1.23-3.225-.12-.3-.54-1.53.12-3.18 0 0 1.005-.315 3.3 1.23.96-.27 1.98-.405 3-.405s2.04.135 3 .405c2.295-1.56 3.3-1.23 3.3-1.23.66 1.65.24 2.88.12 3.18.765.84 1.23 1.905 1.23 3.225 0 4.605-2.805 5.625-5.475 5.925.435.375.81 1.095.81 2.22 0 1.605-.015 2.895-.015 3.3 0 .315.225.69.825.57A12.02 12.02 0 0024 12c0-6.63-5.37-12-12-12z"/></svg>
                    </a>
                    <?php endif; ?>
                    <?php if (get_setting('linkedin_url')): ?>
                    <a href="<?= sanitize_url(get_setting('linkedin_url')) ?>" class="social-link" target="_blank" rel="noopener noreferrer" aria-label="LinkedIn">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.064 2.064 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                    </a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Contact Form -->
            <div data-reveal>
                <form class="contact__form" id="contact-form" novalidate>
                    <div id="form-status" role="alert" aria-live="polite"></div>

                    <div class="form-row">
                        <div class="form-group" id="fg-name">
                            <label class="form-label" for="contact-name">Name *</label>
                            <input type="text" id="contact-name" name="name" class="form-input"
                                   placeholder="Jane Smith" required autocomplete="name">
                            <span class="form-error" id="err-name">Please enter your name.</span>
                        </div>
                        <div class="form-group" id="fg-email">
                            <label class="form-label" for="contact-email">Email *</label>
                            <input type="email" id="contact-email" name="email" class="form-input"
                                   placeholder="jane@example.com" required autocomplete="email">
                            <span class="form-error" id="err-email">Please enter a valid email address.</span>
                        </div>
                    </div>

                    <div class="form-group" id="fg-subject">
                        <label class="form-label" for="contact-subject">Subject</label>
                        <input type="text" id="contact-subject" name="subject" class="form-input"
                               placeholder="Project inquiry">
                    </div>

                    <div class="form-group" id="fg-message">
                        <label class="form-label" for="contact-message">Message *</label>
                        <textarea id="contact-message" name="message" class="form-textarea"
                                  placeholder="Tell me about your project..." required
                                  rows="6" minlength="20"></textarea>
                        <span class="form-error" id="err-message">Message must be at least 20 characters.</span>
                    </div>

                    <button type="submit" class="btn btn--primary" id="contact-submit">
                        Send Message
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
