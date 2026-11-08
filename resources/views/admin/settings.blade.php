
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            System Settings
        </h2>
    </x-slot>

    <style>
        .settings-container {
            max-width: 1280px;
            margin: auto;
            padding: 28px 20px;
        }

        .settings-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }

        .settings-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 22px;
            min-width: 0;
        }

        .settings-title {
            font-size: 16px;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 5px;
        }

        .settings-description {
            font-size: 13px;
            color: #6b7280;
            margin-bottom: 18px;
        }

        .settings-input {
            width: 100%;
            min-width: 0;
            padding: 8px 10px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 13px;
            background: #fff;
        }

        .settings-input:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 2px #dbeafe;
        }

        .settings-btn {
            padding: 8px 14px;
            border: none;
            border-radius: 6px;
            background: #2563eb;
            color: #fff;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            white-space: nowrap;
        }

        .settings-btn:hover {
            background: #1d4ed8;
        }

        .settings-btn-danger {
            background: transparent;
            color: #dc2626;
            padding: 4px 0;
            font-size: 12px;
            border: none;
            cursor: pointer;
        }

        .settings-btn-danger:hover {
            color: #991b1b;
        }

        .settings-link {
            color: #2563eb;
            font-size: 13px;
            font-weight: 500;
            border: none;
            background: none;
            cursor: pointer;
        }

        .settings-row {
            border-top: 1px solid #f1f5f9;
            padding: 14px 0;
        }

        .settings-add-form {
            display: flex;
            gap: 8px;
            margin-bottom: 14px;
        }

        .priority-form {
            display: grid;
            grid-template-columns: 1.2fr .7fr .8fr .8fr;
            gap: 8px;
            align-items: center;
        }

        .sla-form {
            display: grid;
            grid-template-columns: .6fr 1fr 1fr 1fr;
            gap: 8px;
            align-items: center;
            margin-top: 10px;
        }

        .business-hours-grid {
            display: grid;
            grid-template-columns: repeat(7, minmax(0, 1fr));
            gap: 10px;
        }

        .day-card {
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 12px;
            min-width: 0;
            background: #fafafa;
        }

        .day-name {
            font-size: 13px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 10px;
        }

        .day-card input[type="checkbox"] {
            accent-color: #2563eb;
        }

        .day-card input[type="time"] {
            margin-top: 8px;
        }

        .holiday-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            border-top: 1px solid #f1f5f9;
            padding: 12px 0;
            font-size: 13px;
        }

        .settings-alert {
            padding: 12px 16px;
            border-radius: 7px;
            font-size: 13px;
            margin-bottom: 16px;
        }

        @media (max-width: 1100px) {
            .business-hours-grid {
                grid-template-columns: repeat(4, minmax(0, 1fr));
            }
        }

        @media (max-width: 768px) {
            .settings-grid {
                grid-template-columns: 1fr;
            }

            .business-hours-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .priority-form {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .sla-form {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 480px) {
            .settings-container {
                padding: 18px 12px;
            }

            .settings-card {
                padding: 16px;
            }

            .business-hours-grid {
                grid-template-columns: 1fr;
            }

            .priority-form,
            .sla-form {
                grid-template-columns: 1fr;
            }

            .settings-add-form {
                flex-wrap: wrap;
            }
        }
    </style>

    <div class="settings-container">

        {{-- Alerts --}}
        @if($errors->any())
            <div class="settings-alert bg-red-50 border border-red-200 text-red-700">
                {{ $errors->first() }}
            </div>
        @endif

        @if(session('success'))
            <div class="settings-alert bg-green-50 border border-green-200 text-green-700">
                {{ session('success') }}
            </div>
        @endif

        {{-- Categories & Priorities --}}
        <div class="settings-grid">

            {{-- Categories --}}
            <section class="settings-card">
                <h3 class="settings-title">Categories</h3>
                <p class="settings-description">
                    Manage the categories available for support tickets.
                </p>

                <form method="POST" action="{{ route('admin.categories.store') }}" class="settings-add-form">
                    @csrf
                    <input name="name" required placeholder="Enter category name" class="settings-input">
                    <button class="settings-btn">+ Add</button>
                </form>

                @forelse($categories as $category)
                    <div class="settings-row">
                        <form method="POST" action="{{ route('admin.categories.update', $category) }}"
                              class="flex flex-wrap items-center gap-2">
                            @csrf
                            @method('PATCH')

                            <input name="name" value="{{ $category->name }}" required class="settings-input flex-1">

                            <input type="hidden" name="is_active" value="0">

                            <label class="flex items-center gap-1 text-xs text-gray-600 whitespace-nowrap" style="padding: 2px;">
                                <input type="checkbox" name="is_active" value="1"
                                    @checked($category->is_active)>
                                Active
                            </label>

                            <button class="settings-link" style="padding-left: 4px;">Save</button>
                        </form>

                        <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" class="mt-2">
                            @csrf
                            @method('DELETE')
                            <button class="settings-btn-danger">Delete unused category</button>
                        </form>
                    </div>
                @empty
                    <p class="text-sm text-gray-500 py-4">No categories available.</p>
                @endforelse
            </section>

            {{-- Priorities & SLA --}}
            <section class="settings-card">
                <h3 class="settings-title">Priorities & SLA Rules</h3>
                <p class="settings-description">
                    Configure ticket priority levels and their resolution targets.
                </p>

                <form method="POST" action="{{ route('admin.priorities.store') }}"
                      class="settings-add-form">
                    @csrf
                    <input name="name" required placeholder="Priority name" class="settings-input">
                    <input name="level" required type="number" min="1"
                           placeholder="Level" class="settings-input" style="max-width: 90px;">
                    <button class="settings-btn">+ Add</button>
                </form>

                @forelse($priorities as $priority)
                    <div class="settings-row">

                        <form method="POST" action="{{ route('admin.priorities.update', $priority) }}">
                            @csrf
                            @method('PATCH')

                            <div class="priority-form">
                                <input name="name" value="{{ $priority->name }}" required
                                       class="settings-input">

                                <input name="level" type="number" min="1"
                                       value="{{ $priority->level }}" required
                                       class="settings-input">

                                <input name="color" value="{{ $priority->color }}"
                                       placeholder="Color" class="settings-input">

                                <select name="is_active" class="settings-input">
                                    <option value="1" @selected($priority->is_active)>Active</option>
                                    <option value="0" @selected(!$priority->is_active)>Inactive</option>
                                </select>
                            </div>

                            <button class="settings-link mt-2">Save priority</button>
                        </form>

                        {{-- SLA Rule --}}
                        <form method="POST" action="{{ route('admin.sla-rules.save', $priority) }}"
                              class="border-t border-gray-100 mt-3 pt-3">
                            @csrf
                            @method('PUT')

                            <div class="sla-form">
                                <span class="text-xs font-semibold text-gray-600">SLA Rule</span>

                                <input name="resolution_minutes" type="number" min="1" required
                                       value="{{ $priority->slaRule?->resolution_minutes ?? '' }}"
                                       placeholder="Minutes" class="settings-input">

                                <input name="warning_percent" type="number" min="1" max="100" required
                                       value="{{ $priority->slaRule?->warning_percent ?? 20 }}"
                                       placeholder="Warning %" class="settings-input">

                                <select name="is_active" class="settings-input">
                                    <option value="1" @selected($priority->slaRule?->is_active ?? true)>Active</option>
                                    <option value="0" @selected(!($priority->slaRule?->is_active ?? true))>Inactive</option>
                                </select>
                            </div>

                            <button class="settings-link mt-2">Save SLA rule</button>
                        </form>

                        <form method="POST" action="{{ route('admin.priorities.destroy', $priority) }}" class="mt-2">
                            @csrf
                            @method('DELETE')
                            <button class="settings-btn-danger">Delete unused priority</button>
                        </form>
                    </div>
                @empty
                    <p class="text-sm text-gray-500 py-4">No priorities available.</p>
                @endforelse
            </section>
        </div>

        {{-- Business Hours --}}
        <section class="settings-card mb-5">
            <h3 class="settings-title">Business Hours</h3>
            <p class="settings-description">
                Set your organization's working days and operating hours.
            </p>

            <form method="POST" action="{{ route('admin.business-hours.save') }}">
                @csrf
                @method('PUT')

                <div class="business-hours-grid">
                    @foreach(['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'] as $day => $name)
                        @php($hour = $businessHours->get($day))

                        <div class="day-card">
                            <div class="day-name">{{ $name }}</div>

                            <input type="hidden" name="hours[{{ $day }}][is_working_day]" value="0">

                            <label class="flex items-center gap-2 text-xs text-gray-600">
                                <input type="checkbox"
                                       name="hours[{{ $day }}][is_working_day]"
                                       value="1" @checked($hour?->is_working_day)>
                                Working day
                            </label>

                            <input type="time"
                                   name="hours[{{ $day }}][start_time]"
                                   value="{{ $hour?->start_time }}"
                                   class="settings-input">

                            <input type="time"
                                   name="hours[{{ $day }}][end_time]"
                                   value="{{ $hour?->end_time }}"
                                   class="settings-input">
                        </div>
                    @endforeach
                </div>

                <button class="settings-btn mt-4">Save business hours</button>
            </form>
        </section>

        {{-- Holidays --}}
        <section class="settings-card">
            <h3 class="settings-title">Holidays</h3>
            <p class="settings-description">
                Manage holidays when your support team is unavailable.
            </p>

            <form method="POST" action="{{ route('admin.holidays.store') }}"
                  class="settings-add-form">
                @csrf

                <input name="holiday_date" type="date" required class="settings-input" style="max-width: 180px;">

                <input name="name" placeholder="Holiday name" required class="settings-input">

                <button class="settings-btn">+ Add holiday</button>
            </form>

            @forelse($holidays as $holiday)
                <div class="holiday-row">
                    <div>
                        <div class="font-medium text-gray-800">{{ $holiday->name }}</div>
                        <div class="text-xs text-gray-500 mt-1">
                            {{ $holiday->holiday_date->format('d M Y') }}
                        </div>
                    </div>

                    <form method="POST" action="{{ route('admin.holidays.destroy', $holiday) }}">
                        @csrf
                        @method('DELETE')
                        <button class="settings-btn-danger">Delete</button>
                    </form>
                </div>
            @empty
                <p class="text-sm text-gray-500 py-4">No holidays added yet.</p>
            @endforelse
        </section>

    </div>
</x-app-layout>
