<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EventResource\Pages;
use App\Models\Event;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Illuminate\Support\Str;

class EventResource extends Resource
{
    protected static ?string $model = Event::class;
    
    // Icon lebih modern
    protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-line';
    
    protected static ?string $navigationLabel = 'Manajemen Event';
    
    protected static ?string $navigationGroup = 'Menu Utama';

    // Biar urutan menu paling atas
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // === KOLOM KIRI (Konten Utama) ===
                Group::make()
                    ->schema([
                        Section::make('Konten Event')
                            ->description('Isi detail utama acara di sini.')
                            ->schema([
                                Forms\Components\TextInput::make('title')
                                    ->label('Judul Acara')
                                    ->placeholder('Contoh: Seminar Teknologi AI 2025')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true), // Live update kalau mau tambah slug nanti

                                Forms\Components\TextInput::make('location')
                                    ->label('Lokasi / Tempat')
                                    ->placeholder('Contoh: Aula Gedung B, Kampus UBBG')
                                    ->required()
                                    ->prefixIcon('heroicon-m-map-pin'),

                                Forms\Components\RichEditor::make('description')
                                    ->label('Deskripsi Lengkap')
                                    ->toolbarButtons([
                                        'bold', 'italic', 'underline', 'bulletList', 'orderedList', 'h2', 'h3', 'link',
                                    ])
                                    ->required()
                                    ->columnSpanFull(),
                            ]),

                        Section::make('Media Promosi')
                            ->schema([
                                Forms\Components\FileUpload::make('image')
                                    ->label('Banner / Poster')
                                    ->image()
                                    ->imageEditor() // Fitur crop gambar bawaan Filament
                                    ->directory('events')
                                    ->disk('public')
                                    ->columnSpanFull()
                                    ->required(),
                            ]),
                    ])
                    ->columnSpan(['lg' => 2]), // Pakai 2/3 layar

                // === KOLOM KANAN (Pengaturan & Meta) ===
                Group::make()
                    ->schema([
                        Section::make('Status & Jadwal')
                            ->schema([
                                Forms\Components\Select::make('status')
                                    ->options([
                                        'draft' => 'Draft (Disimpan)',
                                        'published' => 'Published (Tayang)',
                                        'closed' => 'Closed (Selesai)',
                                    ])
                                    ->default('draft')
                                    ->selectablePlaceholder(false)
                                    ->native(false)
                                    ->required(),

                                Forms\Components\DateTimePicker::make('event_date')
                                    ->label('Waktu Pelaksanaan')
                                    ->native(false) // Pakai datepicker modern
                                    ->displayFormat('d M Y, H:i')
                                    ->required(),
                            ]),

                        Section::make('Tiket & Kuota')
                            ->schema([
                                Forms\Components\TextInput::make('quota')
                                    ->label('Stok Kuota')
                                    ->numeric()
                                    ->default(100)
                                    ->prefixIcon('heroicon-m-user-group')
                                    ->required(),

                                Forms\Components\Toggle::make('is_paid')
                                    ->label('Tiket Berbayar?')
                                    ->onColor('success')
                                    ->offColor('gray')
                                    ->live()
                                    ->afterStateUpdated(fn (Set $set, $state) => $state ? null : $set('price', 0)),

                                Forms\Components\TextInput::make('price')
                                    ->label('Harga Tiket')
                                    ->prefix('Rp')
                                    ->numeric()
                                    ->default(0)
                                    // Muncul hanya jika toggle dinyalakan
                                    ->visible(fn (Get $get) => $get('is_paid')) 
                                    ->required(fn (Get $get) => $get('is_paid')),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1]), // Pakai 1/3 layar
            ])
            ->columns(3); // Total grid 3 kolom
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // 1. Gambar Kecil (Preview)
                Tables\Columns\ImageColumn::make('image')
                    ->label('Banner')
                    ->circular() // Biar bulat estetik
                    ->defaultImageUrl(url('/images/placeholder.png')),

                // 2. Judul & Lokasi (Stacked)
                Tables\Columns\TextColumn::make('title')
                    ->label('Detail Event')
                    ->description(fn (Event $record): string => Str::limit($record->location, 30))
                    ->searchable()
                    ->weight('bold')
                    ->wrap(),

                // 3. Tanggal (Format Rapi)
                Tables\Columns\TextColumn::make('event_date')
                    ->label('Waktu')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->icon('heroicon-m-calendar'),

                // 4. Kuota (Badge)
                Tables\Columns\TextColumn::make('quota')
                    ->label('Sisa Kuota')
                    ->badge()
                    ->color(fn ($state) => $state > 10 ? 'success' : 'danger')
                    ->sortable(),

                // 5. Harga
                Tables\Columns\TextColumn::make('price')
                    ->label('Harga')
                    ->money('IDR')
                    ->color(fn ($state) => $state == 0 ? 'success' : 'warning')
                    ->formatStateUsing(fn ($state) => $state == 0 ? 'GRATIS' : 'Rp '.number_format($state,0,',','.')),

                // 6. Status (Bisa diedit langsung di tabel)
                Tables\Columns\SelectColumn::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'published' => 'Tayang',
                        'closed' => 'Selesai',
                    ])
                    ->selectablePlaceholder(false)
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                // Filter Status
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'published' => 'Published',
                        'closed' => 'Closed',
                    ]),
                // Filter Berbayar/Gratis
                Tables\Filters\TernaryFilter::make('is_paid')
                    ->label('Jenis Tiket')
                    ->trueLabel('Berbayar')
                    ->falseLabel('Gratis')
                    ->queries(
                        true: fn ($query) => $query->where('price', '>', 0),
                        false: fn ($query) => $query->where('price', 0),
                    ),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            // Nanti kita bisa tambah relasi pendaftar di sini
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEvents::route('/'),
            'create' => Pages\CreateEvent::route('/create'),
            'edit' => Pages\EditEvent::route('/{record}/edit'),
        ];
    }
}