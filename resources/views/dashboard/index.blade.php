
@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">

    {{-- Dashboard Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">Dashboard</h2>
            <p class="text-muted mb-0">
                Welcome back, {{ auth()->user()->name }}!
            </p>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="row g-3 mb-4">

        {{-- Total Open --}}
        <div class="col-md-6 col-lg-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <p class="text-muted mb-2">Open Tickets</p>
                    <h3 class="fw-bold mb-0">{{ $totalOpen }}</h3>
                </div>
            </div>
        </div>

        {{-- Total Active --}}
        <div class="col-md-6 col-lg-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <p class="text-muted mb-2">Active Tickets</p>
                    <h3 class="fw-bold mb-0">{{ $totalActive }}</h3>
                </div>
            </div>
        </div>

        {{-- Total Resolved --}}
        <div class="col-md-6 col-lg-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <p class="text-muted mb-2">Resolved Tickets</p>
                    <h3 class="fw-bold mb-0">{{ $totalResolved }}</h3>
                </div>
            </div>
        </div>

        {{-- Unassigned: Admin and Agent only --}}
        @if (!auth()->user()->isCustomer())
            <div class="col-md-6 col-lg-3">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <p class="text-muted mb-2">Unassigned Tickets</p>
                        <h3 class="fw-bold mb-0">{{ $unassigned }}</h3>
                    </div>
                </div>
            </div>
        @endif

    </div>

    {{-- Admin and Agent Statistics --}}
    @if (!auth()->user()->isCustomer())
        <div class="row g-3 mb-4">

            {{-- Critical Tickets --}}
            <div class="col-md-6">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted mb-2">Critical Tickets</p>
                                <h3 class="fw-bold mb-0 text-danger">
                                    {{ $critical }}
                                </h3>
                            </div>
                            <span class="badge bg-danger">Critical</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- SLA Breached --}}
            <div class="col-md-6">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted mb-2">SLA Breached</p>
                                <h3 class="fw-bold mb-0 text-warning">
                                    {{ $breached }}
                                </h3>
                            </div>
                            <span class="badge bg-warning text-dark">Overdue</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    @endif

    {{-- Tickets by Priority --}}
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0">Tickets by Priority</h5>
        </div>

        <div class="card-body">

            @if ($ticketsByPriority->isEmpty())
                <p class="text-muted text-center mb-0">
                    No tickets available.
                </p>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Priority</th>
                                <th class="text-end">Total Tickets</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($ticketsByPriority as $item)
                                <tr>
                                    <td>
                                        @php
                                            $badgeClass = match (strtolower($item->name)) {
                                                'critical' => 'bg-danger',
                                                'high' => 'bg-warning text-dark',
                                                'medium' => 'bg-info text-dark',
                                                'low' => 'bg-success',
                                                default => 'bg-secondary',
                                            };
                                        @endphp

                                        <span class="badge {{ $badgeClass }}">
                                            {{ $item->name }}
                                        </span>
                                    </td>

                                    <td class="text-end fw-semibold">
                                        {{ $item->total }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>

                    </table>
                </div>
            @endif

        </div>
    </div>

</div>
@endsection
