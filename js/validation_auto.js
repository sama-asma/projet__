document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('formPrim');
    let isValide = true;

    // Fonction pour afficher les messages d'erreur
    function showError(input, message) {
        // Supprime les messages d'erreur existants
        const existingError = input.parentElement.querySelector('.error-message');
        if (existingError) existingError.remove();
        
        // Crée un nouvel élément pour le message d'erreur
        const errorDiv = document.createElement('div');
        errorDiv.className = 'error-message';
        errorDiv.style.color = 'red';
        errorDiv.style.fontSize = '12px';
        errorDiv.style.marginTop = '5px';
        errorDiv.textContent = message;
        
        // Ajoute le message après l'input
        input.parentElement.appendChild(errorDiv);
        input.style.borderColor = 'red';
    }
    
    // Fonction pour nettoyer les erreurs
    function clearError(input) {
        const existingError = input.parentElement.querySelector('.error-message');
        if (existingError) existingError.remove();
        input.style.borderColor = '#ddd';
    }
    
    // Validation en temps réel pour tous les champs requis
    const requiredInputs = form.querySelectorAll('input[required], select[required]');
    requiredInputs.forEach(input => {
        input.addEventListener('blur', function() {
            if (!this.value.trim()) {
                showError(this, 'Ce champ est obligatoire');
            } else {
                clearError(this);
            }
        });
        
        input.addEventListener('input', function() {
            if (this.value.trim()) {
                clearError(this);
            }
        });
    });
    
    // Validation spécifique pour le téléphone 
    const telephoneInput = document.getElementById('telephone');
    telephoneInput.addEventListener('input', function() {
        const regex = /^(\+213|0)(5|6|7)\d{8}$/;
        if (this.value && !regex.test(this.value)) {
            showError(this, 'Format: 05XXXXXXXX ou +2135XXXXXXXX');
        } else {
            clearError(this);
        }
    });
        // Revalidation quand l'utilisateur quitte le champ (blur)
    telephoneInput.addEventListener('blur', function() {
        if (this.value.trim() === "" || !/^(\+213|0)(5|6|7)\d{8}$/.test(this.value)) {
            showError(this, 'Format: 05XXXXXXXX ou +2135XXXXXXXX');
        }
    });

    // Validation pour l'email
    const emailInput = document.getElementById('email');
    emailInput.addEventListener('blur', function() {
        const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!this.value.trim()) {
            showError(this, 'Ce champ est obligatoire');
        } else if (!regex.test(this.value)) {
            showError(this, 'Veuillez entrer une adresse email valide');
        } else {
            clearError(this);
        }
    });
    
    // Validation pour l'âge du conducteur (minimum 18 ans)
    const ageInput = document.getElementById('age_conducteur');
    ageInput.addEventListener('input', function() {
        if (this.value && parseInt(this.value) < 18) {
            showError(this, 'Le conducteur doit avoir au moins 18 ans');
        } else {
            clearError(this);
        }
    });
    
    // Validation pour le bonus-malus (entre 0.5 et 3.5)
    const bonusMalusInput = document.getElementById('bonus_malus');
    bonusMalusInput.addEventListener('input', function() {
        const value = parseFloat(this.value);
        if (this.value && (value < 0.5 || value > 3.5)) {
            showError(this, 'Le coefficient doit être entre 0.5 et 3.5');
        } else {
            clearError(this);
        }
    });
    
    //Set les dates automatique et des Validation
    const dateSubscription = document.getElementById('date_souscription');
    dateSubscription.value = new Date().toISOString().split('T')[0]; // Date par défaut à aujourd'hui
    const dateExpiration = document.getElementById('date_expiration');
    dateExpiration.value = new Date(new Date().setFullYear(new Date().getFullYear() + 1)).toISOString().split('T')[0]; // Date d'expiration par défaut à +1 an
    
    dateSubscription.addEventListener('change', function() {
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        const selectedDate = new Date(this.value);
        
        if (selectedDate < today) {
            showError(this, 'La date de souscription ne peut pas être dans le passé');
        } else {
            clearError(this);
            // Définir automatiquement la date d'expiration à +1 an
            if (this.value) {
                const expirationDate = new Date(selectedDate);
                expirationDate.setFullYear(expirationDate.getFullYear() + 1);
                
                // Format YYYY-MM-DD pour input date
                const formattedDate = expirationDate.toISOString().split('T')[0];
                dateExpiration.value = formattedDate;
            }
        }
    });
    
    dateExpiration.addEventListener('change', function() {
        if (dateSubscription.value) {
            const subscriptionDate = new Date(dateSubscription.value);
            const expirationDate = new Date(this.value);
            
            if (expirationDate <= subscriptionDate) {
                showError(this, 'La date d\'expiration doit être postérieure à la date de souscription');
            } else {
                clearError(this);
            }
        }
    });
    
    // Validation de l'expérience du conducteur par rapport à son âge
    const experienceInput = document.getElementById('experience_conducteur');
    experienceInput.addEventListener('input', function() {
        if (ageInput.value && this.value) {
            const age = parseInt(ageInput.value);
            const experience = parseInt(this.value);
            
            if (experience > age - 18) {
                showError(this, 'L\'expérience ne peut pas dépasser ' + (age - 18) + ' ans');
            } else {
                clearError(this);
            }
        }
    });
    
    // Mise à jour de la validation d'expérience quand l'âge change
    ageInput.addEventListener('change', function() {
        if (experienceInput.value) {
            event = new Event('input');
            experienceInput.dispatchEvent(event);
        }
    });
    
    document.getElementById('calculerPrimeBtn').addEventListener('click', async function() {
        isValide = true;        
        
        const allInputs = form.querySelectorAll('input[required], select[required]');
        allInputs.forEach(input => {
            if (!input.value.trim()) {
                isValide = false;
                showError(input, 'Ce champ est obligatoire');
            }
        });
    
        // Vérifiez s'il y a des erreurs
        if (!isValide) {
            alert('Veuillez corriger les erreurs avant de calculer');
            return;
        }
    
        // Calcul AJAX 
        const btnCalculer = this;
        btnCalculer.disabled = true;
        btnCalculer.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Calcul en cours...';
    
        try {
            const formData = new FormData(document.getElementById('formPrim'));
            const response = await fetch('calcul_prime_auto.php', {
                method: 'POST',
                body: formData
            });
            const data = await response.json();
            if (!response.ok || !data.success) {
                throw new Error(data.message || 'Erreur lors du calcul');
            }
            afficherResultatPrime(data.prime, data.primeNet);
            document.getElementById('souscrireBtn').style.display = 'inline-block';
            document.getElementById('formPrim').dataset.prime = data.prime; // ajouter l'attribut data-prime en html
            document.getElementById('formPrim').dataset.franchise = data.franchise;
        } catch (error) {
            console.error("Erreur:", error);
            alert("Erreur lors du calcul: " + error.message);
        } finally {
            btnCalculer.disabled = false;
            btnCalculer.textContent = 'Calculer la prime';
        }
    });
    // Fonction pour afficher le résultat de la prime
    
    function afficherResultatPrime(prime,primeNet) {
        const resultatDiv = document.getElementById('resultatCalcul');
        const detailDiv = document.getElementById('detailPrime');
        detailDiv.innerHTML = `
            <div class="prime-result">
                <p>Prime Net: ${primeNet.toLocaleString('fr-FR')} DZD</p>
                <p>Prime annuelle: <strong>${prime.toLocaleString('fr-FR')} DZD</strong></p>
                <p>Date d'effet: ${document.getElementById('date_souscription').value}</p>
                <p>Date d'expiration: ${document.getElementById('date_expiration').value}</p>
            </div>
        `;
        resultatDiv.style.display = 'block';
    }

    // Empêcher la soumission par Entrée quand le formulaire est invalide
    document.getElementById('formPrim').addEventListener('keydown', function(e) {
    if (e.key === 'Enter') {
        const submitBtn = document.querySelector('#souscrireBtn');
        if (submitBtn.disabled || !this.dataset.prime) {
            e.preventDefault();
            return false;
        }
    }
    });
    // La soumission finale
    document.getElementById('formPrim').addEventListener('submit',  function(e) {
        
        // Vérifiez si la prime a été calculée
        if (!this.dataset.prime) {
            alert("Veuillez d'abord calculer la prime");
            return;
        }
        document.getElementById('prime').value = this.dataset.prime;
        document.getElementById('franchise').value = this.dataset.franchise;
        const submitBtn = document.getElementById('souscrireBtn');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Souscription en cours...';

    });
});

