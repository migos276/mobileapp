// Installation et gestion de la PWA
class PWAManager {
    constructor() {
        this.deferredPrompt = null;
        this.init();
    }

    init() {
        this.registerServiceWorker();
        this.handleInstallPrompt();
        this.checkOnlineStatus();
    }

    // Enregistrer le service worker
    async registerServiceWorker() {
        if ('serviceWorker' in navigator) {
            try {
                const registration = await navigator.serviceWorker.register('/sw.js');
                console.log('Service Worker enregistré:', registration);
                
                // Vérifier les mises à jour
                registration.addEventListener('updatefound', () => {
                    const newWorker = registration.installing;
                    newWorker.addEventListener('statechange', () => {
                        if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                            this.showUpdateNotification();
                        }
                    });
                });
            } catch (error) {
                console.error('Erreur d\'enregistrement du Service Worker:', error);
            }
        }
    }

    // Gérer l'invite d'installation
    handleInstallPrompt() {
        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            this.deferredPrompt = e;
            this.showInstallButton();
        });

        window.addEventListener('appinstalled', () => {
            console.log('PWA installée');
            this.hideInstallButton();
            this.deferredPrompt = null;
        });
    }

    // Afficher le bouton d'installation
    showInstallButton() {
        const installButton = document.createElement('button');
        installButton.id = 'install-button';
        installButton.innerHTML = '<i class="fas fa-download"></i> Installer l\'app';
        installButton.className = 'btn btn-primary install-btn';
        installButton.style.cssText = `
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1000;
            border-radius: 50px;
            padding: 1rem 1.5rem;
            box-shadow: var(--shadow);
            animation: slideUp 0.3s ease;
        `;

        installButton.addEventListener('click', () => {
            this.installApp();
        });

        document.body.appendChild(installButton);
    }

    // Masquer le bouton d'installation
    hideInstallButton() {
        const installButton = document.getElementById('install-button');
        if (installButton) {
            installButton.remove();
        }
    }

    // Installer l'application
    async installApp() {
        if (this.deferredPrompt) {
            this.deferredPrompt.prompt();
            const { outcome } = await this.deferredPrompt.userChoice;
            console.log('Résultat de l\'installation:', outcome);
            this.deferredPrompt = null;
            this.hideInstallButton();
        }
    }

    // Vérifier le statut en ligne/hors ligne
    checkOnlineStatus() {
        const updateOnlineStatus = () => {
            const status = navigator.onLine ? 'online' : 'offline';
            document.body.setAttribute('data-connection', status);
            
            if (!navigator.onLine) {
                this.showOfflineNotification();
            } else {
                this.hideOfflineNotification();
            }
        };

        window.addEventListener('online', updateOnlineStatus);
        window.addEventListener('offline', updateOnlineStatus);
        updateOnlineStatus();
    }

    // Afficher notification hors ligne
    showOfflineNotification() {
        if (document.getElementById('offline-notification')) return;

        const notification = document.createElement('div');
        notification.id = 'offline-notification';
        notification.innerHTML = `
            <i class="fas fa-wifi"></i>
            <span>Mode hors ligne - Certaines fonctionnalités peuvent être limitées</span>
        `;
        notification.style.cssText = `
            position: fixed;
            top: 80px;
            left: 50%;
            transform: translateX(-50%);
            background: var(--warning-color);
            color: var(--dark-color);
            padding: 1rem 2rem;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            z-index: 9999;
            animation: slideDown 0.3s ease;
        `;

        document.body.appendChild(notification);
    }

    // Masquer notification hors ligne
    hideOfflineNotification() {
        const notification = document.getElementById('offline-notification');
        if (notification) {
            notification.remove();
        }
    }

    // Afficher notification de mise à jour
    showUpdateNotification() {
        const notification = document.createElement('div');
        notification.innerHTML = `
            <div style="display: flex; align-items: center; gap: 1rem;">
                <span>Une nouvelle version est disponible</span>
                <button onclick="window.location.reload()" class="btn btn-primary" style="padding: 0.5rem 1rem;">
                    Mettre à jour
                </button>
            </div>
        `;
        notification.style.cssText = `
            position: fixed;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            background: var(--primary-color);
            color: white;
            padding: 1rem 2rem;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            z-index: 9999;
        `;

        document.body.appendChild(notification);
    }
}

// Animations CSS pour la PWA
const pwaStyles = document.createElement('style');
pwaStyles.textContent = `
    @keyframes slideUp {
        from {
            transform: translateY(100px);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }
    
    @keyframes slideDown {
        from {
            transform: translateX(-50%) translateY(-20px);
            opacity: 0;
        }
        to {
            transform: translateX(-50%) translateY(0);
            opacity: 1;
        }
    }
    
    .install-btn:hover {
        transform: scale(1.05);
        box-shadow: 0 8px 25px rgba(255, 107, 53, 0.3);
    }
    
    [data-connection="offline"] {
        filter: grayscale(0.3);
    }
    
    [data-connection="offline"] .btn:not(.btn-secondary) {
        opacity: 0.7;
        pointer-events: none;
    }
`;
document.head.appendChild(pwaStyles);

// Initialiser la PWA
document.addEventListener('DOMContentLoaded', () => {
    new PWAManager();
});