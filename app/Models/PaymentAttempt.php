<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class PaymentAttempt extends Model {
    protected $guarded = [];
    protected $hidden = ['return_token', 'request_fields'];
    protected $casts = ['request_fields' => 'array', 'is_test' => 'boolean', 'amount_minor' => 'integer'];
    public function order() { return $this->belongsTo(Order::class); }
}
