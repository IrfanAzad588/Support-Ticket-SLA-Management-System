<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Priority;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Ticket>
 */
class TicketFactory extends Factory
{
    protected $model = Ticket::class;

    public function definition(): array
    {
        return [
            'ticket_number' => 'TKT-'.now()->format('Y').'-'.fake()->unique()->numerify('######'),
            'subject' => fake()->sentence(4),
            'description' => fake()->paragraph(),
            'category_id' => fn () => Category::firstOrCreate(['name' => 'Factory Category'])->id,
            'priority_id' => fn () => Priority::firstOrCreate(
                ['name' => 'Factory Priority'],
                ['level' => 99, 'is_active' => true]
            )->id,
            'requester_id' => fn () => User::factory()->create(['role' => 'customer'])->id,
            'status' => Ticket::STATUS_OPEN,
            'due_at' => now()->addDay(),
        ];
    }
}
