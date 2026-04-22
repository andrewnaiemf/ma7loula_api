<?php

use App\Enums\OrderStatus;
use App\Enums\OrderVendorLineStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_vendors', function (Blueprint $table) {
            if (! Schema::hasColumn('order_vendors', 'offered_total')) {
                $table->string('offered_total')->nullable()->after('total');
            }
        });

        if (Schema::getConnection()->getDriverName() !== 'mysql') {
            return;
        }

        $orderAllowed = OrderStatus::values();
        $vendorAllowed = OrderVendorLineStatus::values();

        $this->normalizeColumn('orders', 'status', $orderAllowed, OrderStatus::New->value);
        $this->normalizeColumn('order_vendors', 'status', $vendorAllowed, OrderVendorLineStatus::New->value);

        $orderEnum = $this->enumListSql($orderAllowed);
        $vendorEnum = $this->enumListSql($vendorAllowed);

        DB::statement("ALTER TABLE `orders` MODIFY `status` ENUM({$orderEnum}) NOT NULL DEFAULT 'new'");
        DB::statement("ALTER TABLE `order_vendors` MODIFY `status` ENUM({$vendorEnum}) NOT NULL DEFAULT 'new'");
    }

    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE `orders` MODIFY `status` VARCHAR(255) NOT NULL");
            DB::statement("ALTER TABLE `order_vendors` MODIFY `status` VARCHAR(255) NOT NULL");
        }

        Schema::table('order_vendors', function (Blueprint $table) {
            if (Schema::hasColumn('order_vendors', 'offered_total')) {
                $table->dropColumn('offered_total');
            }
        });
    }

    private function normalizeColumn(string $table, string $column, array $allowed, string $fallback): void
    {
        $allowedSql = collect($allowed)->map(fn ($v) => "'" . str_replace("'", "''", $v) . "'")->implode(',');
        $fb = str_replace("'", "''", $fallback);
        DB::statement("UPDATE `{$table}` SET `{$column}` = '{$fb}' WHERE `{$column}` NOT IN ({$allowedSql})");
    }

    private function enumListSql(array $values): string
    {
        return collect($values)
            ->map(fn ($v) => "'" . str_replace("'", "''", $v) . "'")
            ->implode(',');
    }
};
