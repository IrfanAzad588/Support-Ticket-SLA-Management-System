
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">
            My Support Dashboard
        </h2>
    </x-slot>

    <style>
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(5, minmax(0, 1fr));
            gap: 14px;
        }

        .content-grid {
            display: grid;
            grid-template-columns: minmax(0, 2fr) minmax(280px, 1fr);
            gap: 20px;
        }

        .dashboard-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 20px;
        }

        .stat-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 18px;
            transition: box-shadow .2s;
        }

        .stat-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, .06);
        }

        .stat-label {
            color: #6b7280;
            font-size: 13px;
        }

        .stat-value {
            margin-top: 8px;
            font-size: 27px;
            font-weight: 700;
            color: #111827;
        }

        .dashboard-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
            white-space: nowrap;
        }

        .dashboard-table th {
            background: #f9fafb;
            color: #6b7280;
            font-weight: 600;
            text-align: left;
            padding: 12px;
        }

        .dashboard-table td {
            padding: 13px 12px;
            border-bottom: 1px solid #f1f1f1;
            color: #374151;
        }

        .dashboard-table tbody tr:hover {
            background: #fafafa;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 9px;
            border-radius: 20px;
            background: #f3f4f6;
            color: #374151;
            font-size: 11px;
            font-weight: 600;
            text-transform: capitalize;
        }

        .notification-item {
            display: block;
            padding: 13px 0;
            border-bottom: 1px solid #f1f1f1;
        }

        .notification-item:last-child {
            border-bottom: 0;
        }

        .notification-item:hover p:first-child {
            color: #2563eb;
        }

        .primary-btn {
            display: inline-block;
            background: #2563eb;
            color: #fff;
            padding: 10px 16px;
            border-radius: 7px;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
        }

        .primary-btn:hover {
            background: #1d4ed8;
        }

        .secondary-btn {
            display: inline-block;
            background: #fff;
            color: #374151;
            border: 1px solid #d1d5db;
            padding: 10px 16px;
            border-radius: 7px;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
        }

        .secondary-btn:hover {
            background: #f9fafb;
        }

        @media (max-width: 1024px) {
            .dashboard-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }

            .content-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 640px) {
            .dashboard-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 10px;
            }

            .stat-card {
                padding: 14px;
            }

            .stat-value {
                font-size: 23px;
            }
        }

        @media (max-width: 380px) {
            .dashboard-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div style="background: #f9fafb; min-height: 100vh; padding: 28px 0;">
        <div style="max-width: 1280px; margin: auto; padding: 0 20px;">

            {{-- Success Message --}}
            @if(session('success'))
                <div class="mb-5 rounded-lg bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-800">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Page Actions --}}
            <div class="flex flex-wrap items-center justify-between gap-3 mb-7">
                <div>
                    <h3 class="text-lg font-bold text-gray-800">Dashboard Overview</h3>
                    <p class="text-sm text-gray-500 mt-1">Track and manage your support requests.</p>
                </div>

                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('tickets.index') }}" class="secondary-btn">
                        My Tickets
                    </a>

                    <a href="{{ route('tickets.create') }}" class="primary-btn">
                        + Create Ticket
                    </a>
                </div>
            </div>

            {{-- Ticket Statistics --}}
            <section class="mb-7" style="margin-top: 10px;">
                <h3 class="text-sm font-semibold text-gray-700 mb-3">My Ticket Overview</h3>

                <div class="dashboard-grid">
                    @foreach([
                        'Total Tickets' => ['total_tickets', '#2563eb'],
                        'Open' => ['open_tickets', '#d97706'],
                        'In Progress' => ['in_progress', '#7c3aed'],
                        'Waiting for Me' => ['waiting_for_customer', '#0891b2'],
                        'Resolved' => ['resolved_tickets', '#059669'],
                    ] as $label => $item)
                        <div class="stat-card">
                            <div style="display:flex; align-items:center; justify-content:space-between; gap:8px;">
                                <p class="stat-label">{{ $label }}</p>
                                <span style="width:9px;height:9px;border-radius:50%;background:{{ $item[1] }};"></span>
                            </div>

                            <p class="stat-value">
                                {{ $stats[$item[0]] }}
                            </p>
                        </div>
                    @endforeach
                </div>
            </section>

            {{-- Recent Tickets and Notifications --}}
            <div class="content-grid mb-7" style="margin-top: 10px;">

                {{-- Recent Tickets --}}
                <section class="dashboard-card" style="margin-top: 10px;">
                    <div class="flex items-center justify-between gap-3 mb-5">
                        <div>
                            <h3 class="font-bold text-gray-800">Recent Tickets</h3>
                            <p class="text-xs text-gray-500 mt-1">Your latest support requests</p>
                        </div>

                        <a href="{{ route('tickets.index') }}"
                           class="text-sm font-semibold text-blue-600 hover:text-blue-800">
                            View All →
                        </a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="dashboard-table">
                            <thead>
                                <tr>
                                    <th>Ticket</th>
                                    <th>Status</th>
                                    <th>Priority</th>
                                    <th>Support Agent</th>
                                    <th>Last Updated</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse($latestTickets as $ticket)
                                    <tr>
                                        <td>
                                            <a href="{{ route('tickets.show', $ticket) }}"
                                               class="font-semibold text-blue-600 hover:text-blue-800">
                                                {{ $ticket->ticket_number }}
                                            </a>
                                            <div class="text-xs text-gray-500 mt-1"
                                                 style="max-width:180px; overflow:hidden; text-overflow:ellipsis;">
                                                {{ $ticket->subject }}
                                            </div>
                                        </td>

                                        <td>
                                            <span class="status-badge">
                                                {{ str_replace('_', ' ', $ticket->status) }}
                                            </span>
                                        </td>

                                        <td>{{ $ticket->priority?->name ?? '—' }}</td>

                                        <td>{{ $ticket->assignee?->name ?? 'Unassigned' }}</td>

                                        <td>{{ $ticket->updated_at->diffForHumans() }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-8 text-gray-500">
                                            <div class="text-2xl mb-2">📭</div>
                                            <p class="font-medium">No tickets yet</p>
                                            <p class="text-xs mt-1">Create a ticket if you need support.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>

                {{-- Notifications --}}
                <section class="dashboard-card">
                    <div class="flex items-center justify-between mb-3">
                        <div>
                            <h3 class="font-bold text-gray-800">Notifications</h3>
                            <p class="text-xs text-gray-500 mt-1">Recent updates on your tickets</p>
                        </div>

                        <span class="text-xs bg-blue-50 text-blue-700 px-2 py-1 rounded-full">
                            {{ $notifications->count() }} updates
                        </span>
                    </div>

                    <div>
                        @forelse($notifications as $notification)
                            <a href="{{ route('tickets.show', data_get($notification->data, 'ticket_id')) }}"
                               class="notification-item">
                                <div class="flex gap-3">
                                    <div style="width:32px;height:32px;min-width:32px;border-radius:50%;background:#eff6ff;color:#2563eb;display:flex;align-items:center;justify-content:center;">
                                        <span style="font-size:14px;">●</span>
                                    </div>

                                    <div>
                                        <p class="text-sm font-medium text-gray-800">
                                            {{ data_get($notification->data, 'message', 'Ticket notification') }}
                                        </p>

                                        <p class="text-xs text-gray-400 mt-1">
                                            {{ $notification->created_at->diffForHumans() }}
                                        </p>
                                    </div>
                                </div>
                            </a>
                        @empty
                            <div class="text-center py-10">
                                <div class="text-2xl mb-2">🔔</div>
                                <p class="text-sm font-medium text-gray-600">No notifications</p>
                                <p class="text-xs text-gray-400 mt-1">
                                    New replies and ticket updates will appear here.
                                </p>
                            </div>
                        @endforelse
                    </div>
                </section>
            </div>

            {{-- Reopen Resolved Ticket Information --}}
            <section class="dashboard-card" style="background:#eff6ff; border-color:#dbeafe;">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <h3 class="font-bold text-gray-800">Need a resolved ticket reopened?</h3>

                        <p class="text-sm text-gray-600 mt-1">
                            Open the resolved ticket and choose “Request ticket reopening.”
                            Support will review your request before changing its status.
                        </p>
                    </div>

                    <a href="{{ route('tickets.index', ['status' => 'resolved']) }}"
                       class="secondary-btn"
                       style="background:#fff; border-color:#bfdbfe; color:#1d4ed8;">
                        View Resolved Tickets →
                    </a>
                </div>
            </section>

        </div>
    </div>
</x-app-layout>
