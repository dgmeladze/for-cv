<?php

namespace App\Http\Controllers;

use App\Customer;
use App\OrderProduct;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::with('products')->get();
        return view('customers.index', compact('customers'));
    }

    public function submitForm(Request $request)
    {
        $customerName = $request->input('data');
        $customer = Customer::where('name', $customerName)->first();

        if (!$customer) {
            return redirect()->back()->withErrors(['error' => 'Клиент не найден']);
        }

        $orders = OrderProduct::all();
        $preferenceMatrix = [];

        foreach ($orders as $order) {
            $preferenceMatrix[$order->customer_id][$order->product_id] = $order->quantity;
        }

        $k = 3;
        $nearestNeighbors = $this->findNearestNeighbors($customer->id, $preferenceMatrix, $k);
        $recommendedProducts = $this->getRecommendedProducts($nearestNeighbors, $preferenceMatrix, $customer->id);

        $customers = Customer::with('products')->get();

        return view('customers.index', [
            'customers' => $customers,
            'recommendedProducts' => $recommendedProducts,
            'customerName' => $customerName
        ]);
    }

    private function findNearestNeighbors($customerId, $preferenceMatrix, $k)
    {
        $distances = [];
        $targetCustomerPreferences = $preferenceMatrix[$customerId] ?? [];

        foreach ($preferenceMatrix as $otherCustomerId => $preferences) {
            if ($otherCustomerId == $customerId) {
                continue;
            }

            $distance = 0;
            foreach ($preferences as $productId => $quantity) {
                $targetQuantity = $targetCustomerPreferences[$productId] ?? 0;
                $distance += pow(($quantity - $targetQuantity), 2);
            }
            $distances[$otherCustomerId] = sqrt($distance);
        }

        asort($distances);
        return array_slice(array_keys($distances), 0, $k);
    }

    private function getRecommendedProducts($nearestNeighbors, $preferenceMatrix, $customerId)
    {
        $productScores = [];

        foreach ($nearestNeighbors as $neighborId) {
            foreach ($preferenceMatrix[$neighborId] as $productId => $quantity) {
                if (!isset($preferenceMatrix[$customerId][$productId])) {
                    $productScores[$productId] = ($productScores[$productId] ?? 0) + $quantity;
                }
            }
        }

        arsort($productScores);
        return array_keys($productScores);
    }
}