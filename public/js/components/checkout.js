    const subtotal = window.checkoutData.subtotal;
const provincesUrl = window.checkoutData.provincesUrl;
const citiesUrl = window.checkoutData.citiesUrl;
const ongkirUrl = window.checkoutData.ongkirUrl;
const csrfToken = window.checkoutData.csrfToken;

// Load provinces
fetch(provincesUrl)
    .then(res => res.json())
    .then(data => {
        const select = document.getElementById('provinceSelect');
        data.forEach(p => {
            select.innerHTML += `<option value="${p.id}">${p.name}</option>`;
        });
    });
// Load cities when province changes
document.getElementById('provinceSelect').addEventListener('change', function() {
    const provinceId = this.value;
    const citySelect = document.getElementById('citySelect');
    citySelect.innerHTML = '<option value="">Loading...</option>';

    fetch(`${citiesUrl}/${provinceId}`)
    .then(res => res.json())
    .then(data => {
        citySelect.innerHTML = '<option value="">-- Select City --</option>';
        data.forEach(c => {
            citySelect.innerHTML += `<option value="${c.name}" data-id="${c.id}">${c.name}</option>`;
        });
    });
});

// Save city_id when city changes
document.getElementById('citySelect').addEventListener('change', function() {
    const selected = this.options[this.selectedIndex];
    document.getElementById('cityId').value = selected.getAttribute('data-id');
});

// Check ongkir
document.getElementById('checkOngkirBtn').addEventListener('click', function() {
    const cityId = document.getElementById('cityId').value;
    const courier = document.getElementById('courierSelect').value;

    if (!cityId) {
        alert('Please select a city first!');
        return;
    }

    document.getElementById('ongkirLoading').style.display = 'block';
    document.getElementById('ongkirResult').style.display = 'none';

    fetch(ongkirUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({
            origin: '144',
            destination: cityId,
            weight: 1000,
            courier: courier
        })
    })
    .then(res => res.json())
    .then(data => {
    console.log('Response:', data);
    document.getElementById('ongkirLoading').style.display = 'none';
    document.getElementById('ongkirResult').style.display = 'block';

    const container = document.getElementById('ongkirOptions');
    container.innerHTML = '';

    // Handle berbagai struktur response
    let costs = [];
    if (Array.isArray(data)) {
        costs = data;
    } else if (data && data.data && Array.isArray(data.data)) {
        costs = data.data;
    }

    if (costs.length === 0) {
        container.innerHTML = '<div class="text-muted">No shipping options available</div>';
        return;
    }

    costs.forEach((cost, index) => {
        const price = cost.cost;
        const etd = cost.etd;
        container.innerHTML += `
            <div class="form-check mb-2 p-3 border rounded">
                <input class="form-check-input" type="radio" name="shipping_option"
                       id="service${index}" value="${price}"
                       onchange="updateShipping(${price}, '${cost.code}', '${cost.service}')">
                <label class="form-check-label d-flex justify-content-between" for="service${index}">
                    <span><strong>${cost.code.toUpperCase()} ${cost.service}</strong> - ${cost.description}</span>
                    <span class="text-success fw-bold">Rp ${price.toLocaleString()} (${etd})</span>
                </label>
            </div>
        `;
    });
})
});

function updateShipping(price, courier, service) {
    document.getElementById('shippingCost').value = price;
    document.getElementById('courierHidden').value = courier;
    document.getElementById('courierService').value = service;
    document.getElementById('shippingDisplay').textContent = `Rp ${price.toLocaleString()}`;
    document.getElementById('grandTotal').textContent = `$${subtotal.toFixed(2)} + Rp ${price.toLocaleString()}`;
}