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
