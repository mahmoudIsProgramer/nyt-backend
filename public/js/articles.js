class ArticlesManager {
    constructor() {
        this.searchInput = document.getElementById('searchInput');
        this.searchButton = document.getElementById('searchButton');
        this.articlesGrid = document.getElementById('articlesGrid');
        this.paginationElement = document.getElementById('pagination');
        this.loadingElement = document.getElementById('loading');
        this.articleTemplate = document.getElementById('articleTemplate');
        this.toggleAdvancedButton = document.getElementById('toggleAdvanced');
        this.advancedSearchForm = document.getElementById('advancedSearch');
        
        this.currentPage = 1;
        this.currentQuery = '';
        
        this.init();
    }

    init() {
        // Event listeners
        this.searchButton.addEventListener('click', () => this.handleSearch());
        this.searchInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                this.handleSearch();
            }
        });

        // Toggle advanced search
        this.toggleAdvancedButton.addEventListener('click', () => {
            const isHidden = this.advancedSearchForm.style.display === 'none';
            this.advancedSearchForm.style.display = isHidden ? 'block' : 'none';
            this.toggleAdvancedButton.textContent = isHidden ? 'Hide Advanced' : 'Advanced Search';
        });

        // Initial search if query exists
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('q')) {
            this.searchInput.value = urlParams.get('q');
            this.populateAdvancedFields(urlParams);
            this.handleSearch();
        }
    }

    populateAdvancedFields(params) {
        // Populate all advanced search fields from URL parameters
        const fields = ['begin_date', 'end_date', 'sort', 'fl', 'fq', 'facet', 'facet_filter'];
        fields.forEach(field => {
            const element = document.getElementById(field);
            if (element && params.has(field)) {
                element.value = params.get(field);
            }
        });

        // Handle facet_fields separately as it's a multi-select
        const facetFields = document.getElementById('facet_fields');
        if (facetFields && params.has('facet_fields')) {
            const selectedFields = params.get('facet_fields').split(',');
            Array.from(facetFields.options).forEach(option => {
                option.selected = selectedFields.includes(option.value);
            });
        }
    }

    getSearchParams() {
        const params = new URLSearchParams();
        
        // Add basic search query
        const query = this.searchInput.value.trim();
        if (query) params.set('q', query);

        // Add advanced search parameters
        const advancedParams = {
            sort: document.getElementById('sort'),
            fl: document.getElementById('fl'),
            fq: document.getElementById('fq'),
            facet: document.getElementById('facet'),
            facet_filter: document.getElementById('facet_filter')
        };

        // Handle date fields - convert from YYYY-MM-DD to YYYYMMDD
        const beginDate = document.getElementById('begin_date');
        const endDate = document.getElementById('end_date');
        
        if (beginDate && beginDate.value) {
            params.set('begin_date', beginDate.value.replace(/-/g, ''));
        }
        if (endDate && endDate.value) {
            params.set('end_date', endDate.value.replace(/-/g, ''));
        }

        // Add values for single-value fields
        Object.entries(advancedParams).forEach(([key, element]) => {
            if (element && element.value) {
                params.set(key, element.value);
            }
        });

        // Handle facet_fields (multi-select)
        const facetFields = document.getElementById('facet_fields');
        if (facetFields) {
            const selectedFields = Array.from(facetFields.selectedOptions)
                .map(option => option.value)
                .filter(value => value);
            if (selectedFields.length > 0) {
                params.set('facet_fields', selectedFields.join(','));
            }
        }

        // Add current page
        if (this.currentPage > 1) {
            params.set('page', this.currentPage);
        }

        return params;
    }

    async handleSearch(page = 1) {
        this.currentPage = page;
        this.showLoading();

        try {
            const params = this.getSearchParams();
            await this.searchArticles(params);
            this.updateURL(params);
        } catch (error) {
            console.error('Search error:', error);
            this.showError(`Error searching articles: ${error.message}`);
        } finally {
            this.hideLoading();
        }
    }

    async searchArticles(params) {
        const response = await fetch(`/api/articles/search?${params.toString()}`);
        if (!response.ok) {
            const errorData = await response.json();
            throw new Error(errorData.message || `HTTP error! status: ${response.status}`);
        }
        const data = await response.json();
        this.displayResults(data);
    }

    displayResults(data) {
        // Clear only the articles grid
        while (this.articlesGrid.firstChild) {
            this.articlesGrid.removeChild(this.articlesGrid.firstChild);
        }
        
        if (data.status === 'success' && data.data && data.data.articles) {
            // Create temporary container for new articles
            const tempContainer = document.createElement('div');
            tempContainer.style.display = 'none';
            document.body.appendChild(tempContainer);

            // Create new articles in the temporary container
            data.data.articles.forEach(article => {
                const articleCard = this.createArticleCard(article);
                tempContainer.appendChild(articleCard);
            });

            // Only clear the grid and add new articles when everything is ready
            this.articlesGrid.innerHTML = '';
            while (tempContainer.firstChild) {
                this.articlesGrid.appendChild(tempContainer.firstChild);
            }
            
            // Remove the temporary container
            tempContainer.remove();

            // Update pagination if meta data is available
            if (data.data.pagination) {
                this.updatePagination(data.data.pagination);
                this.paginationElement.style.display = 'flex';
            } else {
                this.paginationElement.style.display = 'none';
            }
        } else {
            this.articlesGrid.innerHTML = `<div class="no-results">
                <p>${data.message || 'No articles found for your search'}</p>
                <p>Try adjusting your search terms or browse our latest articles.</p>
            </div>`;
            this.paginationElement.style.display = 'none';
        }
    }

    createArticleCard(article) {
        const template = document.getElementById('articleTemplate');
        const card = template.content.cloneNode(true);
        
        // Get elements
        const title = card.querySelector('.article-title');
        const abstract = card.querySelector('.article-abstract');
        const date = card.querySelector('.article-date');
        const section = card.querySelector('.article-section');
        const image = card.querySelector('.article-image img');
        const readMore = card.querySelector('.read-more');
        
        // Set content
        title.textContent = article.headline.main;
        abstract.textContent = article.snippet;
        date.textContent = new Date(article.pub_date).toLocaleDateString();
        section.textContent = article.section_name;
        
        // Set image if available
        if (article.multimedia && article.multimedia.length > 0) {
            // Find a suitable image (preferring medium size)
            const mediumImage = article.multimedia.find(media => 
                media.subtype === 'mediumThreeByTwo440' || 
                media.subtype === 'articleLarge' ||
                media.subtype === 'master675'
            );
            
            if (mediumImage) {
                // Add NYT base URL to the image path
                const imageUrl = `https://static01.nyt.com/${mediumImage.url}`;
                image.src = imageUrl;
                image.alt = article.headline.main;
                
                // Add error handling for image load failures
                image.onerror = () => {
                    console.warn(`Failed to load image from ${imageUrl}, falling back to placeholder`);
                    image.src = 'https://static01.nyt.com/images/misc/nytco/placeholder.png';
                    image.alt = 'New York Times';
                };
            } else {
                // Default NYT placeholder image with descriptive alt text
                image.src = 'https://static01.nyt.com/images/misc/nytco/placeholder.png';
                image.alt = 'New York Times Placeholder Image';
            }
        } else {
            // Default NYT placeholder image with descriptive alt text
            image.src = 'https://static01.nyt.com/images/misc/nytco/placeholder.png';
            image.alt = 'New York Times Placeholder Image';
        }
        
        // Set read more link
        readMore.href = article.web_url;
        readMore.target = '_blank';
        
        return card.firstElementChild;
    }

    updatePagination(pagination) {
        console.log('Updating pagination with:', pagination); // Debug log
        
        const paginationContainer = document.getElementById('pagination');
        if (!paginationContainer) {
            console.error('Pagination container not found!');
            return;
        }
        
        paginationContainer.innerHTML = '';

        const currentPage = parseInt(pagination.current_page) || 1;
        const totalPages = parseInt(pagination.total_pages) || 1;
        
        console.log(`Current page: ${currentPage}, Total pages: ${totalPages}`); // Debug log

        // Create pagination controls
        const createPageButton = (pageNum, text, isActive = false, isDisabled = false) => {
            const button = document.createElement('button');
            button.textContent = text;
            button.className = `pagination-button ${isActive ? 'active' : ''} ${isDisabled ? 'disabled' : ''}`;
            if (!isDisabled) {
                button.addEventListener('click', () => {
                    this.handleSearch(pageNum);
                });
            }
            return button;
        };

        // Previous button
        paginationContainer.appendChild(
            createPageButton(currentPage - 1, '←', false, currentPage === 1)
        );

        // First page
        paginationContainer.appendChild(createPageButton(1, '1', currentPage === 1));

        // Ellipsis if needed
        if (currentPage > 3) {
            const ellipsis = document.createElement('span');
            ellipsis.textContent = '...';
            ellipsis.className = 'pagination-ellipsis';
            paginationContainer.appendChild(ellipsis);
        }

        // Current page and surrounding pages
        for (let i = Math.max(2, currentPage - 1); i <= Math.min(totalPages - 1, currentPage + 1); i++) {
            if (i !== 1 && i !== totalPages) { // Skip first and last page as they're always shown
                paginationContainer.appendChild(
                    createPageButton(i, i.toString(), i === currentPage)
                );
            }
        }

        // Ellipsis if needed
        if (currentPage < totalPages - 2) {
            const ellipsis = document.createElement('span');
            ellipsis.textContent = '...';
            ellipsis.className = 'pagination-ellipsis';
            paginationContainer.appendChild(ellipsis);
        }

        // Last page
        if (totalPages > 1) {
            paginationContainer.appendChild(
                createPageButton(totalPages, totalPages.toString(), currentPage === totalPages)
            );
        }

        // Next button
        paginationContainer.appendChild(
            createPageButton(currentPage + 1, '→', false, currentPage >= totalPages)
        );
    }

    updateURL(params) {
        const newURL = `${window.location.pathname}?${params.toString()}`;
        window.history.pushState({ path: newURL }, '', newURL);
    }

    showLoading() {
        // Hide articles and pagination
        if (this.articlesGrid) this.articlesGrid.style.display = 'none';
        if (this.paginationElement) this.paginationElement.style.display = 'none';
        // Show loading spinner
        if (this.loadingElement) this.loadingElement.classList.remove('hidden');
    }

    hideLoading() {
        // Show articles and pagination
        if (this.articlesGrid) this.articlesGrid.style.display = 'grid';
        if (this.paginationElement) this.paginationElement.style.display = 'flex';
        // Hide loading spinner
        if (this.loadingElement) this.loadingElement.classList.add('hidden');
    }

    showError(message) {
        const errorDiv = document.createElement('div');
        errorDiv.className = 'error-message';
        errorDiv.textContent = message;
        document.body.appendChild(errorDiv);
        
        // Remove the error message after animation completes
        setTimeout(() => {
            errorDiv.remove();
        }, 3500); // Slightly longer than the animation duration
    }
}

// Initialize the articles manager when the DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    new ArticlesManager();
});
