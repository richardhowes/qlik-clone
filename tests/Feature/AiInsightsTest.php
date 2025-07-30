<?php

namespace Tests\Feature;

use App\Models\DataSource;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AiInsightsTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected DataSource $dataSource;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $this->dataSource = DataSource::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'active',
            'type' => 'mysql',
            'connection_config' => encrypt(json_encode([
                'host' => 'localhost',
                'database' => 'test',
                'username' => 'root',
                'password' => '',
            ])),
        ]);
    }

    public function test_insights_page_loads_with_data_sources()
    {
        $response = $this->actingAs($this->user)
            ->get('/insights');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Insights')
            ->has('dataSources', 1)
            ->where('dataSources.0.id', $this->dataSource->id)
        );
    }

    public function test_can_ask_natural_language_question()
    {
        $response = $this->actingAs($this->user)
            ->postJson('/insights/ask', [
                'question' => 'Show me all data',
                'data_source_id' => $this->dataSource->id,
            ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'query' => ['sql', 'explanation'],
                'result' => ['data', 'columns', 'row_count', 'execution_time'],
                'visualization',
                'explanation',
                'follow_up_questions',
            ]);
    }

    public function test_unauthorized_user_cannot_access_data_source()
    {
        $otherUser = User::factory()->create();
        
        $response = $this->actingAs($otherUser)
            ->postJson('/insights/ask', [
                'question' => 'Show me all data',
                'data_source_id' => $this->dataSource->id,
            ]);

        $response->assertStatus(403);
    }

    public function test_validates_question_input()
    {
        $response = $this->actingAs($this->user)
            ->postJson('/insights/ask', [
                'question' => '', // Empty question
                'data_source_id' => $this->dataSource->id,
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['question']);
    }

    public function test_can_get_proactive_insights()
    {
        $response = $this->actingAs($this->user)
            ->getJson('/insights/proactive?data_source_id=' . $this->dataSource->id);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'insights',
            ]);
    }

    public function test_can_generate_visualization_config()
    {
        $queryData = [
            'data' => [
                ['month' => 'Jan', 'sales' => 1000],
                ['month' => 'Feb', 'sales' => 1200],
            ],
            'columns' => [
                ['name' => 'month', 'type' => 'string'],
                ['name' => 'sales', 'type' => 'integer'],
            ],
        ];

        $response = $this->actingAs($this->user)
            ->postJson('/insights/visualization', [
                'query_data' => $queryData,
                'chart_type' => 'bar',
            ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'config',
            ]);
    }
}