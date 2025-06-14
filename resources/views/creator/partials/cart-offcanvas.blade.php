<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasCart" aria-labelledby="offcanvasCart">
  <div class="offcanvas-header">
    <h5 class="offcanvas-title" id="offcanvasCartLabel">Shopping Cart</h5>
    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  <div class="offcanvas-body">
    <ul class="list-unstyled">
      <li>
        <div class="row g-2 g-lg-3 align-items-center">
          <a href="" class="col-3"><img class="img-fluid" src="{{ asset('admin_theme/assets/images/products/product-1.jpg') }}"
              alt="Product"></a>
          <div class="col">
            <a href="" class="text-black text-primary-hover lead">Bluetooth Speaker</a>
            <ul class="list-inline text-muted">
              <li class="list-inline-item">Price: <span class="text-secondary">$90</span></li>
              <li class="list-inline-item">Color: <span class="text-secondary">Blue</span></li>
              <li class="list-inline-item">Qty:
                <div class="counter text-secondary" data-counter="qty-1">
                  <span class="counter-minus bi bi-dash"></span>
                  <input type="number" name="qty-1" class="counter-value" value="0" min="0" max="10">
                  <span class="counter-plus bi bi-plus"></span>
                </div>
              </li>
            </ul>
            <a href="" class="text-red underline">Remove</a>
          </div>
        </div>
      </li>
      <li class="mt-4">
        <div class="row g-2 g-lg-3 align-items-center">
          <a href="" class="col-3"><img class="img-fluid" src="{{ asset('admin_theme/assets/images/products/product-2.jpg') }}"
              alt="Product"></a>
          <div class="col">
            <a href="" class="text-black text-primary-hover lead">Bluetooth Speaker</a>
            <ul class="list-inline text-muted">
              <li class="list-inline-item">Price: <span class="text-secondary">$90</span></li>
              <li class="list-inline-item">Color: <span class="text-secondary">Blue</span></li>
              <li class="list-inline-item">Qty:
                <div class="counter text-secondary" data-counter="qty-1">
                  <span class="counter-minus bi bi-dash"></span>
                  <input type="number" name="qty-1" class="counter-value" value="0" min="0" max="10">
                  <span class="counter-plus bi bi-plus"></span>
                </div>
              </li>
            </ul>
            <a href="" class="text-red underline">Remove</a>
          </div>
        </div>
      </li>
      <li class="mt-4">
        <div class="row g-2 g-lg-3 align-items-center">
          <a href="" class="col-3"><img class="img-fluid" src="{{ asset('admin_theme/assets/images/products/product-3.jpg') }}"
              alt="Product"></a>
          <div class="col">
            <a href="" class="text-black text-primary-hover lead">Bluetooth Speaker</a>
            <ul class="list-inline text-muted">
              <li class="list-inline-item">Price: <span class="text-secondary">$90</span></li>
              <li class="list-inline-item">Color: <span class="text-secondary">Blue</span></li>
              <li class="list-inline-item">Qty:
                <div class="counter text-secondary" data-counter="qty-1">
                  <span class="counter-minus bi bi-dash"></span>
                  <input type="number" name="qty-1" class="counter-value" value="0" min="0" max="10">
                  <span class="counter-plus bi bi-plus"></span>
                </div>
              </li>
            </ul>
            <a href="" class="text-red underline">Remove</a>
          </div>
        </div>
      </li>
    </ul>
  </div>
  <div class="offcanvas-footer">
    <div class="d-grid gap-1">
      <a href="{{ url('/shop-cart') }}" class="btn btn-outline-light rounded-pill">View Cart</a>
      <a href="{{ url('/shop-checkout') }}" class="btn btn-primary rounded-pill">Proceed to Checkout</a>
    </div>
  </div>
</div>