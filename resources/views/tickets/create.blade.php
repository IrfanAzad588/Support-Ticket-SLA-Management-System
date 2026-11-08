<style>
    .ticket-form-wrapper {
        max-width: 750px;
        margin: 0 auto;
    }

    .ticket-form-card {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 32px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }

    .ticket-form-group {
        margin-bottom: 22px;
    }

    .ticket-form-label {
        display: block;
        font-size: 14px;
        font-weight: 600;
        color: #374151;
        margin-bottom: 8px;
    }

    .ticket-form-input {
        width: 100%;
        padding: 11px 14px;
        border: 1px solid #d1d5db;
        border-radius: 7px;
        font-size: 14px;
        color: #374151;
        background: #fff;
        outline: none;
        transition: border-color 0.2s, box-shadow 0.2s;
    }

    .ticket-form-input:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
    }

    .ticket-form-input.is-invalid {
        border-color: #dc2626;
    }

    .ticket-form-row {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 20px;
    }

    .ticket-form-error {
        color: #dc2626;
        font-size: 13px;
        margin-top: 6px;
    }

    .ticket-submit-btn {
        background: #2563eb;
        color: white;
        padding: 11px 24px;
        border: none;
        border-radius: 7px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.2s;
    }

    .ticket-submit-btn:hover {
        background: #1d4ed8;
    }

    @media (max-width: 640px) {
        .ticket-form-card {
            padding: 20px;
        }

        .ticket-form-row {
            grid-template-columns: 1fr;
            gap: 0;
        }
    }
</style>

<x-app-layout>

    {{-- Page Header --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Create Support Ticket
        </h2>
    </x-slot>

    <div class="py-10 px-4">

        <div class="ticket-form-wrapper">

            <div class="ticket-form-card">

                <div class="mb-7">
                    <h3 class="text-xl font-bold text-gray-800">
                        Submit a New Ticket
                    </h3>

                    <p class="text-sm text-gray-500 mt-2">
                        Please provide the details below to create your support ticket.
                    </p>
                </div>

                <form method="POST" action="{{ route('tickets.store') }}">
                    @csrf

                    {{-- Subject --}}
                    <div class="ticket-form-group">
                        <label class="ticket-form-label">
                            Subject <span class="text-red-500">*</span>
                        </label>

                        <input
                            type="text"
                            name="subject"
                            value="{{ old('subject') }}"
                            placeholder="Enter ticket subject"
                            class="ticket-form-input @error('subject') is-invalid @enderror"
                            required>

                        @error('subject')
                        <p class="ticket-form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Description --}}
                    <div class="ticket-form-group">
                        <label class="ticket-form-label">
                            Description <span class="text-red-500">*</span>
                        </label>

                        <textarea
                            name="description"
                            rows="5"
                            placeholder="Describe your issue in detail..."
                            class="ticket-form-input @error('description') is-invalid @enderror"
                            required>{{ old('description') }}</textarea>

                        @error('description')
                        <p class="ticket-form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Category and Priority --}}
                    <div class="ticket-form-row">

                        {{-- Category --}}
                        <div class="ticket-form-group">
                            <label class="ticket-form-label">
                                Category <span class="text-red-500">*</span>
                            </label>

                            <select
                                name="category_id"
                                class="ticket-form-input @error('category_id') is-invalid @enderror"
                                required>
                                <option value="">Select Category</option>

                                @foreach($categories as $category)
                                <option
                                    value="{{ $category->id }}"
                                    {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                                @endforeach
                            </select>

                            @error('category_id')
                            <p class="ticket-form-error">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Priority --}}
                        <div class="ticket-form-group">
                            <label class="ticket-form-label">
                                Priority <span class="text-red-500">*</span>
                            </label>

                            <select
                                name="priority_id"
                                class="ticket-form-input @error('priority_id') is-invalid @enderror"
                                required>
                                <option value="">Select Priority</option>

                                @foreach($priorities as $priority)
                                <option
                                    value="{{ $priority->id }}"
                                    {{ old('priority_id') == $priority->id ? 'selected' : '' }}>
                                    {{ $priority->name }}
                                </option>
                                @endforeach
                            </select>

                            @error('priority_id')
                            <p class="ticket-form-error">{{ $message }}</p>
                            @enderror
                        </div>

                    </div>

                    {{-- Submit Button --}}
                    <div class="flex justify-end pt-2">
                        <button type="submit" class="ticket-submit-btn">
                            Submit Ticket
                        </button>
                    </div>

                </form>

            </div>

        </div>

    </div>

</x-app-layout>