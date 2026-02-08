<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

// ===========================
// Auth Routes
// ===========================
Route::get('/login', [App\Http\Controllers\AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [App\Http\Controllers\AuthController::class, 'login'])->name('login.post');
Route::get('/logout', [App\Http\Controllers\AuthController::class, 'logout'])->name('logout');

// ===========================
// Admin Dashboard
// ===========================
Route::get('/dashboard/admin', function () {
    $user = Session::get('user');
    if (!$user || $user->role !== 'admin') return redirect('/login');

    $allUsers = DB::table('users')->get();
    return view('admin_dashboard', ['users' => $allUsers]);
});

// ===========================
// Admin User Management
// ===========================
Route::get('/admin/users', function () {
    $user = Session::get('user');
    if (!$user || $user->role !== 'admin') return redirect('/login');

    $allUsers = DB::table('users')->get();
    return view('admin_users', ['users' => $allUsers]);
});

Route::get('/admin/users/approve/{id}', function ($id) {
    $user = Session::get('user');
    if (!$user || $user->role !== 'admin') return redirect('/login');

    DB::table('users')->where('id', $id)->update(['status' => 'verified']);
    return redirect('/admin/users');
});

Route::get('/admin/users/reject/{id}', function ($id) {
    $user = Session::get('user');
    if (!$user || $user->role !== 'admin') return redirect('/login');

    DB::table('users')->where('id', $id)->update(['status' => 'blocked']);
    return redirect('/admin/users');
});

// ===========================
// Admin Impersonation
// ===========================
Route::get('/admin/users/impersonate/{id}', function ($id) {
    $admin = Session::get('user');
    if (!$admin || $admin->role !== 'admin') return redirect('/login');

    $userToImpersonate = DB::table('users')->where('id', $id)->first();
    if (!$userToImpersonate) return redirect('/admin/users');

    Session::put('admin_original', $admin);
    Session::put('user', $userToImpersonate);

    if ($userToImpersonate->role === 'supplier') return redirect('/dashboard/supplier');
    if ($userToImpersonate->role === 'pharmacy') return redirect('/dashboard/pharmacy');

    return redirect('/dashboard/admin');
});

// Return to Admin
Route::get('/admin/return', function () {
    $original = Session::get('admin_original');
    if ($original) {
        Session::put('user', $original);
        Session::forget('admin_original');
    }
    return redirect('/dashboard/admin');
});

// ===========================
// Supplier Dashboard
// ===========================
Route::get('/dashboard/supplier', function () {
    $user = Session::get('user');
    if (!$user || $user->role !== 'supplier') return redirect('/login');

    return view('supplier_dashboard');
});

// ===========================
// Supplier Listings
// ===========================

// List supplier listings
Route::get('/supplier/listings', function () {
    $user = Session::get('user');
    if (!$user || $user->role !== 'supplier') return redirect('/login');

    $listings = DB::table('listings')
        ->where('supplier_id', $user->supplier_id)
        ->get();

    return view('supplier_listings', ['listings' => $listings]);
});

// Create new listing
Route::post('/supplier/listings/create', function () {
    $user = Session::get('user');
    if (!$user || $user->role !== 'supplier') return redirect('/login');

    $data = request()->only(['name', 'description']);

    DB::table('listings')->insert([
        'name' => $data['name'],
        'description' => $data['description'],
        'is_active' => 1,
        'created_at' => now(),
        'supplier_id' => $user->supplier_id
    ]);

    return redirect('/supplier/listings');
});

// Listing details + products
Route::get('/supplier/listings/{id}', function ($id) {
    $user = Session::get('user');
    if (!$user || $user->role !== 'supplier') return redirect('/login');

    $listing = DB::table('listings')
        ->where('id', $id)
        ->where('supplier_id', $user->supplier_id)
        ->first();

    if (!$listing) return redirect('/supplier/listings');

    $products = DB::table('listing_products')
        ->join('products', 'listing_products.product_id', '=', 'products.id')
        ->where('listing_products.listing_id', $id)
        ->select('listing_products.id as lp_id', 'products.*', 'listing_products.quantity')
        ->get();

    return view('supplier_listing_detail', ['listing' => $listing, 'products' => $products]);
});

// Add product by name
Route::post('/supplier/listings/{listing_id}/add-product', function ($listing_id) {
    $user = Session::get('user');
    if (!$user || $user->role !== 'supplier') return redirect('/login');

    $data = request()->only(['product_name', 'description', 'price', 'quantity']);

    // Check if product already exists for this supplier
    $product = DB::table('products')
        ->where('supplier_id', $user->supplier_id)
        ->where('name', $data['product_name'])
        ->first();

    if (!$product) {
        // Create product
        $product_id = DB::table('products')->insertGetId([
            'supplier_id' => $user->supplier_id,
            'name' => $data['product_name'],
            'description' => $data['description'],
            'price' => $data['price'],
            'created_at' => now()
        ]);
    } else {
        $product_id = $product->id;
    }

    // Add to listing
    DB::table('listing_products')->insert([
        'listing_id' => $listing_id,
        'product_id' => $product_id,
        'quantity' => $data['quantity'],
        'created_at' => now()
    ]);

    return redirect("/supplier/listings/$listing_id");
});


// Update product quantity
Route::post('/supplier/listings/{listing_id}/product/{lp_id}/update', function($listing_id, $lp_id) {
    $user = Session::get('user');
    if (!$user || $user->role !== 'supplier') return redirect('/login');

    $data = request()->only(['name', 'description', 'price', 'quantity']);

    // First get the product_id from listing_products
    $listingProduct = DB::table('listing_products')->where('id', $lp_id)->first();
    if (!$listingProduct) return redirect("/supplier/listings/$listing_id")->with('error', 'Product not found');

    $product_id = $listingProduct->product_id;

    // Update product info
    DB::table('products')->where('id', $product_id)->update([
        'name' => $data['name'],
        'description' => $data['description'],
        'price' => $data['price']
    ]);

    // Update quantity in the pivot table
    DB::table('listing_products')->where('id', $lp_id)->update([
        'quantity' => $data['quantity']
    ]);

    return redirect("/supplier/listings/$listing_id")->with('success', 'Product updated successfully');
});




// Delete product
Route::post('/supplier/listings/{listing_id}/product/{lp_id}/delete', function ($listing_id, $lp_id) {
    $user = Session::get('user');
    if (!$user || $user->role !== 'supplier') return redirect('/login');

    DB::table('listing_products')->where('id', $lp_id)->delete();

    return redirect("/supplier/listings/$listing_id");
});

// Supplier Orders
// Supplier Order History
Route::get('/supplier/orders', function () {
    $user = Session::get('user');
    if (!$user || $user->role !== 'supplier') return redirect('/login');

    // Orders containing products from this supplier
    $orders = DB::table('order_items')
        ->join('orders', 'order_items.order_id', '=', 'orders.id')
        ->join('products', 'order_items.product_id', '=', 'products.id')
        ->where('products.supplier_id', $user->supplier_id)
        ->select(
            'orders.id as order_id',
            'orders.status',
            'orders.created_at',
            'order_items.product_name',
            'order_items.quantity',
            'order_items.subtotal'
        )
        ->orderBy('orders.created_at', 'desc')
        ->get();

    return view('supplier_orders', ['orders' => $orders]);
});

// Supplier validates or rejects order
Route::post('/supplier/orders/{order_id}/update-status', function($order_id) {
    $user = Session::get('user');
    if (!$user || $user->role !== 'supplier') return redirect('/login');

    $status = request('status');
    if (!in_array($status, ['validated', 'rejected'])) {
        return redirect()->back()->with('error', 'Invalid status.');
    }

    DB::table('orders')->where('id', $order_id)->update(['status' => $status]);

    return redirect()->back()->with('success', 'Order status updated.');
});



Route::post('/user/update', function () {
    $user = Session::get('user');
    if (!$user) return redirect('/login');

    $data = request()->only(['email', 'old_password', 'new_password', 'confirm_password']);
    
    $updateData = ['email' => $data['email']];

    // Handle password update only if new password is provided
    if (!empty($data['new_password'])) {
        if (empty($data['old_password'])) {
            return redirect()->back()->with('error', 'Please enter your current password.');
        }

        // Verify old password (plain text)
        if ($data['old_password'] != $user->password) {
            return redirect()->back()->with('error', 'Current password is incorrect.');
        }

        // Confirm new password
        if ($data['new_password'] !== $data['confirm_password']) {
            return redirect()->back()->with('error', 'New password and confirm password do not match.');
        }

        // Update password as plain text
        $updateData['password'] = $data['new_password'];
    }

    // Update email and password if provided
    DB::table('users')->where('id', $user->id)->update($updateData);

    // Refresh session
    $user = DB::table('users')->where('id', $user->id)->first();
    Session::put('user', $user);

    return redirect()->back()->with('success', 'Account updated successfully.');
});








// Pharmacy Dashboard
Route::get('/dashboard/pharmacy', function () {
    $user = Session::get('user');
    if (!$user || $user->role !== 'pharmacy') return redirect('/login');

    return view('pharmacy_dashboard');
});

// View listing products
Route::get('/pharmacy/listing/{id}', function($id) {
    $user = Session::get('user');
    if (!$user || $user->role !== 'pharmacy') return redirect('/login');

    $listing = DB::table('listings')->where('id', $id)->first();
    if (!$listing) return redirect('/dashboard/pharmacy');

    $products = DB::table('listing_products')
        ->join('products', 'listing_products.product_id', '=', 'products.id')
        ->where('listing_products.listing_id', $id)
        ->select('listing_products.id as lp_id', 'products.*', 'listing_products.quantity')
        ->get();

    $cart = Session::get('cart', []);

    return view()->file(resource_path('views/pharmacy_listing_products.php'), [
    'listing' => $listing,
    'products' => $products,
    'cart' => $cart
]);

});


// Add product to cart
Route::post('/pharmacy/listings/{listing_id}/add-to-cart', function($listing_id) {
    $user = Session::get('user');
    if (!$user || $user->role !== 'pharmacy') return redirect('/login');

    $data = request()->only(['product_id', 'product_name', 'price', 'quantity']);
    
    // Get existing cart or create new
    $cart = Session::get('cart', []);

    // Add new item to cart
    $cart[] = [
        'listing_id' => $listing_id,
        'product_id' => $data['product_id'],
        'product_name' => $data['product_name'],
        'price' => $data['price'],
        'quantity' => $data['quantity']
    ];

    Session::put('cart', $cart);

    return redirect()->back()->with('success', 'Product added to cart!');
});
// Remove product from cart
Route::post('/pharmacy/cart/remove/{index}', function($index) {
    $cart = Session::get('cart', []);
    if(isset($cart[$index])) unset($cart[$index]);
    Session::put('cart', $cart);
    return redirect()->back();
});

// Place order
Route::post('/pharmacy/place-order', function(){
    $user = Session::get('user');
    if (!$user || $user->role !== 'pharmacy') return redirect('/login');

    $cart = Session::get('cart', []);
    if(empty($cart)) return redirect()->back()->with('error','Cart empty');

    $order_id = DB::table('orders')->insertGetId([
        'user_id' => $user->id,
        'status' => 'pending',
        'created_at' => now()
    ]);

    foreach($cart as $item){
        DB::table('order_items')->insert([
            'order_id' => $order_id,
            'listing_id' => $item['listing_id'],
            'product_id' => $item['product_id'],
            'product_name' => $item['product_name'],
            'quantity' => $item['quantity'],
            'subtotal' => $item['quantity'] * $item['price']
        ]);
    }

    Session::forget('cart');

    return redirect('/dashboard/pharmacy')->with('success','Order sent to supplier!');
});





Route::get('/pharmacy/orders', function() {
    $user = Session::get('user');
    if (!$user || $user->role !== 'pharmacy') return redirect('/login');

    $orders = DB::table('orders')
        ->join('order_items', 'orders.id', '=', 'order_items.order_id')
        ->where('orders.user_id', $user->id)
        ->where('orders.status', 'validated')
        ->select(
            'orders.id as order_id',
            'orders.created_at',
            'order_items.product_name',
            'order_items.quantity',
            'order_items.subtotal'
        )
        ->orderBy('orders.created_at', 'desc')
        ->get();

    return view('pharmacy_orders', ['orders' => $orders]);
});




Route::get('/supplier/orders/validate', function() {
    $user = Session::get('user');
    if (!$user || $user->role !== 'supplier') return redirect('/login');

    // Fetch only orders containing products from this supplier that are pending
    $orders = DB::table('orders')
        ->join('order_items', 'orders.id', '=', 'order_items.order_id')
        ->join('products', 'order_items.product_id', '=', 'products.id')
        ->where('products.supplier_id', $user->supplier_id)
        ->where('orders.status', 'pending') // only pending
        ->select(
            'orders.id as order_id',
            'orders.created_at',
            'orders.status',
            'order_items.product_name',
            'order_items.quantity',
            'order_items.subtotal'
        )
        ->orderBy('orders.created_at', 'desc')
        ->get();

    return view('supplier_validate_orders', ['orders' => $orders]);
});




Route::post('/supplier/orders/accept/{id}', function($id){
    $user = Session::get('user');
    if (!$user || $user->role !== 'supplier') return redirect('/login');

    DB::table('orders')->where('id',$id)->update([
        'status'=>'validated'
    ]);

    return redirect()->back()->with('success','Order validated');
});


Route::post('/supplier/orders/reject/{id}', function($id){
    $user = Session::get('user');
    if (!$user || $user->role !== 'supplier') return redirect('/login');

    DB::table('orders')->where('id',$id)->update([
        'status'=>'rejected'
    ]);

    return redirect()->back()->with('success','Order rejected');
});

Route::get('/supplier/orders/history', function(){
    $user = Session::get('user');
    if (!$user || $user->role !== 'supplier') return redirect('/login');

    // Get only validated orders for this supplier
    $orders = DB::table('orders')
        ->join('order_items', 'orders.id', '=', 'order_items.order_id')
        ->join('products', 'order_items.product_id', '=', 'products.id')
        ->where('products.supplier_id', $user->supplier_id)
        ->where('orders.status', 'delivered')
        ->select(
            'orders.id as order_id',
            'orders.status',
            'orders.created_at',
            'order_items.product_name',
            'order_items.quantity',
            'order_items.subtotal'
        )
        ->orderBy('orders.created_at', 'desc')
        ->distinct('orders.id') 
        ->get();

    return view('supplier_orders_history', compact('orders'));
});
;

