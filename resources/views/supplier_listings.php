<?php
use Illuminate\Support\Facades\Session;

$user = Session::get('user');
?>

<h1>My Listings</h1>
<p>Welcome, <?= $user->email ?></p>
<a href="/dashboard/supplier">Back to Dashboard</a>
<hr>

<h2>Create New Listing</h2>
<form method="POST" action="/supplier/listings/create">
    <input type="hidden" name="_token" value="<?= csrf_token() ?>">
    <label>Name:</label><br>
    <input type="text" name="name" required><br>

    <label>Description:</label><br>
    <textarea name="description" required></textarea><br>

    <button type="submit">Create Listing</button>
</form>

<hr>

<h2>My Listings</h2>
<?php if(count($listings) > 0): ?>
    <ul>
        <?php foreach($listings as $listing): ?>
            <li>
                <a href="/supplier/listings/<?= $listing->id ?>">
                    <?= $listing->name ?>
                </a> - <?= $listing->description ?>
            </li>
        <?php endforeach; ?>
    </ul>
<?php else: ?>
    <p>You have no listings yet.</p>
<?php endif; ?>
