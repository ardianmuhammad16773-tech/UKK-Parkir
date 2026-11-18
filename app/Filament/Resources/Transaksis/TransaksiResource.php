<?php

namespace App\Filament\Resources\Transaksis;

use App\Filament\Concerns\RestrictedToRoles;
use App\Filament\Resources\Transaksis\Pages\CreateTransaksi;
use App\Filament\Resources\Transaksis\Pages\ListTransaksis;
use App\Models\AreaParkir;
use App\Models\Kendaraan;
use App\Models\Transaksi;
use BackedEnum;
use DomainException;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class TransaksiResource extends Resource
{
    use RestrictedToRoles;

    // Transaksi (masuk, keluar) dan Cetak Struk: hanya Petugas
    protected static array $roles = ['petugas'];

    protected static ?string $model = Transaksi::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowsRightLeft;

    protected static ?string $modelLabel = 'Transaksi';

    protected static ?string $pluralModelLabel = 'Transaksi';

    protected static ?string $slug = 'transaksi';

    protected static ?int $navigationSort = 1;

    // Transaksi tidak diedit atau dihapus; alurnya masuk lalu keluar.
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

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('id_kendaraan')
                ->label('Kendaraan')
                ->options(fn (): array => Kendaraan::query()
                    // Kendaraan yang masih parkir tidak boleh masuk lagi.
                    ->whereDoesntHave('transaksi', fn ($q) => $q->where('status', 'masuk'))
                    ->orderBy('plat_nomor')
                    ->get()
                    ->mapWithKeys(fn (Kendaraan $k) => [
                        $k->id_kendaraan => "{$k->plat_nomor} - {$k->pemilik} (" . ucfirst($k->jenis_kendaraan) . ')',
                    ])
                    ->all())
                ->searchable()
                ->required()
                ->helperText('Tarif dipilih otomatis sesuai jenis kendaraan. Data kendaraan dikelola oleh Admin.'),
            Select::make('id_area')
                ->label('Area Parkir')
                ->options(fn (): array => AreaParkir::query()
                    ->whereColumn('terisi', '<', 'kapasitas')
                    ->orderBy('nama_area')
                    ->get()
                    ->mapWithKeys(fn (AreaParkir $a) => [$a->id_area => "{$a->nama_area} ({$a->terisi}/{$a->kapasitas})"])
                    ->all())
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('waktu_masuk', 'desc')
            ->columns([
                TextColumn::make('kendaraan.plat_nomor')->label('Plat Nomor')->searchable(),
                TextColumn::make('kendaraan.jenis_kendaraan')->label('Jenis')->badge()->formatStateUsing(fn (string $state) => ucfirst($state)),
                TextColumn::make('area.nama_area')->label('Area'),
                TextColumn::make('waktu_masuk')->label('Masuk')->dateTime('d-m-Y H:i')->sortable(),
                TextColumn::make('waktu_keluar')->label('Keluar')->dateTime('d-m-Y H:i')->placeholder('-'),
                TextColumn::make('biaya_total')->label('Biaya')->money('IDR', locale: 'id')->placeholder('-'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'masuk' ? 'warning' : 'success')
                    ->formatStateUsing(fn (string $state): string => $state === 'masuk' ? 'Parkir' : 'Selesai'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(['masuk' => 'Sedang parkir', 'keluar' => 'Selesai'])
                    ->default('masuk'),
            ])
            ->recordActions([
                Action::make('keluar')
                    ->label('Kendaraan Keluar')
                    ->icon(Heroicon::OutlinedArrowRightOnRectangle)
                    ->color('warning')
                    ->visible(fn (Transaksi $record): bool => $record->status === 'masuk')
                    ->requiresConfirmation()
                    ->modalHeading('Kendaraan keluar')
                    ->modalDescription(function (Transaksi $record): string {
                        $hasil = $record->hitungBiaya();

                        return "Durasi {$hasil['jam']} jam. Total biaya Rp " . number_format($hasil['biaya'], 0, ',', '.') . '.';
                    })
                    ->action(function (Transaksi $record): void {
                        try {
                            $record->catatKeluar();
                        } catch (DomainException $e) {
                            Notification::make()->title($e->getMessage())->danger()->send();

                            return;
                        }

                        Notification::make()
                            ->title('Kendaraan keluar')
                            ->body('Total biaya Rp ' . number_format($record->biaya_total, 0, ',', '.') . '. Cetak struk lewat tombol "Cetak Struk".')
                            ->success()
                            ->send();
                    }),
                Action::make('struk')
                    ->label('Cetak Struk')
                    ->icon(Heroicon::OutlinedPrinter)
                    ->color('success')
                    ->visible(fn (Transaksi $record): bool => $record->status === 'keluar')
                    ->url(fn (Transaksi $record): string => route('struk.cetak', $record))
                    ->openUrlInNewTab(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTransaksis::route('/'),
            'create' => CreateTransaksi::route('/create'),
        ];
    }
}
