<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserAddress;
use App\Services\RajaOngkirService;

class AddressController extends Controller
{
    protected RajaOngkirService $rajaOngkir;

    public function __construct(RajaOngkirService $rajaOngkir)
    {
        $this->rajaOngkir = $rajaOngkir;
    }

    // ── Daftar Alamat ─────────────────────────────────────────

    public function index()
    {
        $addresses = UserAddress::where('user_id', auth()->id())
            ->orderByDesc('is_default')
            ->latest()
            ->get();

        return view('buyer.addresses.index', compact('addresses'));
    }

    // ── Form Tambah ───────────────────────────────────────────

    public function create()
    {
        return view('buyer.addresses.create');
    }

    // ── Simpan Alamat Baru ────────────────────────────────────

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

        $validated['user_id'] = auth()->id();
        $isDefault = $request->boolean('is_default');

        // Kalau set default, copot default dari alamat lain
        if ($isDefault) {
            UserAddress::where('user_id', auth()->id())->update(['is_default' => false]);
        }

        // Kalau ini alamat pertama, otomatis jadi default
        $hasAddress = UserAddress::where('user_id', auth()->id())->exists();
        $validated['is_default'] = $isDefault || !$hasAddress;

        UserAddress::create($validated);

        return redirect()->route('buyer.addresses.index')
            ->with('success', 'Alamat berhasil ditambahkan!');
    }

    // ── Form Edit ─────────────────────────────────────────────

    public function edit(UserAddress $address)
    {
        $this->authorizeAddress($address);
        return view('buyer.addresses.edit', compact('address'));
    }

    // ── Update Alamat ─────────────────────────────────────────

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
            UserAddress::where('user_id', auth()->id())
                ->where('id', '!=', $address->id)
                ->update(['is_default' => false]);
        }

        $validated['is_default'] = $isDefault;
        $address->update($validated);

        return redirect()->route('buyer.addresses.index')
            ->with('success', 'Alamat berhasil diperbarui!');
    }

    // ── Hapus Alamat ──────────────────────────────────────────

    public function destroy(UserAddress $address)
    {
        $this->authorizeAddress($address);

        $wasDefault = $address->is_default;
        $address->delete();

        // Kalau yang dihapus adalah default, set alamat lain jadi default otomatis
        if ($wasDefault) {
            $next = UserAddress::where('user_id', auth()->id())->latest()->first();
            if ($next) $next->update(['is_default' => true]);
        }

        return redirect()->route('buyer.addresses.index')
            ->with('success', 'Alamat berhasil dihapus!');
    }

    // ── Set Default ───────────────────────────────────────────

    public function setDefault(UserAddress $address)
    {
        $this->authorizeAddress($address);

        UserAddress::where('user_id', auth()->id())->update(['is_default' => false]);
        $address->update(['is_default' => true]);

        return redirect()->back()->with('success', 'Alamat utama berhasil diubah!');
    }

    // ── Helper ────────────────────────────────────────────────

    private function authorizeAddress(UserAddress $address): void
    {
        if ($address->user_id !== auth()->id()) {
            abort(403);
        }
    }
}