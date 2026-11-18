<?php

namespace App\Filament\Resources\RekapTransaksis;

use App\Filament\Concerns\RestrictedToRoles;
use App\Filament\Resources\RekapTransaksis\Pages\ListRekapTransaksis;
use App\Models\Transaksi;
use BackedEnum;
use Filament\Forms\Components\DatePicker;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\Summarizers\Count;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class RekapTransaksiResource extends Resource
{
    use RestrictedToRoles;

    // Rekap Transaksi: hanya Owner
    protected static array $roles = ['owner'];

    protected static ?string $model = Transaksi::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBar;

    protected static ?string $modelLabel = 'Rekap Transaksi';

    protected static ?string $pluralModelLabel = 'Rekap Transaksi';

    protected static ?string $slug = 'rekap-transaksi';

    // Owner hanya melihat rekap.
    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return $table
            // Hanya transaksi yang sudah selesai (sudah ada biaya).
            ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'keluar'))
            ->defaultSort('waktu_keluar', 'desc')
            ->columns([
                TextColumn::make('kendaraan.plat_nomor')
                    ->label('Plat Nomor')
                    ->searchable()
                    ->summarize(Count::make()->label('Total transaksi')),
                TextColumn::make('kendaraan.jenis_kendaraan')->label('Jenis')->badge()->formatStateUsing(fn (string $state) => ucfirst($state)),
                TextColumn::make('area.nama_area')->label('Area'),
                TextColumn::make('petugas.nama_lengkap')->label('Petugas')->placeholder('-'),
                TextColumn::make('waktu_masuk')->label('Masuk')->dateTime('d-m-Y H:i')->sortable(),
                TextColumn::make('waktu_keluar')->label('Keluar')->dateTime('d-m-Y H:i')->sortable(),
                TextColumn::make('durasi_jam')->label('Durasi (jam)'),
                TextColumn::make('biaya_total')
                    ->label('Biaya')
                    ->money('IDR', locale: 'id')
                    ->sortable()
                    ->summarize(Sum::make()->label('Total pendapatan')->money('IDR', locale: 'id')),
            ])
            ->filters([
                Filter::make('periode')
                    ->label('Periode')
                    ->schema([
                        DatePicker::make('dari')->label('Dari tanggal'),
                        DatePicker::make('sampai')->label('Sampai tanggal'),
                    ])
                    ->query(fn (Builder $query, array $data): Builder => $query
                        ->when($data['dari'] ?? null, fn (Builder $q, $tgl) => $q->whereDate('waktu_keluar', '>=', $tgl))
                        ->when($data['sampai'] ?? null, fn (Builder $q, $tgl) => $q->whereDate('waktu_keluar', '<=', $tgl))),
                SelectFilter::make('jenis')
                    ->label('Jenis kendaraan')
                    ->options(['motor' => 'Motor', 'mobil' => 'Mobil', 'lainnya' => 'Lainnya'])
                    ->query(fn (Builder $query, array $data): Builder => filled($data['value'] ?? null)
                        ? $query->whereHas('kendaraan', fn ($k) => $k->where('jenis_kendaraan', $data['value']))
                        : $query),
                SelectFilter::make('id_area')->label('Area')->relationship('area', 'nama_area'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRekapTransaksis::route('/'),
        ];
    }
}
