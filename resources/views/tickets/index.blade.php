<x-app-layout>
    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Header --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
                <div>
                    <h2 class="text-2xl font-semibold text-gray-800">Support Tickets</h2>
                    <p class="text-sm text-gray-500 mt-1">Manage and track all support requests.</p>
                </div>

                @can('create', App\Models\Ticket::class)
                <a href="{{ route('tickets.create') }}"
                    class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-sm font-medium rounded-lg hover:bg-blue-700 transition" style="color:green">
                    + Create Ticket
                </a>
                @endcan
            </div>

            {{-- Search & Filter --}}
            <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-4 mb-5">
                <form method="GET" action="{{ route('tickets.index') }}"
                    class="flex flex-col sm:flex-row gap-3">

                    <div class="flex-1" style="margin-bottom: 5px;">
                        <input type="text"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Search ticket ID or subject..."
                            class="w-full border-gray-300 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div class="sm:w-64" style="margin-bottom: 5px;">
                        <select name="status"
                            class="w-full border-gray-300 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">All Statuses</option>
                            <option value="open" @selected(request('status')==='open' )>Open</option>
                            <option value="in_progress" @selected(request('status')==='in_progress' )>In Progress</option>
                            <option value="waiting_for_customer" @selected(request('status')==='waiting_for_customer' )>Waiting for Customer</option>
                            <option value="resolved" @selected(request('status')==='resolved' )>Resolved</option>
                            <option value="closed" @selected(request('status')==='closed' )>Closed</option>
                        </select>
                    </div>

                    <div class="sm:w-48" style="margin-bottom: 5px;">
                        <select name="priority_id" class="w-full border-gray-300 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">All priorities</option>
                            @foreach($priorities as $priority)
                            <option value="{{ $priority->id }}" @selected((string) request('priority_id') === (string) $priority->id)>{{ $priority->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="sm:w-48" style="margin-bottom: 5px;">
                        <select name="category_id" class="w-full border-gray-300 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">All categories</option>
                            @foreach($categories as $category)
                            <option value="{{ $category->id }}" @selected((string) request('category_id') === (string) $category->id)>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="sm:w-48" style="margin-bottom: 5px;">
                        <select name="sla_status" class="w-full border-gray-300 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">All SLA statuses</option>
                            <option value="within_sla" @selected(request('sla_status') === 'within_sla')>Within SLA</option>
                            <option value="approaching" @selected(request('sla_status') === 'approaching')>Approaching breach</option>
                            <option value="breached" @selected(request('sla_status') === 'breached')>Breached</option>
                        </select>
                    </div>

                    <div class="sm:w-40" style="margin-bottom: 5px;">
                        <input type="date" name="date_from" value="{{ request('date_from') }}" aria-label="Created from"
                            class="w-full border-gray-300 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div class="sm:w-40" style="margin-bottom: 5px;">
                        <input type="date" name="date_to" value="{{ request('date_to') }}" aria-label="Created to"
                            class="w-full border-gray-300 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    @if(auth()->user()->isAdmin())
                    <div class="sm:w-48" style="margin-bottom: 5px;">
                        <select name="assigned_to" class="w-full border-gray-300 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">All agents</option>
                            @foreach($agents as $agent)
                            <option value="{{ $agent->id }}" @selected((string) request('assigned_to') === (string) $agent->id)>{{ $agent->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="sm:w-48" style="margin-bottom: 5px;">
                        <select name="requester_id" class="w-full border-gray-300 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">All customers</option>
                            @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" @selected((string) request('requester_id') === (string) $customer->id)>{{ $customer->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                    <div class="flex gap-2">
                        <button type="submit"
                            class="px-5 py-2 bg-gray-800 text-white text-sm font-medium rounded-lg hover:bg-gray-700 transition" style="padding: 5px; margin-right:5px">
                            Search
                        </button>

                        @if(request()->hasAny(['search', 'status', 'priority_id', 'category_id', 'assigned_to', 'requester_id', 'date_from', 'date_to', 'sla_status']))
                        <a href="{{ route('tickets.index') }}"
                            class="px-4 py-2 border border-gray-300 text-gray-600 text-sm rounded-lg hover:bg-gray-50 transition">
                            Reset
                        </a>
                        @endif
                    </div>
                </form>
            </div>

            {{-- Tickets Table --}}


            <div class="bg-white border border-gray-200 rounded-xl shadow-sm mt-5 overflow-hidden">

                <div class="overflow-x-auto">
                    <table class="w-full min-w-[1050px] table-fixed divide-y divide-gray-200">

                        <thead class="bg-gray-50">
                            <tr>
                                <th class="w-[20%] px-3 py-3 text-left text-xs font-semibold text-gray-500 uppercase">
                                    Ticket ID
                                </th>
                                <th class="w-[20%] px-3 py-3 text-left text-xs font-semibold text-gray-500 uppercase">
                                    Subject
                                </th>
                                <th class="w-[20%] px-3 py-3 text-left text-xs font-semibold text-gray-500 uppercase">
                                    Customer
                                </th>
                                <th class="w-[20%] px-3 py-3 text-left text-xs font-semibold text-gray-500 uppercase">
                                    Agent
                                </th>
                                <th class="w-[20%] px-3 py-3 text-left text-xs font-semibold text-gray-500 uppercase">
                                    Priority
                                </th>
                                <th class="w-[20%] px-3 py-3 text-left text-xs font-semibold text-gray-500 uppercase">
                                    Status
                                </th>
                                <th class="w-[20%] px-3 py-3 text-left text-xs font-semibold text-gray-500 uppercase">
                                    Due At
                                </th>
                                <th class="w-[7%] px-3 py-3 text-center text-xs font-semibold text-gray-500 uppercase">
                                    Action
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100 bg-white">

                            @forelse($tickets as $ticket)

                            <tr class="hover:bg-gray-50 transition-colors">

                                {{-- Ticket ID --}}
                                <td class="px-3 py-4 text-sm font-semibold text-blue-600 break-words">
                                    {{ $ticket->ticket_number }}
                                </td>

                                {{-- Subject --}}
                                <td class="px-3 py-4 text-sm font-medium text-gray-800 break-words">
                                    {{ $ticket->subject }}
                                </td>

                                {{-- Customer --}}
                                <td class="px-3 py-4 text-sm text-gray-600 break-words">
                                    {{ $ticket->requester->name }}
                                </td>

                                {{-- Agent --}}
                                <td class="px-3 py-4 text-sm text-gray-600 break-words">
                                    {{ $ticket->assignee?->name ?? 'Unassigned' }}
                                </td>

                                {{-- Priority --}}
                                <td class="px-3 py-4">
                                    <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                                        {{ $ticket->priority->name }}
                                    </span>
                                </td>

                                {{-- Status --}}
                                <td class="px-3 py-4">
                                    @php
                                    $statusColors = [
                                    'open' => 'bg-blue-100 text-blue-700',
                                    'in_progress' => 'bg-yellow-100 text-yellow-700',
                                    'waiting_for_customer' => 'bg-orange-100 text-orange-700',
                                    'resolved' => 'bg-green-100 text-green-700',
                                    'closed' => 'bg-gray-100 text-gray-600',
                                    ];
                                    @endphp

                                    <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium {{ $statusColors[$ticket->status] ?? 'bg-gray-100 text-gray-600' }}">
                                        {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                                    </span>
                                </td>

                                {{-- Due At --}}
                                <td class="px-3 py-4 text-xs text-gray-500">
                                    {{ $ticket->due_at?->format('d M Y, h:i A') ?? '—' }}
                                </td>

                                {{-- Action --}}
                                <td class="px-3 py-4 text-center">
                                    <a href="{{ route('tickets.show', $ticket) }}"
                                        class="inline-flex items-center justify-center px-3 py-1.5 border border-blue-200 text-blue-600 text-xs font-medium rounded-md hover:bg-blue-50 transition">
                                        View
                                    </a>
                                </td>

                            </tr>

                            @empty

                            <tr>
                                <td colspan="8" class="px-4 py-12 text-center">
                                    <p class="text-sm font-medium text-gray-700">
                                        No tickets found
                                    </p>
                                    <p class="text-xs text-gray-400 mt-1">
                                        Try changing your search or filter.
                                    </p>
                                </td>
                            </tr>

                            @endforelse

                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if($tickets->hasPages())
                <div class="px-4 py-3 border-t border-gray-200">
                    {{ $tickets->withQueryString()->links() }}
                </div>
                @endif

            </div>


        </div>
    </div>
</x-app-layout>
