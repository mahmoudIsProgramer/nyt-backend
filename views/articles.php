<?php
// Content for the articles page
?>
<div class="page-wrapper">
    <!-- Search Section -->
    <header class="top-bar">
        <div class="search-container">
            <h1>Search NYT Articles</h1>
            <div class="search-box">
                <input type="text" id="searchInput" placeholder="Search articles..." value="<?= htmlspecialchars($query ?? '') ?>">
                <button id="searchButton">Search</button>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Results Section -->
        <div class="articles-grid" id="articlesGrid">
            <!-- Articles will be loaded here dynamically -->
        </div>

        <!-- Pagination -->
        <div class="pagination" id="pagination">
            <!-- Pagination will be added here dynamically -->
        </div>

        <!-- Loading Indicator -->
        <div id="loading" class="loading hidden">
            <div class="spinner"></div>
        </div>
    </main>
</div>

<!-- Article Template -->
<template id="articleTemplate">
    <div class="article-card">
        <div class="article-image">
            <img src="" alt="Article thumbnail">
        </div>
        <div class="article-content">
            <h3 class="article-title"></h3>
            <p class="article-abstract"></p>
            <div class="article-meta">
                <span class="article-date"></span>
                <span class="article-section"></span>
            </div>
            <div class="article-actions">
                <a href="#" class="read-more">Read More</a>
                <button class="favorite-btn">
                    <span class="favorite-icon">♡</span>
                </button>
            </div>
        </div>
    </div>
</template>
