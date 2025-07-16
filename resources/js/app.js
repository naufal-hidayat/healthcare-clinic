import './bootstrap';

// Advanced Chatbot dengan AI capabilities
class HealthCareChatbot {
    constructor() {
        this.sessionId = null;
        this.conversationHistory = [];
        this.isTyping = false;
        this.apiEndpoint = '/api/chatbot/chat';
        this.suggestions = [];
        
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.loadConversationHistory();
    }

    setupEventListeners() {
        // Input event listeners
        const chatInput = document.getElementById('chatInput');
        if (chatInput) {
            chatInput.addEventListener('keypress', (e) => this.handleKeyPress(e));
            chatInput.addEventListener('input', () => this.handleInputChange());
        }

        // Send button
        const sendButton = document.querySelector('.chatbot-send');
        if (sendButton) {
            sendButton.addEventListener('click', () => this.sendMessage());
        }

        // Toggle button
        const toggleButton = document.querySelector('.chatbot-toggle');
        if (toggleButton) {
            toggleButton.addEventListener('click', () => this.toggleChatbot());
        }

        // Close button
        const closeButton = document.querySelector('.chatbot-close');
        if (closeButton) {
            closeButton.addEventListener('click', () => this.toggleChatbot());
        }
    }

    generateSessionId() {
        return 'session_' + Math.random().toString(36).substr(2, 9) + '_' + Date.now();
    }

    toggleChatbot() {
        const window = document.getElementById('chatbotWindow');
        const isVisible = window.style.display === 'flex';
        window.style.display = isVisible ? 'none' : 'flex';
        
        if (!isVisible && !this.sessionId) {
            this.sessionId = this.generateSessionId();
            this.showWelcomeMessage();
        }

        // Focus on input when opened
        if (!isVisible) {
            setTimeout(() => {
                document.getElementById('chatInput')?.focus();
            }, 100);
        }
    }

    showWelcomeMessage() {
        const welcomeMessages = [
            "Halo! 👋 Saya adalah asisten virtual HealthCare Plus. Bagaimana saya bisa membantu Anda hari ini?",
            "Selamat datang di HealthCare Plus! 😊 Saya di sini untuk membantu menjawab pertanyaan tentang layanan kesehatan kami.",
            "Hi! Saya asisten AI HealthCare Plus. Ada yang ingin Anda tanyakan tentang kesehatan atau layanan kami?"
        ];
        
        const randomMessage = welcomeMessages[Math.floor(Math.random() * welcomeMessages.length)];
        
        // Clear existing messages and show new welcome
        const messagesContainer = document.getElementById('chatbotMessages');
        if (messagesContainer) {
            messagesContainer.innerHTML = '';
            this.addMessage(randomMessage, 'bot', [
                'Jam operasional klinik',
                'Layanan yang tersedia',
                'Cara membuat janji',
                'Lokasi klinik'
            ]);
        }
    }

    handleKeyPress(event) {
        if (event.key === 'Enter' && !event.shiftKey) {
            event.preventDefault();
            this.sendMessage();
        }
    }

    handleInputChange() {
        const input = document.getElementById('chatInput');
        if (input && input.value.length > 0) {
            // Show that user is typing (future enhancement)
            this.detectIntent(input.value);
        }
    }

    detectIntent(message) {
        // Simple intent detection for UI hints
        const intents = {
            'appointment': ['janji', 'daftar', 'booking', 'reservasi'],
            'emergency': ['darurat', 'emergency', 'urgent', 'segera'],
            'symptoms': ['sakit', 'nyeri', 'demam', 'batuk', 'pusing'],
            'services': ['layanan', 'biaya', 'harga', 'tarif'],
            'location': ['alamat', 'lokasi', 'dimana']
        };

        // Could add visual hints based on detected intent
        return 'general';
    }

