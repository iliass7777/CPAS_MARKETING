/**
 * AJAX API Client for Back-Office Management
 * Handles all CRUD operations with proper error handling and loading states
 */

class APIClient {
    constructor(baseUrl = './api/') {
        this.baseUrl = baseUrl;
        this.defaultHeaders = {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        };
    }

    /**
     * Generic fetch wrapper with error handling
     */
    async request(url, options = {}) {
        const config = {
            headers: { ...this.defaultHeaders, ...options.headers },
            ...options
        };

        try {
            const response = await fetch(this.baseUrl + url, config);
            const data = await response.json();
            
            if (!response.ok) {
                throw new Error(data.message || `HTTP error! status: ${response.status}`);
            }
            
            return data;
        } catch (error) {
            console.error('API Request failed:', error);
            throw error;
        }
    }

    // Websites API
    async getWebsites() {
        return await this.request('websites.php');
    }

    async getWebsite(id) {
        return await this.request(`websites.php?id=${id}`);
    }

    async createWebsite(websiteData) {
        return await this.request('websites.php', {
            method: 'POST',
            body: JSON.stringify(websiteData)
        });
    }

    async updateWebsite(id, websiteData) {
        return await this.request('websites.php', {
            method: 'PUT',
            body: JSON.stringify({ ...websiteData, id })
        });
    }

    async deleteWebsite(id) {
        return await this.request(`websites.php?id=${id}`, {
            method: 'DELETE'
        });
    }

    // Categories API
    async getCategories() {
        return await this.request('categories.php');
    }

    async getCategory(id) {
        return await this.request(`categories.php?id=${id}`);
    }

    async createCategory(categoryData) {
        return await this.request('categories.php', {
            method: 'POST',
            body: JSON.stringify(categoryData)
        });
    }

    async updateCategory(id, categoryData) {
        return await this.request('categories.php', {
            method: 'PUT',
            body: JSON.stringify({ ...categoryData, id })
        });
    }

    async deleteCategory(id) {
        return await this.request(`categories.php?id=${id}`, {
            method: 'DELETE'
        });
    }

    // Reviews API
    async getReviews(status = 'all') {
        const url = status !== 'all' ? `reviews.php?status=${status}` : 'reviews.php';
        return await this.request(url);
    }

    async getReview(id) {
        return await this.request(`reviews.php?id=${id}`);
    }

    async createReview(reviewData) {
        return await this.request('reviews.php', {
            method: 'POST',
            body: JSON.stringify(reviewData)
        });
    }

    async updateReview(id, reviewData) {
        return await this.request('reviews.php', {
            method: 'PUT',
            body: JSON.stringify({ ...reviewData, id })
        });
    }

    async updateReviewStatus(id, status) {
        return await this.request('reviews.php', {
            method: 'PUT',
            body: JSON.stringify({ id, status, action: 'update_status' })
        });
    }

    async deleteReview(id) {
        return await this.request(`reviews.php?id=${id}`, {
            method: 'DELETE'
        });
    }
}

// UI Helper Functions
class UIHelpers {
    static showLoading(button) {
        if (button) {
            button.disabled = true;
            button.innerHTML = `<span class="material-symbols-outlined animate-spin">refresh</span> Loading...`;
        }
    }

    static hideLoading(button, originalText) {
        if (button) {
            button.disabled = false;
            button.innerHTML = originalText;
        }
    }

    static showMessage(message, type = 'success') {
        // Remove existing messages
        const existingMessage = document.querySelector('.ajax-message');
        if (existingMessage) {
            existingMessage.remove();
        }

        const messageDiv = document.createElement('div');
        messageDiv.className = `ajax-message bg-${type === 'error' ? 'red' : 'green'}-50 dark:bg-${type === 'error' ? 'red' : 'green'}-900/20 border border-${type === 'error' ? 'red' : 'green'}-200 dark:border-${type === 'error' ? 'red' : 'green'}-800 rounded-xl p-4 mb-6`;
        messageDiv.innerHTML = `
            <div class="flex items-center justify-between">
                <p class="text-${type === 'error' ? 'red' : 'green'}-700 dark:text-${type === 'error' ? 'red' : 'green'}-400">${message}</p>
                <button onclick="this.parentElement.parentElement.remove()" class="text-${type === 'error' ? 'red' : 'green'}-500 hover:text-${type === 'error' ? 'red' : 'green'}-700">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
        `;

        // Insert after page heading
        const pageHeading = document.querySelector('.space-y-1');
        if (pageHeading) {
            pageHeading.parentNode.insertBefore(messageDiv, pageHeading.nextSibling);
        }

        // Auto remove after 5 seconds
        setTimeout(() => {
            if (messageDiv && messageDiv.parentElement) {
                messageDiv.remove();
            }
        }, 5000);
    }

    static confirmDelete(itemName = 'item') {
        return confirm(`Are you sure you want to delete this ${itemName}? This action cannot be undone.`);
    }
}

// Initialize API client globally
window.apiClient = new APIClient();
window.UIHelpers = UIHelpers;

// Export for modules
if (typeof module !== 'undefined' && module.exports) {
    module.exports = { APIClient, UIHelpers };
}