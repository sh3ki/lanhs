<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>Reports - Luis Aguado National HighSchool</title>
    <link rel="icon" type="image/png" href="/images/default/icon.png" />

    <!-- Bootstrap 5 & Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />

    <!-- DataTables & flatpickr -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/dataTables.bootstrap5.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" />

    <style>
        :root {
            --primary: #0d6efd;
            --secondary: #6c757d;
            --bg-light: #f8f9fa;
            --bg-dark: #212529;
            --text-light: #ffffff;
            --text-dark: #212529;
        }

        body {
            font-family: 'Segoe UI', sans-serif;
            transition: background 0.3s, color 0.3s;
        }

        body.light-mode {
            background: var(--bg-light);
            color: var(--text-dark);
        }

        body.dark-mode {
            background: var(--bg-dark);
            color: var(--text-light);
        }

        .navbar {
            transition: background 0.3s;
        }

        .navbar.light-mode {
            background: white;
        }

        .navbar.dark-mode {
            background: #343a40;
        }

        .navbar .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
        }

        .btn {
            border-radius: 0.5rem;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .btn i {
            font-size: 1.1rem;
        }

        .btn:hover {
            transform: scale(1.03);
        }

        .table th {
            text-align: center;
        }

        .badge-status, .badge-priority {
            font-size: 0.85rem;
            padding: 0.4em 0.6em;
            border-radius: 0.375rem;
        }

        .badge-status.open { background-color: #0d6efd; }
        .badge-status.closed { background-color: #6c757d; }
        .badge-status.pending { background-color: #ffc107; color: black; }

        .badge-priority.high { background-color: #dc3545; }
        .badge-priority.medium { background-color: #fd7e14; }
        .badge-priority.low { background-color: #20c997; }

        #dateRange {
            border-radius: 0.5rem;
            padding: 0.5rem;
        }

        .mode-toggle {
            cursor: pointer;
        }

        .table-responsive {
            overflow-x: auto;
        }
    </style>
</head>
<body class="light-mode">

<!-- Navbar -->
<nav class="navbar navbar-expand-lg shadow-sm mb-4 py-3 px-4 light-mode">
    <div class="container-fluid">
        <a class="navbar-brand text-primary" href="#"><i class="bi bi-bar-chart-line-fill me-2"></i> Reports Page</a>
        <a class="navbar-brand text-primary" href="#">
            <img src="/images/default/icon.png" alt="Logo" height="40" />
            Luis Aguado National Highschool
        </a>

        <div class="d-flex align-items-center gap-2">
            <button class="btn btn-outline-secondary mode-toggle" id="toggleMode">
                <i class="bi bi-moon-stars-fill"></i>
            </button>
            <a href="/dashboard" class="btn btn-outline-secondary d-flex align-items-center">
                <i class="bi bi-arrow-left me-2"></i>Dashboard
            </a>
        </div>
    </div>
</nav>

<div class="container px-4">
    <h2 class="mb-4 fw-bold">Ticket Reports Summary</h2>

    <!-- Filters -->
    <div class="row mb-3 align-items-end">
        <div class="col-md-4">
            <label for="dateRange" class="form-label fw-semibold">Select Date Range:</label>
            <input type="text" id="dateRange" class="form-control" placeholder="Select date range" readonly />
        </div>
        <div class="col-md-2 mt-3 mt-md-0">
            <label class="form-label d-block fw-semibold invisible">Clear</label>
            <button id="clearDateBtn" class="btn btn-outline-secondary w-100" disabled>
                <i class="bi bi-x-circle me-1"></i>Clear
            </button>
        </div>
        <div class="col-md-3 mt-3 mt-md-0">
            <label class="form-label d-block fw-semibold invisible">Download</label>
            <button id="downloadPdfBtn" class="btn btn-primary w-100">
                <i class="bi bi-download me-2"></i>Download PDF
            </button>
        </div>
    </div>


    <!-- Chart -->
    <div class="card mb-4">
        <div class="card-body" style="position:relative;">
            <canvas id="ticketsChart" height="100"></canvas>
        </div>
    </div>

    <!-- DataTable -->
<div class="card mb-5">
    <div class="card-body table-responsive">
        <table id="ticketsTable" class="table table-bordered table-striped text-center">
            <thead class="table-primary">
                <tr>
                    <th>Subject</th>
                    <th>Status</th>
                    <th>Priority</th>
                    <th>Department</th>
                    <th>User</th>
                    <th>Technician</th>
                    <th>Created At</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tickets as $ticket)
                <tr>
                    <td>{{ $ticket->subject }}</td>
                    <td>
                        <span class="badge-status {{ strtolower($ticket->status->name ?? '') }}">
                            {{ $ticket->status->name ?? 'Unknown' }}
                        </span>
                    </td>
                    <td>
                        <span class="badge-priority {{ strtolower($ticket->priority->name ?? '') }}">
                            {{ $ticket->priority->name ?? 'Unknown' }}
                        </span>
                    </td>
                    <td>
                        <span class="badge-priority">
                            {{ $ticket->department->name ?? 'Unassigned' }}
                        </span>
                    </td>
                    <td>
                        <span class="badge-priority">
                            {{ $ticket->user->name ?? 'Unknown' }}
                            @if($ticket->user && $ticket->user->userRole)
                                <small>({{ $ticket->user->userRole->name }})</small>
                            @endif
                        </span>
                    </td>
                    <td>
                        <span class="badge-priority">
                            {{ $ticket->agent->name ?? 'Unassigned' }}
                        </span>
                    </td>

                    <td>{{ \Carbon\Carbon::parse($ticket->created_at)->format('Y-m-d') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.5/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>

<script>
    $(document).ready(function () {

        // ─── DataTable ────────────────────────────────────────────────────────
        const table = $('#ticketsTable').DataTable({
            lengthMenu: [5, 10, 25, 50],
            pageLength: 10,
            order: [[6, 'desc']],
            language: {
                emptyTable: "No tickets available.",
                zeroRecords: "No matching records found for the selected date range."
            }
        });

        // ─── Date helpers ─────────────────────────────────────────────────────
        function parseLocalDate(str) {
            const [y, m, d] = str.split('-').map(Number);
            return new Date(y, m - 1, d);
        }

        let minDate = null, maxDate = null;

        // ─── Chart ────────────────────────────────────────────────────────────
        const MONTH_NAMES = ['January','February','March','April','May','June',
                             'July','August','September','October','November','December'];

        const ctx = document.getElementById('ticketsChart').getContext('2d');
        const ticketsChart = new Chart(ctx, {
            type: 'bar',
            data: { labels: [], datasets: [{
                label: 'Tickets per Month',
                data: [],
                backgroundColor: 'rgba(13, 110, 253, 0.7)',
                borderColor:     'rgba(13, 110, 253, 1)',
                borderWidth: 1,
                borderRadius: 5,
                barPercentage: 0.5,
                categoryPercentage: 0.6
            }]},
            options: {
                responsive: true,
                scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
            }
        });

        // Rebuild chart — no range = all rows; range = filtered rows
        function refreshChart() {
            const counts = {};
            $('#ticketsTable tbody tr').each(function () {
                const dateStr = $(this).find('td').eq(6).text().trim();
                if (!dateStr || dateStr === 'N/A') return;
                const d = parseLocalDate(dateStr);
                if (minDate && maxDate && (d < minDate || d > maxDate)) return;
                const [y, m] = dateStr.split('-').map(Number);
                const key = `${y}-${String(m).padStart(2, '0')}`;
                if (!counts[key]) counts[key] = { label: `${MONTH_NAMES[m - 1]} ${y}`, n: 0 };
                counts[key].n++;
            });
            const sorted = Object.keys(counts).sort();
            ticketsChart.data.labels           = sorted.map(k => counts[k].label);
            ticketsChart.data.datasets[0].data = sorted.map(k => counts[k].n);
            ticketsChart.resize();
            ticketsChart.update();
        }

        // ─── DataTable date-range filter ──────────────────────────────────────
        $.fn.dataTable.ext.search.push(function (settings, data) {
            if (!minDate || !maxDate) return true;
            const dateStr = data[6];
            if (!dateStr) return false;
            const d = parseLocalDate(dateStr);
            return d >= minDate && d <= maxDate;
        });

        // Initial state: show all
        refreshChart();

        // ─── Flatpickr date-range picker ──────────────────────────────────────
        const picker = flatpickr('#dateRange', {
            mode: 'range',
            dateFormat: 'Y-m-d',
            onChange: function (selectedDates) {
                if (selectedDates.length === 2) {
                    minDate = new Date(
                        selectedDates[0].getFullYear(),
                        selectedDates[0].getMonth(),
                        selectedDates[0].getDate(), 0, 0, 0
                    );
                    maxDate = new Date(
                        selectedDates[1].getFullYear(),
                        selectedDates[1].getMonth(),
                        selectedDates[1].getDate(), 23, 59, 59, 999
                    );
                    $('#clearDateBtn').prop('disabled', false);
                } else {
                    minDate = maxDate = null;
                    $('#clearDateBtn').prop('disabled', true);
                }
                table.draw();
                refreshChart();
            }
        });

        // Clear button
        $('#clearDateBtn').on('click', function () {
            picker.clear();
            minDate = maxDate = null;
            $(this).prop('disabled', true);
            table.draw();
            refreshChart();
        });

        // ─── PDF Download ─────────────────────────────────────────────────────
        $('#downloadPdfBtn').on('click', function () {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF({ orientation: 'landscape', unit: 'mm', format: 'a4' });
            const pageWidth  = doc.internal.pageSize.getWidth();   // 297
            const centerX    = pageWidth / 2;

            // 1. Collect filtered table rows first (before async logo load)
            const tableBody = [];
            table.rows({ search: 'applied' }).every(function () {
                const cells = $('td', this.node());
                tableBody.push([
                    cells.eq(0).text().trim(),
                    cells.eq(1).text().trim(),
                    cells.eq(2).text().trim(),
                    cells.eq(3).text().trim(),
                    cells.eq(4).text().trim(),
                    cells.eq(5).text().trim(),
                    cells.eq(6).text().trim(),
                ]);
            });

            // 2. Capture chart as PNG
            const chartImgData = document.getElementById('ticketsChart').toDataURL('image/png', 1.0);

            // 3. Load logo then build PDF
            const logoImg       = new Image();
            logoImg.crossOrigin = 'Anonymous';
            logoImg.src         = '/images/default/icon.png';

            logoImg.onload = function () {
                // Convert logo to base64
                const tmpCanvas = document.createElement('canvas');
                tmpCanvas.width  = logoImg.width;
                tmpCanvas.height = logoImg.height;
                tmpCanvas.getContext('2d').drawImage(logoImg, 0, 0);
                const logoBase64 = tmpCanvas.toDataURL('image/png');

                // ── Page 1: header + chart image ──────────────────────────────
                // Logo (left of header)
                doc.addImage(logoBase64, 'PNG', centerX - 95, 8, 22, 22);

                // Header text
                doc.setFontSize(13); doc.setFont('helvetica', 'bold');
                doc.text('Department of Education', centerX, 14, { align: 'center' });
                doc.setFontSize(15);
                doc.text('LUIS AGUADO NATIONAL HIGHSCHOOL', centerX, 21, { align: 'center' });
                doc.setFontSize(11); doc.setFont('helvetica', 'normal');
                doc.text('Trece Martires City, Cavite', centerX, 27, { align: 'center' });
                doc.text('School ID: 308905',            centerX, 33, { align: 'center' });

                // Section title & date range
                doc.setFont('helvetica', 'bold'); doc.setFontSize(12);
                doc.text('Ticket Reports Summary', 10, 43);

                const dateLabel = (minDate && maxDate)
                    ? `Date Range: ${minDate.toLocaleDateString('en-CA')} to ${maxDate.toLocaleDateString('en-CA')}`
                    : 'Date Range: All records';
                doc.setFont('helvetica', 'normal'); doc.setFontSize(10);
                doc.text(dateLabel, 10, 49);

                // Chart image (full width, proportional height ~65mm)
                const chartAreaW = pageWidth - 20;
                const chartAreaH = 65;
                doc.addImage(chartImgData, 'PNG', 10, 54, chartAreaW, chartAreaH);

                // ── Table (starts below chart) ────────────────────────────────
                doc.autoTable({
                    startY: 54 + chartAreaH + 4,
                    margin: { left: 10, right: 10 },
                    head: [['Subject','Status','Priority','Department','User','Technician','Created At']],
                    body: tableBody.length ? tableBody : [['No records found','','','','','','']],
                    theme: 'grid',
                    styles: { fontSize: 9, cellPadding: 2.5, halign: 'center', valign: 'middle' },
                    headStyles: { fillColor: [13, 110, 253], textColor: 255, fontSize: 10 },
                    columnStyles: {
                        0: { cellWidth: 65, halign: 'left' },
                        1: { cellWidth: 25 },
                        2: { cellWidth: 25 },
                        3: { cellWidth: 35 },
                        4: { cellWidth: 40 },
                        5: { cellWidth: 40 },
                        6: { cellWidth: 27 },
                    },
                    showHead: 'firstPage',
                    didDrawPage: function (hookData) {
                        // Repeat header on subsequent pages
                        if (hookData.pageNumber > 1) {
                            doc.setFontSize(10); doc.setFont('helvetica', 'italic');
                            doc.text('Ticket Reports Summary (continued)', 10, 10);
                        }
                        // Footer: page number
                        const pageCount = doc.internal.getNumberOfPages();
                        doc.setFontSize(9); doc.setFont('helvetica', 'normal');
                        doc.text(
                            `Page ${hookData.pageNumber} of ${pageCount}`,
                            pageWidth - 10, doc.internal.pageSize.getHeight() - 8,
                            { align: 'right' }
                        );
                    }
                });

                doc.save('ticket_reports.pdf');
            };

            // Fallback if logo fails to load
            logoImg.onerror = function () {
                alert('Logo image could not be loaded. PDF will be generated without it.');
                logoImg.onload();
            };
        });

        // ─── Dark / Light Mode ────────────────────────────────────────────────
        const toggleMode = document.getElementById('toggleMode');
        const body       = document.body;
        const navbar     = document.querySelector('.navbar');

        function setMode(mode) {
            if (mode === 'dark') {
                body.classList.replace('light-mode', 'dark-mode');
                navbar.classList.replace('light-mode', 'dark-mode');
                toggleMode.innerHTML = '<i class="bi bi-brightness-high-fill"></i>';
            } else {
                body.classList.replace('dark-mode', 'light-mode');
                navbar.classList.replace('dark-mode', 'light-mode');
                toggleMode.innerHTML = '<i class="bi bi-moon-stars-fill"></i>';
            }
            localStorage.setItem('theme', mode);
        }

        toggleMode.addEventListener('click', () => {
            setMode(body.classList.contains('dark-mode') ? 'light' : 'dark');
        });

        setMode(localStorage.getItem('theme') || 'light');
    });
</script>
</body>
</html>