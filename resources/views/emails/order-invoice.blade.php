<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tax Invoice — Order {{ $order->order_number }}</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #f1f5f9;
            margin: 0;
            padding: 40px 15px;
            color: #1e293b;
        }
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,0.06);
            border: 1px solid #e2e8f0;
        }
        .email-header {
            background: #6D28D9;
            padding: 30px;
            text-align: center;
            color: #ffffff;
        }
        .email-header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 700;
        }
        .email-header p {
            margin: 6px 0 0 0;
            font-size: 13px;
            opacity: 0.9;
        }
        .status-badge {
            display: inline-block;
            background: #ecfdf5;
            color: #065f46;
            font-weight: 700;
            font-size: 12px;
            padding: 5px 12px;
            border-radius: 999px;
            margin-bottom: 16px;
        }
        .email-body {
            padding: 32px 28px;
            font-size: 14px;
            line-height: 1.6;
        }
        .meta-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 16px;
            margin: 20px 0;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }
        .meta-item h6 {
            margin: 0 0 4px 0;
            font-size: 11px;
            text-transform: uppercase;
            color: #64748b;
            letter-spacing: 0.5px;
        }
        .meta-item p {
            margin: 0;
            font-size: 13px;
            font-weight: 600;
            color: #0f172a;
        }
        .invoice-table {
            width: 100%;
            border-collapse: collapse;
            margin: 24px 0;
        }
        .invoice-table th {
            text-align: left;
            padding: 10px 12px;
            background: #f8fafc;
            font-size: 11px;
            text-transform: uppercase;
            color: #64748b;
            border-top: 1px solid #e2e8f0;
            border-bottom: 1px solid #e2e8f0;
        }
        .invoice-table td {
            padding: 12px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 13px;
        }
        .text-right {
            text-align: right !important;
        }
        .grand-total {
            background: #f8fafc;
            font-size: 15px;
            font-weight: 700;
            color: #6D28D9;
        }
        .btn-view-invoice {
            display: inline-block;
            background-color: #6D28D9;
            color: #ffffff !important;
            text-decoration: none;
            padding: 13px 26px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            margin: 20px 0 10px 0;
            text-align: center;
        }
        .email-footer {
            background-color: #f8fafc;
            padding: 24px;
            text-align: center;
            font-size: 12px;
            color: #64748b;
            border-top: 1px solid #e2e8f0;
        }
    </style>
</head>
<body>
    <div class="email-container">
        {{-- Header --}}
        <div class="email-header">
            <h1>SkillHive</h1>
            <p>Official Tax Invoice &amp; Payment Receipt</p>
        </div>

        {{-- Body --}}
        <div class="email-body">
            <span class="status-badge">&#10003; ORDER COMPLETED &amp; ESCROW RELEASED</span>
            <p>Hello <strong>{{ $order->client ? $order->client->name : 'Client' }}</strong>,</p>
            <p>
                Your order <strong>#{{ $order->order_number }}</strong> has been successfully approved and completed. 
                Escrow funds have been released to your student freelancer. Your official tax invoice receipt is attached below.
            </p>

            {{-- Metadata Box --}}
            <table style="width: 100%; margin: 20px 0; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 14px;" cellpadding="6">
                <tr>
                    <td style="font-size: 12px; color: #64748b;">Invoice Number:</td>
                    <td style="font-size: 13px; font-weight: 700; text-align: right; color: #0f172a;">INV-{{ date('Y') }}-{{ $order->order_number }}</td>
                </tr>
                <tr>
                    <td style="font-size: 12px; color: #64748b;">Order Number:</td>
                    <td style="font-size: 13px; font-weight: 700; text-align: right; color: #0f172a;">{{ $order->order_number }}</td>
                </tr>
                <tr>
                    <td style="font-size: 12px; color: #64748b;">Completion Date:</td>
                    <td style="font-size: 13px; text-align: right; color: #0f172a;">{{ $order->updated_at ? $order->updated_at->format('M d, Y') : date('M d, Y') }}</td>
                </tr>
                <tr>
                    <td style="font-size: 12px; color: #64748b;">Freelancer:</td>
                    <td style="font-size: 13px; text-align: right; color: #0f172a;">{{ $order->freelancer ? $order->freelancer->name : 'Student Freelancer' }}</td>
                </tr>
            </table>

            {{-- Itemized Table --}}
            <table class="invoice-table">
                <thead>
                    <tr>
                        <th>Service Description</th>
                        <th class="text-right">Tier</th>
                        <th class="text-right">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <strong>{{ $order->service ? $order->service->title : 'SkillHive Project' }}</strong><br>
                            <span style="font-size: 11px; color: #64748b;">Category: {{ $order->service ? $order->service->category_name : 'Student Gig' }}</span>
                        </td>
                        <td class="text-right">{{ $order->package_name }}</td>
                        <td class="text-right">&#8377;{{ number_format($order->price, 2) }}</td>
                    </tr>
                    <tr>
                        <td colspan="2" style="color: #64748b;">Platform Escrow Protection Fee (5%)</td>
                        <td class="text-right">&#8377;{{ number_format($order->platform_fee, 2) }}</td>
                    </tr>
                    <tr>
                        <td colspan="2" style="color: #64748b;">GST (18% on platform fee)</td>
                        <td class="text-right">Included</td>
                    </tr>
                    <tr class="grand-total">
                        <td colspan="2"><strong>Total Paid</strong></td>
                        <td class="text-right"><strong>&#8377;{{ number_format($order->total_amount, 2) }}</strong></td>
                    </tr>
                </tbody>
            </table>

            <div style="text-align: center;">
                <a href="{{ route('orders.invoice', $order->order_number) }}" class="btn-view-invoice" target="_blank">
                    View &amp; Print Official PDF Invoice
                </a>
            </div>

            <p style="text-align: center; font-size: 12px; color: #64748b; margin-top: 5px;">
                You can download, save as PDF, or print this invoice anytime from your SkillHive Orders dashboard.
            </p>
        </div>

        {{-- Footer --}}
        <div class="email-footer">
            <p style="margin: 0 0 6px 0;">&copy; {{ date('Y') }} SkillHive. All rights reserved.</p>
            <p style="margin: 0;">Thank you for empowering student freelancers!</p>
        </div>
    </div>
</body>
</html>
