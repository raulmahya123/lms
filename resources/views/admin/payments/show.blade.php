@extends('layouts.admin')
@section('title', 'Detail Pembayaran — Admin')

@section('content')
<div class="max-w-4xl mx-auto space-y-8 pb-12">
    
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-tosca-light text-tosca-dark text-xs font-bold uppercase tracking-wider mb-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Detail Pembayaran
            </div>
            <h1 class="text-3xl font-extrabold text-text-main tracking-tight">Invoice #{{ $payment->id }}</h1>
            <p class="text-sm text-text-soft mt-1">Lihat dan perbarui status transaksi dari pengguna.</p>
        </div>
        
        <div class="shrink-0 flex items-center gap-3">
            <a href="{{ route('admin.payments.index') }}" class="inline-flex items-center gap-2 px-6 py-2.5 bg-white border border-gray-200 text-gray-700 font-bold rounded-xl hover:bg-gray-50 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Kembali
            </a>
        </div>
    </div>

    @if($errors->any())
        <div class="bg-red-50 border border-red-200 rounded-2xl p-4 flex gap-3 text-sm text-red-800 shadow-sm">
            <svg class="w-5 h-5 shrink-0 mt-0.5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            <div>
                <p class="font-bold">Gagal memperbarui status!</p>
                <ul class="mt-2 list-disc pl-4 text-red-700 font-medium space-y-1">
                    @foreach ($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    @if (session('ok') || session('success'))
        <div class="bg-green-50 border border-green-200 rounded-2xl p-4 flex gap-3 text-sm text-green-800 shadow-sm">
            <svg class="w-5 h-5 shrink-0 mt-0.5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <div>
                <p class="font-bold">{{ session('ok') ?? session('success') }}</p>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        {{-- Left Column: User & Item Information --}}
        <div class="md:col-span-2 space-y-8">
            <div class="bg-white rounded-3xl border border-gray-50 shadow-[0_4px_24px_rgba(0,0,0,0.02)] overflow-hidden">
                <div class="px-8 py-5 border-b border-gray-50 bg-gray-50/50">
                    <h2 class="text-lg font-extrabold text-text-main flex items-center gap-2">
                        <svg class="w-5 h-5 text-tosca" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        Pembeli / Pengguna
                    </h2>
                </div>
                <div class="p-8 flex items-center gap-5">
                    <div class="w-16 h-16 rounded-2xl bg-tosca-light flex items-center justify-center text-tosca-dark font-extrabold text-2xl border border-tosca/20">
                        {{ strtoupper(substr($payment->user?->name ?? '?', 0, 1)) }}
                    </div>
                    <div>
                        <div class="font-bold text-text-main text-xl">{{ $payment->user?->name ?? 'Pengguna Tidak Ditemukan' }}</div>
                        <div class="text-sm text-text-soft mt-1">{{ $payment->user?->email ?? '—' }}</div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-3xl border border-gray-50 shadow-[0_4px_24px_rgba(0,0,0,0.02)] overflow-hidden">
                <div class="px-8 py-5 border-b border-gray-50 bg-gray-50/50">
                    <h2 class="text-lg font-extrabold text-text-main flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        Rincian Pesanan
                    </h2>
                </div>
                <div class="p-8">
                    <div class="flex items-start justify-between gap-4">
                        <div class="space-y-2">
                            @if($payment->plan)
                                <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold uppercase tracking-wider bg-blue-50 text-blue-700 border border-blue-100">Paket Langganan</div>
                                <div class="font-bold text-text-main text-xl">{{ $payment->plan->name }}</div>
                            @elseif($payment->course)
                                <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold uppercase tracking-wider bg-purple-50 text-purple-700 border border-purple-100">Course / Kelas</div>
                                <div class="font-bold text-text-main text-xl">{{ $payment->course->title }}</div>
                            @else
                                <div class="font-bold text-text-main text-xl">Item Tidak Diketahui</div>
                            @endif
                        </div>
                        <div class="text-right">
                            <div class="text-xs font-bold text-text-soft uppercase tracking-wider mb-1">Total Tagihan</div>
                            <div class="font-extrabold text-3xl text-tosca-dark">Rp {{ number_format($payment->amount, 0, ',', '.') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Form Update Status --}}
            <form method="POST" action="{{ route('admin.payments.update', $payment) }}" class="bg-white rounded-3xl border border-gray-50 shadow-[0_4px_24px_rgba(0,0,0,0.02)] overflow-hidden">
                @csrf @method('PUT')
                <div class="px-8 py-5 border-b border-gray-50 bg-gray-50/50">
                    <h2 class="text-lg font-extrabold text-text-main flex items-center gap-2">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        Pembaruan Transaksi
                    </h2>
                </div>
                <div class="p-8 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-text-main mb-2">Status Pembayaran <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <select name="status" class="w-full pl-4 pr-10 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-tosca focus:ring-4 focus:ring-tosca/10 outline-none transition-all font-medium text-text-main appearance-none cursor-pointer">
                                    <option value="pending" @selected(old('status', $payment->status) === 'pending')>Menunggu (Pending)</option>
                                    <option value="paid" @selected(old('status', $payment->status) === 'paid')>Berhasil (Paid)</option>
                                    <option value="failed" @selected(old('status', $payment->status) === 'failed')>Gagal (Failed)</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-text-main mb-2">Waktu Bayar (Paid At)</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </div>
                                @php
                                    $paidAtValue = old('paid_at', optional($payment->paid_at)->format('Y-m-d\TH:i'));
                                @endphp
                                <input type="datetime-local" name="paid_at" value="{{ $paidAtValue }}"
                                       class="w-full pl-11 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-tosca focus:ring-4 focus:ring-tosca/10 outline-none transition-all font-medium text-text-main">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-text-main mb-2">Penyedia (Provider)</label>
                            <input type="text" name="provider" value="{{ old('provider', $payment->provider) }}" placeholder="Misal: Midtrans, Manual, Transfer"
                                   class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 focus:bg-white focus:border-tosca focus:ring-4 focus:ring-tosca/10 outline-none transition-all font-medium text-text-main placeholder-gray-400">
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-text-main mb-2">Referensi (Reference ID)</label>
                            <input type="text" name="reference" value="{{ old('reference', $payment->reference) }}" placeholder="Kode referensi unik..."
                                   class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 focus:bg-white focus:border-tosca focus:ring-4 focus:ring-tosca/10 outline-none transition-all font-medium text-text-main placeholder-gray-400">
                        </div>
                    </div>
                </div>
                <div class="px-8 py-4 bg-gray-50/50 border-t border-gray-50 flex justify-end">
                    <button type="submit" class="px-8 py-3 rounded-xl bg-tosca text-white font-bold hover:bg-tosca-dark transition-colors shadow-sm shadow-tosca/30 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>

        {{-- Right Column: Status & Timeline --}}
        <div class="md:col-span-1 space-y-8">
            <div class="bg-white rounded-3xl border border-gray-50 shadow-[0_4px_24px_rgba(0,0,0,0.02)] overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-50 bg-gray-50/50">
                    <h2 class="text-base font-extrabold text-text-main flex items-center gap-2">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Status
                    </h2>
                </div>
                <div class="p-6">
                    @php
                        $badge = match ($payment->status) {
                            'paid' => ['bg-green-50', 'text-green-700', 'border-green-200', 'Berhasil / Paid', 'bg-green-500'],
                            'failed' => ['bg-red-50', 'text-red-700', 'border-red-200', 'Gagal / Failed', 'bg-red-500'],
                            'pending' => ['bg-amber-50', 'text-amber-700', 'border-amber-200', 'Menunggu / Pending', 'bg-amber-500'],
                            default => ['bg-gray-50', 'text-gray-700', 'border-gray-200', ucfirst($payment->status), 'bg-gray-500'],
                        };
                    @endphp
                    <span class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-bold uppercase tracking-wider border {{ $badge[0] }} {{ $badge[1] }} {{ $badge[2] }} w-full justify-center">
                        <span class="w-2 h-2 rounded-full {{ $badge[4] }}"></span>
                        {{ $badge[3] }}
                    </span>
                </div>
            </div>

            <div class="bg-white rounded-3xl border border-gray-50 shadow-[0_4px_24px_rgba(0,0,0,0.02)] overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-50 bg-gray-50/50">
                    <h2 class="text-base font-extrabold text-text-main flex items-center gap-2">
                        <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Log Data
                    </h2>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <div class="text-xs font-bold text-text-soft uppercase tracking-wider mb-1">Dibuat Pada</div>
                        <div class="font-bold text-text-main">{{ $payment->created_at?->format('d M Y, H:i') ?? '—' }}</div>
                    </div>
                    <div>
                        <div class="text-xs font-bold text-text-soft uppercase tracking-wider mb-1">Terakhir Diperbarui</div>
                        <div class="font-bold text-text-main">{{ $payment->updated_at?->format('d M Y, H:i') ?? '—' }}</div>
                    </div>
                    <div>
                        <div class="text-xs font-bold text-text-soft uppercase tracking-wider mb-1">Waktu Bayar (Sistem)</div>
                        <div class="font-bold {{ $payment->paid_at ? 'text-green-600' : 'text-gray-400' }}">{{ $payment->paid_at?->format('d M Y, H:i') ?? 'Belum ada catatan' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
