<?php $user = Session::get('user'); ?>
<h1>Listing: <?= htmlspecialchars($listing->name) ?></h1>
<p><?= htmlspecialchars($listing->description) ?></p>
<a href="/dashboard/pharmacy">&laquo; Back to Listings</a>

<?php if(session('success')): ?>
    <p style="color:green;"><?= session('success') ?></p>
<?php endif; ?>

<hr>
<h2>Products</h2>
<?php if(count($products) > 0): ?>
    <div style="display:flex; flex-wrap:wrap; gap:15px;">
        <?php foreach($products as $product): ?>
            <div style="border:1px solid #ccc; padding:10px; width:200px; border-radius:6px;">
                <strong><?= htmlspecialchars($product->name) ?></strong><br>
                Quantity Available: <?= $product->quantity ?><br>
                Price: <?= $product->price ?><br><br>

                <!-- Add to cart -->
                <form method="POST" action="/pharmacy/listings/<?= $listing->id ?>/add-to-cart">
                    <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                    <input type="hidden" name="product_id" value="<?= $product->id ?>">
                    <input type="hidden" name="product_name" value="<?= htmlspecialchars($product->name) ?>">
                    <input type="hidden" name="price" value="<?= $product->price ?>">
                    <input type="number" name="quantity" value="1" min="1" max="<?= $product->quantity ?>" style="width:50px;">
                    <button type="submit">Add to Cart</button>
                </form>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <p>No products in this listing.</p>
<?php endif; ?>

<hr>
<h2>Cart</h2>
<?php if(!empty($cart)): ?>
    <table border="1" cellpadding="5">
        <tr>
            <th>Product</th>
            <th>Quantity</th>
            <th>Price</th>
            <th>Subtotal</th>
        </tr>
        <?php $total = 0; ?>
        <?php foreach($cart as $item): ?>
            <tr>
                <td><?= htmlspecialchars($item['product_name']) ?></td>
                <td><?= $item['quantity'] ?></td>
                <td><?= $item['price'] ?></td>
                <td><?= $item['price'] * $item['quantity'] ?></td>
            </tr>
            <?php $total += $item['price'] * $item['quantity']; ?>
        <?php endforeach; ?>
        <tr>
            <td colspan="3"><strong>Total</strong></td>
            <td><strong><?= $total ?></strong></td>
        </tr>
    </table>
    <form method="POST" action="/pharmacy/place-order" style="margin-top:10px;">
        <input type="hidden" name="_token" value="<?= csrf_token() ?>">
        <button type="submit" style="padding:5px 10px; background:#28a745; color:white; border:none; border-radius:4px;">Place Order</button>
    </form>
<?php else: ?>
    <p>Cart is empty.</p>
<?php endif; ?>
