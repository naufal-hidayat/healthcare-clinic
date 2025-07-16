@extends('layouts.app')

@section('title', 'HealthCare Plus - Klinik Kesehatan Modern Terpercaya')

@section('content')
<!-- Header -->
<header class="header">
    <nav class="nav container">
        <a href="{{ route('home') }}" class="logo">
            <i class="fas fa-heart-pulse"></i> HealthCare Plus
        </a>
        <ul class="nav-links">
            <li><a href="#home">Beranda</a></li>
            <li><a href="#services">Layanan</a></li>
            <li><a href="#doctors">Dokter</a></li>
            <li><a href="#about">Tentang</a></li>
            <li><a href="#contact">Kontak</a></li>
        </ul>
        <button class="mobile-menu-btn">
            <i class="fas fa-bars"></i>
        </button>
    </nav>
</header>

<!-- Hero Section -->
<section id="home" class="hero">
    <div class="container">
        <div class="hero-content">
            <h1>Kesehatan Terbaik untuk Keluarga Anda</h1>
            <p>Klinik modern dengan teknologi terdepan, tenaga medis berpengalaman, dan pelayanan yang mengutamakan kenyamanan pasien</p>
            <a href="#contact" class="cta-button">
                <i class="fas fa-calendar-check"></i> Buat Janji Sekarang
            </a>
        </div>
    </div>
</section>

<!-- Services Section -->
<section id="services" class="section services">
    <div class="container">
        <h2 class="section-title">Layanan Unggulan Kami</h2>
        <p class="section-subtitle">
            Kami menyediakan layanan kesehatan komprehensif dengan standar medis internasional dan teknologi terkini
        </p>
        
        <div class="services-grid">
            @foreach($services as $category => $categoryServices)
                @foreach($categoryServices as $service)
                <div class="service-card">
                    <div class="service-icon">
                        @switch($service->category)
                            @case('konsultasi')
                                <i class="fas fa-stethoscope"></i>
                                @break
                            @case('laboratorium')
                                <i class="fas fa-microscope"></i>
                                @break
                            @case('checkup')
                                <i class="fas fa-clipboard-check"></i>
                                @break
                            @case('dental')
                                <i class="fas fa-tooth"></i>
                                @break
                            @case('farmasi')
                                <i class="fas fa-pills"></i>
                                @break
                            @default
                                <i class="fas fa-hospital"></i>
                        @endswitch
                    </div>
                    <h3>{{ $service->name }}</h3>
                    <p>{{ $service->description }}</p>
                    <div class="service-price">
                        Rp {{ number_format($service->price, 0, ',', '.') }}
                    </div>
                    <small style="color: #666; margin-top: 0.5rem; display: block;">
                        <i class="fas fa-clock"></i> {{ $service->duration }}
                    </small>
                </div>
                @endforeach
            @endforeach
        </div>
    </div>
</section>

<!-- Doctors Section -->
<section id="doctors" class="section">
    <div class="container">
        <h2 class="section-title">Tim Dokter Berpengalaman</h2>
        <p class="section-subtitle">
            Dokter-dokter terbaik dengan pengalaman bertahun-tahun siap memberikan pelayanan kesehatan terbaik untuk Anda
        </p>
        
        <div class="doctors-grid">
            @foreach($doctors as $doctor)
            <div class="doctor-card">
                <div class="doctor-avatar">
                    <i class="fas fa-user-md"></i>
                </div>
                <div class="doctor-info">
                    <h3 class="doctor-name">Dr. {{ $doctor->name }}</h3>
                    <p class="doctor-specialty">
                        <i class="fas fa-certificate"></i> {{ $doctor->specialization }}
                    </p>
                    <p class="doctor-experience">
                        <i class="fas fa-calendar-alt"></i> {{ $doctor->experience }}
                    </p>
                    <div style="margin-top: 1rem;">
                        <small style="color: #666;">
                            <i class="fas fa-graduation-cap"></i> {{ Str::limit($doctor->education, 50) }}
                        </small>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- About Section -->
