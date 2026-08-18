# Business Flow End-to-End

Ringkasan alur bisnis aplikasi LMS saat ini:

1. Visitor register/login lalu masuk ke dashboard user.
2. User bisa browse course, membeli course/paket membership, mengerjakan quiz, mengikuti tes psikologi/IQ, bertanya di Q&A, dan melihat pembayaran/sertifikat.
3. Course gratis bisa diakses langsung. Course premium butuh enrollment aktif atau membership aktif.
4. Payment dengan status `paid` mengaktifkan enrollment atau membership.
5. Progress lesson dan hasil quiz menjadi dasar penerbitan sertifikat.
6. Tes psikologi/IQ memakai attempt, jawaban, scoring, dan profile rekomendasi.
7. Admin mengelola master data course, lesson, resource, quiz, payment, enrollment, membership, coupon, psikologi/IQ, Q&A, dan sertifikat.

Diagram Mermaid ada di `BUSINESS_FLOW_MERMAID.md`.

