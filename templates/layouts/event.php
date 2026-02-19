<!DOCTYPE html>
<html lang="<?= $currentLang ?? 'es' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Cache control meta tags -->
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">

    <title><?= htmlspecialchars($meta_title ?? 'Evento') ?></title>
    <?php if (!empty($meta_description)): ?>
        <meta name="description" content="<?= htmlspecialchars($meta_description) ?>">
    <?php endif; ?>

    <!-- Open Graph -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="<?= htmlspecialchars($meta_title ?? '') ?>">
    <?php if (!empty($meta_description)): ?>
        <meta property="og:description" content="<?= htmlspecialchars($meta_description) ?>">
    <?php endif; ?>
    <?php if (!empty($meta_image)): ?>
        <meta property="og:image" content="<?= htmlspecialchars($meta_image) ?>">
    <?php endif; ?>
    <meta property="og:url" content="<?= htmlspecialchars((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . ($_SERVER['HTTP_HOST'] ?? '') . ($_SERVER['REQUEST_URI'] ?? '')) ?>">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= htmlspecialchars($meta_title ?? '') ?>">
    <?php if (!empty($meta_description)): ?>
        <meta name="twitter:description" content="<?= htmlspecialchars($meta_description) ?>">
    <?php endif; ?>
    <?php if (!empty($meta_image)): ?>
        <meta name="twitter:image" content="<?= htmlspecialchars($meta_image) ?>">
    <?php endif; ?>

    <!-- Favicon -->
    <link rel="icon" href="/favicon.ico">

    <!-- Fonts - TLOS Brand -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&family=Prompt:wght@400;500;600;700&family=Big+Shoulders+Text:wght@400;500;600;700&family=Roboto+Mono:wght@400;500&display=swap" rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- Main site styles (for header/nav) -->
    <link rel="stylesheet" href="/assets/css/frontend.css">

    <style>
        /* ============================================
           THE LAST OF SAAS - Event Page Styles
           Black/White Alternating Design
           ============================================ */

        :root {
            --bg-dark: #1A1A1A;
            --bg-light: #FFFFFF;
            --text-light: #FFFFFF;
            --text-dark: #000000;
            --text-grey: #86868B;
            --text-grey-dark: #555555;
            --border-light: rgba(255, 255, 255, 0.1);
            --border-dark: rgba(0, 0, 0, 0.1);
            --font-heading: 'Montserrat', sans-serif;
            --font-accent: 'Prompt', sans-serif;
            --font-display: 'Big Shoulders Text', sans-serif;
            --font-mono: 'Roboto Mono', monospace;
            --font-body: 'Montserrat', sans-serif;
            --transition: all 0.3s ease-in-out;
        }

        /* Reset only for event page content, not header */
        .event-hero-parallax *,
        .event-cta *,
        .event-description *,
        .event-companies *,
        .event-agenda *,
        .event-speakers *,
        .event-details *,
        .event-sponsors-section *,
        .event-content-section * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
            overflow-x: hidden;
        }

        body {
            font-family: var(--font-body);
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
            overflow-x: hidden;
            width: 100%;
        }

        /* Container - Full width with padding */
        .container-wide {
            width: 100%;
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 4rem;
            overflow-x: hidden;
        }

        /* Typography */
        h1, h2, h3, h4, h5, h6 {
            font-family: var(--font-heading);
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.02em;
            line-height: 1.1;
        }

        /* Ensure h2 inherits correct color from parent sections */
        .event-description h2,
        .event-agenda h2,
        .event-details h2,
        .event-cta--dark h2 {
            color: var(--text-light);
        }

        .event-companies h2,
        .event-speakers h2,
        .event-sponsors-section h2,
        .event-content-section h2,
        .event-cta--light h2 {
            color: var(--text-dark);
        }

        /* Buttons - No border radius */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            padding: 1rem 2rem;
            font-family: var(--font-heading);
            font-weight: 700;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            text-decoration: none;
            border: 2px solid transparent;
            cursor: pointer;
            transition: var(--transition);
            border-radius: 0;
        }

        .btn-primary {
            background: var(--text-light);
            color: var(--bg-dark);
            border-color: var(--text-light);
        }
        .btn-primary:hover {
            background: transparent;
            color: var(--text-light);
        }

        .btn-dark {
            background: var(--bg-dark);
            color: var(--text-light);
            border-color: var(--bg-dark);
        }
        .btn-dark:hover {
            background: transparent;
            color: var(--bg-dark);
            border-color: var(--bg-dark);
        }

        .btn-lg {
            padding: 1.25rem 3rem;
            font-size: 16px;
        }

        /* ============================================
           SECTION A: Hero with Parallax
           ============================================ */
        .event-hero-parallax {
            position: relative;
            min-height: 80vh;
            display: flex;
            align-items: center;
            background-color: var(--bg-dark);
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            color: var(--text-light);
            padding: 140px 0 80px;
        }

        .event-hero-parallax .hero-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.7);
            z-index: 1;
        }

        .event-hero-parallax .hero-content {
            position: relative;
            z-index: 2;
            width: 100%;
        }

        .event-hero-grid {
            display: flex;
            align-items: flex-start;
            gap: 4rem;
        }

        .event-date-block {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: var(--text-light);
            padding: 2rem 2.5rem;
            text-align: center;
            flex-shrink: 0;
        }

        .event-date-block .day {
            display: block;
            font-family: var(--font-accent);
            font-size: 72px;
            font-weight: 700;
            line-height: 1;
        }

        .event-date-block .month {
            display: block;
            font-family: var(--font-heading);
            font-size: 18px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.2em;
            margin-top: 0.25rem;
        }

        .event-date-block .year {
            display: block;
            font-family: var(--font-mono);
            font-size: 14px;
            color: var(--text-grey);
            margin-top: 0.5rem;
        }

        .event-title-block {
            flex: 1;
        }

        .event-title-block h1 {
            font-size: clamp(32px, 5vw, 56px);
            margin-bottom: 1.5rem;
            color: var(--text-light);
            text-shadow: 0 2px 20px rgba(0, 0, 0, 0.5);
        }

        .event-meta-inline {
            display: flex;
            gap: 3rem;
            font-family: var(--font-mono);
            font-size: 14px;
            color: #fff;
        }

        .event-meta-inline span {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .event-meta-inline i {
            color: var(--text-light);
        }

        .event-title-block .intro-text {
            margin-top: 2rem;
            font-size: 18px;
            line-height: 1.8;
            background-color: #fff;
            padding: 10px;
            color: #000;
            overflow-wrap: break-word;
            word-wrap: break-word;
            word-break: break-word;
        }

        /* Legacy hero minimal support */
        .event-hero-minimal {
            background: var(--bg-light);
            color: var(--text-dark);
            padding: 140px 0 80px;
        }

        .event-hero-minimal .event-date-block {
            background: var(--bg-dark);
        }

        .event-hero-minimal .event-title-block h1 {
            color: var(--text-dark);
            text-shadow: none;
        }

        .event-hero-minimal .event-meta-inline {
            color: var(--text-grey-dark);
        }

        .event-hero-minimal .event-meta-inline i {
            color: var(--text-dark);
        }

        /* ============================================
           SECTION C: CTA (Light version)
           ============================================ */
        .event-cta--light {
            background: var(--bg-light);
            color: var(--text-dark);
            padding: 60px 0;
            border-top: 1px solid var(--border-dark);
            border-bottom: 1px solid var(--border-dark);
        }

        .cta-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 2rem;
        }

        .cta-info {
            display: flex;
            align-items: center;
            gap: 2rem;
        }

        .cta-price {
            font-family: var(--font-accent);
            font-size: 32px;
            font-weight: 700;
        }

        .cta-label {
            font-family: var(--font-mono);
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.15em;
            color: var(--text-grey-dark);
        }

        /* ============================================
           SECTION D: Description (Black bg)
           ============================================ */
        .event-description {
            background: var(--bg-dark);
            color: var(--text-light);
            padding: 100px 0;
        }

        .event-description h2 {
            font-size: clamp(28px, 4vw, 40px);
            margin-bottom: 3rem;
            position: relative;
            display: inline-block;
        }

        .event-description h2::after {
            content: '';
            position: absolute;
            bottom: -15px;
            left: 0;
            width: 60px;
            height: 3px;
            background: var(--text-light);
        }

        .description-content {
            font-size: 18px;
            line-height: 1.9;
            color: var(--text-light);
            overflow-wrap: break-word;
            word-wrap: break-word;
            word-break: break-word;
        }

        .description-content p {
            margin-bottom: 1.5rem;
            overflow-wrap: break-word;
            word-wrap: break-word;
        }

        .description-content strong {
            color: var(--text-light);
            font-weight: 700;
        }

        .description-content a {
            color: var(--text-light);
            text-decoration: underline;
        }

        .description-content ul,
        .description-content ol {
            margin-bottom: 1.5rem;
            padding-left: 1.5rem;
        }

        .description-content li {
            margin-bottom: 0.5rem;
        }

        /* ============================================
           SECTION: Content / Talks & Workshops
           ============================================ */
        .event-content-section {
            background: var(--bg-light);
            color: var(--text-dark);
            padding: 100px 0;
        }

        .event-content-section h2 {
            font-size: clamp(28px, 4vw, 40px);
            margin-bottom: 3rem;
            position: relative;
            display: inline-block;
        }

        .event-content-section h2::after {
            content: '';
            position: absolute;
            bottom: -15px;
            left: 0;
            width: 60px;
            height: 3px;
            background: var(--text-dark);
        }

        .content-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 2rem;
        }

        .content-card {
            background: var(--bg-light);
            border: 2px solid var(--border-dark);
            overflow: hidden;
            transition: var(--transition);
        }

        .content-card:hover {
            border-color: var(--bg-dark);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .content-image {
            width: 100%;
            height: 200px;
            overflow: hidden;
        }

        .content-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .content-card:hover .content-image img {
            transform: scale(1.05);
        }

        .content-body {
            padding: 1.5rem;
        }

        .content-type {
            display: inline-block;
            font-family: var(--font-mono);
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            padding: 0.3rem 0.75rem;
            background: rgba(0, 0, 0, 0.1);
            color: var(--text-dark);
            margin-bottom: 1rem;
        }

        .content-body h3 {
            font-size: 18px;
            font-weight: 700;
            text-transform: uppercase;
            margin-bottom: 0.75rem;
            line-height: 1.3;
        }

        .content-body p {
            font-size: 14px;
            color: var(--text-grey-dark);
            line-height: 1.6;
            margin-bottom: 1rem;
        }

        .content-speaker {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 1rem 0;
            border-top: 1px solid var(--border-dark);
            margin-top: 1rem;
        }

        .content-speaker img {
            width: 48px;
            height: 48px;
            object-fit: cover;
            border-radius: 0;
        }

        .content-speaker .speaker-details {
            display: flex;
            flex-direction: column;
        }

        .content-speaker .speaker-name {
            font-family: var(--font-heading);
            font-size: 14px;
            font-weight: 700;
            color: var(--text-dark);
            text-decoration: none;
            text-transform: uppercase;
        }

        .content-speaker .speaker-name:hover {
            text-decoration: underline;
        }

        .content-speaker .speaker-title {
            font-family: var(--font-mono);
            font-size: 11px;
            color: var(--text-grey-dark);
        }

        .content-meta {
            display: flex;
            gap: 1.5rem;
            padding-top: 1rem;
            border-top: 1px solid var(--border-dark);
            margin-top: 1rem;
        }

        .content-meta span {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-family: var(--font-mono);
            font-size: 12px;
            color: var(--text-grey-dark);
        }

        .content-meta i {
            color: var(--text-dark);
        }

        /* ============================================
           SECTION E: Companies (White bg)
           ============================================ */
        .event-companies {
            background: var(--bg-light);
            color: var(--text-dark);
            padding: 100px 0;
        }

        .event-companies h2 {
            font-size: clamp(28px, 4vw, 40px);
            margin-bottom: 3rem;
            position: relative;
            display: inline-block;
        }

        .event-companies h2::after {
            content: '';
            position: absolute;
            bottom: -15px;
            left: 0;
            width: 60px;
            height: 3px;
            background: var(--text-dark);
        }

        .participants-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 1.5rem;
        }

        .participant-card {
            background: var(--bg-light);
            border: 2px solid var(--border-dark);
            padding: 2rem;
            min-height: 100px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: var(--transition);
            text-decoration: none;
        }

        .participant-card:hover {
            border-color: var(--bg-dark);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        .participant-card img {
            max-width: 100%;
            max-height: 60px;
            object-fit: contain;
            transition: var(--transition);
        }

        .participant-card:hover img {
            transform: scale(1.05);
        }

        .participant-name {
            font-family: var(--font-mono);
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: var(--text-dark);
            font-weight: 600;
        }

        /* ============================================
           SECTION F: Agenda (Black bg)
           ============================================ */
        .event-agenda {
            background: var(--bg-dark);
            color: var(--text-light);
            padding: 100px 0;
        }

        .event-agenda h2 {
            font-size: clamp(28px, 4vw, 40px);
            margin-bottom: 3rem;
            position: relative;
            display: inline-block;
        }

        .event-agenda h2::after {
            content: '';
            position: absolute;
            bottom: -15px;
            left: 0;
            width: 60px;
            height: 3px;
            background: var(--text-light);
        }

        .agenda-day {
            margin-bottom: 3rem;
        }

        .agenda-date {
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 14px;
            font-family: var(--font-mono);
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: var(--text-dark);
            background: var(--text-light);
            border: 1px solid var(--text-light);
            padding: 0.75rem 1.5rem;
            margin: 2rem 0 1.5rem 0;
        }

        .agenda-timeline .agenda-date:first-child {
            margin-top: 0;
        }

        .agenda-date i {
            color: var(--text-dark);
        }

        .agenda-timeline {
            border-left: 2px solid var(--text-light);
            padding-left: 2rem;
            margin-left: 1rem;
        }

        .agenda-item {
            display: flex;
            gap: 1.5rem;
            padding: 1.5rem 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            position: relative;
            text-decoration: none;
            color: inherit;
            transition: all 0.2s ease;
        }

        .agenda-item-link {
            cursor: pointer;
        }

        .agenda-item-link:hover {
            background-color: rgba(255, 255, 255, 0.05);
        }

        .agenda-item-link:hover .time-start {
            transform: scale(1.05);
            display: inline-block;
        }

        .agenda-item::before {
            content: '';
            position: absolute;
            left: -2rem;
            top: 1.75rem;
            width: 12px;
            height: 12px;
            background: var(--bg-dark);
            border: 2px solid var(--text-light);
            margin-left: -6px;
        }

        .agenda-item.featured::before {
            background: var(--text-light);
            border-color: var(--text-light);
        }

        .agenda-time {
            flex-shrink: 0;
            width: 60px;
            text-align: right;
        }

        .agenda-time .time-start {
            display: block;
            font-family: var(--font-accent);
            font-size: 18px;
            font-weight: 700;
            color: var(--text-light);
            transition: transform 0.2s ease;
        }

        .agenda-time .time-end {
            display: block;
            font-family: var(--font-mono);
            font-size: 11px;
            color: var(--text-light);
        }

        .agenda-type {
            display: inline-block;
            font-family: var(--font-mono);
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            padding: 0.25rem 0.75rem;
            background: var(--text-light);
            color: var(--text-dark);
            margin-bottom: 0.5rem;
        }

        .agenda-content {
            flex: 1;
            position: relative;
        }

        .agenda-content h4 {
            font-size: 16px;
            font-weight: 700;
            text-transform: uppercase;
            margin-bottom: 0.5rem;
            color: var(--text-light);
        }

        .agenda-sponsor-badge {
            position: absolute;
            top: 0;
            right: 0;
            background: white;
            padding: 0.35rem 0.6rem;
            border-radius: 4px;
        }

        .agenda-sponsor-badge img {
            max-height: 24px;
            max-width: 80px;
            object-fit: contain;
            display: block;
        }

        .agenda-excerpt {
            font-size: 14px;
            color: rgba(255, 255, 255, 0.7);
            line-height: 1.5;
            margin-bottom: 0.5rem;
        }

        .agenda-read-more {
            display: inline-block;
            font-size: 13px;
            color: var(--accent-color, #FFD700);
            font-weight: 600;
            margin-bottom: 0.75rem;
        }

        .agenda-read-more i {
            font-size: 11px;
            margin-left: 0.25rem;
            transition: transform 0.2s;
        }

        .agenda-item-link:hover .agenda-read-more i {
            transform: translateX(4px);
        }

        .agenda-content p {
            font-size: 14px;
            color: var(--text-light);
            line-height: 1.6;
            margin-bottom: 0.75rem;
        }

        .agenda-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .agenda-speaker {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-family: var(--font-mono);
            font-size: 11px;
            color: var(--text-light);
        }

        .agenda-speaker img {
            width: 24px;
            height: 24px;
            object-fit: cover;
        }

        .agenda-room {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-family: var(--font-mono);
            font-size: 11px;
            color: var(--text-light);
            padding-left: 0.75rem;
            border-left: 3px solid var(--text-light);
        }

        /* Agenda Layout with Room Images */
        .agenda-layout {
            display: grid;
            grid-template-columns: 1fr 350px;
            gap: 4rem;
        }

        .agenda-main {
            flex: 1;
        }

        .agenda-sidebar {
            position: sticky;
            top: 120px;
            align-self: start;
        }

        .agenda-sidebar h3 {
            font-size: 14px;
            font-family: var(--font-mono);
            text-transform: uppercase;
            letter-spacing: 0.15em;
            margin-bottom: 2rem;
            color: var(--text-grey);
        }

        .rooms-gallery {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .room-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid var(--border-light);
            overflow: hidden;
        }

        .room-image {
            width: 100%;
            height: 200px;
            overflow: hidden;
        }

        .room-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .room-card:hover .room-image img {
            transform: scale(1.05);
        }

        .room-info {
            padding: 1rem 1.25rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .room-name {
            font-family: var(--font-heading);
            font-size: 14px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .room-capacity {
            font-family: var(--font-mono);
            font-size: 11px;
            color: var(--text-grey);
        }

        .room-capacity i {
            margin-right: 0.3rem;
        }

        /* ============================================
           SECTION G: Speakers (White bg)
           ============================================ */
        .event-speakers {
            background: var(--bg-light);
            color: var(--text-dark);
            padding: 100px 0;
        }

        .event-speakers h2 {
            font-size: clamp(28px, 4vw, 40px);
            margin-bottom: 3rem;
            position: relative;
            display: inline-block;
        }

        .event-speakers h2::after {
            content: '';
            position: absolute;
            bottom: -15px;
            left: 0;
            width: 60px;
            height: 3px;
            background: var(--text-dark);
        }

        /* Speakers Carousel */
        .speakers-carousel-wrapper {
            position: relative;
            overflow: hidden;
            margin: 0 -20px;
            padding: 0 20px;
        }

        .speakers-carousel {
            display: flex;
            gap: 1.5rem;
            overflow-x: auto;
            scroll-behavior: smooth;
            scrollbar-width: none;
            -ms-overflow-style: none;
            padding: 1rem 0 2rem;
        }

        .speakers-carousel::-webkit-scrollbar {
            display: none;
        }

        .speaker-card {
            flex: 0 0 280px;
            text-align: left;
            text-decoration: none;
            display: block;
            transition: var(--transition);
            background: var(--bg-light);
            overflow: hidden;
        }

        .speaker-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }

        .speaker-photo {
            width: 100%;
            height: 320px;
            overflow: hidden;
            position: relative;
            border-radius: 12px 12px 0 0;
        }

        .speaker-photo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            filter: grayscale(100%);
            transition: all 0.4s ease;
        }

        .speaker-photo .photo-static,
        .speaker-photo .photo-animated {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }

        .speaker-photo .photo-static {
            z-index: 2;
        }

        .speaker-photo .photo-animated {
            z-index: 1;
            opacity: 1;
            filter: grayscale(0%);
        }

        .speaker-card:hover .speaker-photo img {
            filter: grayscale(0%);
        }

        .speaker-card:hover .photo-static {
            opacity: 0;
            z-index: 1;
        }

        .speaker-card:hover .photo-animated {
            z-index: 2;
        }

        .speaker-placeholder {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(0, 0, 0, 0.05);
            color: var(--text-grey-dark);
            font-size: 4rem;
        }

        .speaker-info {
            padding: 1.5rem;
            background: var(--bg-light);
            border-radius: 0 0 12px 12px;
        }

        .speaker-info strong {
            display: block;
            font-size: 18px;
            font-weight: 700;
            text-transform: uppercase;
            margin-bottom: 0.5rem;
            color: var(--text-dark);
        }

        .speaker-position {
            display: block;
            font-family: var(--font-mono);
            font-size: 12px;
            color: var(--text-grey-dark);
            line-height: 1.5;
        }

        .speaker-company {
            display: block;
            font-family: var(--font-mono);
            font-size: 11px;
            color: var(--text-grey-dark);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-top: 0.25rem;
        }

        /* Legacy grid support */
        .speakers-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 2.5rem;
        }

        /* ============================================
           SECTION H: Details (Black bg)
           ============================================ */
        .event-details {
            background: var(--bg-dark);
            color: var(--text-light);
            padding: 100px 0;
        }

        .event-details h2 {
            font-size: clamp(28px, 4vw, 40px);
            margin-bottom: 3rem;
            position: relative;
            display: inline-block;
        }

        .event-details h2::after {
            content: '';
            position: absolute;
            bottom: -15px;
            left: 0;
            width: 60px;
            height: 3px;
            background: var(--text-light);
        }

        .details-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
        }

        .detail-item {
            display: flex;
            align-items: flex-start;
            gap: 1.5rem;
            padding: 2rem;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid var(--border-light);
        }

        .detail-icon {
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--text-light);
            color: var(--bg-dark);
            font-size: 1.25rem;
            flex-shrink: 0;
        }

        .detail-content {
            flex: 1;
        }

        .detail-label {
            display: block;
            font-family: var(--font-mono);
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.15em;
            color: var(--text-grey);
            margin-bottom: 0.5rem;
        }

        .detail-value {
            font-size: 18px;
            font-weight: 500;
        }

        /* Detail item as link */
        a.detail-item--link {
            text-decoration: none;
            color: inherit;
            transition: all 0.2s ease;
        }

        a.detail-item--link:hover {
            background: rgba(255, 255, 255, 0.08);
            border-color: var(--text-light);
        }

        a.detail-item--link .detail-value i {
            font-size: 12px;
            margin-left: 0.5rem;
            opacity: 0.6;
        }

        a.detail-item--link:hover .detail-value i {
            opacity: 1;
        }

        /* ============================================
           SECTION I: Sponsors (White bg)
           ============================================ */
        .event-sponsors-section {
            background: var(--bg-light);
            color: var(--text-dark);
            padding: 100px 0;
        }

        .event-sponsors-section h2 {
            font-size: clamp(28px, 4vw, 40px);
            margin-bottom: 3rem;
            position: relative;
            display: inline-block;
        }

        .event-sponsors-section h2::after {
            content: '';
            position: absolute;
            bottom: -15px;
            left: 0;
            width: 60px;
            height: 3px;
            background: var(--text-dark);
        }

        .sponsors-level {
            margin-bottom: 3rem;
        }

        .sponsors-level h3 {
            font-family: var(--font-mono);
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.2em;
            color: var(--text-grey-dark);
            margin-bottom: 1.5rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid var(--border-dark);
        }

        .sponsors-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .sponsor-card {
            background: var(--bg-light);
            border: 2px solid var(--border-dark);
            padding: 1.5rem 2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: var(--transition);
            text-decoration: none;
        }

        .sponsor-card:hover {
            border-color: var(--bg-dark);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        .sponsors-level--platinum .sponsor-card {
            min-width: 220px;
            min-height: 110px;
        }

        .sponsors-level--gold .sponsor-card {
            min-width: 180px;
            min-height: 90px;
        }

        .sponsors-level--silver .sponsor-card,
        .sponsors-level--bronze .sponsor-card {
            min-width: 140px;
            min-height: 70px;
        }

        .sponsor-card img {
            max-width: 100%;
            max-height: 60px;
            object-fit: contain;
            filter: grayscale(100%);
            opacity: 0.7;
            transition: var(--transition);
        }

        .sponsor-card:hover img {
            filter: grayscale(0%);
            opacity: 1;
        }

        .sponsor-name {
            font-family: var(--font-mono);
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: var(--text-dark);
            font-weight: 600;
        }

        /* ============================================
           SECTION J: Final CTA (Black bg)
           ============================================ */
        .event-cta--dark {
            background: var(--bg-dark);
            color: var(--text-light);
            padding: 100px 0;
        }

        .cta-final {
            text-align: center;
        }

        .cta-final h2 {
            font-size: clamp(32px, 5vw, 56px);
            margin-bottom: 1rem;
        }

        .cta-final p {
            font-family: var(--font-mono);
            font-size: 14px;
            color: var(--text-grey);
            margin-bottom: 2.5rem;
            text-transform: uppercase;
            letter-spacing: 0.1em;
        }

        /* ============================================
           FOOTER
           ============================================ */
        .site-footer {
            background: var(--bg-dark);
            border-top: 1px solid var(--border-light);
            padding: 4rem 0 2rem;
        }

        .footer-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 2rem;
        }

        .footer-logo {
            font-family: var(--font-heading);
            font-weight: 800;
            font-size: 14px;
            color: var(--text-light);
            text-decoration: none;
            text-transform: uppercase;
            letter-spacing: 0.15em;
        }

        .footer-logo-img img {
            height: 32px;
            width: auto;
            max-width: 150px;
            object-fit: contain;
        }

        .footer-social {
            display: flex;
            justify-content: center;
            gap: 1.5rem;
            margin-top: 2rem;
        }

        .footer-social a {
            color: var(--text-grey);
            font-size: 18px;
            transition: var(--transition);
        }

        .footer-social a:hover {
            color: var(--text-light);
        }

        .footer-links {
            display: flex;
            gap: 2rem;
        }

        .footer-links a {
            font-family: var(--font-mono);
            font-size: 12px;
            color: var(--text-grey);
            text-decoration: none;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            transition: var(--transition);
        }

        .footer-links a:hover {
            color: var(--text-light);
        }

        .footer-bottom {
            margin-top: 3rem;
            padding-top: 2rem;
            border-top: 1px solid var(--border-light);
            text-align: center;
        }

        .footer-bottom p {
            font-family: var(--font-mono);
            font-size: 12px;
            color: var(--text-grey);
        }

        /* ============================================
           RESPONSIVE
           ============================================ */
        @media (max-width: 992px) {
            .container-wide {
                padding: 0 2rem;
            }

            .event-hero-grid {
                flex-direction: column;
                gap: 2rem;
            }

            .event-date-block {
                align-self: flex-start;
            }

            .cta-content {
                flex-direction: column;
                text-align: center;
            }

            .cta-info {
                flex-direction: column;
                gap: 0.5rem;
            }

            .details-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .container-wide {
                padding: 0 1rem;
                max-width: 100%;
            }

            .event-hero-minimal,
            .event-hero-parallax {
                padding: 120px 0 60px;
                min-height: auto;
                background-attachment: scroll;
            }

            .event-hero-parallax .hero-content {
                padding: 0;
            }

            .event-hero-grid {
                flex-direction: column;
                gap: 2rem;
            }

            .event-date-block {
                align-self: flex-start;
                padding: 1.5rem 2rem;
            }

            .event-date-block .day {
                font-size: 48px;
            }

            .event-title-block h1 {
                font-size: 28px;
                word-wrap: break-word;
            }

            .event-meta-inline {
                flex-direction: column;
                gap: 0.75rem;
            }

            .intro-text {
                font-size: 14px;
            }

            /* Fix text overflow on mobile */
            .description-content,
            .description-content p,
            .event-title-block,
            .event-title-block h1,
            .event-title-block .intro-text {
                max-width: 100%;
                overflow-wrap: break-word;
                word-wrap: break-word;
                word-break: break-word;
            }

            .event-intro,
            .event-description,
            .event-companies,
            .event-agenda,
            .event-speakers,
            .event-details,
            .event-sponsors-section,
            .event-cta--dark,
            .event-content-section {
                padding: 40px 0;
            }

            /* Hide 'Nuestros Espacios' block on mobile */
            .agenda-sidebar {
                display: none !important;
            }

            .agenda-layout {
                grid-template-columns: 1fr;
                gap: 2rem;
            }

            .agenda-timeline {
                padding-left: 1rem;
                margin-left: 0.5rem;
            }

            .agenda-item {
                flex-direction: column;
                gap: 0.75rem;
            }

            .agenda-item::before {
                left: -1rem;
            }

            .agenda-time {
                text-align: left;
                width: auto;
            }

            /* Content grid responsive */
            .content-grid {
                grid-template-columns: 1fr;
            }

            /* Participants grid responsive */
            .participants-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 1rem;
            }

            .participant-card {
                padding: 1rem;
                min-height: 80px;
            }

            /* Speakers carousel responsive */
            .speaker-card {
                flex: 0 0 200px;
            }

            .speaker-photo {
                height: 240px;
            }

            .speaker-info {
                padding: 1rem;
            }

            .speaker-info strong {
                font-size: 14px;
            }

            .speakers-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 1rem;
            }

            /* Details grid responsive */
            .details-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }

            .detail-item {
                padding: 1.25rem;
                gap: 1rem;
            }

            .detail-icon {
                width: 40px;
                height: 40px;
                font-size: 1rem;
            }

            .detail-value {
                font-size: 16px;
            }

            /* Sponsors responsive */
            .sponsors-grid {
                justify-content: center;
            }

            .sponsor-card {
                padding: 1rem;
            }

            .sponsors-level--platinum .sponsor-card,
            .sponsors-level--gold .sponsor-card {
                min-width: 140px;
                min-height: 70px;
            }

            .sponsors-level--silver .sponsor-card,
            .sponsors-level--bronze .sponsor-card {
                min-width: 100px;
                min-height: 50px;
            }

            /* CTA responsive */
            .cta-price {
                font-size: 24px;
            }

            .btn-lg {
                padding: 1rem 2rem;
                font-size: 14px;
            }

            /* Footer responsive */
            .footer-content {
                flex-direction: column;
                text-align: center;
            }

            .footer-links {
                flex-wrap: wrap;
                justify-content: center;
                gap: 1rem;
            }
        }
    </style>
