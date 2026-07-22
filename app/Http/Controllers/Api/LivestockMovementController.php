<?php

namespace App\Http\Controllers\Api;

use App\Models\Livestock;
use App\Models\LivestockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LivestockMovementController extends BaseApiController
{
    public function index(Request $request)
    {
        $type = $request->input('type'); // in / out

        $movements = LivestockMovement::with('livestock', 'user')
            ->when($type, fn($q) => $q->where('type', $type))
            ->latest()
            ->paginate(10);

        return $this->success($movements, 'Livestock movements retrieved successfully.');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'livestock_id' => 'required|exists:livestocks,id',
            'type'         => 'required|in:in,out',
            'quantity'     => 'required|integer|min:1',
            'date'         => 'required|date',
            'reason'       => 'nullable|string|max:255',
            'notes'        => 'nullable|string',
        ]);

        $livestock = Livestock::findOrFail($validated['livestock_id']);

        if ($validated['type'] === 'out' && $livestock->quantity < $validated['quantity']) {
            return $this->error('Jumlah ternak tidak cukup! Jumlah saat ini: ' . $livestock->quantity, 422);
        }

        $movement = LivestockMovement::create([
            'livestock_id' => $validated['livestock_id'],
            'user_id'      => Auth::id(),
            'type'         => $validated['type'],
            'quantity'     => $validated['quantity'],
            'date'         => $validated['date'],
            'reason'       => $validated['reason'] ?? null,
            'notes'        => $validated['notes'] ?? null,
        ]);

        if ($validated['type'] === 'in') {
            $livestock->increment('quantity', $validated['quantity']);
        } else {
            $livestock->decrement('quantity', $validated['quantity']);
        }

        return $this->success($movement, 'Livestock movement recorded successfully.', 201);
    }
}