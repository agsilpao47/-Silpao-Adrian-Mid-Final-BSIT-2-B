<?php

namespace App\Controllers;

class Store extends BaseController
{
    public function __construct()
    {
        helper(['form', 'url', 'currency']);
    }

    public function select()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login')->with('error', 'Please log in first.');
        }

        if (strtolower($this->request->getMethod()) === 'post') {
            $storeType = (string) $this->request->getPost('store_type');
            if ($storeType !== 'lpg') {
                return redirect()->back()->withInput()->with('error', 'Only LPG store is currently available.');
            }

            session()->set('storeType', 'lpg');
            return redirect()->to('/stock/inventory')->with('success', 'LPG store selected.');
        }

        return view('auth/store_select', ['title' => 'Select Store Type']);
    }

    public function setCurrency()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login')->with('error', 'Please log in first.');
        }

        $currency = (string) $this->request->getPost('currency');
        if (!in_array($currency, ['usd', 'php'], true)) {
            return redirect()->back()->with('error', 'Invalid currency selected.');
        }

        session()->set('currency', $currency);
        return redirect()->back()->with('success', 'Currency updated successfully.');
    }
}
