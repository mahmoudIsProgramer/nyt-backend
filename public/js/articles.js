class ArticlesManager {
    constructor() {
        this.searchInput = document.getElementById('searchInput');
        this.searchButton = document.getElementById('searchButton');
        this.articlesGrid = document.getElementById('articlesGrid');
        this.paginationElement = document.getElementById('pagination');
        this.loadingElement = document.getElementById('loading');
        this.articleTemplate = document.getElementById('articleTemplate');
        
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

        // Initial search if query exists
        const urlParams = new URLSearchParams(window.location.search);
        const query = urlParams.get('q');
        if (query) {
            this.searchInput.value = query;
            this.handleSearch();
        }
    }

    async handleSearch(page = 1) {
        const query = this.searchInput.value.trim();
        if (!query) return;

        this.currentQuery = query;
        this.currentPage = page;
        this.showLoading();

        try {
            await this.searchArticles(query, page);
            this.updateURL(query, page);
        } catch (error) {
            console.error('Search error:', error);
            this.showError(`Error searching articles: ${error.message}`);
        } finally {
            this.hideLoading();
        }
    }

    async searchArticles(query = '', page = 1) {
        try {
            const response = await fetch(`/api/articles/search?q=${encodeURIComponent(query)}&page=${page}`);
            
            if (!response.ok) {
                const errorData = await response.text();
                throw new Error(`Server error ${response.status}: ${errorData}`);
            }
            
            const data = await response.json();
            console.log('API Response:', data); // Debug log
            
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
                    console.log('Pagination:', data.data.pagination); // Debug log
                    this.updatePagination(data.data.pagination);
                } else {
                    console.log('No pagination data found'); // Debug log
                    throw new Error('Pagination information is missing');
                }
            } else {
                throw new Error(data.message || 'No articles found for your search');
            }
        } catch (error) {
            console.error('Error in searchArticles:', error);
            this.articlesGrid.innerHTML = `<div class="no-results">
                <p>${error.message}</p>
                <p>Try adjusting your search terms or browse our latest articles.</p>
            </div>`;
            throw error; // Re-throw the error to be handled by handleSearch
        }
    }

    displayArticles(articles) {
        // Clear only the articles grid
        while (this.articlesGrid.firstChild) {
            this.articlesGrid.removeChild(this.articlesGrid.firstChild);
        }
        
        if (articles.length === 0) {
            const noResults = document.createElement('div');
            noResults.className = 'no-results';
            noResults.textContent = 'No articles found';
            this.articlesGrid.appendChild(noResults);
            return;
        }
        
        articles.forEach(article => {
            const card = this.createArticleCard(article);
            this.articlesGrid.appendChild(card);
        });
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

    async toggleFavorite(articleId, button) {
        try {
            const token = localStorage.getItem('token');
            if (!token) {
                window.location.href = '/login';
                return;
            }

            const response = await fetch('/api/articles/favorites', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${token}`
                },
                body: JSON.stringify({ article_id: articleId })
            });

            const data = await response.json();
            if (data.success) {
                const icon = button.querySelector('.favorite-icon');
                button.classList.toggle('active');
                icon.textContent = button.classList.contains('active') ? '♥' : '♡';
            } else {
                this.showError(data.message || 'Failed to update favorite status');
            }
        } catch (error) {
            this.showError('Failed to update favorite status');
        }
    }

    updateURL(query, page) {
        const url = new URL(window.location);
        url.searchParams.set('q', query);
        if (page > 1) {
            url.searchParams.set('page', page);
        } else {
            url.searchParams.delete('page');
        }
        window.history.pushState({}, '', url);
    }

    showLoading() {
        this.loadingElement.classList.remove('hidden');
        // this.articlesGrid.innerHTML = '';
    }

    hideLoading() {
        this.loadingElement.classList.add('hidden');
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
