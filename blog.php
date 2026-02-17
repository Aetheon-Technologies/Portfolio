<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$pdo = DB::getConnection();

$page    = max(1, (int)($_GET['page'] ?? 1));
$perPage = 9;

$totalStmt = $pdo->query(
    "SELECT COUNT(*) FROM blog_posts WHERE status = 'published' AND published_at <= NOW()"
);
$total = (int)$totalStmt->fetchColumn();
$pag   = paginate($total, $perPage, $page);

$postsStmt = $pdo->prepare(
    "SELECT * FROM blog_posts
     WHERE status = 'published' AND published_at <= NOW()
     ORDER BY published_at DESC
     LIMIT ? OFFSET ?"
);
$postsStmt->execute([$pag['per_page'], $pag['offset']]);
$posts = $postsStmt->fetchAll();

$pageTitle = 'Blog';
require_once __DIR__ . '/includes/header.php';
?>

<main style="padding-top: var(--nav-height);">
    <section class="section">
        <div class="container">
            <div class="section-header" data-reveal>
                <span class="section-label">Writing</span>
                <h1 class="section-title">All <span>Posts</span></h1>
                <p class="section-desc">Thoughts on development, embedded systems, and the things I'm learning.</p>
            </div>

            <?php if ($posts): ?>
            <div class="blog__grid">
                <?php foreach ($posts as $post): ?>
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

                        <?php if ($tags): ?>
                        <div class="project-card__tags" style="margin-top:auto; padding-top:var(--sp-sm);">
                            <?php foreach ($tags as $tag): ?>
                            <span class="tech-pill"><?= sanitize($tag) ?></span>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>

                        <a href="<?= BASE_URL ?>/blog-post.php?slug=<?= urlencode($post['slug']) ?>"
                           class="blog-card__read-more">
                            Read more
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                        </a>
                    </div>
                </article>
                <?php endforeach; ?>
            </div>

            <!-- Pagination -->
            <?php if ($pag['total_pages'] > 1): ?>
            <nav class="pagination" aria-label="Blog pagination" style="display:flex;justify-content:center;gap:var(--sp-sm);margin-top:var(--sp-2xl);">
                <?php if ($pag['has_prev']): ?>
                <a href="?page=<?= $page - 1 ?>" class="btn btn--outline btn--sm">&larr; Previous</a>
                <?php endif; ?>

                <span style="display:flex;align-items:center;color:var(--clr-muted);font-size:var(--text-sm);">
                    Page <?= $page ?> of <?= $pag['total_pages'] ?>
                </span>

                <?php if ($pag['has_next']): ?>
                <a href="?page=<?= $page + 1 ?>" class="btn btn--outline btn--sm">Next &rarr;</a>
                <?php endif; ?>
            </nav>
            <?php endif; ?>

            <?php else: ?>
            <div class="empty-state">
                <p>No posts published yet.</p>
                <a href="<?= BASE_URL ?>/" class="btn btn--outline">Back to Portfolio</a>
            </div>
            <?php endif; ?>
        </div>
    </section>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
