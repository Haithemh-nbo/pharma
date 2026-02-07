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
// Dashboard Routes
// ===========================
Route::get('/dashboard/admin', function () {
    $user = Session::get('user');
    if (!$user || $user->role !== 'admin') {
        return redirect('/login');
    }

    // Fetch all users
    $allUsers = DB::table('users')->get();

    return view('admin_dashboard', ['users' => $allUsers]);
});

Route::get('/dashboard/supplier', function () {
    $user = Session::get('user');
    if (!$user || $user->role !== 'supplier') {
        return redirect('/login');
    }
    return view('supplier_dashboard');
});

Route::get('/dashboard/pharmacy', function () {
    $user = Session::get('user');
    if (!$user || $user->role !== 'pharmacy') {
        return redirect('/login');
    }
    return view('pharmacy_dashboard');
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

    // Store original admin session
    Session::put('admin_original', $admin);

    // Log in as user
    Session::put('user', $userToImpersonate);

    // Redirect to appropriate dashboard
    if ($userToImpersonate->role === 'supplier') {
        return redirect('/dashboard/supplier');
    } elseif ($userToImpersonate->role === 'pharmacy') {
        return redirect('/dashboard/pharmacy');
    }

    return redirect('/dashboard/admin');
});

// Return to Admin after impersonation
Route::get('/admin/return', function () {
    $original = Session::get('admin_original');
    if ($original) {
        Session::put('user', $original);
        Session::forget('admin_original');
    }
    return redirect('/dashboard/admin');
});

// Supplier Dashboard
Route::get('/dashboard/supplier', function () {
    $user = Session::get('user');
    if (!$user || $user->role !== 'supplier') return redirect('/login');

    return view('supplier_dashboard');
});

// List of Supplier Listings
Route::get('/supplier/listings', function () {
    $user = Session::get('user');
    if (!$user || $user->role !== 'supplier') return redirect('/login');

    $listings = DB::table('listings')->where('supplier_id', $user->supplier_id)->get();


    return view('supplier_listings', ['listings' => $listings]);
});

// Create a new listing
Route::post('/supplier/listings/create', function () {
    $user = Session::get('user');
    if (!$user || $user->role !== 'supplier') return redirect('/login');

    $data = request()->only(['name', 'description']);
    $id = DB::table('listings')->insertGetId([
        'name' => $data['name'],
        'description' => $data['description'],
        'is_active' => 1,
        'created_at' => now(),
        'user_id' => $user->id
    ]);

    return redirect('/supplier/listings');
});

// Add product to listing
Route::post('/supplier/listings/{listing_id}/add-product', function ($listing_id) {
    $user = Session::get('user');
    if (!$user || $user->role !== 'supplier') return redirect('/login');

    $data = request()->only(['product_id', 'quantity']);
    DB::table('listing_products')->insert([
        'listing_id' => $listing_id,
        'product_id' => $data['product_id'],
        'quantity' => $data['quantity'],
        'created_at' => now()
    ]);

    return redirect("/supplier/listings");
});

// Supplier Order History
Route::get('/supplier/orders', function () {
    $user = Session::get('user');
    if (!$user || $user->role !== 'supplier') return redirect('/login');

    // Orders containing products from this supplier
    $orders = DB::table('order_items')
        ->join('orders', 'order_items.order_id', '=', 'orders.id')
        ->join('products', 'order_items.product_id', '=', 'products.id')
        ->where('products.supplier_id', $user->supplier_id)
        ->select('orders.*', 'order_items.product_name', 'order_items.quantity', 'order_items.subtotal')
        ->get();

    return view('supplier_orders', ['orders' => $orders]);
});

// Get Pharmacy Info who ordered supplier's products
Route::get('/supplier/pharmacy/{id}', function ($id) {
    $user = Session::get('user');
    if (!$user || $user->role !== 'supplier') return redirect('/login');

   $pharmacy = DB::table('pharmacies')->where('id', $order->user_id)->first();

    // Orders for this supplier by this pharmacy
    $orders = DB::table('order_items')
        ->join('orders', 'order_items.order_id', '=', 'orders.id')
        ->join('products', 'order_items.product_id', '=', 'products.id')
        ->where('products.supplier_id', $user->supplier_id)
        ->where('orders.user_id', $pharmacy->owner_id)
        ->select('orders.*', 'order_items.product_name', 'order_items.quantity', 'order_items.subtotal')
        ->get();

    return view('supplier_pharmacy_info', ['pharmacy' => $pharmacy, 'orders' => $orders]);
});
// Admin or Supplier can view user details
Route::get('/user/{id}', function($id) {
    $current = Session::get('user');
    if (!$current) return redirect('/login');

    // Admin can view any user, supplier can view only pharmacies who ordered
    $user = DB::table('users')->where('id', $id)->first();
    if (!$user) return redirect()->back()->with('error', 'User not found');

    // Get related orders if any
    $orders = DB::table('orders')->where('user_id', $id)->get();

    // Get pharmacy info if user is a pharmacy
    $pharmacy = null;
    if ($user->role === 'pharmacy') {
        $pharmacy = DB::table('pharmacies')->where('owner_id', $id)->first();
    }

    return view('user_config', [
        'user' => $user,
        'orders' => $orders,
        'pharmacy' => $pharmacy
    ]);
});

