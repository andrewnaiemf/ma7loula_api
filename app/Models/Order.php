<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Modules\Core\Observers\OrderObserver;
use Modules\Winch\Observers\OrderObserver as WichOrderObserver;

/**
 *    @var int $id;
 *    @var int $user_id;
 *    @var int $user_car_id;
 *    @var int $car_id;
 *    @var int $address_id;
 *    @var string $status;
 *    @var string $payment_method;
 *    @var string $type;
 *    @var string $products_price;
 *    @var string $services_price;
 *    @var string $tax_price;
 *    @var string $delivery_price;
 *    @var string $total;
 *    @var string $payment_code;
 *    @var Carbon $created_at;
 *
 *    @var User $user;
 *    @var Address $address;
 *    @var UserCar $user_car;
 *    @var Car $car; 
 *    @var Vendors $vendors[];
 *    @var Product $products[];
 */

#[ObservedBy([OrderObserver::class, WichOrderObserver::class])]
class Order extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['user_id', 'user_car_id', 'car_id', 'address_id', 'status', 'payment_method', 'type', 'products_price', 'services_price', 'tax_price', 'delivery_price', 'total', 'payment_code', 'delivery_time', 'has_service'];

    /**
     * @return User $user;
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return Address $address;
     */
    public function address()
    {
        return $this->belongsTo(Address::class);
    }

    /**
     * @return Car $car;
     */
    public function car()
    {
        return $this->belongsTo(Car::class);
    }

    /**
     * @return UserCar $user_car;
     */
    public function user_car()
    {
        return $this->belongsTo(UserCar::class, 'user_car_id');
    }

    /**
     * @return Product $products[];
     */
    public function products()
    {
        return $this->belongsToMany(Product::class, 'order_product')->withTimestamps()->withPivot(['unit_price', 'qty', 'total']);
    }

    /**
     * @return Vendor $vendors[];
     */
    public function vendors()
    {
        return $this->belongsToMany(Vendor::class, 'order_vendors')->withTimestamps()->withPivot(['id', 'status', 'has_service', 'delivery_time', 'products_price', 'services_price', 'tax_price', 'delivery_price', 'total', 'worker_id']);
    }

    public function statuses()
    {
        return $this->hasMany(OrderStatus::class);
    }

    public function getDeliveryTimeAttribute($val)
    {
        return $val ? Carbon::createFromFormat('Y-m-d H:i:s', $val) : null;
    }

    public function rate()
    {
        return $this->hasOne(OrderRate::class);
    }

    public function vendor_orders()
    {
        return $this->hasMany(OrderVendor::class);
    }

    public function winch_order()
    {
        return $this->hasOne(OrderWinch::class);
    }

    public function emergency_order()
    {
        return $this->hasOne(OrderEmergency::class);
    }

    public function workers()
    {
        return $this->belongsToMany(Worker::class, 'order_vendors')->withTimestamps()->withPivot(['id', 'status', 'has_service', 'delivery_time', 'products_price', 'services_price', 'tax_price', 'delivery_price', 'total', 'worker_id']);
    }

    public function services()
    {
        return $this->belongsToMany(Service::class, 'order_service')->withPivot(['price']);
    }
}
