<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CloseTradeRequest extends FormRequest
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
            'date_trade_closed' => 'required|date_format:Y-m-d|before:tomorrow|after_or_equal:' . $this->input('date_trade_opened'),
            'price_closed_at' => 'required|numeric|min:0.00|max:10000000.00',
            'profit_loss' => 'required|numeric|between:-9999999999.99,9999999999.99',
        ];
    }

    public function messages(){
        return [
            'date_trade_closed.required' => 'The date trade closed field is required.',
            'date_trade_closed.date_format' => 'The date trade closed field does not match the format Y-m-d.',
            'date_trade_closed.before' => 'The date trade closed field must be a date before tomorrow.',
            'date_trade_closed.after_or_equal' => 'The date trade closed field must be a date after or equal to the date trade opened field.',
            'price_closed_at.required' => 'The priced closed at field is required.',
            'price_closed_at.numeric' =>  'The price closed at field must be a number.',
            'price_closed_at.min' => 'The price closed at field must be at least 0.00.',
            'price_closed_at.max' =>  'The price closed at field may not be greater than 1000000.00.',
            'profit_loss.required' => 'The profit/loss field is required.',
            'profit_loss.numeric' => 'The profit/loss field must be a number.',
            'profit_loss.between' => 'The profit/loss field must be within two decimal places.',
        ];
    }

}
