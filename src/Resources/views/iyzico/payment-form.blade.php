<!-- resources/views/iyzico/payment-form.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <title>Ödeme Sayfası</title>
</head>
<body>
    <form method="POST" action="{{ route('iyzico.payment.process') }}">
        @csrf
        <label for="card_holder_name">Kart Sahibi Adı:</label>
        <input type="text" name="card_holder_name" required>

        <label for="card_number">Kart Numarası:</label>
        <input type="text" name="card_number" required>

        <label for="expire_month">Son Kullanma Ayı (MM):</label>
        <input type="text" name="expire_month" required>

        <label for="expire_year">Son Kullanma Yılı (YY):</label>
        <input type="text" name="expire_year" required>

        <label for="cvv">CVV:</label>
        <input type="text" name="cvv" required>

        <button type="submit">Ödemeyi Tamamla</button>
    </form>
</body>
</html>
