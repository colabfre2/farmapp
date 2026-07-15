<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Expense extends Model
{
    //
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'expense_category_id',
        'date',
        'description',
        'amount',
        'notes',
    ];

    public function expenseCategory()
    {
        return $this->belongsTo(ExpenseCategory::class);
    }
}
