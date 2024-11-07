<?php

namespace Aghaeian\Iyzico\Http\Controllers;

use Webkul\Checkout\Facades\Cart;
use Webkul\Sales\Repositories\OrderRepository;
use Webkul\Sales\Transformers\OrderResource;
use Webkul\Sales\Repositories\InvoiceRepository;
use Illuminate\Http\Request;

use Aghaeian\Iyzico\Http\Controllers\IyzicoConfig;
use Iyzipay\Model\Payment;
use Iyzipay\Model\PaymentCard;
use Iyzipay\Request\CreatePaymentRequest;
use Iyzipay\Model\Locale;
use Iyzipay\Model\PaymentGroup;
use Iyzipay\Model\Buyer;
use Iyzipay\Model\Address;
use Iyzipay\Model\BasketItem;
use Iyzipay\Model\BasketItemType;

class IyzicoController extends Controller
{
    /**
     * OrderRepository $orderRepository
     *
     * @var \Webkul\Sales\Repositories\OrderRepository
     */
    protected $orderRepository;

    /**
     * InvoiceRepository $invoiceRepository
     *
     * @var \Webkul\Sales\Repositories\InvoiceRepository
     */
    protected $invoiceRepository;

    /**
     * Create a new controller instance.
     *
     * @param  \Webkul\Sales\Repositories\OrderRepository  $orderRepository
     * @param  \Webkul\Sales\Repositories\InvoiceRepository  $invoiceRepository
     * @return void
     */
    public function __construct(OrderRepository $orderRepository, InvoiceRepository $invoiceRepository)
    {
        $this->orderRepository = $orderRepository;
        $this->invoiceRepository = $invoiceRepository;
    }

    /**
     * Ödeme formunu gösterir.
     */
    public function showPaymentForm()
    {
        return view('iyzico::payment-form');
    }

    /**
     * Ödeme işlemini gerçekleştirir.
     */
    public function processPayment(Request $request)
    {
        $cart = Cart::getCart();
        $cartbillingAddress = $cart->billing_address;
        $checkoutToken = $request->session()->get('_token');

        // Ödeme isteğini oluşturma
        $paymentRequest = new CreatePaymentRequest();
        $paymentRequest->setLocale(Locale::TR);
        $paymentRequest->setConversationId($checkoutToken);
        $paymentRequest->setPrice(number_format((float)$cart->sub_total, 2, '.', ''));
        $paymentRequest->setPaidPrice(number_format((float)$cart->grand_total, 2, '.', ''));
        $currency = $cart->cart_currency_code;
        if ($currency == "TRY") $currency = "TL";
        $paymentRequest->setCurrency(constant('Iyzipay\Model\Currency::' . $currency));
        $paymentRequest->setInstallment(1);
        $paymentRequest->setBasketId($cart->id);
        $paymentRequest->setPaymentChannel('WEB');
        $paymentRequest->setPaymentGroup(PaymentGroup::PRODUCT);

        // Ödeme kartı bilgilerini ayarlama
        $paymentCard = new PaymentCard();
        $paymentCard->setCardHolderName($request->input('card_holder_name'));
        $paymentCard->setCardNumber($request->input('card_number'));
        $paymentCard->setExpireMonth($request->input('expire_month'));
        $paymentCard->setExpireYear($request->input('expire_year'));
        $paymentCard->setCvc($request->input('cvv'));
        $paymentCard->setRegisterCard(0);
        $paymentRequest->setPaymentCard($paymentCard);

        // Alıcı bilgilerini ayarlama
        $buyer = new Buyer();
        if ($cartbillingAddress->customer_id) {
            $buyer->setId($cartbillingAddress->customer_id);
        } else {
            $buyer->setId($cart->id);
        }
        $buyer->setName($cartbillingAddress->first_name);
        $buyer->setSurname($cartbillingAddress->last_name);
        $buyer->setGsmNumber($cartbillingAddress->phone);
        $buyer->setEmail($cartbillingAddress->email);
        $buyer->setIdentityNumber("74300864791"); // Gerçek bir TCKN kullanılmalıdır
        $buyer->setRegistrationAddress($cartbillingAddress->address1);
        $buyer->setIp($request->ip());
        $buyer->setCity($cartbillingAddress->city);
        $buyer->setCountry($cartbillingAddress->country);
        $buyer->setZipCode($cartbillingAddress->postcode);
        $paymentRequest->setBuyer($buyer);

        // Fatura adresini ayarlama
        $billingAddress = new Address();
        $billingAddress->setContactName($cartbillingAddress->first_name . ' ' . $cartbillingAddress->last_name);
        $billingAddress->setCity($cartbillingAddress->city);
        $billingAddress->setCountry($cartbillingAddress->country);
        $billingAddress->setAddress($cartbillingAddress->address1);
        $billingAddress->setZipCode($cartbillingAddress->postcode);
        $paymentRequest->setBillingAddress($billingAddress);

        // Kargo adresini ayarlama
        $shippingAddress = new Address();
        $shippingAddress->setContactName($cartbillingAddress->first_name . ' ' . $cartbillingAddress->last_name);
        $shippingAddress->setCity($cartbillingAddress->city);
        $shippingAddress->setCountry($cartbillingAddress->country);
        $shippingAddress->setAddress($cartbillingAddress->address1);
        $shippingAddress->setZipCode($cartbillingAddress->postcode);
        $paymentRequest->setShippingAddress($shippingAddress);

        // Sepet öğelerini ayarlama
        $basketItems = [];
        foreach ($cart->items as $item) {
            $basketItem = new BasketItem();
            $basketItem->setId($item->id);
            $basketItem->setName($item->name);
            $basketItem->setCategory1($item->type);
            $basketItem->setItemType(BasketItemType::PHYSICAL); // Ürünün türüne göre ayarlayın
            $basketItem->setPrice(number_format((float)$item->base_total, 2, '.', ''));
            $basketItems[] = $basketItem;
        }
        $paymentRequest->setBasketItems($basketItems);

        // Ödeme isteğini gerçekleştirme
        $payment = Payment::create($paymentRequest, (new IyzicoConfig)->options());

        if ($payment->getStatus() != "success") {
            session()->flash('error', $payment->getErrorCode() . ", message: " . $payment->getErrorMessage());
            return redirect()->route('shop.checkout.cart.index');
        } else {
            // Siparişi oluşturma ve yönlendirme
            $data = (new OrderResource($cart))->jsonSerialize();
            $order = $this->orderRepository->create($data);
            $this->orderRepository->update(['status' => 'processing'], $order->id);
            if ($order->canInvoice()) {
                $this->invoiceRepository->create($this->prepareInvoiceData($order));
            }
            Cart::deActivateCart();
            session()->flash('order_id', $order->id);
            return redirect()->route('shop.checkout.onepage.success', $order);
        }
    }

    /**
     * Prepares order's invoice data for creation.
     *
     * @param  \Webkul\Sales\Contracts\Order  $order
     * @return array
     */
    protected function prepareInvoiceData($order)
    {
        $invoiceData = ["order_id" => $order->id];

        foreach ($order->items as $item) {
            $invoiceData['invoice']['items'][$item->id] = $item->qty_to_invoice;
        }

        return $invoiceData;
    }
}