</head>
<body>
    <!-- Header (same as main site) -->
    <header class="site-header">
        <div class="container">
            <nav class="main-nav">
                <?php
                // Check if logo is a GIF (for hover animation)
                $isAnimatedLogo = !empty($logoHeader) && preg_match('/\.gif$/i', $logoHeader);
                $logoClass = $isAnimatedLogo ? 'logo has-animated-logo' : 'logo';
                ?>
                <a href="/" class="<?= $logoClass ?>" <?= $isAnimatedLogo ? 'data-animated-src="' . htmlspecialchars($logoHeader) . '"' : '' ?>>
                    <?php if (!empty($logoHeader)): ?>
                        <?php if ($isAnimatedLogo): ?>
                            <canvas class="logo-static" height="40"></canvas>
                            <img src="<?= htmlspecialchars($logoHeader) ?>" alt="" height="40" class="logo-animated">
                        <?php else: ?>
                            <img src="<?= htmlspecialchars($logoHeader) ?>" alt="" height="40">
                        <?php endif; ?>
                    <?php else: ?>
                        THE LAST OF SAAS
                    <?php endif; ?>
                </a>

                <ul class="nav-menu" id="navMenu">
                    <?php if (!empty($mainNav)): ?>
                        <?php foreach ($mainNav as $item): ?>
                            <?php $hasChildren = !empty($item['children']); ?>
                            <li class="<?= $hasChildren ? 'has-submenu' : '' ?>">
                                <a href="<?= $item['url'] ?>" class="<?= strpos($_SERVER['REQUEST_URI'], $item['url']) === 0 && $item['url'] !== '/' ? 'active' : '' ?>">
                                    <?= htmlspecialchars($item['label']) ?>
                                    <?php if ($hasChildren): ?>
                                        <i class="fas fa-chevron-down submenu-arrow"></i>
                                    <?php endif; ?>
                                </a>
                                <?php if ($hasChildren): ?>
                                    <ul class="submenu">
                                        <?php foreach ($item['children'] as $child): ?>
                                            <?php $hasGrandchildren = !empty($child['children']); ?>
                                            <li class="<?= $hasGrandchildren ? 'has-submenu' : '' ?>">
                                                <a href="<?= $child['url'] ?>">
                                                    <?php if (!empty($child['icon'])): ?>
                                                        <i class="<?= htmlspecialchars($child['icon']) ?>"></i>
                                                    <?php endif; ?>
                                                    <?= htmlspecialchars($child['label']) ?>
                                                    <?php if ($hasGrandchildren): ?>
                                                        <i class="fas fa-chevron-right submenu-arrow-right"></i>
                                                    <?php endif; ?>
                                                </a>
                                                <?php if ($hasGrandchildren): ?>
                                                    <ul class="submenu submenu-level-3">
                                                        <?php foreach ($child['children'] as $grandchild): ?>
                                                            <li>
                                                                <a href="<?= $grandchild['url'] ?>">
                                                                    <?php if (!empty($grandchild['icon'])): ?>
                                                                        <i class="<?= htmlspecialchars($grandchild['icon']) ?>"></i>
                                                                    <?php endif; ?>
                                                                    <?= htmlspecialchars($grandchild['label']) ?>
                                                                </a>
                                                            </li>
                                                        <?php endforeach; ?>
                                                    </ul>
                                                <?php endif; ?>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>

                <div class="nav-actions">
                    <?php if (!empty($headerButtons)): ?>
                        <?php foreach ($headerButtons as $btn): ?>
                            <a href="<?= $btn['url'] ?>" class="btn btn-<?= $btn['button_style'] ?? 'primary' ?>"<?= ($btn['target'] ?? '_self') === '_blank' ? ' target="_blank" rel="noopener"' : '' ?>>
                                <?= htmlspecialchars($btn['title']) ?>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <?php if (!empty($sidebarMenu)): ?>
                        <button class="sidebar-toggle" id="sidebarToggle" aria-label="<?= htmlspecialchars($sidebarMenu['name'] ?? 'Menu') ?>">
                            <span><?= htmlspecialchars($sidebarMenu['name'] ?? 'Services') ?></span>
                            <span class="sidebar-toggle-icon">
                                <span></span>
                                <span></span>
                            </span>
                        </button>
                    <?php endif; ?>
                </div>

                <button class="mobile-toggle" id="mobileToggle" aria-label="Menú">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
            </nav>
        </div>
    </header>

    <?php if (!empty($sidebarMenu)): ?>
    <!-- Sidebar Menu -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>
    <aside class="sidebar-menu" id="sidebarMenu">
        <nav class="sidebar-nav">
            <?php foreach ($sidebarMenu['items'] as $item): ?>
                <?php if (!empty($item['children'])): ?>
                    <div class="sidebar-section">
                        <span class="sidebar-section-title"><?= htmlspecialchars($item['label']) ?></span>
                        <?php foreach ($item['children'] as $child): ?>
                            <a href="<?= $child['url'] ?>" class="sidebar-link">
                                <?php if (!empty($child['icon'])): ?>
                                    <i class="<?= htmlspecialchars($child['icon']) ?>"></i>
                                <?php endif; ?>
                                <?= htmlspecialchars($child['label']) ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <a href="<?= $item['url'] ?>" class="sidebar-link">
                        <?php if (!empty($item['icon'])): ?>
                            <i class="<?= htmlspecialchars($item['icon']) ?>"></i>
                        <?php endif; ?>
                        <?= htmlspecialchars($item['label']) ?>
                    </a>
                <?php endif; ?>
            <?php endforeach; ?>
        </nav>
    </aside>
    <?php endif; ?>

    <!-- Main Content -->
    <main>
        <?= $_content ?? '' ?>
    </main>

    <!-- Footer -->
    <footer class="site-footer">
        <div class="container-wide">
            <div class="footer-content">
                <?php if (!empty($logoHeader)): ?>
                    <a href="/" class="footer-logo footer-logo-img">
                        <img src="<?= htmlspecialchars($logoHeader) ?>" alt="" height="32">
                    </a>
                <?php else: ?>
                    <a href="/" class="footer-logo"></a>
                <?php endif; ?>
                <div class="footer-links">
                    <?php if (!empty($mainNav)): ?>
                        <?php foreach (array_slice($mainNav, 0, 5) as $item): ?>
                            <a href="<?= $item['url'] ?>"><?= htmlspecialchars($item['label']) ?></a>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <a href="/eventos">Eventos</a>
                        <a href="/sponsor/login">Sponsors</a>
                        <a href="/empresa/login">Empresas</a>
                        <a href="/privacidad">Privacidad</a>
                    <?php endif; ?>
                </div>
            </div>
            <?php if (!empty($socialLinks)): ?>
            <div class="footer-social">
                <?php foreach ($socialLinks as $social): ?>
                    <a href="<?= htmlspecialchars($social['url']) ?>" target="_blank" rel="noopener" aria-label="<?= htmlspecialchars($social['title'] ?? '') ?>">
                        <i class="<?= htmlspecialchars($social['icon'] ?? 'fas fa-link') ?>"></i>
                    </a>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
            <div class="footer-bottom">
                <p><?= !empty($footerCopyright) ? str_replace('{year}', date('Y'), htmlspecialchars($footerCopyright)) : '&copy; ' . date('Y') ?></p>
            </div>
        </div>
    </footer>

    <!-- Main site scripts (for header/nav) -->
    <script src="/assets/js/frontend.js"></script>

    <!-- Unregister any rogue Service Workers that may be caching pages -->
    <script>
    (function() {
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.getRegistrations().then(function(registrations) {
                registrations.forEach(function(registration) {
                    if (registration.scope.indexOf('/admin/tickets/scanner') === -1) {
                        registration.unregister().then(function(success) {
                            if (success && 'caches' in window) {
                                caches.keys().then(function(names) {
                                    names.forEach(function(name) { caches.delete(name); });
                                });
                            }
                        });
                    }
                });
            });
        }
    })();
    </script>

    <!-- Prevent bfcache from serving stale content -->
    <script>
    window.addEventListener('pageshow', function(event) {
        if (event.persisted) {
            window.location.reload();
        }
    });
    </script>
</body>
</html>
