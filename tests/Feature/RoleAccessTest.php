<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_admin_dashboard()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get('/admin/dashboard');

        $response->assertStatus(200);
    }

    public function test_student_cannot_access_admin_dashboard()
    {
        $student = User::factory()->create(['role' => 'student']);

        $response = $this->actingAs($student)->get('/admin/dashboard');

        $response->assertStatus(403);
    }

    public function test_teacher_can_access_teacher_dashboard()
    {
        $teacher = User::factory()->create(['role' => 'teacher']);

        $response = $this->actingAs($teacher)->get('/teacher/dashboard');

        $response->assertStatus(200);
    }

    public function test_student_can_access_student_dashboard()
    {
        $student = User::factory()->create(['role' => 'student']);

        $response = $this->actingAs($student)->get('/student/dashboard');

        $response->assertStatus(200);
    }
}
