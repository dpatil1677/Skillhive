<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $order->order_number }} — SkillHive</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root {
            --color-primary: #6D28D9;
            --color-primary-dark: #5B21B6;
            --color-text: #1E293B;
            --color-text-secondary: #64748B;
            --color-border: #E2E8F0;
            --color-bg-alt: #F8FAFC;
        }
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            color: var(--color-text);
            background: #F1F5F9;
            padding: 40px 20px;
        }
        .invoice-wrapper {
            max-width: 820px;
            margin: 0 auto;
            background: #FFFFFF;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.06);
            padding: 48px;
            border: 1px solid var(--color-border);
        }
        .invoice-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding-bottom: 32px;
            border-bottom: 2px solid var(--color-border);
            margin-bottom: 32px;
        }
        .brand-logo {
            font-size: 26px;
            font-weight: 800;
            color: var(--color-primary);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .invoice-badge {
            display: inline-block;
            background: #ECFDF5;
            color: #065F46;
            font-weight: 700;
            font-size: 13px;
            padding: 6px 14px;
            border-radius: 999px;
            margin-top: 8px;
        }
        .invoice-details-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 32px;
            margin-bottom: 36px;
        }
        .info-block h6 {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: var(--color-text-secondary);
            margin-bottom: 8px;
            font-weight: 700;
        }
        .info-block p {
            font-size: 14px;
            line-height: 1.6;
            color: var(--color-text);
        }
        .table-invoice {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 32px;
        }
        .table-invoice th {
            text-align: left;
            padding: 14px 16px;
            background: var(--color-bg-alt);
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            color: var(--color-text-secondary);
            border-top: 1px solid var(--color-border);
            border-bottom: 1px solid var(--color-border);
        }
        .table-invoice td {
            padding: 18px 16px;
            border-bottom: 1px solid var(--color-border);
            font-size: 14px;
        }
        .text-end {
            text-align: right !important;
        }
        .totals-section {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 36px;
        }
        .totals-table {
            width: 320px;
        }
        .totals-table tr td {
            padding: 8px 0;
            font-size: 14px;
        }
        .totals-table tr.grand-total td {
            padding-top: 14px;
            border-top: 2px solid var(--color-border);
            font-size: 18px;
            font-weight: 800;
            color: var(--color-primary);
        }
        .invoice-footer {
            padding-top: 24px;
            border-top: 1px solid var(--color-border);
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 12px;
            color: var(--color-text-secondary);
        }
        .actions-bar {
            max-width: 820px;
            margin: 0 auto 20px auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .btn-action {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            font-size: 14px;
            font-weight: 600;
            border-radius: 8px;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s ease;
        }
        .btn-primary {
            background: var(--color-primary);
            color: #fff;
            border: none;
        }
        .btn-primary:hover {
            background: var(--color-primary-dark);
        }
        .btn-outline {
            background: #fff;
            color: var(--color-text);
            border: 1px solid var(--color-border);
        }
        .btn-outline:hover {
            background: var(--color-bg-alt);
        }

        @media print {
            body {
                background: #fff;
                padding: 0;
            }
            .actions-bar {
                display: none !important;
            }
            .invoice-wrapper {
                box-shadow: none;
                border: none;
                padding: 0;
                max-width: 100%;
            }
        }
    </style>
</head>
<body>

    <div class="actions-bar">
        <a href="{{ url()->previous() }}" class="btn-action btn-outline">
            <i class="bi bi-arrow-left"></i> Back
        </a>
        <button onclick="window.print()" class="btn-action btn-primary">
            <i class="bi bi-printer"></i> Print / Download PDF
        </button>
    </div>

    <div class="invoice-wrapper">
        {{-- Invoice Top --}}
        <div class="invoice-top">
            <div>
                <div class="brand-logo">
                    <i class="bi bi-hexagon-fill"></i> SkillHive
                </div>
                <p style="font-size: 13px; color: var(--color-text-secondary); margin-top: 6px;">
                    Student Freelance Marketplace &amp; Escrow
                </p>
                <div class="invoice-badge">
                    <i class="bi bi-shield-check"></i> ESCROW PAYMENT VERIFIED
                </div>
            </div>
            <div class="text-end">
                <h2 style="font-size: 22px; font-weight: 800; color: var(--color-text);">TAX INVOICE</h2>
                <p style="font-size: 13px; color: var(--color-text-secondary); margin-top: 4px;">
                    Invoice #: <strong>INV-{{ date('Y') }}-{{ $order->order_number }}</strong>
                </p>
                <p style="font-size: 13px; color: var(--color-text-secondary);">
                    Order #: <strong>{{ $order->order_number }}</strong>
                </p>
                <p style="font-size: 13px; color: var(--color-text-secondary);">
                    Date: <strong>{{ $order->created_at ? $order->created_at->format('M d, Y') : date('M d, Y') }}</strong>
                </p>
            </div>
        </div>

        {{-- Details Grid --}}
        <div class="invoice-details-grid">
            <div class="info-block">
                <h6>Billed To (Client / Buyer)</h6>
                <p>
                    <strong>{{ $order->client ? $order->client->name : 'Client' }}</strong><br>
                    Email: {{ $order->client ? $order->client->email : '—' }}<br>
                    College: {{ $order->client && $order->client->college ? $order->client->college : 'Verified SkillHive Member' }}
                </p>
            </div>
            <div class="info-block">
                <h6>Service Provider (Student Freelancer)</h6>
                <p>
                    <strong>{{ $order->freelancer ? $order->freelancer->name : 'Freelancer' }}</strong><br>
                    Email: {{ $order->freelancer ? $order->freelancer->email : '—' }}<br>
                    College: {{ $order->freelancer && $order->freelancer->college ? $order->freelancer->college : 'Student Freelancer' }}
                </p>
            </div>
        </div>

        {{-- Table --}}
        <table class="table-invoice">
            <thead>
                <tr>
                    <th style="width: 55%;">Item &amp; Description</th>
                    <th class="text-end">Package Tier</th>
                    <th class="text-end">Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <strong>{{ $order->service ? $order->service->title : 'SkillHive Service' }}</strong>
                        <p style="font-size: 12px; color: var(--color-text-secondary); margin-top: 4px;">
                            Category: {{ $order->service ? $order->service->category_name : 'Freelance Project' }}
                        </p>
                    </td>
                    <td class="text-end fw-semibold">
                        {{ $order->package_name }}
                    </td>
                    <td class="text-end fw-bold">
                        ₹{{ number_format($order->price, 2) }}
                    </td>
                </tr>
            </tbody>
        </table>

        {{-- Totals --}}
        <div class="totals-section">
            <table class="totals-table">
                <tr>
                    <td class="text-secondary">Service Subtotal:</td>
                    <td class="text-end fw-semibold">₹{{ number_format($order->price, 2) }}</td>
                </tr>
                <tr>
                    <td class="text-secondary">Platform Escrow Fee (5%):</td>
                    <td class="text-end fw-semibold">₹{{ number_format($order->platform_fee, 2) }}</td>
                </tr>
                <tr>
                    <td class="text-secondary">GST (18% on platform fee):</td>
                    <td class="text-end fw-semibold">Included</td>
                </tr>
                <tr class="grand-total">
                    <td>Total Paid:</td>
                    <td class="text-end">₹{{ number_format($order->total_amount, 2) }}</td>
                </tr>
            </table>
        </div>

        {{-- Footer --}}
        <div class="invoice-footer">
            <div>
                Payment Status: <strong>{{ ucfirst($order->status) }}</strong> • Escrow Protected
            </div>
            <div>
                Thank you for supporting student talent on SkillHive!
            </div>
        </div>
    </div>

</body>
</html>
