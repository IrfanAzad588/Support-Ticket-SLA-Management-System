<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Ticket;
use App\Models\Category;
use App\Models\Priority;
use App\Models\SlaRule;
use App\Models\BusinessHour;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_create_ticket(): void
    {
        $this->seed();

        $customer = User::factory()->create([
            'role' => 'customer',
        ]);

        $category = Category::first();
        $priority = Priority::first();

        $response = $this->actingAs($customer)
            ->post(route('tickets.store'), [
                'subject' => 'Login problem',
                'description' => 'Unable to login.',
                'category_id' => $category->id,
                'priority_id' => $priority->id,
            ]);

        $response->assertRedirect();

        $ticket = Ticket::first();

        $this->assertNotNull($ticket);
        $this->assertEquals($customer->id, $ticket->requester_id);
        $this->assertStringStartsWith('TKT-', $ticket->ticket_number);
        $this->assertNotNull($ticket->due_at);
    }

    public function test_admin_can_create_ticket_and_is_redirected_to_ticket_list(): void
    {
        $this->seed();

        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)
            ->post(route('tickets.store'), [
                'subject' => 'Admin-created ticket',
                'description' => 'Created by an administrator.',
                'category_id' => Category::first()->id,
                'priority_id' => Priority::first()->id,
            ]);

        $response->assertRedirect(route('tickets.index'));

        $this->assertDatabaseHas('tickets', [
            'requester_id' => $admin->id,
            'subject' => 'Admin-created ticket',
        ]);
    }
}
