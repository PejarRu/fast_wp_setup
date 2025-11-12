console.log('WP Fast Setup: Script loaded successfully!');
window.WPFastSetupMain = true;
let wpfsPluginControlsInitialized = false;

const initWPFastSetup = () => {
    console.log('WP Fast Setup: DOMContentLoaded fired');

    const ajaxUrl = (window.wpFastSetupAjax && window.wpFastSetupAjax.ajaxurl) || window.wpfs_ajaxurl || (typeof ajaxurl !== 'undefined' ? ajaxurl : '');
    const defaultNonce = (window.wpFastSetupAjax && window.wpFastSetupAjax.nonce) || '';

    if (!ajaxUrl) {
        console.warn('WP Fast Setup: AJAX URL not available. AJAX-powered actions may fail.');
    }

    // Tab switching
    const tabs = document.querySelectorAll('.wpf-tab');
    const tabContents = document.querySelectorAll('.wpf-tab-content');

    console.log('WP Fast Setup: Found', tabs.length, 'tabs and', tabContents.length, 'tab contents');

    tabs.forEach(tab => {
        tab.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('WP Fast Setup: Tab clicked:', this.getAttribute('data-tab'));

            // Remove active class from all tabs
            tabs.forEach(t => t.classList.remove('active'));
            tabContents.forEach(tc => tc.classList.remove('active'));

            // Add active class to clicked tab and corresponding content
            this.classList.add('active');
            const targetTab = this.getAttribute('data-tab');
            const targetContent = document.getElementById(targetTab);
            if (targetContent) {
                targetContent.classList.add('active');

                // Initialize plugin controls when plugins tab is activated
                if (targetTab === 'plugins') {
                    initializePluginControls();
                }
            }
        });
    });

    // Preset pages selector
    const presetSelect = document.getElementById('preset_pages_select');
    if (presetSelect) {
        presetSelect.addEventListener('change', function() {
            const preset = this.value;
            const textarea = document.getElementById('pages_input');
            if (!textarea) return;

            let presetText = '';

            switch (preset) {
                case 'basico':
                    presetText = "Inicio\nNosotros\nServicios\nContacto";
                    break;
                case 'completo':
                    presetText = "Inicio\nNosotros\nServicios\nPortfolio\nBlog\nContacto";
                    break;
                case 'especial':
                    presetText = "Home\nAbout Us\nProducts\nFAQ\nSupport\nContact";
                    break;
            }

            textarea.value = presetText;
        });
    }

    // AJAX form submission for plugins
    const pluginForm = document.querySelector('#plugins form');
    const fixedProgress = document.getElementById('wpf-fixed-progress');
    const fixedProgressFill = document.getElementById('wpf-fixed-progress-fill');
    const fixedProgressStatus = document.querySelector('.wpf-fixed-progress-status');

    function collectPluginSelections(form) {
        const selections = {
            repo: [],
            local: [],
            drive: [],
            any: []
        };

        if (!form) {
            return selections;
        }

        let checkboxes = form.querySelectorAll('.wpf-plugin-checkbox:checked');
        if (!checkboxes.length) {
            checkboxes = form.querySelectorAll('input[type="checkbox"]:checked');
        }

        checkboxes.forEach(checkbox => {
            const effectiveType = checkbox.dataset.pluginEffectiveType || checkbox.dataset.pluginSource || checkbox.dataset.pluginType || 'repo';
            const slug = checkbox.dataset.pluginSlug || checkbox.value || '';
            const label = checkbox.dataset.pluginLabel || slug;
            const entry = {
                type: effectiveType,
                slug,
                label
            };

            if (effectiveType === 'repo') {
                if (slug && !selections.repo.includes(slug)) {
                    selections.repo.push(slug);
                }
            } else if (effectiveType === 'local') {
                const zip = checkbox.dataset.pluginZip || checkbox.value || slug;
                if (zip) {
                    entry.zip = zip;
                    if (!selections.local.some(item => item.zip === zip)) {
                        selections.local.push({
                            zip,
                            label
                        });
                    }
                }
            } else if (effectiveType === 'drive') {
                const driveId = checkbox.dataset.pluginDriveId || checkbox.value || '';
                if (driveId) {
                    entry.id = driveId;
                    if (!selections.drive.some(item => item.id === driveId)) {
                        selections.drive.push({
                            id: driveId,
                            name: slug || label
                        });
                    }
                }
            } else if (slug) {
                if (!selections.repo.includes(slug)) {
                    selections.repo.push(slug);
                }
            }

            selections.any.push(entry);
        });

        return selections;
    }

    if (pluginForm && !pluginForm.dataset.wpfsAjaxBound) {
        pluginForm.dataset.wpfsAjaxBound = '1';
        pluginForm.addEventListener('submit', function(e) {
            e.preventDefault();

            if (!ajaxUrl) {
                alert('No se pudo determinar la URL de AJAX. Recarga la página e inténtalo nuevamente.');
                return;
            }

            if (fixedProgress) {
                fixedProgress.classList.add('show');
            }
            if (fixedProgressFill) {
                fixedProgressFill.style.width = '0%';
            }
            if (fixedProgressStatus) {
                fixedProgressStatus.textContent = 'Preparando instalación...';
            }

            const selections = collectPluginSelections(pluginForm);
            if (!selections.any.length) {
                if (fixedProgressStatus) {
                    fixedProgressStatus.textContent = 'Selecciona al menos un plugin.';
                }
                alert('Debes seleccionar al menos un plugin para instalar.');
                setTimeout(() => {
                    if (fixedProgress) {
                        fixedProgress.classList.remove('show');
                    }
                }, 2000);
                return;
            }

            if (fixedProgressStatus) {
                fixedProgressStatus.textContent = `Instalando ${selections.any.length} plugin${selections.any.length === 1 ? '' : 's'}...`;
            }

            const formData = new FormData();
            const nonceField = pluginForm.querySelector('input[name="wp_fast_setup_nonce_plugins"]');
            if (nonceField) {
                formData.append('wp_fast_setup_nonce_plugins', nonceField.value);
            }
            if (defaultNonce) {
                formData.append('nonce', defaultNonce);
            }
            formData.append('action', 'wp_fast_setup_install_plugins');

            selections.repo.forEach(slug => formData.append('plugins[]', slug));
            selections.local.forEach(item => formData.append('local_zips[]', item.zip));
            selections.drive.forEach(item => formData.append('drive_files[' + item.id + ']', item.name));

            fetch(ajaxUrl, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                console.log('WP Fast Setup: Plugin AJAX response:', data);
                if (data.success) {
                    if (fixedProgressFill) {
                        fixedProgressFill.style.width = '100%';
                    }
                    if (fixedProgressStatus) {
                        fixedProgressStatus.textContent = 'Instalación completada exitosamente';
                    }
                    setTimeout(() => {
                        if (fixedProgress) {
                            fixedProgress.classList.remove('show');
                        }
                        location.reload();
                    }, 2000);
                } else {
                    const errorMessage = data.message || (data.data && data.data.message) || 'Error desconocido';
                    if (fixedProgressStatus) {
                        fixedProgressStatus.textContent = 'Error: ' + errorMessage;
                    }
                    alert('❌ Error: ' + errorMessage);
                    setTimeout(() => {
                        if (fixedProgress) {
                            fixedProgress.classList.remove('show');
                        }
                    }, 3000);
                }
            })
            .catch(error => {
                console.error('WP Fast Setup: Plugin AJAX error:', error);
                if (fixedProgressStatus) {
                    fixedProgressStatus.textContent = 'Error de conexión';
                }
                alert('❌ Error de conexión al instalar plugins');
                setTimeout(() => {
                    if (fixedProgress) {
                        fixedProgress.classList.remove('show');
                    }
                }, 3000);
            });
        });
    }

    // AJAX form submission for site settings
    const siteSettingsForm = document.querySelector('#site-settings-form');
    if (siteSettingsForm && !siteSettingsForm.dataset.wpfsAjaxBound) {
        siteSettingsForm.dataset.wpfsAjaxBound = '1';
        let isSubmitting = false; // Prevent double submission

        siteSettingsForm.addEventListener('submit', function(e) {
            e.preventDefault();

            if (isSubmitting) return; // Prevent double submission
            isSubmitting = true;

            console.log('WP Fast Setup: Site settings form submitted');

            if (!ajaxUrl) {
                alert('No se pudo determinar la URL de AJAX. Recarga la página e inténtalo nuevamente.');
                isSubmitting = false;
                return;
            }

            const formData = new FormData(siteSettingsForm);
            formData.append('action', 'wp_fast_setup_save_site_settings');
            if (defaultNonce) {
                formData.append('nonce', defaultNonce);
            }

            // Log all form data
            console.log('WP Fast Setup: Form data:');
            for (let [key, value] of formData.entries()) {
                console.log(key + ': ' + value);
            }

            fetch(ajaxUrl, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                console.log('WP Fast Setup: Site settings response:', data);
                if (data.success) {
                    let message = '✅ Configuración del sitio guardada correctamente';
                    if (data.language_updated && !data.language_available) {
                        message += '\n⚠️ Los archivos de idioma no están disponibles. El cambio de idioma puede no surtir efecto hasta que instales los archivos de idioma correspondientes.';
                    }
                    alert(message);
                    // Force reload from server to ensure language changes take effect
                    window.location.reload(true);
                } else {
                    alert('❌ Error: ' + (data.message || 'Error desconocido'));
                }
                isSubmitting = false; // Reset flag
            })
            .catch(error => {
                console.error('WP Fast Setup: Site settings error:', error);
                alert('❌ Error de conexión al guardar configuración');
                isSubmitting = false; // Reset flag
            });
        });
    }

    // AJAX form submission for Google Drive settings
    const googleDriveForm = document.querySelector('#google-drive-form');
    if (googleDriveForm) {
        const driveApiInput = document.getElementById('wpfs_google_drive_api_key');
        const driveFolderInput = document.getElementById('wpfs_google_drive_folder_id');
        const useAntonButton = document.getElementById('wpfs-use-anton-drive');
        const testDriveButton = document.getElementById('wpfs-test-drive');
        const driveResultBox = document.getElementById('wpfs-google-drive-test-result');

        if (useAntonButton && driveApiInput && driveFolderInput) {
            useAntonButton.addEventListener('click', function() {
                const defaultApi = this.dataset.defaultApi || '';
                const defaultFolder = this.dataset.defaultFolder || '';
                if (defaultApi) {
                    driveApiInput.value = defaultApi;
                }
                if (defaultFolder) {
                    driveFolderInput.value = defaultFolder;
                }
                if (driveResultBox) {
                    driveResultBox.style.display = 'block';
                    driveResultBox.style.color = '#2271b1';
                    driveResultBox.textContent = 'Valores de Anton cargados. Guarda o prueba la conexión cuando quieras.';
                }
            });
        }

        if (testDriveButton && driveApiInput && driveFolderInput) {
            testDriveButton.addEventListener('click', function() {
                if (!ajaxUrl) {
                    alert('No se pudo determinar la URL de AJAX. Recarga la página e inténtalo nuevamente.');
                    return;
                }

                if (driveResultBox) {
                    driveResultBox.style.display = 'block';
                    driveResultBox.style.color = '#646970';
                    driveResultBox.textContent = 'Probando conexión con Google Drive...';
                }

                testDriveButton.disabled = true;

                const formData = new FormData();
                formData.append('action', 'wp_fast_setup_test_google_drive');
                if (defaultNonce) {
                    formData.append('nonce', defaultNonce);
                }
                formData.append('google_drive_api_key', driveApiInput.value.trim());
                formData.append('google_drive_folder_id', driveFolderInput.value.trim());

                fetch(ajaxUrl, {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                    }
                    return response.json();
                })
                .then(data => {
                    const payload = data && typeof data.data !== 'undefined' ? data.data : {};
                    if (driveResultBox) {
                        driveResultBox.style.display = 'block';
                        if (data.success) {
                            driveResultBox.style.color = '#008a20';
                            const filesFound = typeof payload.files_found !== 'undefined' ? payload.files_found : 0;
                            const baseMessage = payload.message || 'Conexión correcta con Google Drive.';
                            driveResultBox.textContent = `${baseMessage}${filesFound ? ` (ZIPs encontrados: ${filesFound})` : ''}`;
                        } else {
                            driveResultBox.style.color = '#b32d2e';
                            const errorMessage = typeof payload === 'string' && payload ? payload : (data.message || 'No se pudo probar la conexión.');
                            driveResultBox.textContent = errorMessage;
                        }
                    }
                })
                .catch(error => {
                    console.error('WP Fast Setup: Google Drive test error:', error);
                    if (driveResultBox) {
                        driveResultBox.style.display = 'block';
                        driveResultBox.style.color = '#b32d2e';
                        driveResultBox.textContent = error.message;
                    }
                })
                .finally(() => {
                    testDriveButton.disabled = false;
                });
            });
        }

        googleDriveForm.addEventListener('submit', function(e) {
            e.preventDefault();
            console.log('WP Fast Setup: Google Drive form submitted');

            if (!ajaxUrl) {
                alert('No se pudo determinar la URL de AJAX. Recarga la página e inténtalo nuevamente.');
                return;
            }

            const formData = new FormData(googleDriveForm);
            formData.append('action', 'wp_fast_setup_save_google_drive');
            if (defaultNonce) {
                formData.append('nonce', defaultNonce);
            }

            fetch(ajaxUrl, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                console.log('WP Fast Setup: Google Drive response:', data);
                if (data.success) {
                    alert('✅ Configuración de Google Drive guardada correctamente');
                    location.reload();
                } else {
                    alert('❌ Error: ' + (data.message || 'Error desconocido'));
                }
            })
            .catch(error => {
                console.error('WP Fast Setup: Google Drive error:', error);
                alert('❌ Error de conexión al guardar configuración de Google Drive');
            });
        });
    }

    // AJAX form submission for content/pages creation
    const contentForm = document.getElementById('content-form');
    if (contentForm && !contentForm.dataset.wpfsAjaxBound) {
        contentForm.dataset.wpfsAjaxBound = '1';
        contentForm.addEventListener('submit', function(e) {
            e.preventDefault();

            if (!ajaxUrl) {
                alert('No se pudo determinar la URL de AJAX. Recarga la página e inténtalo nuevamente.');
                return;
            }

            const submitter = e.submitter || document.activeElement || contentForm.querySelector('button[type="submit"]');
            const originalText = submitter ? submitter.textContent : '';
            if (submitter) {
                submitter.disabled = true;
                submitter.textContent = 'Procesando...';
            }

            const formData = new FormData();
            formData.append('action', 'wp_fast_setup_create_pages');
            if (defaultNonce) {
                formData.append('nonce', defaultNonce);
            }

            const pagesInput = contentForm.querySelector('[name="pages_input"]');
            const templateInput = contentForm.querySelector('[name="page_template"]:checked');
            const deleteExisting = contentForm.querySelector('[name="delete_existing"]');
            const createMenu = contentForm.querySelector('[name="create_menu"]');
            const pageAction = contentForm.querySelector('[name="page_action"]');

            formData.append('pages_input', pagesInput ? pagesInput.value : '');
            formData.append('page_template', templateInput ? templateInput.value : '');
            if (deleteExisting) {
                formData.append('delete_existing', deleteExisting.value);
            }
            if (createMenu) {
                formData.append('create_menu', createMenu.value);
            }
            if (pageAction) {
                formData.append('page_action', pageAction.value);
            }

            fetch(ajaxUrl, {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('WP Fast Setup: Pages response:', data);
                if (data.success) {
                    if (submitter) {
                        submitter.textContent = '✅ Completado';
                    }
                    setTimeout(() => {
                        if (submitter) {
                            submitter.disabled = false;
                            submitter.textContent = originalText || 'Procesar';
                        }
                    }, 2000);
                } else {
                    alert('❌ Error: ' + (data.data || data.message || 'Error desconocido'));
                    if (submitter) {
                        submitter.disabled = false;
                        submitter.textContent = originalText || 'Procesar';
                    }
                }
            })
            .catch(error => {
                console.error('WP Fast Setup: Pages error:', error);
                alert('❌ Error de conexión: ' + error.message);
                if (submitter) {
                    submitter.disabled = false;
                    submitter.textContent = originalText || 'Procesar';
                }
            });
        });
    }

    // AJAX form submission for features
    const featuresForm = document.querySelector('#features-form');
    const createAdminCheckbox = document.getElementById('feature_create_admin');
    const adminUsernameInput = document.getElementById('feature_create_admin_username');
    const adminEmailInput = document.getElementById('feature_create_admin_email');
    const adminSummary = document.getElementById('feature_create_admin_summary');

    const elementorBrandingForm = document.getElementById('elementor-branding-form');
    const elementorLogoInput = document.getElementById('elementor_logo_id');
    const elementorFaviconInput = document.getElementById('elementor_favicon_id');
    const elementorLogoPreview = document.getElementById('elementor-logo-preview');
    const elementorFaviconPreview = document.getElementById('elementor-favicon-preview');
    const elementorLogoContainer = document.getElementById('elementor-logo-preview-container');
    const elementorFaviconContainer = document.getElementById('elementor-favicon-preview-container');
    const elementorLogoRemoveBtn = document.getElementById('remove-elementor-logo-btn');
    const elementorFaviconRemoveBtn = document.getElementById('remove-elementor-favicon-btn');
    const importKitLogosButton = document.getElementById('wpfs-import-kit-logos');
    const importKitLogosStatus = document.getElementById('wpfs-import-kit-logos-status');

    const updateAdminSummary = () => {
        if (!adminSummary) {
            return;
        }

        if (!createAdminCheckbox || !createAdminCheckbox.checked) {
            adminSummary.textContent = '';
            adminSummary.style.display = 'none';
            return;
        }

        const username = adminUsernameInput ? adminUsernameInput.value.trim() : '';
        const email = adminEmailInput ? adminEmailInput.value.trim() : '';

        if (!username && !email) {
            adminSummary.textContent = 'Se solicitarán credenciales personalizadas antes de crear el administrador auxiliar.';
            adminSummary.style.display = 'block';
            adminSummary.style.color = '#1d2327';
            return;
        }

        adminSummary.textContent = `Se creará un administrador auxiliar con usuario "${username}" y correo "${email}".`;
        adminSummary.style.display = 'block';
        adminSummary.style.color = '#1d2327';
    };

    const collectAdminCredentials = (forcePrompt = false) => {
        if (!createAdminCheckbox || !createAdminCheckbox.checked) {
            updateAdminSummary();
            return null;
        }

        const defaultUsername = adminUsernameInput ? adminUsernameInput.value.trim() : '';
        const defaultEmail = adminEmailInput ? adminEmailInput.value.trim() : '';

        if (!forcePrompt && defaultUsername && defaultEmail) {
            updateAdminSummary();
            return {
                username: defaultUsername,
                email: defaultEmail,
                reused: true
            };
        }

        const requestedUsername = window.prompt('Introduce el nombre de usuario para el administrador auxiliar:', defaultUsername || 'admin_aux');
        if (requestedUsername === null) {
            updateAdminSummary();
            return false;
        }

        const finalUsername = requestedUsername.trim();
        if (!finalUsername) {
            alert('Debes indicar un nombre de usuario válido.');
            updateAdminSummary();
            return false;
        }

        const requestedEmail = window.prompt('Introduce el correo electrónico para el administrador auxiliar:', defaultEmail || '');
        if (requestedEmail === null) {
            updateAdminSummary();
            return false;
        }

        const finalEmail = requestedEmail.trim();
        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!finalEmail || !emailPattern.test(finalEmail)) {
            alert('Introduce un correo electrónico válido.');
            updateAdminSummary();
            return false;
        }

        if (adminUsernameInput) {
            adminUsernameInput.value = finalUsername;
        }
        if (adminEmailInput) {
            adminEmailInput.value = finalEmail;
        }

        updateAdminSummary();

        return {
            username: finalUsername,
            email: finalEmail,
            reused: false
        };
    };

    if (createAdminCheckbox) {
        createAdminCheckbox.addEventListener('change', () => {
            if (!createAdminCheckbox.checked) {
                if (adminUsernameInput) {
                    adminUsernameInput.value = '';
                }
                if (adminEmailInput) {
                    adminEmailInput.value = '';
                }
                updateAdminSummary();
                return;
            }

            const credentials = collectAdminCredentials(true);
            if (credentials === false) {
                createAdminCheckbox.checked = false;
                if (adminUsernameInput) {
                    adminUsernameInput.value = '';
                }
                if (adminEmailInput) {
                    adminEmailInput.value = '';
                }
            }
            updateAdminSummary();
        });
    }

    updateAdminSummary();

    if (featuresForm && !featuresForm.dataset.wpfsAjaxBound) {
        featuresForm.dataset.wpfsAjaxBound = '1';
        featuresForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const selectedFeatures = Array.from(featuresForm.querySelectorAll('.wpf-feature-checkbox:checked'));
            if (!selectedFeatures.length) {
                alert('Selecciona al menos una característica para aplicar.');
                return;
            }

            if (!ajaxUrl) {
                alert('No se pudo determinar la URL de AJAX. Recarga la página e inténtalo nuevamente.');
                return;
            }

            const submitButton = e.submitter || featuresForm.querySelector('button[type="submit"]');
            const originalText = submitButton ? submitButton.innerHTML : '';

            const adminCredentials = collectAdminCredentials(false);
            if (adminCredentials === false) {
                if (submitButton) {
                    submitButton.disabled = false;
                    submitButton.innerHTML = originalText || '⚡ Aplicar Cambios';
                }
                return;
            }

            if (submitButton) {
                submitButton.disabled = true;
                submitButton.innerHTML = '⏳ Aplicando...';
            }

            const formData = new FormData();
            formData.append('action', 'wp_fast_setup_activate_features');
            if (defaultNonce) {
                formData.append('nonce', defaultNonce);
            }

            const nonceField = featuresForm.querySelector('input[name="wp_fast_setup_nonce_features"]');
            if (nonceField) {
                formData.append('wp_fast_setup_nonce_features', nonceField.value);
            }

            selectedFeatures.forEach(checkbox => {
                const featureValue = checkbox.value || checkbox.dataset.feature || checkbox.id;
                if (featureValue) {
                    formData.append('features[]', featureValue);
                }
            });

            if (createAdminCheckbox && createAdminCheckbox.checked && adminCredentials && adminCredentials.username && adminCredentials.email) {
                formData.append('feature_create_admin_username', adminCredentials.username);
                formData.append('feature_create_admin_email', adminCredentials.email);
            }

            fetch(ajaxUrl, {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('WP Fast Setup: Features response:', data);
                if (data.success) {
                    alert('✅ Características aplicadas correctamente');
                    window.location.reload();
                } else {
                    alert('❌ Error: ' + (data.message || 'Error desconocido'));
                    if (submitButton) {
                        submitButton.disabled = false;
                        submitButton.innerHTML = originalText;
                    }
                }
            })
            .catch(error => {
                console.error('WP Fast Setup: Features error:', error);
                alert('❌ Error de conexión al aplicar características');
                if (submitButton) {
                    submitButton.disabled = false;
                    submitButton.innerHTML = originalText;
                }
            })
            .finally(() => {
                if (submitButton) {
                    submitButton.disabled = false;
                    submitButton.innerHTML = originalText || '⚡ Aplicar Cambios';
                }
            });
        });
    }

    if (elementorBrandingForm && !elementorBrandingForm.dataset.wpfsAjaxBound) {
        elementorBrandingForm.dataset.wpfsAjaxBound = '1';
        elementorBrandingForm.addEventListener('submit', function(e) {
            e.preventDefault();

            if (!ajaxUrl) {
                alert('No se pudo determinar la URL de AJAX. Recarga la página e inténtalo nuevamente.');
                return;
            }

            const submitButton = e.submitter || elementorBrandingForm.querySelector('button[type="submit"]');
            const originalText = submitButton ? submitButton.textContent : '';
            if (submitButton) {
                submitButton.disabled = true;
                submitButton.textContent = '⏳ Guardando...';
            }

            const formData = new FormData(elementorBrandingForm);
            formData.append('action', 'wp_fast_setup_save_elementor_branding');
            if (defaultNonce) {
                formData.append('nonce', defaultNonce);
            }

            fetch(ajaxUrl, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const payload = data.data || {};
                    alert('✅ Identidad de Elementor actualizada correctamente.');

                    if (typeof payload.logo_id !== 'undefined' && elementorLogoInput) {
                        elementorLogoInput.value = payload.logo_id || '';
                    }

                    if (typeof payload.logo_url !== 'undefined' && elementorLogoPreview) {
                        if (payload.logo_url) {
                            elementorLogoPreview.src = payload.logo_url;
                            if (elementorLogoContainer) {
                                elementorLogoContainer.style.display = 'block';
                            }
                            if (elementorLogoRemoveBtn) {
                                elementorLogoRemoveBtn.style.display = 'inline-block';
                            }
                        } else {
                            elementorLogoPreview.src = '';
                            if (elementorLogoContainer) {
                                elementorLogoContainer.style.display = 'none';
                            }
                            if (elementorLogoRemoveBtn) {
                                elementorLogoRemoveBtn.style.display = 'none';
                            }
                        }
                    }

                    if (typeof payload.favicon_id !== 'undefined' && elementorFaviconInput) {
                        elementorFaviconInput.value = payload.favicon_id || '';
                    }

                    if (typeof payload.favicon_url !== 'undefined' && elementorFaviconPreview) {
                        if (payload.favicon_url) {
                            elementorFaviconPreview.src = payload.favicon_url;
                            elementorFaviconPreview.style.display = 'block';
                            if (elementorFaviconContainer) {
                                elementorFaviconContainer.style.display = 'block';
                            }
                            if (elementorFaviconRemoveBtn) {
                                elementorFaviconRemoveBtn.style.display = 'inline-block';
                            }
                        } else {
                            elementorFaviconPreview.src = '';
                            if (elementorFaviconContainer) {
                                elementorFaviconContainer.style.display = 'none';
                            }
                            if (elementorFaviconRemoveBtn) {
                                elementorFaviconRemoveBtn.style.display = 'none';
                            }
                        }
                    }
                } else {
                    alert('❌ Error: ' + (data.message || 'No se pudo actualizar Elementor.'));
                }
            })
            .catch(error => {
                console.error('WP Fast Setup: Elementor branding error:', error);
                alert('❌ Error de conexión al guardar identidad de Elementor');
            })
            .finally(() => {
                if (submitButton) {
                    submitButton.disabled = false;
                    submitButton.textContent = originalText || submitButton.textContent;
                }
            });
        });
    }

    if (importKitLogosButton) {
        importKitLogosButton.addEventListener('click', () => {
            if (!ajaxUrl) {
                alert('No se pudo determinar la URL de AJAX. Recarga la página e inténtalo nuevamente.');
                return;
            }

            const originalText = importKitLogosButton.textContent;
            importKitLogosButton.disabled = true;
            importKitLogosButton.textContent = '⏳ Importando...';

            if (importKitLogosStatus) {
                importKitLogosStatus.style.color = '#646970';
                importKitLogosStatus.textContent = 'Importando logos del Kit Digital...';
            }

            const formData = new FormData();
            formData.append('action', 'wp_fast_setup_import_media');

            const mediaNonce = importKitLogosButton.dataset.nonce || '';
            if (mediaNonce) {
                formData.append('_wpnonce', mediaNonce);
            }
            if (defaultNonce) {
                formData.append('nonce', defaultNonce);
            }

            fetch(ajaxUrl, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (importKitLogosStatus) {
                        importKitLogosStatus.style.color = '#008a20';
                        const imported = data.data && typeof data.data.imported_count !== 'undefined' ? data.data.imported_count : '';
                        importKitLogosStatus.textContent = imported ? `Se importaron ${imported} elementos del kit.` : 'Logos importados correctamente.';
                    }
                    alert('✅ Imágenes del Kit Digital importadas a la biblioteca.');
                } else {
                    if (importKitLogosStatus) {
                        importKitLogosStatus.style.color = '#b32d2e';
                        importKitLogosStatus.textContent = data.message || 'No se pudieron importar los logos.';
                    }
                    alert('❌ Error: ' + (data.message || 'No se pudieron importar los logos.'));
                }
            })
            .catch(error => {
                console.error('WP Fast Setup: Import Kit Logos error:', error);
                if (importKitLogosStatus) {
                    importKitLogosStatus.style.color = '#b32d2e';
                    importKitLogosStatus.textContent = error.message;
                }
                alert('❌ Error de conexión al importar logos del Kit Digital');
            })
            .finally(() => {
                importKitLogosButton.disabled = false;
                importKitLogosButton.textContent = originalText;
            });
        });
    }

    // Media library selectors for logo and favicon
    function initMediaSelector(buttonId, inputId, previewId, containerId, removeBtnId) {
        const button = document.getElementById(buttonId);
        const input = document.getElementById(inputId);
        const preview = document.getElementById(previewId);
        const container = document.getElementById(containerId);
        const removeBtn = document.getElementById(removeBtnId);

        if (button) {
            button.addEventListener('click', function(e) {
                e.preventDefault();

                if (typeof wp !== 'undefined' && wp.media) {
                    const frame = wp.media({
                        title: 'Seleccionar imagen',
                        button: {
                            text: 'Usar esta imagen'
                        },
                        multiple: false
                    });

                    frame.on('select', function() {
                        const attachment = frame.state().get('selection').first().toJSON();
                        if (input) input.value = attachment.id;
                        if (preview) preview.src = attachment.sizes && attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.url;
                        if (container) container.style.display = 'block';
                        if (removeBtn) removeBtn.style.display = 'inline-block';
                    });

                    frame.open();
                } else {
                    alert('WordPress Media Library no está disponible');
                }
            });
        }

        if (removeBtn) {
            removeBtn.addEventListener('click', function(e) {
                e.preventDefault();
                if (input) input.value = '';
                if (preview) preview.src = '';
                if (container) container.style.display = 'none';
                if (removeBtn) removeBtn.style.display = 'none';
            });
        }
    }

    // Initialize media selectors
    initMediaSelector('select-logo-btn', 'site_logo_id', 'logo-preview', 'logo-preview-container', 'remove-logo-btn');
    initMediaSelector('select-favicon-btn', 'site_favicon_id', 'favicon-preview', 'favicon-preview-container', 'remove-favicon-btn');
    initMediaSelector('select-elementor-logo-btn', 'elementor_logo_id', 'elementor-logo-preview', 'elementor-logo-preview-container', 'remove-elementor-logo-btn');
    initMediaSelector('select-elementor-favicon-btn', 'elementor_favicon_id', 'elementor-favicon-preview', 'elementor-favicon-preview-container', 'remove-elementor-favicon-btn');

    // Initialize plugin controls when plugins tab is activated
    function initializePluginControls() {
        console.log('WP Fast Setup: Initializing plugin controls');
        if (wpfsPluginControlsInitialized) {
            console.log('WP Fast Setup: Plugin controls already initialized');
            return;
        }

        // Plugin search and control functionality
        const pluginSearch = document.getElementById('plugin-search');
        const sortAscBtn = document.getElementById('sort-asc');
        const sortDescBtn = document.getElementById('sort-desc');
        const selectAllBtn = document.getElementById('select-all-plugins');
        const deselectAllBtn = document.getElementById('deselect-all-plugins');
        const clearAllBtn = document.getElementById('clear-all-plugins');
        const pluginItems = document.querySelectorAll('.wpf-plugin-item');

        console.log('WP Fast Setup: Plugin controls - sortAscBtn:', sortAscBtn, 'sortDescBtn:', sortDescBtn, 'pluginItems:', pluginItems.length);

        // Plugin search functionality
        if (pluginSearch) {
            pluginSearch.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase().trim();

                pluginItems.forEach(item => {
                    const label = item.querySelector('label');
                    if (label) {
                        const pluginName = label.textContent.toLowerCase();
                        if (pluginName.includes(searchTerm)) {
                            item.style.display = 'block';
                        } else {
                            item.style.display = 'none';
                        }
                    }
                });
            });
        }

        // Sort functionality
        if (sortAscBtn) {
            sortAscBtn.addEventListener('click', function() {
                console.log('WP Fast Setup: Sort ascending clicked');
                const container = document.querySelector('.wpf-plugin-list');
                console.log('WP Fast Setup: Container found:', container);
                if (!container) return;

                const items = Array.from(pluginItems);
                console.log('WP Fast Setup: Sorting', items.length, 'items ascending');
                items.sort((a, b) => {
                    const nameA = a.querySelector('label').textContent.toLowerCase();
                    const nameB = b.querySelector('label').textContent.toLowerCase();
                    return nameA.localeCompare(nameB);
                });

                items.forEach(item => container.appendChild(item));
                console.log('WP Fast Setup: Sort ascending completed');
            });
        }

        if (sortDescBtn) {
            sortDescBtn.addEventListener('click', function() {
                console.log('WP Fast Setup: Sort descending clicked');
                const container = document.querySelector('.wpf-plugin-list');
                console.log('WP Fast Setup: Container found:', container);
                if (!container) return;

                const items = Array.from(pluginItems);
                console.log('WP Fast Setup: Sorting', items.length, 'items descending');
                items.sort((a, b) => {
                    const nameA = a.querySelector('label').textContent.toLowerCase();
                    const nameB = b.querySelector('label').textContent.toLowerCase();
                    return nameB.localeCompare(nameA);
                });

                items.forEach(item => container.appendChild(item));
                console.log('WP Fast Setup: Sort descending completed');
            });
        }

        // Select/Deselect all functionality
        if (selectAllBtn) {
            selectAllBtn.addEventListener('click', function() {
                pluginItems.forEach(item => {
                    const checkbox = item.querySelector('input[type="checkbox"]');
                    if (checkbox) {
                        checkbox.checked = true;
                    }
                });
            });
        }

        if (deselectAllBtn) {
            deselectAllBtn.addEventListener('click', function() {
                pluginItems.forEach(item => {
                    const checkbox = item.querySelector('input[type="checkbox"]');
                    if (checkbox) {
                        checkbox.checked = false;
                    }
                });
            });
        }

        // Clear all selections
        if (clearAllBtn) {
            clearAllBtn.addEventListener('click', function() {
                pluginItems.forEach(item => {
                    const checkbox = item.querySelector('input[type="checkbox"]');
                    if (checkbox) {
                        checkbox.checked = false;
                    }
                });
                if (pluginSearch) {
                    pluginSearch.value = '';
                    pluginSearch.dispatchEvent(new Event('input'));
                }
            });
        }

        // Plugin item click functionality - toggle checkbox when clicking the item
        pluginItems.forEach(item => {
            item.addEventListener('click', function(e) {
                // Don't toggle if clicking on the checkbox or label directly
                if (e.target.tagName === 'INPUT' || e.target.tagName === 'LABEL') {
                    return;
                }

                const checkbox = this.querySelector('input[type="checkbox"]');
                if (checkbox) {
                    checkbox.checked = !checkbox.checked;
                    // Trigger change event for any listeners
                    checkbox.dispatchEvent(new Event('change', { bubbles: true }));
                }
            });

            // Add cursor pointer to indicate clickability
            item.style.cursor = 'pointer';
        });

        wpfsPluginControlsInitialized = true;
    }

    // Initialize plugin controls if plugins tab is active by default
    if (document.getElementById('plugins') && document.getElementById('plugins').classList.contains('active')) {
        initializePluginControls();
    }

    console.log('WP Fast Setup: Initialization complete');
};

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initWPFastSetup);
} else {
    initWPFastSetup();
}

// Function to set page creation action
function setPageAction(action) {
    console.log('WP Fast Setup: Setting page action:', action);

    const deleteExistingField = document.getElementById('delete_existing');
    const createMenuField = document.getElementById('create_menu');
    const pageActionField = document.getElementById('page_action');

    if (deleteExistingField && createMenuField && pageActionField) {
        pageActionField.value = action;

        switch(action) {
            case 'create':
                deleteExistingField.value = '0';
                createMenuField.value = '0';
                break;
            case 'delete':
                deleteExistingField.value = '1';
                createMenuField.value = '0';
                break;
            case 'create_menu':
                deleteExistingField.value = '0';
                createMenuField.value = '1';
                break;
            case 'delete_menu':
                deleteExistingField.value = '1';
                createMenuField.value = '1';
                break;
        }

        console.log('WP Fast Setup: Action set - delete_existing:', deleteExistingField.value, 'create_menu:', createMenuField.value);
    } else {
        console.error('WP Fast Setup: Required hidden fields not found');
    }
}