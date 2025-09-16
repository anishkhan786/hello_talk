<?php 
namespace App\Http\Controllers;

use App\Models\Currencies;
use Illuminate\Http\Request;

class CurrencyController extends Controller
{
    public function index()
    {
        $currencies = Currencies::latest()->paginate(10);
        return view('admin.currencies.index', compact('currencies'));
    }

    public function create()
    {
        return view('admin.currencies.add');
    }

    public function store(Request $request)
    {
        $request->validate([
            'country_code' => 'required|string|max:50|unique:currencies,country_code',
            'currency_name' => 'required|string|max:50|unique:currencies,currency_name',
            'currency_code' => 'required|string|max:50|unique:currencies,currency_code',
            'symbol'       => 'nullable|string|max:10',
            'base_price'   => 'required|numeric',
            'is_active'    => 'boolean',
        ]);

        Currencies::create($request->all());

        return redirect()->route('currencies.index')->with('success', 'Currency created successfully.');
    }

    public function show(Currencies $currency)
    {
        return view('currencies.show', compact('currency'));
    }

    public function edit(Currencies $currency)
    {
        return view('admin.currencies.edit', compact('currency'));
    }

    public function update(Request $request, Currencies $currency)
    {
        $request->validate([
            'country_code' => 'required|string|max:50|unique:currencies,country_code,' . $currency->id,
            'currency_name' => 'required|string|max:50|unique:currencies,currency_name,' . $currency->id,
            'currency_code' => 'required|string|max:50|unique:currencies,currency_code,' . $currency->id,
            'symbol'       => 'nullable|string|max:10',
            'base_price'   => 'required|numeric',
            'is_active'    => 'boolean',
        ]);

        $currency->update($request->all());

        return redirect()->route('currencies.index')->with('success', 'Currency updated successfully.');
    }

    public function destroy(Currencies $currency)
    {
        $currency->delete();
        return redirect()->route('currencies.index')->with('success', 'Currency deleted successfully.');
    }
}
