@extends('app.layouts.base')
@section('title', 'Membership Saya')

@section('content')
<div class="max-w-6xl mx-auto">
  {{-- Flash --}}
  @if(session('ok'))
    <div class="mb-4 rounded border border-green-200 bg-green-50 text-green-800 px-3 py-2 text-sm">
      {{ session('ok') }}
    </div>
  @endif
  @if(session('info'))
    <div class="mb-4 rounded border border-blue-200 bg-blue-50 text-blue-800 px-3 py-2 text-sm">
      {{ session('info') }}
    </div>
  @endif
  @if($errors->any())
    <div class="mb-4 rounded border border-red-200 bg-red-50 text-red-800 px-3 py-2 text-sm">
      {{ $errors->first() }}
    </div>
  @endif

  <header class="mb-6">
    <div class="flex items-center justify-between gap-3">
      <h1 class="text-2xl md:text-3xl font-semibold text-gray-900">Membership Saya</h1>
      <a href="{{ route('app.memberships.plans') }}"
         class="inline-flex items-center gap-2 px-3 py-2 rounded-lg border bg-white hover:bg-gray-50">
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M12 5v14m-7-7h14"/></svg>
        Lihat Paket
      </a>
    </div>
    <p class="mt-1 text-sm text-gray-600">Kelola status membership, lihat riwayat dan masa aktif.</p>
  </header>

  {{-- Kartu status terkini --}}
  <section class="mb-8">
    <div class="p-4 rounded-lg border bg-white">
      <div class="flex items-start justify-between gap-4">
        <div>
          <div class="text-sm text-gray-500">Membership aktif/terbaru</div>
          @if($current)
            <div class="mt-1 text-lg font-semibold">
              {{ $current->plan->name ?? 'Plan' }}
            </div>
            <div class="mt-2 flex items-center gap-2">
              @php
                $badge = match($current->status) {
                  'active' => 'bg-green-50 text-green-700 border-green-200',
                  'pending' => 'bg-amber-50 text-amber-700 border-amber-200',
                  default => 'bg-gray-50 text-gray-700 border-gray-200'
                };
              @endphp
              <span class="inline-flex items-center text-xs px-2 py-0.5 border rounded {{ $badge }}">
                {{ ucfirst($current->status) }}
              </span>
              @if($current->activated_at)
                <span class="text-xs text-gray-500">
                  Aktif: {{ \Illuminate\Support\Carbon::parse($current->activated_at)->format('d M Y H:i') }}
                </span>
              @endif
              @if($current->expires_at)
                <span class="text-xs text-gray-500">
                  Berakhir: {{ \Illuminate\Support\Carbon::parse($current->expires_at)->format('d M Y H:i') }}
                </span>
              @endif
            </div>
          @else
            <div class="mt-1 text-lg font-semibold text-gray-700">Belum ada membership</div>
            <p class="text-sm text-gray-500">Mulai dengan memilih paket di halaman Paket.</p>
          @endif
        </div>

        <div class="shrink-0 flex flex-col gap-2">
          @if($current)
            @if($current->status === 'pending')
              <a href="{{ route('app.memberships.checkout', $current) }}"
                 class="px-3 py-2 rounded-lg bg-purple-600 text-white hover:bg-purple-700 text-sm text-center">
                Lanjutkan Pembayaran
              </a>
              <form method="POST" action="{{ route('app.memberships.cancel', $current) }}">
                @csrf
                <button class="w-full px-3 py-2 rounded-lg border bg-white hover:bg-gray-50 text-sm"
                        onclick="return confirm('Batalkan membership ini?')">
                  Batalkan
                </button>
              </form>
            @elseif($current->status === 'active')
              <form method="POST" action="{{ route('app.memberships.cancel', $current) }}">
                @csrf
                <button class="px-3 py-2 rounded-lg border bg-white hover:bg-gray-50 text-sm"
                        onclick="return confirm('Nonaktifkan membership sekarang?')">
                  Nonaktifkan
                </button>
              </form>
            @else
              <a href="{{ route('app.memberships.plans') }}"
                 class="px-3 py-2 rounded-lg border bg-white hover:bg-gray-50 text-sm text-center">
                Pilih Paket
              </a>
            @endif
          @else
            <a href="{{ route('app.memberships.plans') }}"
               class="px-3 py-2 rounded-lg bg-gray-900 text-white hover:bg-gray-800 text-sm text-center">
              Pilih Paket
            </a>
          @endif
        </div>
      </div>
    </div>
  </section>

  {{-- Riwayat --}}
  <section>
    <div class="mb-3 flex items-center justify-between">
      <h2 class="text-base font-semibold text-gray-900">Riwayat Membership</h2>
    </div>

    @if($history->count())
      <div class="overflow-x-auto rounded border bg-white">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr class="text-left text-xs font-semibold text-gray-600">
              <th class="px-4 py-3">Plan</th>
              <th class="px-4 py-3">Status</th>
              <th class="px-4 py-3">Aktif</th>
              <th class="px-4 py-3">Berakhir</th>
              <th class="px-4 py-3"></th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100 text-sm">
            @foreach($history as $m)
              @php
                $badge = match($m->status) {
                  'active' => 'bg-green-50 text-green-700 border-green-200',
                  'pending' => 'bg-amber-50 text-amber-700 border-amber-200',
                  default => 'bg-gray-50 text-gray-700 border-gray-200'
                };
              @endphp
              <tr>
                <td class="px-4 py-3 font-medium text-gray-900">{{ $m->plan->name ?? 'Plan' }}</td>
                <td class="px-4 py-3">
                  <span class="inline-flex items-center text-xs px-2 py-0.5 border rounded {{ $badge }}">
                    {{ ucfirst($m->status) }}
                  </span>
                </td>
                <td class="px-4 py-3 text-gray-700">
                  {{ $m->activated_at ? \Illuminate\Support\Carbon::parse($m->activated_at)->format('d M Y H:i') : '—' }}
                </td>
                <td class="px-4 py-3 text-gray-700">
                  {{ $m->expires_at ? \Illuminate\Support\Carbon::parse($m->expires_at)->format('d M Y H:i') : '—' }}
                </td>
                <td class="px-4 py-3 text-right">
                  @if($m->status === 'pending')
                    <a href="{{ route('app.memberships.checkout', $m) }}"
                       class="text-blue-700 hover:underline">Checkout</a>
                  @endif
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      <div class="mt-4">
        {{ $history->links() }}
      </div>
    @else
      <div class="rounded border bg-white p-6 text-center text-sm text-gray-500">
        Belum ada riwayat membership.
      </div>
    @endif
  </section>
</div>
@endsection
