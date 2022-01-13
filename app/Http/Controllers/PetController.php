<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Product;
use Darryldecode\Cart\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class PetController extends Controller
{
    public function index()
    {
        $randomProducts = Product::query()->inRandomOrder()->get();
        $product = Product::query()->inRandomOrder()->limit(1)->get();

        $sessionId = Session::getId();
        \Cart::session($sessionId);
        $cart = \Cart::getContent();
        $sum = \Cart::getTotal('price');

        return view('pet-shop/index', compact('randomProducts', 'product', 'sessionId', 'cart', 'sum'));
    }

    public function about()
    {
        return view('pet-shop/about-us');
    }

    public function shopPage()
    {
        $products = Product::get();
        return view('pet-shop/shop-page', compact('products'));
    }

    public function addCart(Request $request)
    {
        $product = Product::query()->where(['id' => $request->id])->first();
        
        $sessionId = Session::getId();
        \Cart::session($sessionId)->add([
            'id' => $product->id,
            'name' => $product->name,
            'price' => $product->price,
            'quantity' => $request->qty ?? 1,
            'attributes' => [
                'image' => $product->image,
            ]

        ]);

        $cart = \Cart::getContent();
        return redirect()->back();
    }

    public function loginRegister()
    {
        $sessionId = Session::getId();
        \Cart::session($sessionId);
        $cart = \Cart::getContent();
        $sum = \Cart::getTotal('price');

        return view('pet-shop/login-register', compact('sessionId', 'cart', 'sum'))->with('flashMessage', 'You need to login or register at first ');
    }

    public function myAccount()
    {
        $sessionId = Session::getId();
        \Cart::session($sessionId);
        $cart = \Cart::getContent();
        $sum = \Cart::getTotal('price');

        return view('pet-shop/my-account', compact('sessionId', 'cart', 'sum'));
    }

    public function checkout()
    {
        $user = auth()->user();
        $sessionId = Session::getId();
        \Cart::session($sessionId);
        $cart = \Cart::getContent();
        $sum = \Cart::getTotal('price');

        $messageSuccessOrder = \session('successOrder');

        $orders = Order::query()->where(['user_id' => $user->getAuthIdentifier()])->orderBy('id', 'desc')->get();

        $orders->transform(function($order){
            $order->cart_data = unserialize($order->cart_data);
            return $order;
        });

        if(!empty($messageSuccessOrder)) {
            return view('pet-shop/checkout', compact('sessionId', 'cart', 'sum', 'user'))->with('messageSuccessOrder', $messageSuccessOrder);
        }

        return view('pet-shop/checkout', compact('sessionId', 'cart', 'sum', 'user'))->with('messageSuccessOrder', $messageSuccessOrder);
    }

    public function makeOrder(Request $request)
    {
        $user = auth()->user();
        $sessionId = Session::getId();
        \Cart::session($sessionId);
        $cart = \Cart::getContent();
        $sum = \Cart::getTotal('price');

        $order = new Order();
        $order->user_id = $user->id;
        $order->cart_data = $order->setCartDataAttribute($cart);
        $order->total_sum = $sum;
        $order->phone = $request->phone;
        $order->adress = $request->adress . ' ' . $request->city . ' ' . $request->state . ' ' . $request->post;

        if($order->save()) {
            \Cart::clear();

            Session::flash('successOrder', 'Order created successfully');

            return back();
        }

        Session::flash('errorOrder', 'Something went wrong');

        return back();
    }
}
