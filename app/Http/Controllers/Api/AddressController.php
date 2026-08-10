<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseApiController;
use Illuminate\Http\Request;
use App\Models\UserAddress;
use Illuminate\Support\Facades\Auth;

class AddressController extends BaseApiController
{
    // GET /addresses
    public function index()
    {
        $addresses = UserAddress::where('user_id', Auth::id())
            ->orderByDesc('is_default')
            ->latest()
            ->get();

        return $this->success($addresses, 'Daftar alamat berhasil diambil.');
    }

    public function show(UserAddress $address)
    {
        $this->authorizeAddress($address);
        return $this->success($address, 'Detail alamat berhasil diambil.');
    }

    // POST /addresses
    public function store(Request $request)
    {
        $validated = $request->validate([
            'label'          => 'required|string|max:50',
            'recipient_name' => 'required|string|max:255',
            'phone'          => 'required|string|max:20',
            'destination_id' => 'required|string',
            'province'       => 'required|string|max:255',
            'city'           => 'required|string|max:255',
            'district'       => 'required|string|max:255',
            'subdistrict'    => 'nullable|string|max:255',
            'address_detail' => 'required|string',
            'postal_code'    => 'nullable|string|max:10',
            'is_default'     => 'nullable|boolean',
        ]);

        $validated['user_id'] = Auth::id();
        $isDefault = $request->boolean('is_default');

        if ($isDefault) {
            UserAddress::where('user_id', Auth::id())->update(['is_default' => false]);
        }

        $hasAddress = UserAddress::where('user_id', Auth::id())->exists();
        $validated['is_default'] = $isDefault || !$hasAddress;

        $address = UserAddress::create($validated);

        return $this->success($address, 'Alamat berhasil ditambahkan.', 201);
    }

    // PUT /addresses/{address}
    public function update(Request $request, UserAddress $address)
    {
        $this->authorizeAddress($address);

        $validated = $request->validate([
            'label'          => 'required|string|max:50',
            'recipient_name' => 'required|string|max:255',
            'phone'          => 'required|string|max:20',
            'destination_id' => 'required|string',
            'province'       => 'required|string|max:255',
            'city'           => 'required|string|max:255',
            'district'       => 'required|string|max:255',
            'subdistrict'    => 'nullable|string|max:255',
            'address_detail' => 'required|string',
            'postal_code'    => 'nullable|string|max:10',
            'is_default'     => 'nullable|boolean',
        ]);

        $isDefault = $request->boolean('is_default');

        if ($isDefault) {
            UserAddress::where('user_id', Auth::id())
                ->where('id', '!=', $address->id)
                ->update(['is_default' => false]);
        }

        $validated['is_default'] = $isDefault;
        $address->update($validated);

        return $this->success($address, 'Alamat berhasil diperbarui.');
    }

    // DELETE /addresses/{address}
    public function destroy(UserAddress $address)
    {
        $this->authorizeAddress($address);

        $wasDefault = $address->is_default;
        $address->delete();

        if ($wasDefault) {
            $next = UserAddress::where('user_id', Auth::id())->latest()->first();
            if ($next) $next->update(['is_default' => true]);
        }

        return $this->success(null, 'Alamat berhasil dihapus.');
    }

    // PATCH /addresses/{address}/set-default
    public function setDefault(UserAddress $address)
    {
        $this->authorizeAddress($address);

        UserAddress::where('user_id', Auth::id())->update(['is_default' => false]);
        $address->update(['is_default' => true]);

        return $this->success($address, 'Alamat utama berhasil diubah.');
    }

    private function authorizeAddress(UserAddress $address): void
    {
        if ($address->user_id !== Auth::id()) {
            abort(403, 'Anda tidak berhak mengakses alamat ini.');
        }
    }
}
