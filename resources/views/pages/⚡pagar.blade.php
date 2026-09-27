<?php

use Livewire\Component;

new class extends Component {
    //
};
?>

<form action="/stripe/checkout" method="POST">
    <!-- Note: If using PHP set the action to /create-checkout-session.php -->
    <button type="submit">Checkout</button>
</form>
