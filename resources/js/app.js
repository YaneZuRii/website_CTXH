// Modern JS Utils - resources/js/app.js
// Shared across all views - ES6 Modules compatible

class CTXHApp {
  constructor() {
    this.API_BASE = import.meta.env.VITE_API_URL || 'http://localhost:3000/api';
    this.init();
  }

  init() {
    this.bindGlobalEvents();
    this.loadTheme();
  }

  // API Client with error handling
  async api(endpoint, options = {}) {
    try {
      const url = `${this.API_BASE}${endpoint}`;
      const config = {
        headers: { 'Content-Type': 'application/json', ...options.headers },
        ...options
      };
      const res = await fetch(url, config);
      if (!res.ok) throw new Error(`API Error: ${res.status}`);
      return await res.json();
    } catch (error) {
      this.toast(`Lỗi kết nối: ${error.message}`, 'error');
      throw error;
    }
  }

  // Toast notifications
  toast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    toast.textContent = message;
    document.body.appendChild(toast);
    
    // Auto remove
    setTimeout(() => {
      toast.classList.add('show');
      setTimeout(() => {
        toast.remove();
      }, 3000);
    }, 100);
  }

  // Form handling with validation
  handleForm(form, callback) {
    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      const formData = new FormData(form);
      const btn = form.querySelector('button[type="submit"]');
      const originalText = btn.textContent;
      
      btn.disabled = true;
      btn.innerHTML = '<span class="spinner me-1"></span>Đang xử lý...';
      
      try {
        await callback(Object.fromEntries(formData));
        this.toast('Thao tác thành công!');
      } catch (error) {
        console.error(error);
      } finally {
        btn.disabled = false;
        btn.textContent = originalText;
      }
    });
  }

  // Debounced search
  debounceSearch(input, callback, delay = 300) {
    let timeout;
    input.addEventListener('input', (e) => {
      clearTimeout(timeout);
      timeout = setTimeout(() => callback(e.target.value), delay);
    });
  }

  // Theme toggle
  toggleTheme() {
    const theme = document.documentElement.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
    document.documentElement.setAttribute('data-theme', theme);
    localStorage.setItem('theme', theme);
  }

  loadTheme() {
    const saved = localStorage.getItem('theme');
    if (saved) document.documentElement.setAttribute('data-theme', saved);
  }

  // User list renderer
  renderUserList(container, users) {
    if (!users.length) {
      container.innerHTML = '<div class="card p-4 text-center">Chưa có dữ liệu.</div>';
      return;
    }
    
    container.innerHTML = users.map(user => `
      <div class="user-item fade-in-up">
        <div class="user-info">
          <div class="user-avatar">${user.name.charAt(0).toUpperCase()}</div>
          <div>
            <div class="user-name">${user.name}</div>
            <div class="user-id">ID: ${user.id}</div>
          </div>
        </div>
        <div class="status-badge ${user.status === 'checked_in' ? 'status-success' : ''}">
          ${user.status === 'checked_in' ? ' Đã điểm danh' : ' Chưa điểm danh'}
        </div>
      </div>
    `).join('');
  }

  bindGlobalEvents() {
    // Theme toggle if exists
    document.addEventListener('click', (e) => {
      if (e.target.matches('[data-theme-toggle]')) this.toggleTheme();
    });
  }
}

// Global app instance
const app = new CTXHApp();

// Export for modules
if (typeof module !== 'undefined') {
  module.exports = { CTXHApp };
}

// Auto-init when DOM ready
document.addEventListener('DOMContentLoaded', () => app.init());
