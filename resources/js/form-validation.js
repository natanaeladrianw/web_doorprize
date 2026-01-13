// Validasi form di sisi client
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('doorprizeForm');
    
    if (!form) return;

    form.addEventListener('submit', function(e) {
        let isValid = true;
        
        // Hapus pesan error sebelumnya
        document.querySelectorAll('.error-message').forEach(el => {
            el.classList.add('hidden');
            el.textContent = '';
        });

        // Validasi setiap field yang wajib diisi
        const requiredFields = form.querySelectorAll('[required]');
        
        requiredFields.forEach(field => {
            const fieldId = field.id || field.name;
            const errorElement = document.getElementById('error_' + fieldId);
            
            // Validasi input text
            if (field.type === 'text') {
                const fieldLabel = field.closest('.mb-6')?.querySelector('label')?.textContent.toLowerCase() || '';
                
                if (field.value.trim() === '') {
                    isValid = false;
                    if (errorElement) {
                        errorElement.textContent = 'Field ini wajib diisi';
                        errorElement.classList.remove('hidden');
                    }
                    field.classList.add('border-red-500');
                } else {
                    // Validasi email
                    if (fieldLabel.includes('email')) {
                        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                        if (!emailPattern.test(field.value)) {
                            isValid = false;
                            if (errorElement) {
                                errorElement.textContent = 'Format email tidak valid';
                                errorElement.classList.remove('hidden');
                            }
                            field.classList.add('border-red-500');
                        } else {
                            field.classList.remove('border-red-500');
                        }
                    }
                    // Validasi nomor telepon
                    else if (fieldLabel.includes('telepon') || fieldLabel.includes('phone') || fieldLabel.includes('hp')) {
                        const phonePattern = /^[0-9+\-\s()]+$/;
                        if (!phonePattern.test(field.value)) {
                            isValid = false;
                            if (errorElement) {
                                errorElement.textContent = 'Nomor telepon hanya boleh berisi angka';
                                errorElement.classList.remove('hidden');
                            }
                            field.classList.add('border-red-500');
                        } else if (field.value.replace(/\D/g, '').length < 10) {
                            isValid = false;
                            if (errorElement) {
                                errorElement.textContent = 'Nomor telepon minimal 10 digit';
                                errorElement.classList.remove('hidden');
                            }
                            field.classList.add('border-red-500');
                        } else {
                            field.classList.remove('border-red-500');
                        }
                    } else {
                        field.classList.remove('border-red-500');
                    }
                }
            }

            // Validasi radio button
            if (field.type === 'radio') {
                const radioGroup = form.querySelectorAll(`input[name="${field.name}"]`);
                const isChecked = Array.from(radioGroup).some(radio => radio.checked);
                
                if (!isChecked) {
                    isValid = false;
                    const errorId = 'error_' + field.name;
                    const errorEl = document.getElementById(errorId);
                    if (errorEl) {
                        errorEl.textContent = 'Pilih salah satu opsi';
                        errorEl.classList.remove('hidden');
                    }
                }
            }

            // Validasi dropdown
            if (field.tagName === 'SELECT' && field.value === '') {
                isValid = false;
                if (errorElement) {
                    errorElement.textContent = 'Pilih salah satu opsi';
                    errorElement.classList.remove('hidden');
                }
                field.classList.add('border-red-500');
            } else if (field.tagName === 'SELECT') {
                field.classList.remove('border-red-500');
            }
        });

        // Validasi checkbox (grup)
        const checkboxGroups = {};
        form.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
            const groupName = checkbox.name;
            if (!checkboxGroups[groupName]) {
                checkboxGroups[groupName] = [];
            }
            checkboxGroups[groupName].push(checkbox);
        });

        Object.keys(checkboxGroups).forEach(groupName => {
            const checkboxes = checkboxGroups[groupName];
            const isRequired = checkboxes[0].hasAttribute('required');
            const isChecked = checkboxes.some(cb => cb.checked);
            
            if (isRequired && !isChecked) {
                isValid = false;
                const fieldId = groupName.replace('[]', '');
                const errorEl = document.getElementById('error_' + fieldId);
                if (errorEl) {
                    errorEl.textContent = 'Pilih minimal satu opsi';
                    errorEl.classList.remove('hidden');
                }
            }
        });

        if (!isValid) {
            e.preventDefault();
            // Scroll ke error pertama
            const firstError = document.querySelector('.error-message:not(.hidden)');
            if (firstError) {
                firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }
    });

    // Validasi real-time saat user mengetik
    form.querySelectorAll('input, select').forEach(field => {
        field.addEventListener('input', function() {
            const errorElement = document.getElementById('error_' + (field.id || field.name));
            if (errorElement && !errorElement.classList.contains('hidden')) {
                errorElement.classList.add('hidden');
                errorElement.textContent = '';
            }
            field.classList.remove('border-red-500');
        });
    });
});
