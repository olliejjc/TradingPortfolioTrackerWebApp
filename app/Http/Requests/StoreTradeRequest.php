<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTradeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(){
        return [
            'asset_name' => 'required',
            'trade_size' => 'required|numeric|min:0.00|max:10000000.00',
            'trade_value' => 'required|numeric|min:0.00|max:100000000.00',
            'date_trade_opened' => 'required|date_format:Y-m-d|before:tomorrow|after_or_equal:2017-01-01',
            'price_purchased_at' => 'required|numeric|min:0.00|max:10000000.00',
            'screenshots.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ];
    }
}
