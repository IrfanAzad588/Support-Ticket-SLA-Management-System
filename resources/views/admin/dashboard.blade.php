<x-app-layout>

    <style>
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 16px;
        }

        @media (max-width: 1024px) {
            .stats-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 640px) {
            .stats-grid {
                grid-template-columns: repeat(1, minmax(0, 1fr));
            }
        }
    </style>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if(session('success'))<div class="rounded-lg bg-green-50 border border-green-200 px-4 py-3 text-green-800">{{ session('success') }}</div>@endif



            <section>
                <h3 class="text-lg font-semibold mb-3">System Overview</h3>
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-3">
                    <a href="#" class="bg-blue-600 px-3 py-1 rounded-md hover:bg-blue-700 text-white text-sm font-medium">View All</a>
                </div>
                <div class="stats-grid">
                    @foreach([
                    'Total Tickets' => $stats['total_tickets'],
                    'Open Tickets' => $stats['open_tickets'],
                    'Unassigned Tickets' => $stats['unassigned_tickets'],
                    'Critical Tickets' => $stats['critical_tickets'],
                    'SLA Breached' => $stats['sla_breached'],
                    'SLA Approaching' => $stats['sla_approaching'],
                    'Resolved Tickets' => $stats['resolved_tickets'],
                    'Total Agents' => $stats['total_agents'],
                    ] as $label => $value)

                    <div class="bg-white p-5 rounded-lg shadow hover:shadow-lg transition-shadow" style="padding: 10px;">

                        <p class="text-sm text-gray-500">
                            {{ $label }}
                        </p>

                        <p class="text-2xl font-bold mt-2">
                            {{ $value }}
                        </p>
                    </div>

                    @endforeach
                </div>
            </section>

            <div class="grid lg:grid-cols-3 gap-6 mt-6">
                <div class="bg-white rounded-lg shadow mt-8 p-5" style="padding: 10px;">

                    <h3 class="text-lg font-semibold mb-4">
                        Recent Tickets
                    </h3>

                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">

                            <thead>
                                <tr class="border-b text-left text-gray-600">
                                    <th class="py-3 px-2">Ticket ID</th>
                                    <th class="py-3 px-2">Subject</th>
                                    <th class="py-3 px-2">Customer</th>
                                    <th class="py-3 px-2">Agent</th>
                                    <th class="py-3 px-2">Status</th>
                                    <th class="py-3 px-2">Created</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse($latestTickets as $ticket)
                                <tr class="border-b hover:bg-gray-50">

                                    <td class="py-3 px-2">
                                        #{{ $ticket->id }}
                                    </td>

                                    <td class="py-3 px-2">
                                        {{ $ticket->subject }}
                                    </td>

                                    <td class="py-3 px-2">
                                        {{ $ticket->requester?->name ?? 'N/A' }}
                                    </td>

                                    <td class="py-3 px-2">
                                        {{ $ticket->assignee?->name ?? 'Unassigned' }}
                                    </td>

                                    <td class="py-3 px-2">
                                        {{ ucwords(str_replace('_', ' ', $ticket->status)) }}
                                    </td>

                                    <td class="py-3 px-2">
                                        {{ $ticket->created_at->format('d M Y') }}
                                    </td>

                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="py-5 text-center text-gray-500">
                                        No tickets found.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>

                        </table>
                    </div>

                </div>
                <section class="bg-white border rounded-lg p-5" style="padding: 10px;">
                    <h3 class="font-semibold mb-4">CSV import</h3>
                    <form method="POST" action="{{ route('ticket-imports.store') }}" enctype="multipart/form-data" class="space-y-3">@csrf<input name="file" type="file" accept=".csv,.txt" required class="block text-sm w-full"><button class="px-3 py-2 rounded bg-blue-600 text-sm" style="color:green">Import tickets</button></form>
                    <p class="mt-3 text-xs text-gray-500">Imports run in the background and report row errors.</p>
                    <div class="mt-4 space-y-2">@foreach($recentImports as $import)<a class="block text-sm text-blue-600" href="{{ route('ticket-imports.show', $import) }}">{{ $import->original_file_name }} <span class="text-gray-500">({{ $import->status }})</span></a>@endforeach</div>
                </section>
            </div>

            <div class="grid lg:grid-cols-2 gap-6 mt-6">
                <section class="bg-white border rounded-lg p-5" style="padding: 10px;">
                    <div class="flex justify-between mb-4">
                        <h3 class="font-semibold">Agent workload</h3><a class="text-sm text-blue-600" href="{{ route('admin.users.index') }}">Manage agents</a>
                    </div>
                    <div class="space-y-3">@forelse($agentWorkloads as $agent)<div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium">{{ $agent->name }}</p>
                                <p class="text-xs text-gray-500">{{ $agent->is_active ? 'Active' : 'Inactive' }}</p>
                            </div><span class="rounded-full bg-blue-50 text-blue-700 px-3 py-1 text-sm">{{ $agent->active_ticket_count }} active</span>
                        </div>@empty<p class="text-sm text-gray-500">No agents yet.</p>@endforelse</div>
                </section>
                <section class="bg-white border rounded-lg p-5" style="padding: 10px;">
                    <h3 class="font-semibold mb-4">SLA escalations</h3>
                    <div class="space-y-3">@forelse($recentEscalations as $escalation)<a href="{{ route('tickets.show', $escalation->ticket) }}" class="block border-l-4 {{ $escalation->type === 'breached' ? 'border-red-500' : 'border-amber-400' }} pl-3">
                            <p class="text-sm font-medium">{{ $escalation->ticket?->ticket_number }} — {{ ucfirst($escalation->type) }}</p>
                            <p class="text-xs text-gray-500">{{ $escalation->triggered_at?->diffForHumans() }}</p>
                        </a>@empty<p class="text-sm text-gray-500">No escalations recorded.</p>@endforelse</div>
                </section>
            </div>

            <div class="grid lg:grid-cols-2 gap-6 mt-6">
                <section class="bg-white border rounded-lg p-5" style="padding: 10px;">
                    <h3 class="font-semibold mb-4">Activity history</h3>
                    <div class="space-y-3">@forelse($recentActivities as $activity)<div class="text-sm"><a class="text-blue-600" href="{{ route('tickets.show', $activity->ticket) }}">{{ $activity->ticket?->ticket_number }}</a> {{ $activity->description }}
                            <p class="text-xs text-gray-500">{{ $activity->causer?->name ?? 'System' }} · {{ $activity->created_at->diffForHumans() }}</p>
                        </div>@empty<p class="text-sm text-gray-500">No activity yet.</p>@endforelse</div>
                </section>
                <section class="bg-white border rounded-lg p-5" style="padding: 10px;">
                    <h3 class="font-semibold mb-4">Notifications</h3>
                    <div class="space-y-3">@forelse($notifications as $notification)<div class="text-sm">
                            <p>{{ data_get($notification->data, 'message', 'System notification') }}</p>
                            <p class="text-xs text-gray-500">{{ $notification->created_at->diffForHumans() }}</p>
                        </div>@empty<p class="text-sm text-gray-500">No notifications.</p>@endforelse</div>
                </section>
            </div>

            <section class="bg-white border rounded-lg p-5 mt-6" style="padding: 10px;">
                <h3 class="font-semibold">Administration</h3>
                <p class="mt-1 text-sm text-gray-500">Manage categories, priorities, SLA rules, business hours, and holidays from one settings page. Ticket pages provide assignment, conversations, activity history, and authorized attachment access.</p><a class="inline-block mt-3 text-sm font-medium text-blue-600" href="{{ route('admin.settings.index') }}">Open system settings →</a>
            </section>
        </div>
    </div>
</x-app-layout>