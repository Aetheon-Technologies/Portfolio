<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= sanitize(get_setting('tagline', 'Fullstack Developer & Robotics Engineer')) ?>">
    <meta property="og:title" content="<?= sanitize(get_setting('site_name', 'Your Name')) ?> — Portfolio">
    <meta property="og:description" content="<?= sanitize(get_setting('hero_summary', '')) ?>">
    <meta property="og:type" content="website">
    <title><?= isset($pageTitle) ? sanitize($pageTitle) . ' — ' : '' ?><?= sanitize(get_setting('site_name', 'Portfolio')) ?></title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/main.css">
    <link rel="icon" href="<?= BASE_URL ?>/assets/images/favicon.ico" type="image/x-icon">
</head>
<body>

<!-- Navigation -->
<nav class="nav" id="nav" role="navigation" aria-label="Main navigation">
    <div class="container">
        <div class="nav__inner">
            <a href="<?= BASE_URL ?>/" class="nav__logo">
                <span>&lt;</span><?= sanitize(get_setting('site_name', 'YN')) ?><span>/&gt;</span>
            </a>

            <ul class="nav__links" id="nav-links" role="list">
                <li><a href="<?= BASE_URL ?>/#about">About</a></li>
                <li><a href="<?= BASE_URL ?>/#skills">Skills</a></li>
                <li><a href="<?= BASE_URL ?>/#projects">Projects</a></li>
                <li><a href="<?= BASE_URL ?>/#robotics">Robotics</a></li>
                <li><a href="<?= BASE_URL ?>/#blog">Blog</a></li>
                <li><a href="<?= BASE_URL ?>/#contact" class="nav__cta">Contact</a></li>
            </ul>

            <button class="nav__toggle" id="nav-toggle" aria-label="Toggle navigation" aria-expanded="false" aria-controls="nav-links">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>
    </div>
</nav>
