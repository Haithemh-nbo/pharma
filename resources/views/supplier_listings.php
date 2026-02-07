<?php
$user = Session::get('user');
?>

<h1>My Listings</h1>

<!-- Create Listing Form -->
<h3>Create New Listing</h3>
<form method="POST" action="/supplier/listings/create">
    <label>Name:</label><br>
    <input type="text" name="name" required><br>
    <label>Description:</label><br>
    <textarea name="description" required></textarea><br>
    <button type="submit">Create Listing</button>
</form>

<hr>

<h3>Existing Listings</h3>
<?php if (count($listings) > 0): ?>
    <ul>
        <?php foreach ($listings as $listing): ?>
            <li>
                <strong><?= $listing->name ?></strong> - <?= $listing->description ?>
                <form method="POST" action="/supplier/listings/<?= $listing->id ?>/add-product">
                    <label>Add Product:</label>
                    <select name="product_id">
                        <?php
                        $products = DB::table('products')->where('supplier_id', $user->supplier_id)->get();
                        foreach ($products as $product):
                        ?>
                            <option value="<?= $product->id ?>"><?= $product->name ?></option>
                        <?php endforeach; ?>
                    </select>
                    <input type="number" name="quantity" value="1" min="1">
                    <button type="submit">Add Product</button>
                </form>
            </li>
        <?php endforeach; ?>
    </ul>
<?php else: ?>
    <p>No listings yet.</p>
<?php endif; ?>

<a href="/dashboard/supplier">Back to Dashboard</a>
