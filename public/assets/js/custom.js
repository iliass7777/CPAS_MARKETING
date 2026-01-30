// Dark mode toggle functionality
document.addEventListener('DOMContentLoaded', function() {
    const themeToggle = document.getElementById('theme-toggle');
    const html = document.documentElement;

    // Check for saved theme preference or default to light mode
    const currentTheme = localStorage.getItem('theme') || 'light';
    if (currentTheme === 'dark') {
        html.classList.remove('light');
        html.classList.add('dark');
    }
    updateToggleIcon(currentTheme);

    function updateToggleIcon(theme) {
        const icon = themeToggle?.querySelector('.material-symbols-outlined');
        if (icon) {
            icon.textContent = theme === 'dark' ? 'light_mode' : 'dark_mode';
        }
    }

    if (themeToggle) {
        themeToggle.addEventListener('click', () => {
            const isDark = html.classList.contains('dark');
            if (isDark) {
                html.classList.remove('dark');
                html.classList.add('light');
                localStorage.setItem('theme', 'light');
                updateToggleIcon('light');
            } else {
                html.classList.remove('light');
                html.classList.add('dark');
                localStorage.setItem('theme', 'dark');
                updateToggleIcon('dark');
            }
        });
    }
});

/**
 * AJAX CRUD Manager
 * Handles all CRUD operations with AJAX for better UX
 */
class CRUDManager {
    constructor(entityType, apiClient) {
        this.entityType = entityType;
        this.api = apiClient;
        this.currentEditId = null;
        this.setupEventListeners();
    }

    setupEventListeners() {
        // Create button
        const createBtn = document.getElementById('open-drawer-btn');
        if (createBtn) {
            createBtn.addEventListener('click', (e) => {
                e.preventDefault();
                this.openCreateForm();
            });
        }

        // Close drawer buttons
        const closeBtn = document.getElementById('close-drawer-btn');
        const backdrop = document.querySelector('[id$="-drawer-backdrop"]');
        
        if (closeBtn) {
            closeBtn.addEventListener('click', (e) => {
                e.preventDefault();
                this.closeDrawer();
            });
        }
        
        if (backdrop) {
            backdrop.addEventListener('click', () => {
                this.closeDrawer();
            });
        }

        // Form submission
        const form = document.querySelector('form');
        if (form && !form.hasAttribute('data-ajax-enabled')) {
            form.setAttribute('data-ajax-enabled', 'true');
            form.addEventListener('submit', (e) => {
                e.preventDefault();
                this.handleFormSubmit(e);
            });
        }

        // Escape key to close drawer
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                this.closeDrawer();
            }
        });
    }

    async handleFormSubmit(e) {
        const form = e.target;
        const formData = new FormData(form);
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalBtnText = submitBtn.innerHTML;
        
        // Show loading state
        UIHelpers.showLoading(submitBtn);

        try {
            const data = this.formDataToObject(formData);
            let response;

            if (this.currentEditId) {
                // Update existing item
                response = await this.updateEntity(this.currentEditId, data);
            } else {
                // Create new item
                response = await this.createEntity(data);
            }

            if (response.success) {
                UIHelpers.showMessage(response.message, 'success');
                this.closeDrawer();
                this.refreshTable();
            } else {
                UIHelpers.showMessage(response.message, 'error');
            }
        } catch (error) {
            console.error('Form submission error:', error);
            UIHelpers.showMessage('An error occurred. Please try again.', 'error');
        } finally {
            UIHelpers.hideLoading(submitBtn, originalBtnText);
        }
    }

    async createEntity(data) {
        switch (this.entityType) {
            case 'website':
                return await this.api.createWebsite(data);
            case 'category':
                return await this.api.createCategory(data);
            case 'review':
                return await this.api.createReview(data);
            default:
                throw new Error('Unknown entity type');
        }
    }

    async updateEntity(id, data) {
        switch (this.entityType) {
            case 'website':
                return await this.api.updateWebsite(id, data);
            case 'category':
                return await this.api.updateCategory(id, data);
            case 'review':
                return await this.api.updateReview(id, data);
            default:
                throw new Error('Unknown entity type');
        }
    }

    async deleteEntity(id) {
        switch (this.entityType) {
            case 'website':
                return await this.api.deleteWebsite(id);
            case 'category':
                return await this.api.deleteCategory(id);
            case 'review':
                return await this.api.deleteReview(id);
            default:
                throw new Error('Unknown entity type');
        }
    }

    formDataToObject(formData) {
        const obj = {};
        for (const [key, value] of formData.entries()) {
            if (key !== 'create' && key !== 'update' && key !== 'delete') {
                obj[key] = value;
            }
        }
        return obj;
    }

    openCreateForm() {
        this.currentEditId = null;
        this.resetForm();
        this.updateFormTitle('Create');
        this.openDrawer();
    }

    async openEditForm(id) {
        try {
            this.currentEditId = id;
            
            let response;
            switch (this.entityType) {
                case 'website':
                    response = await this.api.getWebsite(id);
                    break;
                case 'category':
                    response = await this.api.getCategory(id);
                    break;
                case 'review':
                    response = await this.api.getReview(id);
                    break;
                default:
                    throw new Error('Unknown entity type');
            }

            if (response.success) {
                this.populateForm(response.data);
                this.updateFormTitle('Edit');
                this.openDrawer();
            } else {
                UIHelpers.showMessage('Failed to load item for editing', 'error');
            }
        } catch (error) {
            console.error('Edit form error:', error);
            UIHelpers.showMessage('Failed to load item for editing', 'error');
        }
    }

    async handleDelete(id, itemName) {
        if (!UIHelpers.confirmDelete(itemName)) {
            return;
        }

        try {
            const response = await this.deleteEntity(id);
            if (response.success) {
                UIHelpers.showMessage(response.message, 'success');
                this.refreshTable();
            } else {
                UIHelpers.showMessage(response.message, 'error');
            }
        } catch (error) {
            console.error('Delete error:', error);
            UIHelpers.showMessage('Failed to delete item', 'error');
        }
    }

    openDrawer() {
        const drawer = document.querySelector('.fixed.inset-0.z-40');
        const backdrop = drawer?.querySelector('[id$="-drawer-backdrop"]');
        const section = backdrop?.nextElementSibling;

        if (drawer && backdrop && section) {
            drawer.classList.remove('pointer-events-none');
            drawer.setAttribute('aria-hidden', 'false');
            backdrop.classList.remove('opacity-0', 'pointer-events-none');
            backdrop.classList.add('opacity-100', 'pointer-events-auto');
            section.classList.remove('translate-x-full');
            section.classList.add('translate-x-0');
        }
    }

    closeDrawer() {
        const drawer = document.querySelector('.fixed.inset-0.z-40');
        const backdrop = drawer?.querySelector('[id$="-drawer-backdrop"]');
        const section = backdrop?.nextElementSibling;

        if (drawer && backdrop && section) {
            drawer.classList.add('pointer-events-none');
            drawer.setAttribute('aria-hidden', 'true');
            backdrop.classList.add('opacity-0', 'pointer-events-none');
            backdrop.classList.remove('opacity-100', 'pointer-events-auto');
            section.classList.add('translate-x-full');
            section.classList.remove('translate-x-0');
        }

        this.currentEditId = null;
        this.resetForm();
    }

    resetForm() {
        const form = document.querySelector('form');
        if (form) {
            form.reset();
            
            // Remove hidden input fields
            const hiddenInputs = form.querySelectorAll('input[name="id"], input[name="update"], input[name="create"]');
            hiddenInputs.forEach(input => input.remove());
        }
    }

    populateForm(data) {
        Object.keys(data).forEach(key => {
            const input = document.querySelector(`[name="${key}"]`);
            if (input) {
                input.value = data[key] || '';
            }
        });
    }

    updateFormTitle(action) {
        const title = document.querySelector('h3');
        const submitBtn = document.querySelector('button[type="submit"]');
        
        if (title) {
            const entityName = this.entityType.charAt(0).toUpperCase() + this.entityType.slice(1);
            title.textContent = `${action} ${entityName}`;
        }
        
        if (submitBtn) {
            submitBtn.innerHTML = `${action} ${this.entityType.charAt(0).toUpperCase() + this.entityType.slice(1)}`;
        }
    }

    refreshTable() {
        // Reload the page to refresh the table
        // In a more advanced implementation, you could update the DOM directly
        setTimeout(() => {
            window.location.reload();
        }, 1000);
    }
}

