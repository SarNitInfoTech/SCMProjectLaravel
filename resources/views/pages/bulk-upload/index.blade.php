@extends("layouts.layout")

@section("bodyContent")
<div class="container mx-auto px-4 py-8">
    <!-- Breadcrumb & Header -->
    <div class="md:flex block items-center justify-between mb-8 page-header-breadcrumb">
        <div>
            <h4 class="text-2xl font-bold text-gray-800 leading-tight flex items-center gap-2">
                <i class="bi bi-file-earmark-arrow-up-fill text-indigo-600"></i> {{ $title }}
            </h4>
            <p class="text-sm text-gray-500 mt-1">Upload CSV or Excel spreadsheets to import master data or documents in bulk.</p>
        </div>
    </div>

    <!-- Alert Messages -->
    @if (session('success'))
        <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 rounded-r text-green-700 text-sm shadow-sm flex items-center gap-3">
            <i class="bi bi-check-circle-fill text-lg"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    @if (session('error'))
        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-r text-red-700 text-sm shadow-sm flex items-center gap-3">
            <i class="bi bi-exclamation-triangle-fill text-lg"></i>
            <div>{{ session('error') }}</div>
        </div>
    @endif

    <!-- Main Content Layout -->
    <div class="grid grid-cols-12 gap-8">
        
        <!-- Left Column: Form & Drag-and-Drop (8 Cols) -->
        <div class="col-span-12 lg:col-span-8 space-y-6">
            
            <div class="card shadow-md border border-gray-100 rounded-xl bg-white overflow-hidden transition-all duration-300 hover:shadow-lg">
                <div class="card-header bg-gradient-to-r from-gray-50 to-white px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                        <i class="bi bi-cloud-upload text-indigo-500"></i> Import Settings
                    </h3>
                </div>
                
                <div class="p-6">
                    <form method="POST" action="{{ route('bulk-upload.store') }}" enctype="multipart/form-data" id="import-form">
                        @csrf
                        
                        <!-- Select Module -->
                        <div class="mb-6">
                            <label for="module-select" class="block text-sm font-semibold text-gray-700 mb-2">Select Target Module <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <select 
                                    name="module" 
                                    id="module-select" 
                                    class="block w-full rounded-lg border border-gray-200 px-4 py-3 bg-gray-50 text-gray-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all appearance-none cursor-pointer"
                                >
                                    @foreach($modules as $key => $mod)
                                        <option value="{{ $key }}" {{ $selectedModule === $key ? 'selected' : '' }} data-desc="{{ $mod['description'] }}" data-icon="{{ $mod['icon'] }}">
                                            {{ $mod['name'] }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400">
                                    <i class="bi bi-chevron-down"></i>
                                </div>
                            </div>
                            <p class="text-xs text-gray-400 mt-1.5 flex items-center gap-1">
                                <i class="bi bi-info-circle"></i> <span id="module-desc-helper">Import records into the database.</span>
                            </p>
                        </div>

                        <!-- Drag and Drop Box -->
                        <div class="mb-6">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Spreadsheet File <span class="text-red-500">*</span></label>
                            
                            <div 
                                id="dropzone"
                                class="border-2 border-dashed border-gray-200 hover:border-indigo-400 rounded-xl p-8 text-center bg-gray-50 hover:bg-indigo-50/20 transition-all cursor-pointer relative"
                            >
                                <input 
                                    type="file" 
                                    name="file" 
                                    id="file-input" 
                                    accept=".csv,.txt,.xlsx,.xls" 
                                    class="hidden" 
                                    required
                                >
                                
                                <div class="space-y-3" id="dropzone-prompt">
                                    <div class="w-14 h-14 bg-indigo-50 rounded-full flex items-center justify-center mx-auto text-indigo-600 text-2xl shadow-inner transition-transform duration-300" id="upload-icon-container">
                                        <i class="bi bi-file-earmark-spreadsheet-fill"></i>
                                    </div>
                                    <div class="text-sm">
                                        <span class="font-semibold text-indigo-600 hover:text-indigo-700">Click to upload</span> or drag and drop
                                    </div>
                                    <p class="text-xs text-gray-400">Supports CSV, XLSX, and XLS up to 10MB</p>
                                </div>

                                <div class="hidden space-y-2" id="dropzone-file-info">
                                    <div class="w-14 h-14 bg-green-50 rounded-full flex items-center justify-center mx-auto text-green-600 text-2xl">
                                        <i class="bi bi-check-all"></i>
                                    </div>
                                    <div class="text-sm font-semibold text-gray-800" id="selected-file-name">filename.csv</div>
                                    <p class="text-xs text-gray-400" id="selected-file-size">14 KB</p>
                                    <button 
                                        type="button" 
                                        id="remove-file-btn" 
                                        class="mt-2 px-3 py-1 text-xs font-medium text-red-600 bg-red-50 hover:bg-red-100 rounded-full transition-all"
                                    >
                                        Remove File
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center justify-between border-t pt-5 mt-6">
                            <a 
                                href="#" 
                                id="download-template-btn"
                                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-gray-700 hover:text-indigo-600 bg-gray-100 hover:bg-indigo-50 rounded-lg shadow-sm transition-all"
                            >
                                <i class="bi bi-download"></i> Download CSV Template
                            </a>

                            <button 
                                type="submit" 
                                class="inline-flex items-center gap-2 px-6 py-2.5 text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg shadow transition-all hover:shadow-md"
                            >
                                <i class="bi bi-cloud-arrow-up"></i> Upload & Import
                            </button>
                        </div>

                    </form>
                </div>
            </div>

            <!-- Import results logs -->
            @if (session('import_results'))
                @php
                    $results = session('import_results');
                @endphp
                <div class="card shadow-md border border-gray-100 rounded-xl bg-white overflow-hidden" id="results-card">
                    <div class="card-header bg-gradient-to-r from-gray-50 to-white px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                            <i class="bi bi-clipboard-data text-indigo-500"></i> Import Results Summary
                        </h3>
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-800">
                                {{ $results['success_count'] }} Passed
                            </span>
                            @if($results['error_count'] > 0)
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-800">
                                    {{ $results['error_count'] }} Failed
                                </span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="p-6">
                        <div class="border rounded-xl overflow-hidden">
                            <div class="max-h-[350px] overflow-y-auto">
                                <table class="min-w-full divide-y divide-gray-100 text-sm">
                                    <thead class="bg-gray-50 sticky top-0">
                                        <tr>
                                            <th class="px-4 py-3 text-left font-semibold text-gray-700 w-24">Row</th>
                                            <th class="px-4 py-3 text-left font-semibold text-gray-700 w-28">Status</th>
                                            <th class="px-4 py-3 text-left font-semibold text-gray-700">Message / Issue</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100">
                                        @foreach($results['logs'] as $log)
                                            <tr class="hover:bg-gray-50 transition-colors">
                                                <td class="px-4 py-3 font-semibold text-gray-600">Row {{ $log['row'] }}</td>
                                                <td class="px-4 py-3">
                                                    @if($log['status'] === 'success')
                                                        <span class="inline-flex items-center gap-1 text-xs font-bold text-green-700 bg-green-50 px-2 py-0.5 rounded-full">
                                                            <i class="bi bi-check-circle"></i> Success
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center gap-1 text-xs font-bold text-red-700 bg-red-50 px-2 py-0.5 rounded-full">
                                                            <i class="bi bi-x-circle"></i> Failed
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="px-4 py-3 text-gray-700 font-medium">
                                                    {{ $log['message'] }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

        </div>

        <!-- Right Column: Live Help & Guidelines (4 Cols) -->
        <div class="col-span-12 lg:col-span-4">
            
            <div class="card shadow-md border border-gray-100 rounded-xl bg-white overflow-hidden sticky top-8">
                <div class="card-header bg-gradient-to-r from-gray-50 to-white px-6 py-4 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                        <i class="bi bi-file-earmark-ruled text-indigo-500"></i> Column Guidelines
                    </h3>
                </div>
                
                <div class="p-6">
                    <div id="module-guide-header" class="mb-4 flex items-center gap-2 pb-3 border-b">
                        <div class="w-9 h-9 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center text-lg" id="guide-module-icon">
                            <i class="bi bi-building"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-800" id="guide-module-title">Departments</h4>
                            <p class="text-xs text-gray-400" id="guide-module-desc">Dynamic column layout instructions</p>
                        </div>
                    </div>

                    <div class="max-h-[450px] overflow-y-auto pr-1" id="guide-columns-list">
                        <!-- Populated by JS -->
                    </div>

                    <div class="mt-6 pt-4 border-t border-gray-100 bg-indigo-50/30 -mx-6 -mb-6 p-6 rounded-b-xl">
                        <h5 class="text-xs font-bold text-indigo-800 uppercase tracking-wider mb-2">Import Guidelines</h5>
                        <ul class="text-xs text-gray-600 space-y-1.5 list-disc pl-4 leading-relaxed">
                            <li>Keep header names unchanged in the first row.</li>
                            <li>Dates must be written in <strong class="text-gray-800">YYYY-MM-DD</strong> format.</li>
                            <li>Ensure linked parent names (e.g. Department, Unit) exist in master tables.</li>
                            <li>Blank rows will be skipped automatically.</li>
                        </ul>
                    </div>
                </div>
            </div>

        </div>

    </div>
</div>

<!-- Styles and JavaScript -->
<style>
    #dropzone.dragover {
        border-color: #4f46e5;
        background-color: #eef2ff;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const modules = @json($modules);
        const moduleSelect = document.getElementById('module-select');
        const downloadTemplateBtn = document.getElementById('download-template-btn');
        const moduleDescHelper = document.getElementById('module-desc-helper');
        
        // Guide elements
        const guideModuleTitle = document.getElementById('guide-module-title');
        const guideModuleDesc = document.getElementById('guide-module-desc');
        const guideModuleIcon = document.getElementById('guide-module-icon');
        const guideColumnsList = document.getElementById('guide-columns-list');

        // Drag and drop elements
        const dropzone = document.getElementById('dropzone');
        const fileInput = document.getElementById('file-input');
        const dropzonePrompt = document.getElementById('dropzone-prompt');
        const dropzoneFileInfo = document.getElementById('dropzone-file-info');
        const selectedFileName = document.getElementById('selected-file-name');
        const selectedFileSize = document.getElementById('selected-file-size');
        const removeFileBtn = document.getElementById('remove-file-btn');
        const uploadIconContainer = document.getElementById('upload-icon-container');

        // Update Template Download URL & Help Guideline dynamically
        function updateModuleDetails() {
            const val = moduleSelect.value;
            const mod = modules[val];
            if (!mod) return;

            // Update helper text
            moduleDescHelper.textContent = mod.description;

            // Update template download button URL
            downloadTemplateBtn.href = `{{ url('/bulk-upload/template') }}/${val}`;

            // Update right guide
            guideModuleTitle.textContent = mod.name;
            guideModuleDesc.textContent = mod.description;
            guideModuleIcon.innerHTML = `<i class="bi ${mod.icon}"></i>`;

            // Render columns guidelines
            guideColumnsList.innerHTML = '';
            mod.columns.forEach(col => {
                const colItem = document.createElement('div');
                colItem.className = 'mb-4 border-b border-gray-50 pb-3 last:border-0 last:pb-0';
                
                colItem.innerHTML = `
                    <div class="flex items-center justify-between">
                        <span class="font-bold text-sm text-gray-800">${col.name}</span>
                        <div class="flex items-center gap-1.5">
                            <span class="text-[10px] font-bold px-1.5 py-0.5 rounded ${col.required ? 'bg-red-50 text-red-700' : 'bg-gray-100 text-gray-500'}">
                                ${col.required ? 'Required' : 'Optional'}
                            </span>
                            <span class="text-[10px] bg-indigo-50 text-indigo-700 font-bold px-1.5 py-0.5 rounded">${col.type}</span>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 mt-1 leading-relaxed">${col.desc}</p>
                `;
                guideColumnsList.appendChild(colItem);
            });
        }

        // Initialize details
        moduleSelect.addEventListener('change', updateModuleDetails);
        updateModuleDetails();

        // Check query parameter to scroll to results
        @if (session('import_results'))
            const resultsCard = document.getElementById('results-card');
            if (resultsCard) {
                resultsCard.scrollIntoView({ behavior: 'smooth' });
            }
        @endif

        // Drag and drop logic
        dropzone.addEventListener('click', () => fileInput.click());

        dropzone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropzone.classList.add('dragover');
            uploadIconContainer.classList.add('scale-110');
        });

        dropzone.addEventListener('dragleave', () => {
            dropzone.classList.remove('dragover');
            uploadIconContainer.classList.remove('scale-110');
        });

        dropzone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropzone.classList.remove('dragover');
            uploadIconContainer.classList.remove('scale-110');

            if (e.dataTransfer.files.length) {
                fileInput.files = e.dataTransfer.files;
                updateFileInfo();
            }
        });

        fileInput.addEventListener('change', updateFileInfo);

        function updateFileInfo() {
            if (fileInput.files.length) {
                const file = fileInput.files[0];
                selectedFileName.textContent = file.name;
                selectedFileSize.textContent = formatBytes(file.size);
                
                dropzonePrompt.classList.add('hidden');
                dropzoneFileInfo.classList.remove('hidden');
            }
        }

        removeFileBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            fileInput.value = '';
            dropzonePrompt.classList.remove('hidden');
            dropzoneFileInfo.classList.add('hidden');
        });

        function formatBytes(bytes, decimals = 2) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const dm = decimals < 0 ? 0 : decimals;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
        }
    });
</script>
@endsection
