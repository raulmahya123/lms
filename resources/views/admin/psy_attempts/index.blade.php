{{-- resources/views/admin/psy_attempts/index.blade.php --}}
@extends('layouts.admin')
@section('title', 'Riwayat Tes Psikologi — Admin')

@section('content')
@php
  use Carbon\Carbon;

  $q         = request('q');
  $testId    = request('test_id');
  $status    = request('status');
  $dateFrom  = request('date_from');
  $dateTo    = request('date_to');
@endphp

<div x-data="{ q:@js($q ?? ''), showFilters: {{ request()->hasAny(['q','test_id','status','date_from','date_to'])?'true':'false' }} }" class="space-y-6 pb-12">

  {{-- HEADER / ACTIONS --}}
  <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
        <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-tosca-light text-tosca-dark text-xs font-bold uppercase tracking-wider mb-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            Riwayat & Laporan
        </div>
        <h1 class="text-3xl font-extrabold text-text-main tracking-tight">Riwayat Tes Psikologi</h1>
        <p class="text-sm text-text-soft mt-1">Pantau histori pengerjaan tes, durasi, dan skor peserta (attempt).</p>
    </div>

    <div class="flex items-center gap-3">
        <button type="button" @click="showFilters=!showFilters"
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-gray-200 bg-white text-gray-700 font-bold hover:bg-gray-50 hover:border-gray-300 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
            Filter Data
        </button>
    </div>
  </div>

  {{-- FILTERS / SEARCH --}}
  <form method="GET" x-show="showFilters" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-2" style="display: none;"
        class="bg-white rounded-3xl border border-gray-50 shadow-[0_4px_24px_rgba(0,0,0,0.02)] p-6">
    
    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-6">
        <div class="md:col-span-1 lg:col-span-2">
            <label class="block text-xs font-bold text-text-soft uppercase tracking-wider mb-2">Cari Peserta / ID</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <input type="text" name="q" x-model="q" placeholder="Nama, email, ID attempt..."
                       class="w-full pl-11 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-tosca focus:ring-4 focus:ring-tosca/10 outline-none transition-all font-medium text-text-main placeholder-gray-400">
            </div>
        </div>

        <div>
            <label class="block text-xs font-bold text-text-soft uppercase tracking-wider mb-2">Pilih Tes</label>
            <div class="relative">
                <select name="test_id" class="w-full pl-4 pr-10 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-tosca focus:ring-4 focus:ring-tosca/10 outline-none transition-all font-medium text-text-main appearance-none cursor-pointer">
                    <option value="">— Semua Tes —</option>
                    @foreach($tests as $t)
                        <option value="{{ $t->id }}" @selected($testId==$t->id)>{{ Str::limit($t->title, 25) }}</option>
                    @endforeach
                </select>
                <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </div>
            </div>
        </div>

        <div>
            <label class="block text-xs font-bold text-text-soft uppercase tracking-wider mb-2">Status</label>
            <div class="relative">
                <select name="status" class="w-full pl-4 pr-10 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-tosca focus:ring-4 focus:ring-tosca/10 outline-none transition-all font-medium text-text-main appearance-none cursor-pointer">
                    <option value="">— Semua Status —</option>
                    <option value="in-progress" @selected($status==='in-progress')>Sedang Dikerjakan</option>
                    <option value="submitted"   @selected($status==='submitted')>Selesai (Submitted)</option>
                </select>
                <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </div>
            </div>
        </div>

        <div class="flex items-end gap-3 pt-4 border-t border-gray-50 md:border-none md:pt-0">
            <button class="w-full px-6 py-3 rounded-xl bg-gray-900 text-white font-bold hover:bg-black transition-colors text-center">
                Filter
            </button>
        </div>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6 pt-6 border-t border-gray-100">
        <div>
            <label class="block text-xs font-bold text-text-soft uppercase tracking-wider mb-2">Tanggal Mulai (Dari)</label>
            <input type="date" name="date_from" value="{{ $dateFrom }}" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-tosca focus:ring-4 focus:ring-tosca/10 outline-none transition-all font-medium text-text-main">
        </div>

        <div class="flex items-end gap-3">
            <div class="flex-1">
                <label class="block text-xs font-bold text-text-soft uppercase tracking-wider mb-2">Tanggal Mulai (Sampai)</label>
                <input type="date" name="date_to" value="{{ $dateTo }}" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-tosca focus:ring-4 focus:ring-tosca/10 outline-none transition-all font-medium text-text-main">
            </div>
            
            @if(request()->hasAny(['q','test_id','status','date_from','date_to']) && ($q||$testId||$status||$dateFrom||$dateTo))
                <a href="{{ route('admin.psy-attempts.index') }}" class="px-6 py-3 rounded-xl bg-white border border-gray-200 text-gray-700 font-bold hover:bg-gray-50 transition-colors text-center h-[50px] flex items-center shrink-0">
                    Reset
                </a>
            @endif
        </div>
    </div>
  </form>

  {{-- TABLE CARD --}}
  <div class="bg-white rounded-3xl border border-gray-50 shadow-[0_4px_24px_rgba(0,0,0,0.02)] overflow-hidden flex flex-col">
    <div class="p-6 border-b border-gray-50 bg-softbg/30 flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="flex flex-wrap items-center gap-2">
            <span class="text-sm font-bold text-text-main">Total {{ $attempts->total() }} Riwayat</span>
            
            @if($testId)
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider bg-blue-50 text-blue-700 border border-blue-100">
                    Filter Tes Aktif
                </span>
            @endif
            
            @if($status)
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider bg-emerald-50 text-emerald-700 border border-emerald-100">
                    Status: {{ $status === 'submitted' ? 'Selesai' : 'Sedang Dikerjakan' }}
                </span>
            @endif

            @if($dateFrom || $dateTo)
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider bg-purple-50 text-purple-700 border border-purple-100">
                    Tgl: {{ $dateFrom ? \Carbon\Carbon::parse($dateFrom)->format('d/m/y') : '...' }} - {{ $dateTo ? \Carbon\Carbon::parse($dateTo)->format('d/m/y') : '...' }}
                </span>
            @endif

            @if($q)
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider bg-amber-50 text-amber-700 border border-amber-100">
                    Pencarian: "{{ $q }}"
                </span>
            @endif
        </div>
        <div class="text-xs font-bold text-text-soft uppercase tracking-wider">
            Halaman {{ $attempts->currentPage() }} dari {{ $attempts->lastPage() }}
        </div>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full text-left border-collapse min-w-[1100px]">
        <thead>
          <tr class="bg-gray-50 border-b border-gray-100 text-xs uppercase tracking-wider text-text-soft">
            <th class="px-6 py-4 font-bold text-center">ID</th>
            <th class="px-6 py-4 font-bold">Peserta</th>
            <th class="px-6 py-4 font-bold">Tes</th>
            <th class="px-6 py-4 font-bold text-center">Durasi</th>
            <th class="px-6 py-4 font-bold">Waktu Mulai & Selesai</th>
            <th class="px-6 py-4 font-bold text-center">Skor / Hasil</th>
            <th class="px-6 py-4 font-bold text-right">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
          @forelse($attempts as $a)
            @php
              $startedAt  = $a->started_at ? Carbon::parse($a->started_at) : null;
              $submittedAt= $a->submitted_at ? Carbon::parse($a->submitted_at) : null;

              if ($startedAt) {
                $to   = $submittedAt ?: now();
                $diff = $to->diff($startedAt);
                $dur  = sprintf('%02d:%02d:%02d', ($diff->days*24)+$diff->h, $diff->i, $diff->s);
              } else {
                $dur = '—';
              }

              $scoreSummary = '—';
              $score = $a->score_json;
              if (is_array($score) && !empty($score)) {
                if (isset($score['total'])) {
                  $scoreSummary = 'Total: '.$score['total'];
                } elseif (isset($score['score'])) {
                  $scoreSummary = 'Skor: '.$score['score'];
                } else {
                  $k = array_key_first($score);
                  $scoreSummary = ucfirst($k).': '.$score[$k];
                }
              }
            @endphp
            <tr class="hover:bg-gray-50/50 transition-colors">
              <td class="px-6 py-4 text-center">
                  <div class="inline-flex items-center justify-center h-8 px-2 rounded-lg bg-gray-100 font-bold text-gray-700 text-xs font-mono">
                      #{{ $a->id }}
                  </div>
              </td>

              <td class="px-6 py-4">
                <div class="font-bold text-text-main text-sm">{{ $a->user?->name ?? '— Tidak Diketahui —' }}</div>
                @if($a->user?->email)
                    <div class="text-xs text-text-soft mt-0.5">{{ $a->user->email }}</div>
                @endif
              </td>

              <td class="px-6 py-4">
                <div class="text-sm font-bold text-text-main line-clamp-2 max-w-[200px]" title="{{ $a->test?->title ?? '' }}">
                    {{ $a->test?->title ?? '— Tes Dihapus —' }}
                </div>
              </td>

              <td class="px-6 py-4 text-center">
                <div class="inline-flex items-center gap-1.5 justify-center h-8 rounded-lg bg-gray-50 border border-gray-200 font-bold text-text-main text-sm px-3 font-mono">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    {{ $dur }}
                </div>
              </td>

              <td class="px-6 py-4">
                  <div class="flex flex-col gap-1 text-sm font-medium">
                      <div class="flex items-center gap-2">
                          <span class="w-2 h-2 rounded-full bg-blue-400"></span>
                          <span class="text-text-main">{{ $startedAt ? $startedAt->format('d M Y, H:i') : '—' }}</span>
                      </div>
                      <div class="flex items-center gap-2">
                          @if($submittedAt)
                              <span class="w-2 h-2 rounded-full bg-green-500"></span>
                              <span class="text-text-main">{{ $submittedAt->format('d M Y, H:i') }}</span>
                          @else
                              <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                              <span class="text-amber-600 font-bold text-xs italic">Belum Selesai</span>
                          @endif
                      </div>
                  </div>
              </td>

              <td class="px-6 py-4 text-center">
                  <div class="inline-flex items-center gap-1.5 justify-center min-w-[5rem] h-8 rounded-lg {{ $submittedAt ? 'bg-tosca/10 border border-tosca/20 text-tosca-dark' : 'bg-gray-100 border border-gray-200 text-gray-500' }} font-bold text-sm px-3">
                      {{ $scoreSummary }}
                  </div>
              </td>

              <td class="px-6 py-4">
                <div class="flex items-center justify-end gap-2">
                  <a href="{{ route('admin.psy-attempts.show', $a) }}" class="p-2 bg-white border border-gray-200 text-gray-700 rounded-lg hover:bg-gray-50 hover:text-tosca transition-colors" title="Lihat Detail & Laporan">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                  </a>

                  <form method="POST" action="{{ route('admin.psy-attempts.destroy', $a) }}" class="inline js-delete-form" data-title="Riwayat #{{ $a->id }}">
                      @csrf @method('DELETE')
                      <button type="button" class="js-delete-btn p-2 bg-white border border-gray-200 text-gray-400 rounded-lg hover:bg-red-50 hover:border-red-200 hover:text-red-600 transition-colors" title="Hapus Riwayat">
                          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                      </button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7">
                  <div class="py-16 text-center flex flex-col items-center justify-center">
                      <div class="w-20 h-20 rounded-3xl bg-softbg flex items-center justify-center mb-6">
                          <svg class="w-10 h-10 text-tosca" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                      </div>
                      <h3 class="text-xl font-bold text-text-main mb-2">Belum Ada Riwayat Tes</h3>
                      <p class="text-text-soft max-w-sm">Data pengerjaan tes oleh peserta akan muncul di sini.</p>
                  </div>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    @if($attempts->hasPages())
        <div class="px-6 py-4 border-t border-gray-50 bg-gray-50/50 flex justify-center">
            {{ $attempts->withQueryString()->links() }}
        </div>
    @endif
  </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    (function() {
        function bindDeleteButtons() {
            document.querySelectorAll('.js-delete-btn').forEach(btn => {
                if (btn.dataset.bound) return;
                btn.dataset.bound = '1';

                btn.addEventListener('click', (e) => {
                    const form = e.currentTarget.closest('form.js-delete-form');
                    const title = form?.dataset.title || 'data ini';

                    Swal.fire({
                        title: 'Hapus Riwayat Tes?',
                        html: `<b>${title}</b> beserta rincian jawabannya akan dihapus permanen.`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, Hapus',
                        cancelButtonText: 'Batal',
                        reverseButtons: true,
                        focusCancel: true,
                        customClass: {
                            popup: 'rounded-3xl',
                            confirmButton: 'rounded-xl font-bold px-6 py-2.5 bg-red-600 hover:bg-red-700 text-white border-0',
                            cancelButton: 'rounded-xl font-bold px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 border-0'
                        }
                    }).then((res) => {
                        if (res.isConfirmed) {
                            if (!form.dataset.submitting) {
                                form.dataset.submitting = '1';
                                const b = form.querySelector('.js-delete-btn');
                                if (b) {
                                    b.disabled = true;
                                    b.innerHTML = '<span class="animate-spin mr-2">⏳</span>';
                                }
                                form.submit();
                            }
                        }
                    });
                });
            });
        }

        document.addEventListener('DOMContentLoaded', bindDeleteButtons);
        document.addEventListener('turbo:load', bindDeleteButtons);
        document.addEventListener('livewire:navigated', bindDeleteButtons);
    })();
</script>
@if (session('ok'))
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: @json(session('ok')),
                showConfirmButton: false,
                timer: 2500,
                timerProgressBar: true,
                customClass: { popup: 'rounded-2xl' }
            });
        });
    </script>
@endif
@endpush
@endsection