    async sendMessage() {
        const input = document.getElementById('chatInput');
        const message = input.value.trim();
        
        if (message && !this.isTyping) {
            // Add user message to UI
            this.addMessage(message, 'user');
            this.conversationHistory.push({ role: 'user', content: message });
            
            // Clear input
            input.value = '';
            
            // Show typing indicator
            this.showTypingIndicator();
            
            try {
                const response = await this.callChatbotAPI(message);
                this.hideTypingIndicator();
                
                if (response.success) {
                    this.sessionId = response.data.session_id;
                    this.addMessage(response.data.response, 'bot', response.data.suggestions);
                    this.conversationHistory.push({ 
                        role: 'assistant', 
                        content: response.data.response 
                    });
                    
                    // Save conversation to localStorage for persistence
                    this.saveConversationHistory();
                    
                    // Handle special intents
                    this.handleSpecialIntents(response.data.intent, response.data.response);
                } else {
                    this.addMessage(response.message || 'Maaf, terjadi kesalahan. Silakan coba lagi.', 'bot');
                }
            } catch (error) {
                this.hideTypingIndicator();
                this.addMessage('Maaf, tidak dapat terhubung ke server. Silakan coba lagi atau hubungi (021) 1234-5678.', 'bot');
                console.error('Chatbot error:', error);
            }
        }
    }

