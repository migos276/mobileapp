// Modal functions
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'block';
        document.body.style.overflow = 'hidden';
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
    }
}

// Close modal when clicking outside
window.onclick = function(event) {
    if (event.target.classList.contains('modal')) {
        event.target.style.display = 'none';
        document.body.style.overflow = 'auto';
    }
}

// Notification system
function showNotification(message, type = 'info') {
    // Remove existing notifications
    const existingNotifications = document.querySelectorAll('.notification');
    existingNotifications.forEach(notification => notification.remove());

    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <i class="fas ${getNotificationIcon(type)}"></i>
        <span>${message}</span>
        <button onclick="this.parentElement.remove()" class="notification-close">&times;</button>
    `;

    // Add to page
    document.body.appendChild(notification);

    // Auto remove after 5 seconds
    setTimeout(() => {
        if (notification.parentElement) {
            notification.remove();
        }
    }, 5000);
}

function getNotificationIcon(type) {
    switch(type) {
        case 'success': return 'fa-check-circle';
        case 'error': return 'fa-exclamation-triangle';
        case 'warning': return 'fa-exclamation-circle';
        default: return 'fa-info-circle';
    }
}

// Price formatting
function formatPrice(price) {
    return new Intl.NumberFormat('fr-FR', {
        style: 'currency',
        currency: 'XAF',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(price);
}

// Map initialization
function initMap(mapId, lat, lng, zoom = 13) {
    if (typeof L === 'undefined') {
        console.error('Leaflet not loaded');
        return null;
    }

    const map = L.map(mapId).setView([lat, lng], zoom);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    return map;
}

// Add marker to map
function addMarker(map, lat, lng, popupText, iconColor = 'blue') {
    if (!map) return;

    const icon = L.divIcon({
        className: 'custom-marker',
        html: `<div style="background-color: ${iconColor}; width: 20px; height: 20px; border-radius: 50%; border: 2px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.3);"></div>`,
        iconSize: [20, 20],
        iconAnchor: [10, 10]
    });

    const marker = L.marker([lat, lng], { icon }).addTo(map);

    if (popupText) {
        marker.bindPopup(popupText);
    }

    return marker;
}

// Get user location
function getUserLocation(callback) {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            (position) => {
                callback({
                    lat: position.coords.latitude,
                    lng: position.coords.longitude
                });
            },
            (error) => {
                console.error('Erreur de géolocalisation:', error);
                callback(null);
            }
        );
    } else {
        console.error('Géolocalisation non supportée');
        callback(null);
    }
}

// Form validation
function validateForm(formId) {
    const form = document.getElementById(formId);
    if (!form) return false;

    const inputs = form.querySelectorAll('input[required], select[required], textarea[required]');
    let isValid = true;

    inputs.forEach(input => {
        if (!input.value.trim()) {
            input.classList.add('error');
            isValid = false;
        } else {
            input.classList.remove('error');
        }
    });

    return isValid;
}

// Loading state
function setLoading(element, loading = true) {
    if (loading) {
        element.disabled = true;
        element.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Chargement...';
    } else {
        element.disabled = false;
        element.innerHTML = element.dataset.originalText || 'Soumettre';
    }
}

// Debounce function for search inputs
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Store original button text
    const submitButtons = document.querySelectorAll('button[type="submit"]');
    submitButtons.forEach(button => {
        button.dataset.originalText = button.innerHTML;
    });

    // Handle form submissions
    document.addEventListener('submit', function(e) {
        const form = e.target;
        if (form.id === 'orderForm') {
            e.preventDefault();
            handleOrderSubmission(form);
        }
    });
});

// Handle order form submission
function handleOrderSubmission(form) {
    if (!validateForm('orderForm')) {
        showNotification('Veuillez remplir tous les champs requis', 'error');
        return;
    }

    const submitButton = form.querySelector('button[type="submit"]');
    setLoading(submitButton, true);

    const formData = new FormData(form);
    const orderData = {
        station_id: formData.get('station_id'),
        produit_id: formData.get('produit_id'),
        quantite: formData.get('quantite'),
        methode_paiement: formData.get('methode_paiement'),
        telephone_paiement: formData.get('telephone_paiement'),
        adresse_livraison: formData.get('adresse_livraison'),
        latitude_livraison: formData.get('latitude_livraison') || 0,
        longitude_livraison: formData.get('longitude_livraison') || 0
    };

    fetch('/api/orders', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(orderData)
    })
    .then(response => response.json())
    .then(data => {
        setLoading(submitButton, false);

        if (data.success) {
            showNotification('Commande passée avec succès !', 'success');
            closeModal('orderModal');
            setTimeout(() => window.location.reload(), 2000);
        } else {
            showNotification(data.message || 'Erreur lors de la commande', 'error');
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        setLoading(submitButton, false);
        showNotification('Erreur de connexion', 'error');
    });
}
