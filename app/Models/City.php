<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class City extends Model
{
    protected $fillable = ['name', 'slug', 'qr_code_path'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($city) {
            if (empty($city->slug)) {
                do {
                    $slug = Str::random(10);
                } while (self::where('slug', $slug)->exists());
                $city->slug = $slug;
            }
        });

        static::created(function ($city) {
            $city->generateQrCode();
        });
    }

    /**
     * Get the registrations for the city.
     */
    public function registrations(): HasMany
    {
        return $this->hasMany(Registration::class);
    }

    /**
     * Generate QR code for this city.
     */
    public function generateQrCode()
    {
        $url = route('registration.show', $this->slug);
        $fileName = 'qrcode_' . $this->slug . '.svg';
        $directoryPath = storage_path('app/public/qrcodes');

        if (!file_exists($directoryPath)) {
            mkdir($directoryPath, 0755, true);
        }

        $filePath = $directoryPath . '/' . $fileName;

        QrCode::format('svg')
            ->size(300)
            ->margin(1)
            ->generate($url, $filePath);

        $this->qr_code_path = 'storage/qrcodes/' . $fileName;
        $this->saveQuietly();
    }
}
