<?php

use App\Models\Role;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique()->index();
            $table->boolean('is_deletable')->default(true);
            $table->boolean('is_updatable')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Role::insert(
            [
                [
                    'name' => 'Admin',
                    'code' => 'admin',
                    'is_deletable' => false,
                    'is_updatable' => false,
                    'created_at' => now()
                ],
                [
                    'name' => 'Client',
                    'code' => 'client',
                    'is_deletable' => false,
                    'is_updatable' => false,
                    'created_at' => now()
                ],
                [
                    'name' => 'Vendor - Car parts',
                    'code' => 'vendor_cp',
                    'is_deletable' => false,
                    'is_updatable' => false,
                    'created_at' => now()
                ],
                [
                    'name' => 'Vendor - Battries and Tires',
                    'code' => 'vendor_bt',
                    'is_deletable' => false,
                    'is_updatable' => false,
                    'created_at' => now()
                ],
                [
                    'name' => 'Winch Driver',
                    'code' => 'winch_driver',
                    'is_deletable' => false,
                    'is_updatable' => false,
                    'created_at' => now()
                ],
                [
                    'name' => 'Worker - Battries and Tires',
                    'code' => 'worker_bt',
                    'is_deletable' => false,
                    'is_updatable' => false,
                    'created_at' => now()
                ],
                [
                    'name' => 'Worker - Emergency',
                    'code' => 'worker_sos',
                    'is_deletable' => false,
                    'is_updatable' => false,
                    'created_at' => now()
                ]
            ]
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
