<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EventResource\Pages;
use App\Models\Event;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

// IMPORT BARU (WAJIB ADA BIAR LOGIC JALAN)
use Filament\Forms\Get;
use Filament\Forms\Set;

// Import Layout Grid
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;

class EventResource extends Resource
{
    protected static ?string $model = Event::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';
    protected static ?string $navigationLabel = 'Manajemen Event';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Detail Acara')
                    ->description('Informasi utama mengenai event.')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Judul Event')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Select::make('status')
                            ->options([
                                'draft' => 'Draft (Konsep)',
                                'published' => 'Published (Tayang)',
                                'closed' => 'Closed (Selesai)',
                            ])
                            ->required()
                            ->default('draft'),
                            
                        Forms\Components\DateTimePicker::make('event_date')
                            ->label('Tanggal & Jam')
                            ->required(),

                        Forms\Components\TextInput::make('location')
                            ->label('Lokasi')
                            ->required()
                            ->maxLength(255),

                        // --- BAGIAN KUOTA & HARGA PINTAR ---
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('quota')
                                    ->label('Kuota Peserta')
                                    ->numeric()
                                    ->required(),

                                // 1. LOGIC GRATIS vs BAYAR
                                Forms\Components\Radio::make('is_paid')
                                    ->label('Jenis Tiket')
                                    ->boolean()
                                    ->options([
                                        0 => 'Gratis',
                                        1 => 'Berbayar',
                                    ])
                                    // Logic: Kalau harga > 0, otomatis kepilih Berbayar saat Edit
                                    ->formatStateUsing(fn (?Event $record) => $record?->price > 0 ? 1 : 0)
                                    ->dehydrated(false) // Gak disimpan ke DB (Cuma alat bantu UI)
                                    ->live()
                                    ->afterStateUpdated(function ($state, Set $set) {
                                        if ($state == 0) {
                                            $set('price', 0); // Kalau Gratis, paksa harga jadi 0
                                        }
                                    }),

                                // 2. INPUT HARGA (Hanya muncul kalau Berbayar)
                                Forms\Components\TextInput::make('price')
                                    ->label('Nominal Harga')
                                    ->prefix('Rp')
                                    ->numeric()
                                    ->default(0)
                                    ->live()
                                    ->hint(fn ($state) => $state ? 'Terbaca: Rp ' . number_format($state, 0, ',', '.') : '') 
                                    ->visible(fn (Get $get) => $get('is_paid') == 1) // Sembunyi kalau Gratis
                                    ->required(fn (Get $get) => $get('is_paid') == 1),
                            ]),

                    ])->columns(2),

                Forms\Components\Section::make('Media & Deskripsi')
                    ->schema([
                        Forms\Components\FileUpload::make('banner')
                            ->label('Poster Event')
                            ->image()
                            ->directory('banners')
                            ->disk('public')       
                            ->visibility('public') 
                            ->required()
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('description')
                            ->label('Deskripsi Lengkap')
                            ->rows(5)
                            ->required()
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Stack::make([
                    // 1. BAGIAN GAMBAR
                    ImageColumn::make('banner')
                        ->disk('public')
                        ->width('100%')
                        ->height('auto')
                        ->extraImgAttributes([
                            'class' => 'w-full rounded-t-xl',
                        ]),

                    // 2. BAGIAN DETAIL INFO
                    Stack::make([
                        TextColumn::make('title')
                            ->weight('bold')
                            ->size('lg')
                            ->color('primary')
                            ->searchable()
                            ->extraAttributes(['class' => 'mt-2']),

                        TextColumn::make('description')
                            ->color('gray')
                            ->size('sm')
                            ->lineClamp(2)
                            ->extraAttributes(['class' => 'mb-2']),

                        TextColumn::make('event_date')
                            ->formatStateUsing(fn ($state) => $state->format('d M Y • H:i') . ' WIB')
                            ->icon('heroicon-m-calendar')
                            ->color('gray')
                            ->size('sm'),

                        TextColumn::make('location')
                            ->icon('heroicon-m-map-pin')
                            ->color('gray')
                            ->limit(30)
                            ->size('sm'),

                        // Tampilan Harga
                        TextColumn::make('price')
                            ->formatStateUsing(fn ($state) => $state == 0 ? 'GRATIS' : 'Rp ' . number_format($state, 0, ',', '.'))
                            ->weight('bold')
                            ->color('success')
                            ->size('md')
                            ->extraAttributes(['class' => 'mt-2']),
                        
                        Split::make([
                            TextColumn::make('status')
                                ->badge()
                                ->color(fn (string $state): string => match ($state) {
                                    'draft' => 'gray',
                                    'published' => 'success',
                                    'closed' => 'danger',
                                }),
                                
                            TextColumn::make('quota')
                                ->prefix('Sisa: ')
                                ->weight('bold')
                                ->alignRight(),
                        ])->extraAttributes(['class' => 'mt-2 pt-2 border-t border-gray-100 dark:border-gray-700']),

                    ])->space(1)->extraAttributes(['class' => 'p-4 bg-white dark:bg-gray-800 rounded-b-xl border-x border-b border-gray-200 dark:border-gray-700 shadow-sm']), 
                    
                ])->space(0)
            ])
            ->contentGrid([
                'md' => 2,
                'xl' => 3,
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ])
                ->icon('heroicon-m-ellipsis-vertical')
                ->tooltip('Menu Aksi'),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
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