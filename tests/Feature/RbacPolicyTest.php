<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class RbacPolicyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'manage_products',
            'manage_orders',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }
    }

    public function test_user_without_permission_cannot_manage_products()
    {
        $user = User::factory()->create();

        $this->assertFalse($user->can('viewAny', Product::class));
        $this->assertFalse($user->can('create', Product::class));
    }

    public function test_user_with_permission_can_manage_products()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('manage_products');

        $this->assertTrue($user->can('viewAny', Product::class));
        $this->assertTrue($user->can('create', Product::class));
    }

    public function test_user_without_permission_cannot_manage_orders()
    {
        $user = User::factory()->create();

        $this->assertFalse($user->can('viewAny', Order::class));
        $this->assertFalse($user->can('create', Order::class));
    }

    public function test_user_with_permission_can_manage_orders()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('manage_orders');

        $this->assertTrue($user->can('viewAny', Order::class));
        $this->assertTrue($user->can('create', Order::class));
    }
}
