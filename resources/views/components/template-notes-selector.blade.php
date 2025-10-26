{{-- filepath: resources/views/components/template-notes-selector.blade.php --}}
<div class="template-notes-selector-wrapper">
    <div class="template-section">
        <div class="info-card">
            <i class="bx bx-bulb me-2"></i>
            <small>Pilih template catatan atau tulis catatan Anda sendiri</small>
        </div>

        @foreach ($getTemplates() as $key => $templateGroup)
            <div class="template-group template-group-{{ $textareaId }}"
                id="template-{{ $key }}-{{ $textareaId }}" style="display: none;">
                <div class="template-category">
                    <i class="bx {{ $templateGroup['icon'] }}"></i> {{ $templateGroup['category'] }}
                </div>
                <div class="d-flex flex-wrap">
                    @foreach ($templateGroup['items'] as $item)
                        <span class="badge {{ $item['class'] }} template-badge template-badge-{{ $textareaId }}"
                            data-target="{{ $textareaId }}" data-template="{{ $item['template'] }}">
                            {{ $item['text'] }}
                        </span>
                    @endforeach
                </div>
            </div>
        @endforeach

        <!-- Quick Actions -->
        <div class="quick-actions mt-3">
            <button type="button" class="btn btn-sm btn-outline-secondary btn-clear-{{ $textareaId }}"
                data-target="{{ $textareaId }}">
                <i class="bx bx-eraser"></i> Hapus Template
            </button>
            <button type="button" class="btn btn-sm btn-outline-info btn-custom-{{ $textareaId }}"
                data-target="{{ $textareaId }}">
                <i class="bx bx-edit"></i> Tulis Custom
            </button>
            <button type="button" class="btn btn-sm btn-outline-primary btn-preview-{{ $textareaId }}"
                data-target="{{ $textareaId }}">
                <i class="bx bx-show"></i> Preview
            </button>
        </div>
    </div>

    <!-- Preview Catatan -->
    <div class="preview-catatan preview-{{ $textareaId }}" style="display: none;">
        <strong><i class="bx bx-show me-1"></i> Preview Catatan:</strong>
        <p class="mb-0 mt-2 preview-text-{{ $textareaId }}"></p>
    </div>

    <textarea name="{{ $textareaName }}" id="{{ $textareaId }}" class="form-control mt-2 template-textarea"
        rows="3" required data-status-field="{{ $statusField }}"
        placeholder="Pilih template atau tulis catatan Anda...">{{ $currentValue }}</textarea>

    <div class="char-counter char-counter-{{ $textareaId }}">
        <span class="current-chars-{{ $textareaId }}">0</span> / 500 karakter
    </div>
</div>

