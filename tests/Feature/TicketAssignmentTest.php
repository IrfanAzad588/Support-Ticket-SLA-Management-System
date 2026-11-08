<?php

namespace Tests\Feature;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketAssignmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_agent_can_accept_an_unassigned_ticket(): void
    {
        $agent = User::factory()->create(['role' => 'agent', 'is_active' => true]);
        $ticket = Ticket::factory()->create();

        $response = $this->actingAs($agent)->post(route('tickets.accept', $ticket));

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'assigned_to' => $agent->id,
            'status' => Ticket::STATUS_IN_PROGRESS,
        ]);
    }
}
