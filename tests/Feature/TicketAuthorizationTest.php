<?php

namespace Tests\Feature;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_cannot_view_another_customers_ticket(): void
    {
        $customerA = User::factory()->create([
            'role' => 'customer',
        ]);

        $customerB = User::factory()->create([
            'role' => 'customer',
        ]);

        $ticket = Ticket::factory()->create([
            'requester_id' => $customerA->id,
        ]);

        $response = $this->actingAs($customerB)
            ->get(route('tickets.show', $ticket));

        $response->assertForbidden();
    }
}