@once
    @push('styles')
        <style>
            .template-section {
                background-color: #f8f9fa;
                border-radius: 8px;
                padding: 15px;
                margin-bottom: 15px;
            }

            .template-badge {
                cursor: pointer;
                transition: all 0.3s ease;
                margin: 4px;
                padding: 6px 12px;
            }

            .template-badge:hover {
                transform: translateY(-2px);
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
            }

            .selected-template {
                background-color: #0d6efd !important;
                color: white !important;
                border-color: #0d6efd !important;
                box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.25);
            }

            .template-category {
                font-weight: 600;
                color: #495057;
                margin-bottom: 10px;
                font-size: 0.95rem;
            }

            .info-card {
                border-left: 4px solid #0d6efd;
                background-color: #e7f1ff;
                padding: 12px;
                border-radius: 4px;
                margin-bottom: 15px;
            }

            .quick-actions {
                display: flex;
                gap: 8px;
                flex-wrap: wrap;
                margin-top: 10px;
            }

            .preview-catatan {
                background-color: #fff3cd;
                border: 1px dashed #ffc107;
                padding: 10px;
                border-radius: 4px;
                margin-top: 10px;
            }

            .char-counter {
                font-size: 0.85rem;
                color: #6c757d;
                text-align: right;
                margin-top: 5px;
            }

            .char-counter.warning {
                color: #ffc107;
            }

            .char-counter.danger {
                color: #dc3545;
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                console.log('Template Notes Selector initialized');

                // Universal Template Handler
                class TemplateNotesHandler {
                    constructor(textareaId, statusField) {
                        this.textareaId = textareaId;
                        this.statusField = statusField;
                        this.textarea = document.getElementById(textareaId);
                        this.charCounter = document.querySelector(`.char-counter-${textareaId}`);
                        this.currentChars = document.querySelector(`.current-chars-${textareaId}`);
                        this.preview = document.querySelector(`.preview-${textareaId}`);
                        this.previewText = document.querySelector(`.preview-text-${textareaId}`);

                        if (!this.textarea) {
                            console.error(`Textarea with id ${textareaId} not found`);
                            return;
                        }

                        console.log(`Initializing handler for textarea: ${textareaId}`);
                        this.init();
                    }

                    init() {
                        this.setupBadgeListeners();
                        this.setupButtonListeners();
                        this.setupCharCounter();
                        this.setupStatusListener();
                        this.updateCharCounter();

                        // Initial load - show templates based on current status
                        setTimeout(() => {
                            this.showTemplatesByStatus();
                        }, 100);
                    }

                    setupBadgeListeners() {
                        const badges = document.querySelectorAll(`.template-badge-${this.textareaId}`);
                        console.log(`Found ${badges.length} badges for ${this.textareaId}`);

                        badges.forEach(badge => {
                            badge.addEventListener('click', () => {
                                const template = badge.getAttribute('data-template');
                                console.log('Template selected:', template);

                                // Remove selected class from all badges
                                badges.forEach(b => b.classList.remove('selected-template'));

                                // Add selected class to clicked badge
                                badge.classList.add('selected-template');

                                // Set textarea value
                                this.textarea.value = template;
                                this.updateCharCounter();

                                this.showToast('success', 'Template dipilih!');
                            });
                        });
                    }

                    setupButtonListeners() {
                        // Clear button
                        const clearBtn = document.querySelector(`.btn-clear-${this.textareaId}`);
                        if (clearBtn) {
                            clearBtn.addEventListener('click', () => {
                                this.textarea.value = '';
                                document.querySelectorAll(`.template-badge-${this.textareaId}`).forEach(
                                b => {
                                    b.classList.remove('selected-template');
                                });
                                this.updateCharCounter();
                                if (this.preview) this.preview.style.display = 'none';
                                this.showToast('info', 'Template dihapus');
                            });
                        }

                        // Custom button
                        const customBtn = document.querySelector(`.btn-custom-${this.textareaId}`);
                        if (customBtn) {
                            customBtn.addEventListener('click', () => {
                                this.textarea.focus();
                                document.querySelectorAll(`.template-badge-${this.textareaId}`).forEach(
                                b => {
                                    b.classList.remove('selected-template');
                                });
                                this.showToast('info', 'Tulis catatan custom Anda');
                            });
                        }

                        // Preview button
                        const previewBtn = document.querySelector(`.btn-preview-${this.textareaId}`);
                        if (previewBtn) {
                            previewBtn.addEventListener('click', () => {
                                const text = this.textarea.value.trim();
                                if (text && this.preview && this.previewText) {
                                    this.previewText.textContent = text;
                                    this.preview.style.display = 'block';
                                } else {
                                    this.showToast('warning', 'Catatan masih kosong');
                                }
                            });
                        }
                    }

                    setupCharCounter() {
                        if (this.textarea) {
                            this.textarea.addEventListener('input', () => this.updateCharCounter());
                        }
                    }

                    setupStatusListener() {
                        const statusSelect = document.getElementById(this.statusField);
                        console.log(`Status field: ${this.statusField}`, statusSelect);

                        if (statusSelect) {
                            statusSelect.addEventListener('change', () => {
                                console.log('Status changed to:', statusSelect.value);
                                this.showTemplatesByStatus();
                            });
                        }
                    }

                    showTemplatesByStatus() {
                        const statusSelect = document.getElementById(this.statusField);

                        // Hide all template groups for this textarea
                        const allGroups = document.querySelectorAll(`.template-group-${this.textareaId}`);
                        console.log(`Hiding ${allGroups.length} template groups`);
                        allGroups.forEach(el => {
                            el.style.display = 'none';
                        });

                        if (statusSelect) {
                            const statusValue = statusSelect.value;
                            console.log(`Showing templates for status: ${statusValue}`);

                            // Show appropriate template group
                            const templateGroup = document.getElementById(
                                `template-${statusValue}-${this.textareaId}`);
                            if (templateGroup) {
                                templateGroup.style.display = 'block';
                                console.log(
                                    `Template group found and displayed: template-${statusValue}-${this.textareaId}`
                                    );
                            } else {
                                console.warn(
                                `Template group not found: template-${statusValue}-${this.textareaId}`);
                            }
                        } else {
                            console.warn(`Status select not found: ${this.statusField}`);
                        }
                    }

                    updateCharCounter() {
                        if (!this.currentChars || !this.charCounter) return;

                        const length = this.textarea.value.length;
                        this.currentChars.textContent = length;

                        this.charCounter.classList.remove('warning', 'danger');
                        if (length > 450) {
                            this.charCounter.classList.add('danger');
                        } else if (length > 400) {
                            this.charCounter.classList.add('warning');
                        }
                    }

                    showToast(type, message) {
                        const bgColor = {
                            'success': '#28a745',
                            'error': '#dc3545',
                            'warning': '#ffc107',
                            'info': '#17a2b8'
                        };

                        if (typeof Toastify !== 'undefined') {
                            Toastify({
                                text: message,
                                duration: 3000,
                                gravity: "top",
                                position: "right",
                                backgroundColor: bgColor[type] || bgColor['info'],
                                stopOnFocus: true
                            }).showToast();
                        } else {
                            console.log(`Toast (${type}): ${message}`);
                        }
                    }
                }

                // Auto-initialize all template selectors
                const textareas = document.querySelectorAll('.template-textarea');
                console.log(`Found ${textareas.length} template textareas`);

                textareas.forEach(textarea => {
                    const statusField = textarea.getAttribute('data-status-field') || 'status';
                    console.log(`Initializing textarea: ${textarea.id} with status field: ${statusField}`);
                    new TemplateNotesHandler(textarea.id, statusField);
                });
            });
        </script>
    @endpush
@endonce
