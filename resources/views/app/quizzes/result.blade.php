    @extends('app.layouts.base')
    @section('title','Hasil Kuis')

    @section('content')
    <h1 class="text-xl font-semibold">Hasil Kuis</h1>

        {{-- Flash status khusus quiz --}}
    @if(session('quiz_status'))
    <div class="p-3 mt-3 border rounded text-emerald-800 bg-emerald-50">
        {{ session('quiz_status') }}
    </div>
    @endif


    @php
        // Data untuk banner & tombol
        $attemptNo = min($submittedCount ?? 0, $maxAttempts ?? 2);
        // Kunci unduh berdasarkan NILAI ATTEMPT INI (>=80 baru bisa unduh)
        $canDownload = isset($percent) ? ($percent >= 80) : false;
        // Tampilan cooldown maksimal 3 digit
        $cooldownRemain = (int)($cooldownRemain ?? 0);
        $cooldownRemainDisplay = $cooldownRemain > 999 ? '999+' : (string) $cooldownRemain;

        $retryUrl = route('app.quiz.start', $attempt->quiz->lesson);
    @endphp

    {{-- Banner info attempt & aksi --}}
    <div class="p-4 mt-3 border rounded-lg bg-gray-50">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="text-sm text-gray-600">
            Percobaan: <span class="font-semibold">{{ $attemptNo }}</span> / <span class="font-semibold">{{ $maxAttempts }}</span>.
            </div>

            @if(($remainAttempts ?? 0) > 0)
            <div class="mt-1 text-sm text-amber-700">
                Sisa percobaan: <span class="font-semibold">{{ $remainAttempts }}</span>.
                Hindari klik berulang—sistem membatasi total percobaan.
            </div>
            @elseif((!($hasPassed ?? false)) && $cooldownRemain > 0)
            <div class="mt-1 text-sm text-rose-700" id="lockText">
                Batas {{ $maxAttempts }} percobaan tercapai. Terkunci
                <span id="lockRemain" data-raw="{{ $cooldownRemain }}">{{ $cooldownRemainDisplay }}</span> detik.
            </div>
            @else
            <div class="mt-1 text-sm text-rose-700">
                Batas {{ $maxAttempts }} percobaan tercapai. Anda tidak dapat mencoba lagi.
            </div>
            @endif
        </div>

        {{-- Tombol aksi --}}
        <div class="flex items-center gap-2">
            @if(($remainAttempts ?? 0) > 0)
            <a href="{{ $retryUrl }}"
                class="inline-flex items-center px-4 py-2 text-white bg-indigo-600 rounded hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-400"
                onclick="this.classList.add('pointer-events-none','opacity-60'); this.textContent='Memulai...';">
                Percobaan kembali
            </a>
            @elseif((!($hasPassed ?? false)) && $cooldownRemain > 0)
            {{-- Saat cooldown: tampilkan tombol disabled yang otomatis aktif ketika timer 0 --}}
            <button type="button"
                    id="retryBtnDisabled"
                    class="inline-flex items-center px-4 py-2 text-gray-600 bg-gray-300 rounded cursor-not-allowed"
                    disabled>
                Percobaan kembali
            </button>
            <a href="{{ $retryUrl }}"
                id="retryBtnActive"
                class="inline-flex items-center hidden px-4 py-2 text-white bg-indigo-600 rounded hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                Percobaan kembali
            </a>
            @else
            <button type="button"
                    class="inline-flex items-center px-4 py-2 text-gray-600 bg-gray-300 rounded cursor-not-allowed"
                    disabled>
                Percobaan kembali
            </button>
            @endif
        </div>
        </div>
    </div>

    {{-- Ringkasan nilai --}}
    <div class="grid gap-3 mt-3 sm:grid-cols-3">
        <div class="p-4 bg-white border rounded-lg">
        <div class="text-xs text-gray-500">Skor Attempt Ini</div>
        <div class="mt-1 text-xl font-semibold">{{ $attempt->score }}</div>
        </div>
        <div class="p-4 bg-white border rounded-lg">
        <div class="text-xs text-gray-500">% Benar Attempt Ini</div>
        <div class="mt-1 text-xl font-semibold">
            {{ isset($percent) ? number_format($percent,2) : '-' }}%
            @isset($correct)
            <span class="text-sm text-gray-500">({{ $correct }}/{{ $total }})</span>
            @endisset
        </div>
        </div>
        <div class="p-4 bg-white border rounded-lg">
        <div class="text-xs text-gray-500">% Benar Terbaik (Semua Attempt)</div>
        <div class="mt-1 text-xl font-semibold">
            {{ isset($best_percent) ? number_format($best_percent,2) : (isset($percent) ? number_format($percent,2) : '-') }}%
            @isset($best_correct)
            <span class="text-sm text-gray-500">({{ $best_correct }}/{{ $best_total }})</span>
            @endisset
        </div>
        </div>
    </div>

    {{-- Status sertifikat + kunci unduh --}}
    @if($canDownload)
        <div class="p-4 mt-4 border rounded bg-emerald-50 text-emerald-800">
        <div class="font-semibold">Selamat! Kamu memenuhi syarat sertifikat 🎉</div>
        <p class="mt-1 text-sm">Minimal 80% benar telah tercapai.</p>
        <a href="{{ route('app.certificate.course', $course) }}"
            class="inline-block px-4 py-2 mt-3 text-white rounded bg-emerald-600 hover:bg-emerald-700">
            Unduh Sertifikat
        </a>
        </div>
    @else
        <div class="p-4 mt-4 border rounded bg-rose-50 text-rose-800">
        <div class="font-semibold">Belum memenuhi syarat sertifikat</div>
        <p class="mt-1 text-sm">
            Butuh minimal <span class="font-semibold">80%</span> jawaban benar dari soal pilihan ganda pada attempt ini.
        </p>
        @isset($best_percent)
            <p class="mt-1 text-xs text-rose-700">Persentase terbaikmu saat ini: {{ number_format($best_percent,2) }}%.</p>
        @endisset

        {{-- Tombol unduh dikunci --}}
        <button type="button"
                class="inline-flex items-center gap-2 px-4 py-2 mt-3 text-gray-600 bg-gray-300 rounded cursor-not-allowed"
                title="Terkunci: capai minimal 80% pada attempt ini untuk mengunduh sertifikat"
                aria-disabled="true"
                disabled>
            🔒 Unduh Sertifikat (terkunci)
        </button>
        </div>
    @endif

    {{-- Daftar jawaban --}}
    <div class="mt-4 space-y-4">
        @foreach($attempt->answers as $ans)
        <div class="p-3 bg-white border rounded">
            <div class="font-medium">
            {{ $loop->iteration }}. {{ $ans->question->prompt }}
            </div>
            <div class="mt-2 text-sm">
            @if($ans->question->type === 'mcq')
                <div>
                Jawabanmu:
                {{ optional($ans->question->options->firstWhere('id', $ans->option_id))->text ?? '—' }}
                </div>
            @else
                <div>Jawabanmu: {{ $ans->text_answer ?? '—' }}</div>
            @endif

            <span class="{{ $ans->is_correct ? 'text-emerald-700' : 'text-rose-700' }}">
                {{ $ans->question->type === 'mcq'
                    ? ($ans->is_correct ? 'Benar' : 'Salah')
                    : 'Perlu review (jawaban esai tidak dinilai otomatis)' }}
            </span>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Countdown lock (jika gagal & cooldown > 0) --}}
    @if((!($hasPassed ?? false)) && $cooldownRemain > 0)
        <script>
        (function(){
            const el   = document.getElementById('lockRemain');
            const wrap = document.getElementById('lockText');
            const btnD = document.getElementById('retryBtnDisabled');
            const btnA = document.getElementById('retryBtnActive');
            if (!el) return;

            let raw = parseInt(el.dataset.raw || '0', 10);
            const draw = v => { el.textContent = v > 999 ? '999+' : String(v); };
            draw(raw);

            const t = setInterval(() => {
            raw = Math.max(0, raw - 1);
            draw(raw);
            if (raw <= 0) {
                clearInterval(t);
                if (wrap) wrap.textContent = 'Kunci berakhir. Anda bisa mencoba kembali.';
                if (btnD && btnA) {
                btnD.classList.add('hidden');
                btnA.classList.remove('hidden');
                }
            }
            }, 1000);
        })();
        </script>
    @endif
    @endsection