    async callChatbotAPI(message) {
        const response = await fetch(this.apiEndpoint, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                message: message,
                session_id: this.sessionId,
                context: this.getConversationContext()
            })
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();
        return { success: true, data };
    }

    getConversationContext() {
        // Return last 5 messages for context
        return this.conversationHistory.slice(-5);
    }

    addMessage(text, sender, suggestions = null) {
        const messagesContainer = document.getElementById('chatbotMessages');
        if (!messagesContainer) return;

        const messageDiv = document.createElement('div');
        messageDiv.className = `message ${sender}`;
        
        // Format message with better HTML structure
        let messageHTML = `<div class="message-bubble">${this.formatMessageText(text)}</div>`;
        
        // Add suggestions if provided
        if (suggestions && suggestions.length > 0) {
            messageHTML += '<div class="chatbot-suggestions">';
            suggestions.forEach(suggestion => {
                messageHTML += `<button class="suggestion-btn" onclick="chatbot.selectSuggestion('${this.escapeHtml(suggestion)}')">${suggestion}</button>`;
            });
            messageHTML += '</div>';
        }
        
        messageDiv.innerHTML = messageHTML;
        messagesContainer.appendChild(messageDiv);
        
        // Smooth scroll to bottom
        this.scrollToBottom();
        
        // Add message animation
        messageDiv.style.opacity = '0';
        messageDiv.style.transform = 'translateY(20px)';
        
        requestAnimationFrame(() => {
            messageDiv.style.transition = 'all 0.3s ease';
            messageDiv.style.opacity = '1';
            messageDiv.style.transform = 'translateY(0)';
        });
    }

    formatMessageText(text) {
        // Convert URLs to links
        const urlRegex = /(https?:\/\/[^\s]+)/g;
        text = text.replace(urlRegex, '<a href="$1" target="_blank" rel="noopener">$1</a>');
        
        // Convert phone numbers to clickable links
        const phoneRegex = /(\(?\d{3}\)?[-.\s]?\d{3}[-.\s]?\d{4})/g;
        text = text.replace(phoneRegex, '<a href="tel:$1">$1</a>');
        
        // Convert line breaks to <br>
        text = text.replace(/\n/g, '<br>');
        
        return text;
    }

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    selectSuggestion(suggestion) {
        document.getElementById('chatInput').value = suggestion;
        this.sendMessage();
    }

    showTypingIndicator() {
        this.isTyping = true;
        const indicator = document.getElementById('typingIndicator');
        if (indicator) {
            indicator.style.display = 'block';
            this.scrollToBottom();
        }

        // Disable send button
        const sendBtn = document.querySelector('.chatbot-send');
        if (sendBtn) {
            sendBtn.disabled = true;
            sendBtn.innerHTML = '<div class="loading"></div>';
        }
    }

    hideTypingIndicator() {
        this.isTyping = false;
        const indicator = document.getElementById('typingIndicator');
        if (indicator) {
            indicator.style.display = 'none';
        }

        // Re-enable send button
        const sendBtn = document.querySelector('.chatbot-send');
        if (sendBtn) {
            sendBtn.disabled = false;
            sendBtn.innerHTML = '<i class="fas fa-paper-plane"></i>';
        }
    }

    scrollToBottom() {
        const messagesContainer = document.getElementById('chatbotMessages');
        if (messagesContainer) {
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }
    }

    handleSpecialIntents(intent, response) {
        switch (intent) {
            case 'emergency':
                // Highlight emergency contact
                this.addEmergencyContact();
                break;
            case 'appointment':
                // Show appointment booking options
                this.showAppointmentOptions();
                break;
            case 'services':
                // Show quick service links
                this.showServiceLinks();
                break;
        }
    }

    addEmergencyContact() {
        setTimeout(() => {
            this.addMessage(
                '🚨 <strong>PENTING:</strong> Jika ini adalah kondisi darurat yang mengancam jiwa, segera hubungi:<br><br>' +
                '📞 <strong>(021) 999-8888</strong> - Darurat 24 Jam<br>' +
                '🏥 Atau datang langsung ke UGD kami di Jl. Kesehatan No. 123',
                'bot'
            );
        }, 1000);
    }

    showAppointmentOptions() {
        setTimeout(() => {
            this.addMessage(
                'Untuk membuat janji temu, Anda bisa:<br><br>' +
                '1. 📞 Telepon: (021) 1234-5678<br>' +
                '2. 🌐 Website: www.healthcareplus.com<br>' +
                '3. 🏥 Datang langsung ke klinik<br><br>' +
                'Jam layanan: Senin-Sabtu 08:00-20:00, Minggu 08:00-16:00',
                'bot',
                ['Lihat jadwal dokter', 'Biaya konsultasi', 'Persiapan konsultasi']
            );
        }, 1000);
    }

    showServiceLinks() {
        setTimeout(() => {
            this.addMessage(
                'Kami menyediakan layanan lengkap:<br><br>' +
                '🩺 Konsultasi Dokter Umum & Spesialis<br>' +
                '🔬 Laboratorium & Medical Check-up<br>' +
                '🦷 Perawatan Gigi & Mulut<br>' +
                '💊 Farmasi & Konsultasi Obat<br><br>' +
                'Semua layanan menggunakan teknologi modern dan standar medis terbaik.',
                'bot',
                ['Detail biaya layanan', 'Paket medical check-up', 'Jadwal praktik dokter']
            );
        }, 1000);
    }

    saveConversationHistory() {
        if (this.sessionId && this.conversationHistory.length > 0) {
            localStorage.setItem(`chatbot_history_${this.sessionId}`, JSON.stringify({
                history: this.conversationHistory,
                timestamp: Date.now()
            }));
        }
    }

    loadConversationHistory() {
        // Load recent conversation if exists (within last hour)
        const keys = Object.keys(localStorage).filter(key => key.startsWith('chatbot_history_'));
        
        keys.forEach(key => {
            const data = JSON.parse(localStorage.getItem(key) || '{}');
            if (data.timestamp && (Date.now() - data.timestamp) > 3600000) { // 1 hour
                localStorage.removeItem(key); // Clean old conversations
            }
        });
    }

    // Public method to start conversation with specific topic
    startConversation(topic) {
        if (!this.sessionId) {
            this.sessionId = this.generateSessionId();
        }
        
        this.toggleChatbot();
        
        setTimeout(() => {
            document.getElementById('chatInput').value = topic;
            this.sendMessage();
        }, 500);
    }

    // Method to clear conversation
    clearConversation() {
        if (confirm('Hapus riwayat percakapan?')) {
            const messagesContainer = document.getElementById('chatbotMessages');
            if (messagesContainer) {
                messagesContainer.innerHTML = '';
                this.showWelcomeMessage();
            }
            this.conversationHistory = [];
            if (this.sessionId) {
                localStorage.removeItem(`chatbot_history_${this.sessionId}`);
            }
        }
    }
}

