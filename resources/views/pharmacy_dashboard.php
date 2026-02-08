<?php
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

$user = Session::get('user');
$cart = Session::get('cart', []);

// Fetch all active listings
$listings = DB::table('listings')->where('is_active', 1)->get();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Pharmacy Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .sidebar {
            height: 100%;
            width: 0;
            position: fixed;
            top: 0;
            right: 0;
            background-color: #f8f9fa;
            overflow-x: hidden;
            transition: 0.3s;
            /* padding: 20px; */
            border-left: 1px solid #ccc;
            z-index: 1000;
        }
        .sidebar-main{
            padding: 10px;
        }
        .sidebar.open { width: 400px; }
        .close-btn { cursor:pointer; float:right; font-size:20px; }
    </style>
</head>
<body class="p-3">

<h1>Pharmacy Dashboard</h1>
<p>Welcome, <?= htmlspecialchars($user->email) ?></p>

<?php if(Session::has('admin_original')): ?>
    <a href="/admin/return">Return to Admin</a>
<?php endif; ?>
<a href="/logout" class="ms-3">Logout</a>

<hr>

<!-- Buttons -->
<button class="btn btn-primary" onclick="openSidebar('cartSidebar')">
    Cart (<?= count($cart) ?>)
</button>

<a href="/pharmacy/orders" class="btn btn-secondary ms-2">
    Orders History
</a>

<hr>

<h2>All Listings</h2>
<div class="row">
<?php foreach($listings as $listing): ?>
    <div class="col-md-4 mb-3">
        <div class="card">
            <div class="card-body">
                <h5><?= htmlspecialchars($listing->name) ?></h5>
                <p><?= htmlspecialchars($listing->description) ?></p>
                <a href="/pharmacy/listing/<?= $listing->id ?>" class="btn btn-success">
                    View Products
                </a>
            </div>
        </div>
    </div>
<?php endforeach; ?>
</div>

<!-- CART SIDEBAR -->
<div id="cartSidebar" class="sidebar">
<div class="sidebar-main">
    <span class="close-btn" onclick="closeSidebar('cartSidebar')">&times;</span>
    <h3>My Cart</h3>

    <?php if(count($cart) > 0): ?>
        <table class="table table-sm">
            <tr>
                <th>Product</th><th>Qty</th><th>Price</th><th>Subtotal</th><th></th>
            </tr>
            <?php foreach($cart as $index => $item): ?>
            <tr>
                <td><?= $item['product_name'] ?></td>
                <td><?= $item['quantity'] ?></td>
                <td><?= $item['price'] ?></td>
                <td><?= $item['price'] * $item['quantity'] ?></td>
                <td>
                    <form method="POST" action="/pharmacy/cart/remove/<?= $index ?>">
                        <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                        <button class="btn btn-danger btn-sm">X</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>

        <form method="POST" action="/pharmacy/place-order">
            <input type="hidden" name="_token" value="<?= csrf_token() ?>">
            <button class="btn btn-success w-100">Place Order</button>
        </form>
    <?php else: ?>
        <p>Cart is empty.</p>
    <?php endif; ?>
    </div>
</div>

<script>
function openSidebar(id){ document.getElementById(id).classList.add('open'); }
function closeSidebar(id){ document.getElementById(id).classList.remove('open'); }
</script>

</body>
</html>
