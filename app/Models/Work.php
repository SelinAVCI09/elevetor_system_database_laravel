<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Work extends Model
{
    use HasFactory;

    // Zaman damgalarını kullanmıyoruz
    public $timestamps = false;

    // Tablo adı
    protected $table = 'works';

    // Mass assignment (toplu atama) için izin verilen alanlar
    protected $fillable = [
        'admin_id',   // admin_id alanı eklendi
        'text',       // text alanı eklendi
        'label',      // label alanı eklendi
        'photo_urls', // photo_urls alanı eklendi
    ];

    // JSON veriyi dizi olarak cast et
    protected $casts = [
        'photo_urls' => 'array',
    ];

    // İlk fotoğraf URL'sini döndür
    public function getFirstPhotoUrlAttribute()
    {
        // Eğer JSON içinde fotoğraflar varsa, ilk fotoğraf URL'sini döndür
        return !empty($this->photo_urls) && is_array($this->photo_urls) ? $this->photo_urls[0] : 'https://via.placeholder.com/500x300.png';
    }

    // Admin modeline ilişkili olarak tanımlayabilirsiniz
    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id'); // Admin ile ilişki tanımlandı
    }
}
