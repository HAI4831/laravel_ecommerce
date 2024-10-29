<?php
namespace App\Http\Controllers;

use App\Models\DiscountCode;
use Illuminate\Http\Request;

class DiscountCodeController extends Controller
{
    public function store(Request $request)
    {
        // Validation and storing logic
    }

    public function apply(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'total_amount' => 'required|numeric',
        ]);

        try {
            $newTotal = $this->applyDiscount($request->code, $request->total_amount);
            return response()->json(['new_total' => $newTotal], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
}
