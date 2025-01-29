# CRM Randevu Sistemi

## Proje Özellikleri

### 1. Kategori Yönetimi
- Kategorileri oluşturma, düzenleme ve silme
- Her kategori için:
  - İsim tanımlama
  - Açıklama ekleme
  - Aktiflik durumu
  - Otomatik slug oluşturma
  - Varsayılan kategori koruması
- Özel Özellikler:
  - Kategori pasif yapıldığında alt slotlar otomatik pasif olur
  - Kategori silindiğinde alt slotlar varsayılan kategoriye taşınır
  - Varsayılan kategori silinemez ve her zaman aktiftir
  - Benzersiz slug sistemi (otomatik artırımlı)

### 2. Zaman Dilimi Yönetimi
- Zaman dilimlerini oluşturma, düzenleme ve silme
- Her zaman dilimi için:
  - İsim tanımlama
  - Başlangıç ve bitiş saati (24 saat formatında)
  - Randevu aralığı seçimi (2, 3, 4 veya 6 saat)
  - Maksimum randevu sayısı
  - Aktiflik durumu
  - Açıklama ekleme
  - Kategori seçimi

### 3. Randevu Yönetimi
- Detaylı randevu bilgileri:
  - Kişisel Bilgiler:
    - İsim ve Soyisim
    - Telefon (+90 formatında)
    - E-posta
    - Görüşülen Kişinin Cinsiyeti (Kadın/Erkek)
    - Hanede Yaşayan Kişi Sayısı
    - Müşteri Yaşı
  
  - Konum Bilgileri:
    - Harita üzerinden konum seçimi
    - Sokak adresi
    - Şehir
    - Posta kodu
  
  - Teknik Bilgiler:
    - Aylık elektrik tüketimi
    - Çatı fotoğrafı
    - Müşteri notları

  - Randevu Detayları:
    - Tarih seçimi
    - Zaman dilimi seçimi
    - Ekip ataması
    - Randevu durumu (Beklemede, Onaylandı, Tamamlandı, İptal Edildi)

### 4. Ekip Yönetimi
- Ekip oluşturma ve düzenleme
- Ekip üyelerini atama
- Ekiplere randevu atama

### 5. Kullanıcı Yönetimi
- Admin paneli erişimi
- Rol ve izin yönetimi
- Kullanıcı oluşturma ve düzenleme

## Teknik Özellikler

### Veritabanı Tabloları
1. `categories`: Kategori yönetimi
   - Temel bilgiler (isim, açıklama)
   - Slug sistemi
   - Aktiflik durumu
   - Soft delete özelliği

2. `time_slots`: Zaman dilimlerinin yönetimi
   - Temel bilgiler (isim, saat aralığı)
   - Randevu ayarları
   - Kategori ilişkisi
   - Soft delete özelliği

3. `appointments`: Randevu kayıtları
   - Müşteri bilgileri
   - Konum bilgileri
   - Randevu detayları
   - Soft delete özelliği

4. `teams`: Ekip yönetimi
   - Ekip bilgileri
   - Üye ilişkileri

### Kullanılan Teknolojiler
- Laravel Framework
- Orchid Admin Panel
- MySQL Veritabanı
- Google Maps Entegrasyonu (Konum seçimi için)

### Güvenlik Özellikleri
- Kullanıcı doğrulama
- Rol tabanlı yetkilendirme
- CSRF koruması
- Soft delete ile veri güvenliği

## Kurulum

1. Veritabanı migration'larını çalıştırma:
```bash
php artisan migrate:fresh
```

2. Admin kullanıcısı oluşturma:
```bash
php artisan orchid:admin admin admin@admin.com password123
```

3. Development server'ı başlatma:
```bash
php artisan serve
```

## Erişim Bilgileri

### Admin Panel
- URL: http://127.0.0.1:8000/admin
- E-posta: admin@admin.com
- Şifre: password123

## Önemli Notlar
- Randevu oluşturmadan önce kategori ve zaman dilimi tanımlanmalıdır
- Randevular için ekip ataması zorunludur
- Tamamlanan veya iptal edilen randevular düzenlenemez
- Çatı fotoğrafları için sadece resim dosyaları kabul edilir
- Telefon numaraları otomatik olarak +90 formatına dönüştürülür
- Saat seçimleri 24 saat formatında yapılır
- Varsayılan kategori silinemez ve her zaman aktiftir
- Kategori pasif yapıldığında içindeki tüm slotlar otomatik pasif olur