<section id="about" class="section">
    <div class="container">
        <h2 class="section-title">Tentang HealthCare Plus</h2>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 3rem; align-items: center; margin-bottom: 3rem;">
            <div>
                <h3 style="font-size: 1.5rem; margin-bottom: 1rem; color: var(--primary-color);">
                    Komitmen Kami untuk Kesehatan Anda
                </h3>
                <p style="margin-bottom: 1rem; line-height: 1.8;">
                    HealthCare Plus adalah klinik kesehatan modern yang berkomitmen memberikan pelayanan kesehatan terbaik untuk masyarakat. Dengan tim medis yang berpengalaman dan fasilitas berstandar internasional, kami siap melayani kebutuhan kesehatan Anda dan keluarga.
                </p>
                <p style="margin-bottom: 1rem; line-height: 1.8;">
                    Kami menggunakan teknologi medis terkini dan pendekatan holistik untuk memastikan setiap pasien mendapat perawatan yang optimal. Kepuasan dan kesembuhan pasien adalah prioritas utama kami.
                </p>
            </div>
            <div style="background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); height: 300px; border-radius: 15px; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.5rem; text-align: center;">
                <div>
                    <i class="fas fa-hospital" style="font-size: 4rem; margin-bottom: 1rem; display: block;"></i>
                    Fasilitas Modern & Nyaman
                </div>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 2rem;">
            <div style="text-align: center; padding: 2rem; background: var(--bg-light); border-radius: 15px;">
                <i class="fas fa-eye" style="font-size: 3rem; color: var(--primary-color); margin-bottom: 1rem;"></i>
                <h4 style="margin-bottom: 1rem; color: var(--text-dark);">Visi</h4>
                <p style="color: var(--text-light);">
                    Menjadi klinik kesehatan terdepan yang memberikan pelayanan berkualitas tinggi dengan sentuhan kemanusiaan.
                </p>
            </div>
            <div style="text-align: center; padding: 2rem; background: var(--bg-light); border-radius: 15px;">
                <i class="fas fa-bullseye" style="font-size: 3rem; color: var(--primary-color); margin-bottom: 1rem;"></i>
                <h4 style="margin-bottom: 1rem; color: var(--text-dark);">Misi</h4>
                <p style="color: var(--text-light);">
                    Memberikan layanan kesehatan komprehensif, terjangkau, dan berkualitas tinggi untuk meningkatkan kualitas hidup masyarakat.
                </p>
            </div>
            <div style="text-align: center; padding: 2rem; background: var(--bg-light); border-radius: 15px;">
                <i class="fas fa-heart" style="font-size: 3rem; color: var(--primary-color); margin-bottom: 1rem;"></i>
                <h4 style="margin-bottom: 1rem; color: var(--text-dark);">Nilai</h4>
                <p style="color: var(--text-light);">
                    Integritas, empati, keunggulan medis, dan inovasi dalam setiap aspek pelayanan kesehatan.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Contact Section -->
