<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$pdo  = DB::getConnection();
$slug = trim($_GET['slug'] ?? '');

if (!$slug) {
    redirect('/blog.php');
}

$stmt = $pdo->prepare(
    "SELECT * FROM blog_posts
     WHERE slug = ? AND status = 'published' AND published_at <= NOW()
     LIMIT 1"
);
$stmt->execute([$slug]);
$post = $stmt->fetch();

if (!$post) {
    http_response_code(404);
    $pageTitle = 'Post Not Found';
    require_once __DIR__ . '/includes/header.php';
    echo '<main style="padding-top:var(--nav-height);min-height:60vh;display:flex;align-items:center;justify-content:center;">';
    echo '<div class="empty-state"><p>Post not found.</p><a href="' . BASE_URL . '/blog.php" class="btn btn--outline">Back to Blog</a></div>';
    echo '</main>';
    require_once __DIR__ . '/includes/footer.php';
    exit;
}

$pageTitle = $post['title'];
$tags      = parse_tags($post['tags'] ?? '');

require_once __DIR__ . '/includes/header.php';
?>

<main style="padding-top: var(--nav-height);">
    <article class="section">
        <div class="container" style="max-width: 780px;">

            <!-- Back link -->
            <a href="<?= BASE_URL ?>/blog.php"
               style="display:inline-flex;align-items:center;gap:.4em;color:var(--clr-muted);font-size:var(--text-sm);margin-bottom:var(--sp-xl);text-decoration:none;transition:color var(--tr-fast);"
               onmouseover="this.style.color='var(--clr-cyan)'"
               onmouseout="this.style.color='var(--clr-muted)'">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                Back to Blog
            </a>

            <!-- Post Header -->
            <header data-reveal>
                <?php if ($tags): ?>
                <div class="project-card__tags" style="margin-bottom:var(--sp-md);">
                    <?php foreach ($tags as $tag): ?>
                    <span class="tech-pill"><?= sanitize($tag) ?></span>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <h1 style="font-size:var(--text-3xl);margin-bottom:var(--sp-md);">
                    <?= sanitize($post['title']) ?>
                </h1>

                <div class="blog-card__meta" style="justify-content:flex-start;margin-bottom:var(--sp-xl);">
                    <time datetime="<?= htmlspecialchars($post['published_at']) ?>">
                        <?= format_date($post['published_at']) ?>
                    </time>
                    <span class="dot">•</span>
                    <span><?= (int)$post['read_time'] ?> min read</span>
                </div>

                <?php if ($post['featured_image']): ?>
                <div style="border-radius:var(--radius-lg);overflow:hidden;margin-bottom:var(--sp-xl);">
                    <img src="<?= sanitize($post['featured_image']) ?>"
                         alt="<?= sanitize($post['title']) ?>"
                         style="width:100%;height:auto;object-fit:cover;">
                </div>
                <?php endif; ?>
            </header>

            <!-- Post Content -->
            <div class="blog-post-body" data-reveal>
                <?= $post['body'] /* body is stored as trusted HTML, entered by authenticated admin */ ?>
            </div>

        </div>
    </article>
</main>

<style>
.blog-post-body {
    color: var(--clr-muted);
    line-height: 1.85;
    font-size: var(--text-base);
}
.blog-post-body h1, .blog-post-body h2, .blog-post-body h3,
.blog-post-body h4, .blog-post-body h5, .blog-post-body h6 {
    color: var(--clr-text);
    margin-top: var(--sp-xl);
    margin-bottom: var(--sp-md);
    font-family: var(--ff-heading);
}
.blog-post-body p { margin-bottom: var(--sp-md); }
.blog-post-body a { color: var(--clr-cyan); }
.blog-post-body a:hover { color: var(--clr-violet); }
.blog-post-body ul, .blog-post-body ol {
    list-style: revert;
    padding-left: var(--sp-xl);
    margin-bottom: var(--sp-md);
}
.blog-post-body blockquote {
    border-left: 3px solid var(--clr-cyan);
    padding-left: var(--sp-lg);
    margin: var(--sp-xl) 0;
    color: var(--clr-muted);
    font-style: italic;
}
.blog-post-body code {
    font-family: var(--ff-mono);
    font-size: 0.9em;
    background: rgba(78,205,196,0.08);
    padding: .15em .4em;
    border-radius: 4px;
    color: var(--clr-cyan);
}
.blog-post-body pre {
    background: var(--clr-bg-card);
    border: 1px solid var(--clr-border);
    border-radius: var(--radius-md);
    padding: var(--sp-md);
    overflow-x: auto;
    margin-bottom: var(--sp-lg);
}
.blog-post-body pre code {
    background: none;
    padding: 0;
    color: var(--clr-text);
}
.blog-post-body img {
    border-radius: var(--radius-md);
    margin-block: var(--sp-lg);
}
</style>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