// Initialize chatbot when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Initialize chatbot
    window.chatbot = new HealthCareChatbot();
    
    // Smooth scrolling for navigation links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // Auto-pulse chatbot button after 3 seconds
    setTimeout(() => {
        const toggle = document.querySelector('.chatbot-toggle');
        if (toggle && !toggle.classList.contains('pulsed')) {
            toggle.style.animation = 'pulse 2s infinite';
            toggle.classList.add('pulsed');
            
            // Stop pulsing after 10 seconds
            setTimeout(() => {
                toggle.style.animation = '';
            }, 10000);
        }
    }, 3000);

    // Intersection Observer for animations
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('fade-in');
            }
        });
    }, observerOptions);

    // Observe all service cards and doctor cards
    document.querySelectorAll('.service-card, .doctor-card').forEach(card => {
        observer.observe(card);
    });

    // Mobile menu functionality
    const mobileMenuBtn = document.querySelector('.mobile-menu-btn');
    const navLinks = document.querySelector('.nav-links');
    
    if (mobileMenuBtn && navLinks) {
        mobileMenuBtn.addEventListener('click', () => {
            navLinks.classList.toggle('active');
        });
    }

    // Service card click handlers for more interactivity
    document.querySelectorAll('.service-card').forEach(card => {
        card.addEventListener('click', () => {
            const serviceName = card.querySelector('h3').textContent;
            if (window.chatbot) {
                window.chatbot.startConversation(`Saya ingin tahu lebih lanjut tentang ${serviceName}`);
            }
        });
    });

    // Doctor card click handlers
    document.querySelectorAll('.doctor-card').forEach(card => {
        card.addEventListener('click', () => {
            const doctorName = card.querySelector('.doctor-name').textContent;
            if (window.chatbot) {
                window.chatbot.startConversation(`Saya ingin membuat janji dengan ${doctorName}`);
            }
        });
    });

    // Add loading animation to CTA buttons
    document.querySelectorAll('.cta-button').forEach(button => {
        button.addEventListener('click', (e) => {
            if (button.getAttribute('href') === '#contact') {
                e.preventDefault();
                
                // Scroll to contact section
                const contactSection = document.getElementById('contact');
                if (contactSection) {
                    contactSection.scrollIntoView({ behavior: 'smooth' });
                }
                
                // Optional: Open chatbot for quick booking
                setTimeout(() => {
                    if (window.chatbot) {
                        window.chatbot.startConversation('Saya ingin membuat janji dengan dokter');
                    }
                }, 1000);
            }
        });
    });

    // Add tooltips to icons
    document.querySelectorAll('[title]').forEach(element => {
        element.addEventListener('mouseenter', (e) => {
            // Simple tooltip implementation
            const tooltip = document.createElement('div');
            tooltip.className = 'tooltip';
            tooltip.textContent = e.target.getAttribute('title');
            tooltip.style.cssText = `
                position: absolute;
                background: rgba(0,0,0,0.8);
                color: white;
                padding: 5px 10px;
                border-radius: 5px;
                font-size: 12px;
                z-index: 1000;
                pointer-events: none;
                top: ${e.pageY - 30}px;
                left: ${e.pageX}px;
            `;
            document.body.appendChild(tooltip);
            
            e.target.addEventListener('mouseleave', () => {
                tooltip.remove();
            }, { once: true });
        });
    });
});

// Add global methods for easy integration
window.openChatbot = function() {
    if (window.chatbot) {
        window.chatbot.toggleChatbot();
    }
};

window.startChatWithTopic = function(topic) {
    if (window.chatbot) {
        window.chatbot.startConversation(topic);
    }
};