<?php
namespace App;

use App\Models\Card;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CustomCardsGeneratedExport implements FromCollection, WithMapping, WithHeadings
{
    public function collection()
    {
        return Card::all();
    }

    public function headings(): array
    {
        return [
            'rfid_no',
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
