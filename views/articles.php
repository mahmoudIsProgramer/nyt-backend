<?php
// Content for the articles page
?>
<div class="page-wrapper">
    <!-- Search Section -->
    <header class="top-bar">
        <div class="search-container">
            <h1>Search NYT Articles</h1>
            <div class="search-box">
                <input type="text" id="searchInput" name="q" placeholder="Search articles..." value="<?= htmlspecialchars($query ?? '') ?>">
                <button type="button" id="toggleAdvanced">Advanced Search</button>
                <button id="searchButton">Search</button>
            </div>
            
            <!-- Advanced Search Form -->
            <div id="advancedSearch" class="advanced-search" style="display: none;">
                <div class="form-row">
                    <div class="form-group">
                        <label for="begin_date">Begin Date:</label>
                        <input type="date" id="begin_date" name="begin_date" placeholder="YYYYMMDD">
                    </div>
                    <div class="form-group">
                        <label for="end_date">End Date:</label>
                        <input type="date" id="end_date" name="end_date" placeholder="YYYYMMDD">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="sort">Sort By:</label>
                        <select id="sort" name="sort">
                            <option value="relevance">Relevance</option>
                            <option value="newest">Newest</option>
                            <option value="oldest">Oldest</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="facet_fields">Facet Fields:</label>
                        <select id="facet_fields" name="facet_fields" multiple>
                            <option value="day_of_week">Day of Week</option>
                            <option value="document_type">Document Type</option>
                            <option value="ingredients">Ingredients</option>
                            <option value="news_desk">News Desk</option>
                            <option value="pub_month">Publication Month</option>
                            <option value="pub_year">Publication Year</option>
                            <option value="section_name">Section Name</option>
                            <option value="source">Source</option>
                            <option value="subsection_name">Subsection Name</option>
                            <option value="type_of_material">Type of Material</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="fl">Field List (comma-separated):</label>
                        <input type="text" id="fl" name="fl" placeholder="e.g., headline,pub_date,section_name">
                    </div>
                    <div class="form-group">
                        <label for="fq">Filter Query:</label>
                        <input type="text" id="fq" name="fq" placeholder="Filter query">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="facet">Enable Faceting:</label>
                        <select id="facet" name="facet">
                            <option value="">Select</option>
                            <option value="true">Yes</option>
                            <option value="false">No</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="facet_filter">Enable Facet Filtering:</label>
                        <select id="facet_filter" name="facet_filter">
                            <option value="">Select</option>
                            <option value="true">Yes</option>
                            <option value="false">No</option>
                        </select>
                    </div>
                </div>
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
