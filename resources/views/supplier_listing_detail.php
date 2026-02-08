<?php
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

$user = Session::get('user');
?>

<h1>Listing: <?= $listing->name ?></h1>
<p><?= $listing->description ?></p>

<a href="/supplier/listings">Back to Listings</a>

<hr>

<h3>Products in this Listing</h3>
<?php if(count($products) > 0): ?>
    <table border="1" cellpadding="5">
<?php foreach($products as $product): ?>
<tr>
    <form method="POST" action="/supplier/listings/<?= $listing->id ?>/product/<?= $product->lp_id ?>/update">
        <input type="hidden" name="_token" value="<?= csrf_token() ?>">
        <td><input type="text" name="name" value="<?= htmlspecialchars($product->name) ?>" required></td>
        <td><textarea name="description" required><?= htmlspecialchars($product->description) ?></textarea></td>
        <td><input type="number" name="quantity" value="<?= $product->quantity ?>" min="1" required></td>
        <td><input type="number" name="price" value="<?= $product->price ?>" step="0.01" required></td>
        <td>
            <button type="submit">Update</button>
            <!-- Delete product -->
            <form method="POST" action="/supplier/listings/<?= $listing->id ?>/product/<?= $product->lp_id ?>/delete" style="display:inline;">
                <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                <button type="submit" onclick="return confirm('Delete this product?')">Delete</button>
            </form>
        </td>
    </form>
</tr>
<?php endforeach; ?>

    </table>
<?php else: ?>
    <p>No products in this listing yet.</p>
<?php endif; ?>

<hr>

<h3>Add Product to Listing</h3>
<form method="POST" action="/supplier/listings/<?= $listing->id ?>/add-product">
    <input type="hidden" name="_token" value="<?= csrf_token() ?>">

    <label>Product Name:</label>
    <input type="text" name="product_name" placeholder="Enter product name" required><br><br>

    <label>Description:</label>
    <textarea name="description" placeholder="Enter product description" required></textarea><br><br>

    <label>Price:</label>
    <input type="number" name="price" step="0.01" placeholder="Enter price" required><br><br>

    <label>Quantity:</label>
    <input type="number" name="quantity" value="1" min="1" required><br><br>

    <button type="submit">Add Product</button>
</form>
