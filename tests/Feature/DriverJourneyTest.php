<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DriverJourneyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Setup any necessary state if needed
    }

    public function test_driver_can_transition_through_journey_statuses(): void
    {
        $ambulance = \App\Models\Ambulance::factory()->create([
            'username' => 'amb01',
            'password' => bcrypt('password'),
            'status' => 'on_duty'
        ]);

        $driver = \App\Models\Driver::factory()->create(['status' => 'on_duty']);

        $dispatch = \App\Models\Dispatch::create([
            'ambulance_id' => $ambulance->id,
            'driver_id' => $driver->id,
            'patient_name' => 'John Doe',
            'patient_condition' => 'Critical',
            'pickup_address' => 'Home',
            'destination' => 'Hospital',
            'status' => 'assigned'
        ]);

        $this->actingAs($ambulance, 'ambulance');

        $flow = [
            'assigned' => 'enroute_pickup',
            'enroute_pickup' => 'on_scene',
            'on_scene' => 'enroute_destination',
            'enroute_destination' => 'arrived_destination',
            'arrived_destination' => 'completed',
        ];

        foreach ($flow as $current => $next) {
            $response = $this->post(route('driver.dispatches.update-status', $dispatch->id));
            
            $response->assertStatus(200);
            $response->assertJson(['success' => true, 'new_status' => $next]);
            
            $dispatch->refresh();
            $this->assertEquals($next, $dispatch->status);
        }

        // Verify ambulance and driver are freed up after completion
        $ambulance->refresh();
        $driver->refresh();
        $this->assertEquals('ready', $ambulance->status);
        $this->assertEquals('available', $driver->status);
    }
}
