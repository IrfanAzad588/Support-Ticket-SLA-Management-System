<x-app-layout>
    <x-slot name="header">
        <div class="agent-header">
            <h2 class="font-semibold text-xl text-gray-800">
                Agent Dashboard
            </h2>

            <nav class="agent-nav">
                <a href="{{ route('tickets.index') }}" class="active">
                    My & Available Tickets
                </a>
                <a href="{{ route('tickets.index', ['status' => 'open']) }}">
                    Open Tickets
                </a>
                <a href="{{ route('tickets.index', ['status' => 'in_progress']) }}">
                    In Progress
                </a>
            </nav>
        </div>
    </x-slot>

    <style>
        .agent-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
        }

        .agent-nav {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .agent-nav a {
            padding: 8px 14px;
            border: 1px solid #e5e7eb;
            border-radius: 7px;
            color: #4b5563;
            font-size: 13px;
            font-weight: 500;
            text-decoration: none;
            transition: 0.2s;
        }

        .agent-nav a:hover,
        .agent-nav a.active {
            background: #2563eb;
            border-color: #2563eb;
            color: white;
        }

        .dashboard-grid {
            display: grid;
            gap: 20px;
        }

        .stats-grid {
            grid-template-columns: repeat(7, minmax(0, 1fr));
        }

        .content-grid {
            grid-template-columns: 2fr 1fr;
        }

        .bottom-grid {
            grid-template-columns: 1fr 1fr;
        }

        .activity-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .dashboard-card {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 20px;
        }

        .stat-card {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 9px;
            padding: 16px;
        }

        .stat-label {
            color: #6b7280;
            font-size: 12px;
        }

        .stat-value {
            color: #111827;
            font-size: 25px;
            font-weight: 700;
            margin-top: 5px;
        }

        .section-title {
            font-size: 16px;
            font-weight: 600;
            color: #111827;
            margin-bottom: 16px;
        }

        .dashboard-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
            text-align: left;
        }

        .dashboard-table th {
            color: #6b7280;
            font-weight: 500;
            background: #f9fafb;
        }

        .dashboard-table th,
        .dashboard-table td {
            padding: 12px 10px;
            border-bottom: 1px solid #f1f5f9;
        }

        .dashboard-table tr:last-child td {
            border-bottom: none;
        }

        .ticket-link {
            color: #2563eb;
            font-weight: 600;
            text-decoration: none;
        }

        .ticket-link:hover {
            text-decoration: underline;
        }

        .accept-btn {
            background: #2563eb;
            color: white;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 500;
            border: none;
            cursor: pointer;
        }

        .accept-btn:hover {
            background: #1d4ed8;
        }

        .empty-state {
            color: #9ca3af;
            font-size: 13px;
            text-align: center;
            padding: 24px 10px;
        }

        @media (max-width: 1200px) {
            .stats-grid {
                grid-template-columns: repeat(4, minmax(0, 1fr));
            }
        }

        @media (max-width: 900px) {

            .content-grid,
            .bottom-grid {
                grid-template-columns: 1fr;
            }

            .activity-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 640px) {
            .agent-header {
                align-items: flex-start;
            }

            .agent-nav {
                width: 100%;
            }

            .agent-nav a {
                flex: 1;
                text-align: center;
            }

            .stats-grid,
            .activity-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .dashboard-card {
                padding: 14px;
            }
        }

        @media (max-width: 400px) {

            .stats-grid,
            .activity-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
            <div class="rounded-lg bg-green-50 border border-green-200 px-4 py-3 text-green-800 text-sm">
                {{ session('success') }}
            </div>
            @endif

            {{-- My Workload --}}
            <section>
                <h3 class="section-title">My Workload</h3>

                <div class="dashboard-grid stats-grid">
                    @foreach([
                    'Assigned' => 'total_tickets',
                    'Open' => 'open_tickets',
                    'In Progress' => 'in_progress',
                    'Waiting' => 'waiting_for_customer',
                    'Resolved' => 'resolved_tickets',
                    'Critical' => 'critical_tickets',
                    'SLA Breached' => 'sla_breached'
                    ] as $label => $key)
                    <div class="stat-card">
                        <p class="stat-label">{{ $label }}</p>
                        <p class="stat-value">{{ $stats[$key] }}</p>
                    </div>
                    @endforeach
                </div>
            </section>

            {{-- Recent Tickets and SLA --}}
            <div class="dashboard-grid content-grid">

                <section class="dashboard-card">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="section-title !mb-0">My Recent Tickets</h3>
                        <a href="{{ route('tickets.index') }}" class="ticket-link text-sm">
                            View All
                        </a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="dashboard-table">
                            <thead>
                                <tr>
                                    <th>Ticket</th>
                                    <th>Customer</th>
                                    <th>Priority</th>
                                    <th>Status</th>
                                    <th>SLA Due</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($latestTickets as $ticket)
                                <tr>
                                    <td>
                                        <a href="{{ route('tickets.show', $ticket) }}" class="ticket-link">
                                            {{ $ticket->ticket_number }}
                                        </a>
                                        <div class="text-xs text-gray-500 mt-1">
                                            {{ $ticket->subject }}
                                        </div>
                                    </td>
                                    <td>{{ $ticket->requester?->name }}</td>
                                    <td>{{ $ticket->priority?->name }}</td>
                                    <td class="capitalize">
                                        {{ str_replace('_', ' ', $ticket->status) }}
                                    </td>
                                    <td class="{{ $ticket->due_at?->isPast() ? 'text-red-600 font-semibold' : 'text-gray-600' }}">
                                        {{ $ticket->due_at?->format('d M, h:i A') ?? '—' }}
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="empty-state">
                                        No assigned tickets.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>

                {{-- SLA Watchlist --}}
                <section class="dashboard-card">
                    <h3 class="section-title">SLA Watchlist</h3>

                    <div class="space-y-4">
                        @forelse($slaWatchlist as $ticket)
                        <a href="{{ route('tickets.show', $ticket) }}"
                            class="block border-l-4 {{ $ticket->due_at->isPast() ? 'border-red-500' : 'border-amber-400' }} pl-3 py-1">
                            <p class="text-sm font-semibold text-gray-800">
                                {{ $ticket->ticket_number }}
                            </p>
                            <p class="text-xs text-gray-500 mt-1">
                                {{ $ticket->due_at->diffForHumans() }}
                            </p>
                        </a>
                        @empty
                        <p class="empty-state">
                            No approaching SLA deadlines.
                        </p>
                        @endforelse
                    </div>
                </section>
            </div>

            {{-- Available Tickets and Notifications --}}
            <div class="dashboard-grid bottom-grid">

                <section class="dashboard-card">
                    <h3 class="section-title">Available Tickets</h3>

                    <div class="space-y-4">
                        @forelse($availableTickets as $ticket)
                        <div class="flex justify-between items-center gap-3 border-b border-gray-100 pb-3">
                            <div class="min-w-0">
                                <a href="{{ route('tickets.show', $ticket) }}" class="ticket-link text-sm">
                                    {{ $ticket->ticket_number }} — {{ $ticket->subject }}
                                </a>
                                <p class="text-xs text-gray-500 mt-1">
                                    {{ $ticket->requester?->name }}
                                    · {{ $ticket->priority?->name }}
                                </p>
                            </div>

                            <form method="POST" action="{{ route('tickets.accept', $ticket) }}">
                                @csrf
                                <button type="submit" class="accept-btn">
                                    Accept
                                </button>
                            </form>
                        </div>
                        @empty
                        <p class="empty-state">
                            No unassigned tickets available.
                        </p>
                        @endforelse
                    </div>
                </section>

                {{-- Notifications --}}
                <section class="dashboard-card">
                    <h3 class="section-title">Notifications</h3>

                    <div class="space-y-4">
                        @forelse($notifications as $notification)
                        <div class="border-b border-gray-100 pb-3">
                            <p class="text-sm text-gray-700">
                                {{ data_get($notification->data, 'message', 'Ticket notification') }}
                            </p>
                            <p class="text-xs text-gray-400 mt-1">
                                {{ $notification->created_at->diffForHumans() }}
                            </p>
                        </div>
                        @empty
                        <p class="empty-state">
                            No notifications.
                        </p>
                        @endforelse
                    </div>
                </section>
            </div>

            {{-- Recent Activity --}}
            <section class="dashboard-card">
                <h3 class="section-title">Recent Activity</h3>

                <div class="dashboard-grid activity-grid">
                    @forelse($recentActivities as $activity)
                    <div class="border border-gray-100 rounded-lg p-3">
                        <a href="{{ route('tickets.show', $activity->ticket) }}" class="ticket-link text-sm">
                            {{ $activity->ticket?->ticket_number }}
                        </a>
                        <p class="text-sm text-gray-700 mt-1">
                            {{ $activity->description }}
                        </p>
                        <p class="text-xs text-gray-400 mt-2">
                            {{ $activity->causer?->name ?? 'System' }}
                            · {{ $activity->created_at->diffForHumans() }}
                        </p>
                    </div>
                    @empty
                    <p class="empty-state">
                        No ticket activity yet.
                    </p>
                    @endforelse
                </div>
            </section>

        </div>
    </div>
</x-app-layout>