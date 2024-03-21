<?php
namespace App;

use App\Models\Card;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CustomCardsGeneratedExport implements FromCollection, WithMapping, WithHeadings
{
    public function collection()
    {

        $num  = Session::get('cards_num');
        return Card::where('auto_generate', '1')->latest()->take($num)->get();
    }

    public function headings(): array
    {
        return [
            'Virtual Card No.',
        ];
    }

    /**
     * @var Card $card
     */
    public function map($card): array
    {
        return [
            $card->rfid_no
        ];
    }
}