// Initialize CRUD managers based on page
document.addEventListener('DOMContentLoaded', function() {
    if (typeof window.apiClient === 'undefined') {
        console.error('API Client not loaded. Please include api-client.js');
        return;
    }

    // Determine page type and initialize appropriate manager
    const currentPage = window.location.pathname;
    let crudManager;

    if (currentPage.includes('websites.php')) {
        crudManager = new CRUDManager('website', window.apiClient);
        
        // Global edit function for websites
        window.editWebsite = function(id) {
            crudManager.openEditForm(id);
        };
        
        // Global delete function for websites
        window.deleteWebsite = function(id, name) {
            crudManager.handleDelete(id, `website "${name}"`);
        };
        
    } else if (currentPage.includes('categories.php')) {
        crudManager = new CRUDManager('category', window.apiClient);
        
        // Global edit function for categories
        window.editCategory = function(id) {
            crudManager.openEditForm(id);
        };
        
        // Global delete function for categories
        window.deleteCategory = function(id, name) {
            crudManager.handleDelete(id, `category "${name}"`);
        };
        
    } else if (currentPage.includes('reviews.php')) {
        crudManager = new CRUDManager('review', window.apiClient);
        
        // Global edit function for reviews
        window.editReview = function(id) {
            crudManager.openEditForm(id);
        };
        
        // Global delete function for reviews
        window.deleteReview = function(id, authorName) {
            crudManager.handleDelete(id, `review by "${authorName}"`);
        };
        
        // Global status update function for reviews
        window.updateReviewStatus = async function(id, status) {
            try {
                const response = await window.apiClient.updateReviewStatus(id, status);
                if (response.success) {
                    UIHelpers.showMessage(response.message, 'success');
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    UIHelpers.showMessage(response.message, 'error');
                }
            } catch (error) {
                console.error('Status update error:', error);
                UIHelpers.showMessage('Failed to update review status', 'error');
            }
        };
    }

    // Make manager globally available for debugging
    window.crudManager = crudManager;
});