<section id="contact" class="section contact">
    <div class="container">
        <h2 class="section-title">Hubungi Kami</h2>
        <p class="section-subtitle">
            Kami siap melayani Anda 24/7. Jangan ragu untuk menghubungi kami untuk konsultasi atau darurat medis
        </p>
        
        <div class="contact-grid">
            <div class="contact-info">
                <h3 style="margin-bottom: 1.5rem; color: var(--primary-color);">
                    <i class="fas fa-info-circle"></i> Informasi Kontak
                </h3>
                
                <div class="contact-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <div>
                        <strong>Alamat</strong>
                        Jl. Kesehatan No. 123, Jakarta Selatan 12345<br>
                        <small style="color: #666;">Dekat dengan stasiun MRT dan akses jalan utama</small>
                    </div>
                </div>
                
                <div class="contact-item">
                    <i class="fas fa-phone"></i>
                    <div>
                        <strong>Telepon</strong>
                        (021) 1234-5678<br>
                        <small style="color: #666;">Senin - Sabtu: 08:00 - 20:00</small>
                    </div>
                </div>
                
                <div class="contact-item">
                    <i class="fas fa-envelope"></i>
                    <div>
                        <strong>Email</strong>
                        info@healthcareplus.com<br>
                        <small style="color: #666;">Respons dalam 24 jam</small>
                    </div>
                </div>
                
                <div class="contact-item">
                    <i class="fas fa-clock"></i>
                    <div>
                        <strong>Jam Operasional</strong>
                        Senin - Sabtu: 08:00 - 20:00<br>
                        Minggu: 08:00 - 16:00<br>
                        {{-- <small style="color: var(--success-color);">
                            <i class="fas fa-circle status-online"></i> Saat ini: Buka
                        </small> --}}
                    </div>
                </div>
            </div>
            
            <div class="contact-info">
                <h3 style="margin-bottom: 1.5rem; color: var(--error-color);">
                    <i class="fas fa-ambulance"></i> Layanan Darurat
                </h3>
                
                <div style="background: linear-gradient(135deg, var(--error-color), #ff6b6b); color: white; padding: 1.5rem; border-radius: 15px; margin-bottom: 1.5rem; text-align: center;">
                    <h4 style="margin-bottom: 1rem;">
                        <i class="fas fa-phone-alt"></i> DARURAT 24 JAM
                    </h4>
                    <p style="font-size: 1.5rem; font-weight: bold; margin-bottom: 0.5rem;">
                        (021) 999-8888
                    </p>
                    <small>Respons cepat untuk kondisi darurat</small>
                </div>
                
                <div style="background: var(--bg-light); padding: 1.5rem; border-radius: 15px;">
                    <h4 style="margin-bottom: 1rem; color: var(--text-dark);">
                        <i class="fas fa-exclamation-triangle"></i> Kondisi Darurat
                    </h4>
                    <ul style="list-style: none; color: var(--text-light);">
                        <li style="margin-bottom: 0.5rem;">
                            <i class="fas fa-chevron-right" style="color: var(--error-color); margin-right: 0.5rem;"></i>
                            Kesulitan bernapas
                        </li>
                        <li style="margin-bottom: 0.5rem;">
                            <i class="fas fa-chevron-right" style="color: var(--error-color); margin-right: 0.5rem;"></i>
                            Nyeri dada hebat
                        </li>
                        <li style="margin-bottom: 0.5rem;">
                            <i class="fas fa-chevron-right" style="color: var(--error-color); margin-right: 0.5rem;"></i>
                            Kehilangan kesadaran
                        </li>
                        <li style="margin-bottom: 0.5rem;">
                            <i class="fas fa-chevron-right" style="color: var(--error-color); margin-right: 0.5rem;"></i>
                            Perdarahan hebat
                        </li>
                    </ul>
                </div>
                
                <div style="margin-top: 1rem; padding: 1rem; background: var(--primary-color); color: white; border-radius: 10px; text-align: center;">
                    <strong>💬 Atau gunakan chatbot kami untuk pertanyaan cepat!</strong>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Footer -->
<footer class="footer">
    <div class="container">
        <div class="footer-content">
            <div class="footer-section">
                <h3><i class="fas fa-heart-pulse"></i> HealthCare Plus</h3>
                <p>
                    Klinik kesehatan modern yang mengutamakan kualitas pelayanan dan kepuasan pasien. 
                    Kesehatan Anda adalah prioritas utama kami.
                </p>
                <div style="margin-top: 1rem;">
                    <strong>🏆 Terakreditasi Nasional</strong><br>
                    <small>Standar pelayanan kesehatan terbaik</small>
                </div>
            </div>
            
            <div class="footer-section">
                <h3>Layanan Unggulan</h3>
                <ul>
                    <li><a href="#services">Konsultasi Dokter</a></li>
                    <li><a href="#services">Laboratorium</a></li>
                    <li><a href="#services">Medical Check-up</a></li>
                    <li><a href="#services">Kesehatan Gigi</a></li>
                    <li><a href="#services">Farmasi</a></li>
                </ul>
            </div>
            
            <div class="footer-section">
                <h3>Kontak Cepat</h3>
                <ul>
                    <li><i class="fas fa-phone"></i> (021) 1234-5678</li>
                    <li><i class="fas fa-ambulance"></i> (021) 999-8888</li>
                    <li><i class="fas fa-envelope"></i> info@healthcareplus.com</li>
                    <li><i class="fas fa-map-marker-alt"></i> Jakarta Selatan</li>
                </ul>
            </div>
            
            <div class="footer-section">
                <h3>Ikuti Kami</h3>
                <p>Dapatkan tips kesehatan dan informasi terbaru</p>
                <div style="margin-top: 1rem;">
                    <a href="#" style="margin-right: 1rem; font-size: 1.5rem;">
                        <i class="fab fa-facebook"></i>
                    </a>
                    <a href="#" style="margin-right: 1rem; font-size: 1.5rem;">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="#" style="margin-right: 1rem; font-size: 1.5rem;">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="#" style="font-size: 1.5rem;">
                        <i class="fab fa-youtube"></i>
                    </a>
                </div>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p>&copy; {{ date('Y') }} HealthCare Plus. Semua hak dilindungi. | Dibuat dengan ❤️ untuk kesehatan Anda</p>
        </div>
    </div>
</footer>
@endsection