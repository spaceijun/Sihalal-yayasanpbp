@extends('layouts.app')

@section('template_title')
    Verifikators
@endsection

@section('content')

    <style>
        /* â”€â”€ PAGE LAYOUT â”€â”€ */
        .verk-page {
            padding: 28px 24px;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        /* â”€â”€ PAGE HEADER â”€â”€ */
        .verk-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            margin-bottom: 24px;
        }

        .verk-header-left h1 {
            font-family: 'Sora', sans-serif;
            font-size: 22px;
            font-weight: 600;
            color: #0F1F40;
            letter-spacing: -0.3px;
            margin-bottom: 3px;
        }

        .verk-header-left p {
            font-size: 13px;
            color: #8A99B3;
        }

        .verk-btn-create {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            background: linear-gradient(135deg, #1A5FC8 0%, #1040A0 100%);
            color: #fff;
            border: none;
            border-radius: 10px;
            padding: 10px 18px;
            font-size: 13px;
            font-weight: 600;
            font-family: 'Plus Jakarta Sans', sans-serif;
            text-decoration: none;
            cursor: pointer;
            box-shadow: 0 4px 14px rgba(26, 95, 200, 0.28);
            transition: all 0.2s;
            white-space: nowrap;
        }

        .verk-btn-create:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 22px rgba(26, 95, 200, 0.36);
            color: #fff;
            text-decoration: none;
        }

        .verk-btn-create svg {
            width: 14px;
            height: 14px;
            stroke: rgba(255, 255, 255, 0.9);
            fill: none;
            stroke-width: 2.5;
            flex-shrink: 0;
        }

        /* â”€â”€ STAT CARDS â”€â”€ */
        .verk-stats {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 14px;
            margin-bottom: 24px;
        }

        .verk-stat {
            background: #fff;
            border: 1px solid rgba(100, 140, 210, 0.12);
            border-radius: 14px;
            padding: 18px 20px;
            box-shadow: 0 2px 12px rgba(60, 100, 180, 0.06);
            transition: box-shadow 0.2s;
        }

        .verk-stat:hover {
            box-shadow: 0 4px 20px rgba(60, 100, 180, 0.1);
        }

        .verk-stat.is-accent {
            background: linear-gradient(135deg, #1A5FC8 0%, #1040A0 100%);
            border-color: transparent;
            box-shadow: 0 4px 18px rgba(26, 95, 200, 0.3);
        }

        .verk-stat-label {
            font-size: 10.5px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.07em;
            color: #B0BCCE;
            margin-bottom: 8px;
        }

        .verk-stat.is-accent .verk-stat-label {
            color: rgba(255, 255, 255, 0.55);
        }

        .verk-stat-value {
            font-family: 'Sora', sans-serif;
            font-size: 24px;
            font-weight: 600;
            color: #0F1F40;
            letter-spacing: -0.5px;
            line-height: 1;
            margin-bottom: 4px;
        }

        .verk-stat.is-accent .verk-stat-value {
            color: #fff;
        }

        .verk-stat-value.is-warn {
            color: #B86800;
        }

        .verk-stat-value.is-success {
            color: #0F6E56;
            font-size: 19px;
        }

        .verk-stat-sub {
            font-size: 11.5px;
            color: #B0BCCE;
        }

        .verk-stat.is-accent .verk-stat-sub {
            color: rgba(255, 255, 255, 0.45);
        }

        /* â”€â”€ TABLE CARD â”€â”€ */
        .verk-card {
            background: #fff;
            border: 1px solid rgba(100, 140, 210, 0.12);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 2px 16px rgba(60, 100, 180, 0.06);
        }

        .verk-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px 22px;
            border-bottom: 1px solid #EDF0F7;
        }

        .verk-card-title {
            font-family: 'Sora', sans-serif;
            font-size: 14px;
            font-weight: 600;
            color: #0F1F40;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .verk-card-title svg {
            width: 16px;
            height: 16px;
            stroke: #1A5FC8;
            fill: none;
            stroke-width: 2;
        }

        .verk-count-badge {
            display: inline-flex;
            align-items: center;
            background: #EEF4FF;
            color: #1A5FC8;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            padding: 2px 9px;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        /* â”€â”€ TABLE â”€â”€ */
        .verk-table {
            width: 100%;
            border-collapse: collapse;
        }

        .verk-table thead th {
            font-size: 10.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.07em;
            color: #B0BCCE;
            padding: 11px 18px;
            text-align: left;
            background: #F8F9FC;
            border-bottom: 1px solid #EDF0F7;
            white-space: nowrap;
        }

        .verk-table thead th.tc {
            text-align: center;
        }

        .verk-table tbody tr {
            border-bottom: 1px solid #F1F4FA;
            transition: background 0.12s;
        }

        .verk-table tbody tr:last-child {
            border-bottom: none;
        }

        .verk-table tbody tr:hover {
            background: #FAFBFF;
        }

        .verk-table td {
            padding: 14px 18px;
            vertical-align: middle;
            color: #3A4A6B;
            font-size: 13px;
        }

        .verk-table td.tc {
            text-align: center;
        }

        /* â”€â”€ AVATAR + NAME â”€â”€ */
        .verk-name-cell {
            display: flex;
            align-items: center;
            gap: 11px;
        }

        .verk-avatar {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: #EEF4FF;
            color: #1A5FC8;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: 700;
            flex-shrink: 0;
            font-family: 'Sora', sans-serif;
        }

        .verk-name-cell strong {
            font-size: 13.5px;
            font-weight: 600;
            color: #0F1F40;
            display: block;
        }

        .verk-name-cell small {
            font-size: 11.5px;
            color: #B0BCCE;
        }

        /* â”€â”€ MONO NUMERIC â”€â”€ */
        .verk-mono {
            font-family: 'DM Mono', 'Courier New', monospace;
            font-size: 12.5px;
            color: #5A6A8A;
        }

        /* â”€â”€ ADDRESS â”€â”€ */
        .verk-addr {
            max-width: 150px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            color: #8A99B3;
            font-size: 12.5px;
        }

        /* â”€â”€ BADGES â”€â”€ */
        .verk-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            border-radius: 20px;
            font-size: 11.5px;
            font-weight: 600;
            padding: 4px 10px;
            line-height: 1.3;
            white-space: nowrap;
        }

        .verk-badge-warn {
            background: #FFF4E0;
            color: #B86800;
        }

        .verk-badge-success {
            background: #EBFAF2;
            color: #0F6E56;
        }

        .verk-badge .dot {
            width: 5px;
            height: 5px;
            border-radius: 50%;
            background: currentColor;
            display: inline-block;
            flex-shrink: 0;
        }

        .verk-pending-sub {
            font-size: 10.5px;
            color: #B0BCCE;
            margin-top: 4px;
            display: block;
        }

        /* â”€â”€ AMOUNT â”€â”€ */
        .verk-amount {
            font-family: 'DM Mono', 'Courier New', monospace;
            font-size: 13px;
            font-weight: 600;
            color: #0F6E56;
        }

        .verk-amount-zero {
            font-family: 'DM Mono', 'Courier New', monospace;
            font-size: 12.5px;
            color: #C8D3E8;
        }

        /* â”€â”€ ACTION BUTTONS â”€â”€ */
        .verk-actions {
            display: flex;
            align-items: center;
            gap: 5px;
            justify-content: center;
            flex-wrap: nowrap;
        }

        .verk-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            height: 30px;
            border-radius: 8px;
            border: 1px solid #E0E7F0;
            background: #fff;
            cursor: pointer;
            font-size: 12.5px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: #6B7A99;
            transition: all 0.15s;
            text-decoration: none;
            padding: 0 8px;
            white-space: nowrap;
        }

        .verk-btn svg {
            width: 13px;
            height: 13px;
            fill: none;
            stroke: currentColor;
            stroke-width: 2;
            flex-shrink: 0;
        }

        .verk-btn:hover {
            background: #F5F7FB;
            border-color: #C8D3E8;
            color: #0F1F40;
        }

        .verk-btn.pay {
            gap: 5px;
            padding: 0 11px;
            background: #EEF4FF;
            border-color: #C3D6F7;
            color: #1A5FC8;
            font-weight: 600;
        }

        .verk-btn.pay:hover {
            background: #1A5FC8;
            border-color: #1A5FC8;
            color: #fff;
        }

        .verk-btn.danger:hover {
            background: #FEF2F2;
            border-color: #FECACA;
            color: #DC2626;
        }

        .verk-btn:disabled,
        .verk-btn[disabled] {
            opacity: 0.3;
            cursor: not-allowed;
            pointer-events: none;
        }

        .verk-btn-icon {
            width: 30px;
        }

        /* â”€â”€ ROW NUMBER â”€â”€ */
        .verk-rownum {
            font-size: 12px;
            color: #C8D3E8;
            font-weight: 500;
        }

        /* â”€â”€ EMPTY STATE â”€â”€ */
        .verk-empty {
            text-align: center;
            padding: 60px 20px;
            color: #B0BCCE;
        }

        .verk-empty svg {
            width: 40px;
            height: 40px;
            stroke: #D0DAEC;
            fill: none;
            stroke-width: 1.5;
            margin-bottom: 12px;
        }

        .verk-empty p {
            font-size: 13px;
            color: #B0BCCE;
        }

        /* â”€â”€ LOADING â”€â”€ */
        .verk-loading {
            text-align: center;
            padding: 60px 20px;
            display: none;
        }

        .verk-loading .spinner-border {
            width: 2.5rem;
            height: 2.5rem;
        }

        .verk-loading p {
            margin-top: 12px;
            color: #8A99B3;
            font-size: 13px;
            font-weight: 500;
        }

        /* â”€â”€ FOOTER / PAGINATION â”€â”€ */
        .verk-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 13px 20px;
            border-top: 1px solid #EDF0F7;
            background: #F8F9FC;
        }

        .verk-footer-info {
            font-size: 12px;
            color: #B0BCCE;
        }

        /* â”€â”€ MODALS â”€â”€ */
        .verk-modal .modal-content {
            border: none;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(15, 31, 64, 0.15);
            overflow: hidden;
        }

        .verk-modal .modal-header {
            background: linear-gradient(135deg, #1A5FC8 0%, #1040A0 100%);
            border: none;
            padding: 18px 22px;
        }

        .verk-modal .modal-title {
            font-family: 'Sora', sans-serif;
            font-size: 15px;
            font-weight: 600;
            color: #fff;
        }

        .verk-modal .modal-header svg {
            width: 16px;
            height: 16px;
            stroke: rgba(255, 255, 255, 0.8);
            fill: none;
            stroke-width: 2;
        }

        .verk-modal .modal-body {
            padding: 24px;
        }

        .verk-modal .modal-footer {
            padding: 14px 22px;
            border-top: 1px solid #EDF0F7;
            background: #F8F9FC;
        }

        .verk-info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
        }

        .verk-info-table th {
            background: #F5F7FB;
            font-size: 11.5px;
            font-weight: 600;
            color: #8A99B3;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 10px 14px;
            border: 1px solid #EDF0F7;
            text-align: left;
            width: 42%;
        }

        .verk-info-table td {
            padding: 10px 14px;
            border: 1px solid #EDF0F7;
            font-size: 13.5px;
            color: #0F1F40;
            font-weight: 500;
        }

        .verk-total-box {
            background: linear-gradient(135deg, #EBFAF2, #D1F5E4);
            border: 1px solid #A7DDD0;
            border-radius: 12px;
            padding: 14px 18px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 14px;
        }

        .verk-total-box .label {
            font-size: 13px;
            font-weight: 600;
            color: #0A5240;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .verk-total-box .label svg {
            width: 15px;
            height: 15px;
            stroke: #0F6E56;
            fill: none;
            stroke-width: 2;
        }

        .verk-total-box .amount {
            font-family: 'DM Mono', monospace;
            font-size: 20px;
            font-weight: 600;
            color: #065F46;
        }

        .verk-warn-box {
            background: #FFF9F0;
            border: 1px solid #FDE5B8;
            border-radius: 10px;
            padding: 11px 14px;
            display: flex;
            align-items: flex-start;
            gap: 9px;
            font-size: 12.5px;
            color: #92400E;
        }

        .verk-warn-box svg {
            width: 15px;
            height: 15px;
            stroke: #B86800;
            fill: none;
            stroke-width: 2;
            flex-shrink: 0;
            margin-top: 1px;
        }

        .verk-modal-plain .modal-header {
            background: #fff;
            border-bottom: 1px solid #EDF0F7;
        }

        .verk-modal-plain .modal-title {
            font-family: 'Sora', sans-serif;
            font-size: 15px;
            font-weight: 600;
            color: #0F1F40;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .verk-modal-plain .modal-title span.accent {
            color: #1A5FC8;
        }

        .verk-modal-plain .modal-title svg {
            width: 16px;
            height: 16px;
            stroke: #8A99B3;
            fill: none;
            stroke-width: 2;
        }

        /* â”€â”€ RIWAYAT TABLE â”€â”€ */
        .verk-riwayat-table {
            width: 100%;
            border-collapse: collapse;
        }

        .verk-riwayat-table thead th {
            font-size: 10.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.07em;
            color: #B0BCCE;
            padding: 10px 14px;
            background: #F8F9FC;
            border-bottom: 1px solid #EDF0F7;
        }

        .verk-riwayat-table th.tr {
            text-align: right;
        }

        .verk-riwayat-table th.tc {
            text-align: center;
        }

        .verk-riwayat-table tbody td {
            padding: 12px 14px;
            border-bottom: 1px solid #F1F4FA;
            font-size: 13px;
            color: #3A4A6B;
        }

        .verk-riwayat-table tfoot td {
            padding: 12px 14px;
            background: #EBFAF2;
            font-size: 13px;
            font-weight: 700;
            color: #0F6E56;
            border-top: 1px solid #A7DDD0;
        }

        .verk-riwayat-table tfoot td.tc {
            text-align: center;
        }

        .verk-riwayat-table tfoot td.tr {
            text-align: right;
        }

        /* â”€â”€ KALKULASI SUMMARY â”€â”€ */
        .kalk-stats {
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            gap: 10px;
            margin-bottom: 20px;
        }

        .kalk-stat {
            background: #F8F9FC;
            border: 1px solid #EDF0F7;
            border-radius: 12px;
            padding: 13px 14px;
            text-align: center;
        }

        .kalk-stat.is-warn {
            background: #FFF9F0;
            border-color: #FDE5B8;
        }

        .kalk-stat.is-info {
            background: #EFF8FF;
            border-color: #C3E5FA;
        }

        .kalk-stat.is-success {
            background: #EBFAF2;
            border-color: #A7DDD0;
        }

        .kalk-stat p.label {
            font-size: 9.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.07em;
            color: #B0BCCE;
            margin-bottom: 5px;
        }

        .kalk-stat.is-warn p.label {
            color: #D97706;
        }

        .kalk-stat.is-info p.label {
            color: #0284C7;
        }

        .kalk-stat.is-success p.label {
            color: #0F6E56;
        }

        .kalk-stat p.value {
            font-family: 'Sora', sans-serif;
            font-size: 16px;
            font-weight: 600;
            color: #0F1F40;
            margin: 0;
        }

        .kalk-stat.is-warn p.value {
            color: #B86800;
        }

        .kalk-stat.is-info p.value {
            color: #0369A1;
        }

        .kalk-stat.is-success p.value {
            color: #0F6E56;
            font-size: 13px;
            font-family: 'DM Mono', monospace;
        }

        /* â”€â”€ SEARCH BAR â”€â”€ */
        .verk-search-shell {
            position: relative;
            display: flex;
            align-items: center;
        }

        .verk-search-icon {
            position: absolute;
            left: 10px;
            width: 13px;
            height: 13px;
            stroke: #B0BCCE;
            fill: none;
            stroke-width: 2;
            pointer-events: none;
        }

        .verk-search-input {
            height: 32px;
            width: 210px;
            background: #F5F7FB;
            border: 1px solid #E0E7F0;
            border-radius: 8px;
            padding: 0 12px 0 30px;
            font-size: 12.5px;
            color: #0F1F40;
            font-family: 'Plus Jakarta Sans', sans-serif;
            outline: none;
            transition: border-color .18s, background .18s, box-shadow .18s;
        }

        .verk-search-input::placeholder {
            color: #B0BCCE;
        }

        .verk-search-input:focus {
            border-color: #1A5FC8;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(26, 95, 200, .08);
        }

        /* â”€â”€ FILTER BUTTONS â”€â”€ */
        .verk-filter-group {
            display: flex;
            gap: 5px;
        }

        .verk-filter-btn {
            padding: 6px 13px;
            border-radius: 8px;
            border: 1px solid #E0E7F0;
            background: #fff;
            font-size: 12px;
            font-weight: 500;
            color: #6B7A99;
            font-family: 'Plus Jakarta Sans', sans-serif;
            cursor: pointer;
            transition: all 0.15s;
        }

        .verk-filter-btn:hover {
            background: #F5F7FB;
        }

        .verk-filter-btn.active {
            background: #EEF4FF;
            border-color: #C3D6F7;
            color: #1A5FC8;
            font-weight: 600;
        }

        .verk-filter-btn.active.warn {
            background: #FFF9F0;
            border-color: #FDE5B8;
            color: #B86800;
        }

        .verk-filter-btn.active.success {
            background: #EBFAF2;
            border-color: #A7DDD0;
            color: #0F6E56;
        }

        /* â”€â”€ TABS â”€â”€ */
        .verk-tabs {
            display: flex;
            gap: 0;
            border-bottom: 2px solid #EDF0F7;
            margin-bottom: 18px;
        }

        .verk-tab {
            padding: 10px 18px;
            font-size: 13px;
            font-weight: 500;
            color: #8A99B3;
            cursor: pointer;
            border-bottom: 2px solid transparent;
            margin-bottom: -2px;
            transition: all 0.15s;
            display: flex;
            align-items: center;
            gap: 6px;
            background: none;
            border-top: none;
            border-left: none;
            border-right: none;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .verk-tab svg {
            width: 13px;
            height: 13px;
            stroke: currentColor;
            fill: none;
            stroke-width: 2;
        }

        .verk-tab:hover {
            color: #3A4A6B;
        }

        .verk-tab.active {
            color: #1A5FC8;
            border-bottom-color: #1A5FC8;
            font-weight: 600;
        }

        /* â”€â”€ SECTION LABEL â”€â”€ */
        .verk-section-label {
            font-size: 10.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.07em;
            color: #B0BCCE;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .verk-section-label svg {
            width: 12px;
            height: 12px;
            stroke: #C8D3E8;
            fill: none;
            stroke-width: 2;
        }

        .verk-section-label::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #EDF0F7;
        }

        /* â”€â”€ KALKULASI TABLES â”€â”€ */
        .kalk-table {
            width: 100%;
            border-collapse: collapse;
        }

        .kalk-table thead th {
            font-size: 10.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.07em;
            color: #B0BCCE;
            padding: 9px 13px;
            background: #F8F9FC;
            border-bottom: 1px solid #EDF0F7;
            text-align: left;
        }

        .kalk-table thead th.tc {
            text-align: center;
        }

        .kalk-table thead th.tr {
            text-align: right;
        }

        .kalk-table tbody td {
            padding: 11px 13px;
            border-bottom: 1px solid #F1F4FA;
            font-size: 12.5px;
            color: #3A4A6B;
        }

        .kalk-table tbody td.tc {
            text-align: center;
        }

        .kalk-table tbody td.tr {
            text-align: right;
        }

        .kalk-table tbody tr:last-child td {
            border-bottom: none;
        }

        .kalk-table tbody tr:hover {
            background: #FAFBFF;
        }

        /* â”€â”€ PAGINATION (kalk) â”€â”€ */
        .verk-pagi {
            display: flex;
            gap: 4px;
        }

        .verk-pagi-btn {
            min-width: 30px;
            height: 30px;
            border-radius: 8px;
            border: 1px solid #E0E7F0;
            background: #fff;
            font-size: 12px;
            color: #6B7A99;
            cursor: pointer;
            font-family: 'Plus Jakarta Sans', sans-serif;
            transition: all 0.12s;
            padding: 0 6px;
        }

        .verk-pagi-btn:hover:not(.active) {
            background: #F5F7FB;
        }

        .verk-pagi-btn.active {
            background: #1A5FC8;
            color: #fff;
            border-color: #1A5FC8;
            font-weight: 600;
        }

        .verk-pagi-btn:disabled {
            opacity: 0.3;
            cursor: not-allowed;
        }

        /* â”€â”€ NO DATA (kalk) â”€â”€ */
        .kalk-nodata {
            text-align: center;
            padding: 30px 0;
            color: #C8D3E8;
            font-size: 13px;
        }

        @media (max-width: 992px) {
            .verk-stats {
                grid-template-columns: repeat(2, 1fr);
            }

            .kalk-stats {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        @media (max-width: 640px) {
            .verk-stats {
                grid-template-columns: 1fr 1fr;
            }

            .verk-header {
                flex-direction: column;
                gap: 14px;
            }

            .verk-page {
                padding: 16px;
            }
        }
    </style>

    <div class="verk-page">
        @include('layouts.messages')

        {{-- â”€â”€ PAGE HEADER â”€â”€ --}}
        <div class="verk-header">
            <div class="verk-header-left">
                <h1>Verifikator</h1>
                <p>Kelola data verifikator dan status pembayaran</p>
            </div>
            <a href="{{ route('superadmin.verifikators.create') }}" class="verk-btn-create">
                <svg viewBox="0 0 24 24">
                    <line x1="12" y1="5" x2="12" y2="19" />
                    <line x1="5" y1="12" x2="19" y2="12" />
                </svg>
                Tambah Verifikator
            </a>
        </div>

        {{-- â”€â”€ STAT SUMMARY â”€â”€ --}}
        @php
            $totalVerifikator = $verifikators->total();
            $totalPending = $verifikators->sum('total_belum_dibayar');
            $totalNominal = $verifikators->sum(fn($v) => $v->total_belum_dibayar * $v->rate_per_data);
            $totalLunas = $verifikators->sum(fn($v) => $v->verifikatorPayments->sum('jumlah_data') ?? 0);
        @endphp
        <div class="verk-stats">
            <div class="verk-stat is-accent">
                <div class="verk-stat-label">Total Verifikator</div>
                <div class="verk-stat-value">{{ $totalVerifikator }}</div>
                <div class="verk-stat-sub">Terdaftar</div>
            </div>
            <div class="verk-stat">
                <div class="verk-stat-label">Data Pending</div>
                <div class="verk-stat-value is-warn">{{ number_format($totalPending) }}</div>
                <div class="verk-stat-sub">Belum dibayar</div>
            </div>
            <div class="verk-stat">
                <div class="verk-stat-label">Nominal Pending</div>
                <div class="verk-stat-value is-success">Rp {{ number_format($totalNominal, 0, ',', '.') }}</div>
                <div class="verk-stat-sub">Perlu diselesaikan</div>
            </div>
            <div class="verk-stat">
                <div class="verk-stat-label">Total Terbayar</div>
                <div class="verk-stat-value">{{ number_format($totalLunas) }}</div>
                <div class="verk-stat-sub">Data lunas</div>
            </div>
        </div>

        {{-- â”€â”€ TABLE CARD â”€â”€ --}}
        <div class="verk-card">
            <div class="verk-card-header">
                <div class="verk-card-title">
                    <svg viewBox="0 0 24 24">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                        <circle cx="9" cy="7" r="4" />
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                        <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                    </svg>
                    Daftar Verifikator
                    <span class="verk-count-badge">{{ $verifikators->total() }}</span>
                </div>

                {{-- Search bar --}}
                <form id="searchForm" style="display:flex; align-items:center; gap:8px;">
                    @csrf
                    <div class="verk-search-shell">
                        <svg class="verk-search-icon" viewBox="0 0 24 24">
                            <circle cx="11" cy="11" r="8" />
                            <line x1="21" y1="21" x2="16.65" y2="16.65" />
                        </svg>
                        <input class="verk-search-input" type="text" id="search" name="search"
                            placeholder="Cari nama verifikator..."
                            value="{{ request('search') }}">
                    </div>
                    <button type="button" id="resetBtn" class="verk-btn" title="Reset">
                        <svg viewBox="0 0 24 24">
                            <polyline points="1 4 1 10 7 10" />
                            <path d="M3.51 15a9 9 0 1 0 .49-3.51" />
                        </svg>
                    </button>
                </form>
            </div>

            {{-- Loading --}}
            <div id="tableLoading" class="verk-loading">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p>Memuat data...</p>
            </div>

            {{-- Table --}}
            <div id="tableWrapper">
                <div class="table-responsive">
                    <table class="verk-table">
                        <thead>
                            <tr>
                                <th style="width:44px">#</th>
                                <th>Nama Verifikator</th>
                                <th>Telephone</th>
                                <th>Alamat</th>
                                <th>Rate / Data</th>
                                <th class="tc">Pending</th>
                                <th class="tc">Total Tagihan</th>
                                <th class="tc" style="width:180px">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody">
                            @forelse ($verifikators as $verifikator)
                                @php
                                    $initials = collect(explode(' ', $verifikator->nama_lengkap))
                                        ->take(2)
                                        ->map(fn($w) => strtoupper(substr($w, 0, 1)))
                                        ->implode('');
                                    $avatarColors = [
                                        ['#EEF4FF', '#1A5FC8'],
                                        ['#EBFAF2', '#0F6E56'],
                                        ['#FFF4E0', '#B86800'],
                                        ['#F5F0FF', '#6D28D9'],
                                        ['#E0F4FF', '#0369A1'],
                                        ['#FFF0FA', '#9D174D'],
                                    ];
                                    $colorPair = $avatarColors[$loop->index % count($avatarColors)];
                                    $totalNominalRow = $verifikator->total_belum_dibayar * $verifikator->rate_per_data;
                                @endphp
                                <tr>
                                    <td><span class="verk-rownum">{{ ++$i }}</span></td>
                                    <td>
                                        <div class="verk-name-cell">
                                            <div class="verk-avatar"
                                                style="background:{{ $colorPair[0] }}; color:{{ $colorPair[1] }};">
                                                {{ $initials }}
                                            </div>
                                            <div>
                                                <strong>{{ $verifikator->nama_lengkap }}</strong>
                                                <small>Verifikator</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="verk-mono">{{ $verifikator->telephone ?? '-' }}</td>
                                    <td>
                                        <div class="verk-addr" title="{{ $verifikator->alamat_lengkap }}">
                                            {{ $verifikator->alamat_lengkap ?? '-' }}
                                        </div>
                                    </td>
                                    <td class="verk-mono">Rp {{ number_format($verifikator->rate_per_data, 0, ',', '.') }}
                                    </td>
                                    <td class="tc">
                                        @if ($verifikator->total_belum_dibayar > 0)
                                            <span class="verk-badge verk-badge-warn">
                                                <span class="dot"></span>
                                                {{ $verifikator->total_belum_dibayar }} Data
                                            </span>
                                            @if ($verifikator->jumlah_belum_dibayar_progress > 0)
                                                <span class="verk-pending-sub">
                                                    {{ $verifikator->jumlah_belum_dibayar }} lapangan
                                                    + {{ $verifikator->jumlah_belum_dibayar_progress }} progress
                                                </span>
                                            @endif
                                        @else
                                            <span class="verk-badge verk-badge-success">âœ“ Lunas</span>
                                        @endif
                                    </td>
                                    <td class="tc">
                                        @if ($totalNominalRow > 0)
                                            <span class="verk-amount">Rp
                                                {{ number_format($totalNominalRow, 0, ',', '.') }}</span>
                                        @else
                                            <span class="verk-amount-zero">Rp 0</span>
                                        @endif
                                    </td>
                                    <td class="tc">
                                        <div class="verk-actions">

                                            {{-- BAYAR --}}
                                            @if ($verifikator->total_belum_dibayar > 0)
                                                <button type="button" class="verk-btn pay btn-open-bayar"
                                                    data-id="{{ $verifikator->id }}"
                                                    data-nama="{{ $verifikator->nama_lengkap }}"
                                                    data-jumlah="{{ $verifikator->total_belum_dibayar }}"
                                                    data-rate="Rp {{ number_format($verifikator->rate_per_data, 0, ',', '.') }}"
                                                    data-total="Rp {{ number_format($totalNominalRow, 0, ',', '.') }}"
                                                    data-action="{{ route('superadmin.verifikators.bayar', $verifikator->hashed_id) }}">
                                                    <svg viewBox="0 0 24 24">
                                                        <rect x="2" y="5" width="20" height="14" rx="2" />
                                                        <line x1="2" y1="10" x2="22" y2="10" />
                                                    </svg>
                                                    Bayar
                                                </button>
                                            @else
                                                <button type="button" class="verk-btn verk-btn-icon" disabled
                                                    title="Tidak ada pending">
                                                    <svg viewBox="0 0 24 24">
                                                        <rect x="2" y="5" width="20" height="14" rx="2" />
                                                        <line x1="2" y1="10" x2="22" y2="10" />
                                                    </svg>
                                                </button>
                                            @endif

                                            {{-- KALKULASI --}}
                                            <button type="button" class="verk-btn verk-btn-icon btn-kalkulasi"
                                                data-id="{{ $verifikator->id }}"
                                                data-nama="{{ $verifikator->nama_lengkap }}"
                                                data-url="{{ route('superadmin.verifikators.kalkulasi', $verifikator->hashed_id) }}"
                                                title="Kalkulasi">
                                                <svg viewBox="0 0 24 24">
                                                    <rect x="4" y="2" width="16" height="20" rx="2" />
                                                    <line x1="8" y1="6" x2="16" y2="6" />
                                                    <line x1="8" y1="10" x2="16" y2="10" />
                                                    <line x1="8" y1="14" x2="12" y2="14" />
                                                </svg>
                                            </button>

                                            {{-- RIWAYAT --}}
                                            <button type="button" class="verk-btn verk-btn-icon btn-open-riwayat"
                                                data-id="{{ $verifikator->id }}"
                                                data-nama="{{ $verifikator->nama_lengkap }}"
                                                data-payments='@json($verifikator->verifikatorPayments->sortByDesc('paid_at')->values())' title="Riwayat Pembayaran">
                                                <svg viewBox="0 0 24 24">
                                                    <circle cx="12" cy="12" r="10" />
                                                    <polyline points="12 6 12 12 16 14" />
                                                </svg>
                                            </button>

                                            {{-- EDIT --}}
                                            <a class="verk-btn verk-btn-icon"
                                                href="{{ route('superadmin.verifikators.edit', $verifikator->hashed_id) }}"
                                                title="Edit">
                                                <svg viewBox="0 0 24 24">
                                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                                                </svg>
                                            </a>

                                            {{-- HAPUS --}}
                                            <form
                                                action="{{ route('superadmin.verifikators.destroy', $verifikator->hashed_id) }}"
                                                method="POST" class="d-inline">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="verk-btn verk-btn-icon danger"
                                                    onclick="return confirm('Yakin hapus verifikator ini?')"
                                                    title="Hapus">
                                                    <svg viewBox="0 0 24 24">
                                                        <polyline points="3 6 5 6 21 6" />
                                                        <path d="M19 6l-1 14H6L5 6" />
                                                        <path d="M10 11v6" />
                                                        <path d="M14 11v6" />
                                                        <path d="M9 6V4h6v2" />
                                                    </svg>
                                                </button>
                                            </form>

                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8">
                                        <div class="verk-empty">
                                            <svg viewBox="0 0 24 24">
                                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                                                <circle cx="9" cy="7" r="4" />
                                                <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                                                <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                                            </svg>
                                            <p>Belum ada data verifikator.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="verk-footer">
                    <span class="verk-footer-info">
                        Menampilkan {{ $verifikators->firstItem() ?? 0 }}â€“{{ $verifikators->lastItem() ?? 0 }}
                        dari {{ $verifikators->total() }} verifikator
                    </span>
                    <div id="paginationWrapper">
                        @include('layouts.pagination', ['paginator' => $verifikators])
                    </div>
                </div>
            </div>
        </div>
    </div>


    {{-- â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
     MODAL BAYAR
â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• --}}
    <div id="modalBayar" class="modal fade verk-modal" tabindex="-1" aria-labelledby="modalBayarLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width:440px;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalBayarLabel">
                        <svg viewBox="0 0 24 24" style="display:inline;margin-right:7px;vertical-align:-2px;">
                            <rect x="2" y="5" width="20" height="14" rx="2" />
                            <line x1="2" y1="10" x2="22" y2="10" />
                        </svg>
                        Konfirmasi Pembayaran
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p style="font-size:13px; color:#8A99B3; margin-bottom:16px;">Periksa detail sebelum konfirmasi
                        pembayaran.</p>

                    <table class="verk-info-table">
                        <tr>
                            <th>Nama Verifikator</th>
                            <td id="bayar-nama" style="font-weight:600; color:#0F1F40;">-</td>
                        </tr>
                        <tr>
                            <th>Jumlah Data</th>
                            <td id="bayar-jumlah">-</td>
                        </tr>
                        <tr>
                            <th>Rate Per Data</th>
                            <td id="bayar-rate" style="font-family:'DM Mono',monospace;">-</td>
                        </tr>
                    </table>

                    <div class="verk-total-box">
                        <div class="label">
                            <svg viewBox="0 0 24 24">
                                <line x1="12" y1="1" x2="12" y2="23" />
                                <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" />
                            </svg>
                            Total Dibayarkan
                        </div>
                        <div class="amount" id="bayar-total">-</div>
                    </div>

                    <div class="verk-warn-box">
                        <svg viewBox="0 0 24 24">
                            <path
                                d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z" />
                            <line x1="12" y1="9" x2="12" y2="13" />
                            <line x1="12" y1="17" x2="12.01" y2="17" />
                        </svg>
                        <span>Setelah dikonfirmasi, kalkulasi pending akan <strong>direset ke 0</strong> dan tidak dapat
                            diurungkan.</span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="font-size:13px;">
                        Batal
                    </button>
                    <form id="formBayar" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="verk-btn-create" style="height:38px; font-size:13px;">
                            <svg viewBox="0 0 24 24" style="width:13px;height:13px;">
                                <polyline points="20 6 9 17 4 12" />
                            </svg>
                            Konfirmasi Bayar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>


    {{-- â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
     MODAL RIWAYAT
â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• --}}
    <div id="modalRiwayat" class="modal fade verk-modal verk-modal-plain" tabindex="-1"
        aria-labelledby="modalRiwayatLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <svg viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10" />
                            <polyline points="12 6 12 12 16 14" />
                        </svg>
                        Riwayat Pembayaran â€”
                        <span class="accent" id="riwayat-nama"></span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="padding:20px;">
                    <div id="riwayat-body"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal"
                        style="font-size:13px;">Tutup</button>
                </div>
            </div>
        </div>
    </div>


    {{-- â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
     MODAL KALKULASI
â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• --}}
    <div id="kalkulasiModal" class="modal fade verk-modal verk-modal-plain" tabindex="-1"
        aria-labelledby="kalkulasiModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <svg viewBox="0 0 24 24">
                            <rect x="4" y="2" width="16" height="20" rx="2" />
                            <line x1="8" y1="6" x2="16" y2="6" />
                            <line x1="8" y1="10" x2="16" y2="10" />
                            <line x1="8" y1="14" x2="12" y2="14" />
                        </svg>
                        Kalkulasi â€”
                        <span class="accent" id="kalkulasiNama"></span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="padding:22px;">

                    {{-- Loading --}}
                    <div id="kalkulasiLoading" class="text-center py-5">
                        <div class="spinner-border" style="color:#1A5FC8; width:2.2rem; height:2.2rem;" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p style="margin-top:12px; color:#8A99B3; font-size:13px;">Memuat data kalkulasi...</p>
                    </div>

                    <div id="kalkulasiContent" style="display:none;">

                        {{-- Summary --}}
                        <div class="kalk-stats">
                            <div class="kalk-stat">
                                <p class="label">Rate / Data</p>
                                <p class="value" id="kalk-rate"></p>
                            </div>
                            <div class="kalk-stat">
                                <p class="label">Total Lapangan</p>
                                <p class="value" id="kalk-total"></p>
                            </div>
                            <div class="kalk-stat is-warn">
                                <p class="label">Pending Lapangan</p>
                                <p class="value" id="kalk-pending-lapangan"></p>
                            </div>
                            <div class="kalk-stat is-info">
                                <p class="label">Pending Progress</p>
                                <p class="value" id="kalk-pending-progress"></p>
                            </div>
                            <div class="kalk-stat is-warn">
                                <p class="label">Total Pending</p>
                                <p class="value" id="kalk-pending"></p>
                            </div>
                            <div class="kalk-stat is-success">
                                <p class="label">Total Nominal</p>
                                <p class="value" id="kalk-nominal"></p>
                            </div>
                        </div>

                        {{-- Tabs --}}
                        <div class="verk-tabs">
                            <button class="verk-tab active" id="tab-lapangan-link" onclick="switchKalkTab('lapangan')">
                                <svg viewBox="0 0 24 24">
                                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" />
                                    <circle cx="12" cy="10" r="3" />
                                </svg>
                                Data Lapangan
                            </button>
                            <button class="verk-tab" id="tab-progress-link" onclick="switchKalkTab('progress')">
                                <svg viewBox="0 0 24 24">
                                    <polyline points="22 12 18 12 15 21 9 3 6 12 2 12" />
                                </svg>
                                Data Entry Progress
                            </button>
                        </div>

                        {{-- Filter --}}
                        <div style="display:flex; justify-content:flex-end; margin-bottom:16px;">
                            <div class="verk-filter-group">
                                <button class="verk-filter-btn active" data-filter="semua">Semua</button>
                                <button class="verk-filter-btn warn" data-filter="pending">Belum Dibayar</button>
                                <button class="verk-filter-btn success" data-filter="lunas">Sudah Dibayar</button>
                            </div>
                        </div>

                        {{-- TAB: Lapangan --}}
                        <div id="kalkTabLapangan">
                            <div class="verk-section-label" style="margin-bottom:12px;">
                                <svg viewBox="0 0 24 24">
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
                                    <line x1="16" y1="2" x2="16" y2="6" />
                                    <line x1="8" y1="2" x2="8" y2="6" />
                                    <line x1="3" y1="10" x2="21" y2="10" />
                                </svg>
                                Rekap Per Bulan â€” Data Lapangan
                            </div>
                            <div class="table-responsive mb-4">
                                <table class="kalk-table">
                                    <thead>
                                        <tr>
                                            <th>Bulan</th>
                                            <th class="tc">Total</th>
                                            <th class="tc">Lunas</th>
                                            <th class="tc">Pending</th>
                                            <th class="tr">Nominal Pending</th>
                                        </tr>
                                    </thead>
                                    <tbody id="kalk-rekap-body"></tbody>
                                </table>
                            </div>

                            <div class="verk-section-label" style="margin-bottom:12px;">
                                <svg viewBox="0 0 24 24">
                                    <line x1="8" y1="6" x2="21" y2="6" />
                                    <line x1="8" y1="12" x2="21" y2="12" />
                                    <line x1="8" y1="18" x2="21" y2="18" />
                                    <line x1="3" y1="6" x2="3.01" y2="6" />
                                    <line x1="3" y1="12" x2="3.01" y2="12" />
                                    <line x1="3" y1="18" x2="3.01" y2="18" />
                                </svg>
                                Detail Data Lapangan
                            </div>
                            <div class="table-responsive">
                                <table class="kalk-table">
                                    <thead>
                                        <tr>
                                            <th style="width:44px">No</th>
                                            <th>Nama PU</th>
                                            <th>Tgl Verifikasi</th>
                                            <th class="tc">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody id="kalk-data-body"></tbody>
                                </table>
                            </div>
                            <div style="display:flex; justify-content:space-between; align-items:center; margin-top:14px;">
                                <small id="kalk-pagination-info" style="font-size:12px; color:#B0BCCE;"></small>
                                <div id="kalk-pagination-buttons" class="verk-pagi"></div>
                            </div>
                        </div>

                        {{-- TAB: Progress --}}
                        <div id="kalkTabProgress" style="display:none;">
                            <div class="verk-section-label" style="margin-bottom:12px;">
                                <svg viewBox="0 0 24 24">
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
                                    <line x1="16" y1="2" x2="16" y2="6" />
                                    <line x1="8" y1="2" x2="8" y2="6" />
                                    <line x1="3" y1="10" x2="21" y2="10" />
                                </svg>
                                Rekap Per Bulan â€” Data Entry Progress
                            </div>
                            <div class="table-responsive mb-4">
                                <table class="kalk-table">
                                    <thead>
                                        <tr>
                                            <th>Bulan</th>
                                            <th class="tc">Total</th>
                                            <th class="tc">Lunas</th>
                                            <th class="tc">Pending</th>
                                            <th class="tr">Nominal Pending</th>
                                        </tr>
                                    </thead>
                                    <tbody id="kalk-rekap-progress-body"></tbody>
                                </table>
                            </div>

                            <div class="verk-section-label" style="margin-bottom:12px;">
                                <svg viewBox="0 0 24 24">
                                    <line x1="8" y1="6" x2="21" y2="6" />
                                    <line x1="8" y1="12" x2="21" y2="12" />
                                    <line x1="8" y1="18" x2="21" y2="18" />
                                    <line x1="3" y1="6" x2="3.01" y2="6" />
                                    <line x1="3" y1="12" x2="3.01" y2="12" />
                                    <line x1="3" y1="18" x2="3.01" y2="18" />
                                </svg>
                                Detail Data Entry Progress
                            </div>
                            <div class="table-responsive">
                                <table class="kalk-table">
                                    <thead>
                                        <tr>
                                            <th style="width:44px">No</th>
                                            <th>Nama PU</th>
                                            <th>Data Entry</th>
                                            <th>Type</th>
                                            <th>Tgl Verifikasi</th>
                                            <th class="tc">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody id="kalk-progress-body"></tbody>
                                </table>
                            </div>
                            <div style="display:flex; justify-content:space-between; align-items:center; margin-top:14px;">
                                <small id="kalk-pagination-progress-info" style="font-size:12px; color:#B0BCCE;"></small>
                                <div id="kalk-pagination-progress-buttons" class="verk-pagi"></div>
                            </div>
                        </div>

                    </div>{{-- /kalkulasiContent --}}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal"
                        style="font-size:13px;">Tutup</button>
                </div>
            </div>
        </div>
    </div>


    <script>
        const kalkState = {
            url: '',
            filter: 'semua',
            pageLapangan: 1,
            pageProgress: 1,
            activeTab: 'lapangan',
        };

        function switchKalkTab(tab) {
            kalkState.activeTab = tab;
            document.getElementById('kalkTabLapangan').style.display = tab === 'lapangan' ? 'block' : 'none';
            document.getElementById('kalkTabProgress').style.display = tab === 'progress' ? 'block' : 'none';
            document.getElementById('tab-lapangan-link').classList.toggle('active', tab === 'lapangan');
            document.getElementById('tab-progress-link').classList.toggle('active', tab === 'progress');
        }

        document.addEventListener('DOMContentLoaded', function() {

            const rupiah = n => 'Rp ' + Number(n).toLocaleString('id-ID');

            const formatTanggal = str => {
                if (!str) return '-';
                const d = new Date(str);
                if (isNaN(d)) return str;
                return d.toLocaleDateString('id-ID', {
                    day: 'numeric',
                    month: 'long',
                    year: 'numeric'
                });
            };

            function openModal(id) {
                const el = document.getElementById(id);
                if (!el) return;
                if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                    bootstrap.Modal.getOrCreateInstance(el).show();
                    return;
                }
                if (typeof $ !== 'undefined' && $.fn && $.fn.modal) {
                    $(el).modal('show');
                    return;
                }
                el.style.display = 'block';
                el.classList.add('show');
                document.body.classList.add('modal-open');
                const bd = document.createElement('div');
                bd.className = 'modal-backdrop fade show';
                bd.id = 'manual-backdrop';
                document.body.appendChild(bd);
            }

            function closeModal(id) {
                const el = document.getElementById(id);
                if (!el) return;
                if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                    const m = bootstrap.Modal.getInstance(el);
                    if (m) m.hide();
                    return;
                }
                if (typeof $ !== 'undefined' && $.fn && $.fn.modal) {
                    $(el).modal('hide');
                    return;
                }
                el.style.display = 'none';
                el.classList.remove('show');
                document.body.classList.remove('modal-open');
                const bd = document.getElementById('manual-backdrop');
                if (bd) bd.remove();
            }

            document.querySelectorAll('[data-bs-dismiss="modal"]').forEach(btn => {
                btn.addEventListener('click', function() {
                    const modal = this.closest('.modal');
                    if (modal) closeModal(modal.id);
                });
            });

            // â”€â”€ Search / Reset â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
            const searchInput = document.getElementById('search');
            const resetBtn = document.getElementById('resetBtn');

            if (searchInput) {
                let searchTimer;
                searchInput.addEventListener('input', function() {
                    clearTimeout(searchTimer);
                    searchTimer = setTimeout(() => {
                        const url = new URL(window.location.href);
                        if (this.value) {
                            url.searchParams.set('search', this.value);
                        } else {
                            url.searchParams.delete('search');
                        }
                        url.searchParams.delete('page');
                        window.location.href = url.toString();
                    }, 500);
                });
            }

            if (resetBtn) {
                resetBtn.addEventListener('click', function() {
                    const url = new URL(window.location.href);
                    url.searchParams.delete('search');
                    url.searchParams.delete('page');
                    window.location.href = url.toString();
                });
            }

            // â”€â”€ Modal Bayar â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
            document.querySelectorAll('.btn-open-bayar').forEach(btn => {
                btn.addEventListener('click', function() {
                    document.getElementById('bayar-nama').textContent = this.dataset.nama;
                    document.getElementById('bayar-jumlah').innerHTML =
                        `<span class="verk-badge verk-badge-warn"><span class="dot"></span>${this.dataset.jumlah} data</span>`;
                    document.getElementById('bayar-rate').textContent = this.dataset.rate;
                    document.getElementById('bayar-total').textContent = this.dataset.total;
                    document.getElementById('formBayar').action = this.dataset.action;
                    openModal('modalBayar');
                });
            });

            // â”€â”€ Modal Riwayat â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
            document.querySelectorAll('.btn-open-riwayat').forEach(btn => {
                btn.addEventListener('click', function() {
                    document.getElementById('riwayat-nama').textContent = this.dataset.nama;
                    let payments = [];
                    try {
                        payments = JSON.parse(this.dataset.payments);
                    } catch (e) {}
                    const body = document.getElementById('riwayat-body');

                    if (!payments.length) {
                        body.innerHTML = `
                        <div style="text-align:center; padding:50px 20px; color:#B0BCCE;">
                            <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#D0DAEC" stroke-width="1.5" style="display:block;margin:0 auto 10px;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                            <p style="font-size:13px;">Belum ada riwayat pembayaran.</p>
                        </div>`;
                    } else {
                        let rows = '',
                            totalData = 0,
                            totalNominal = 0;
                        payments.forEach((p, idx) => {
                            const tgl = p.paid_at ? new Date(p.paid_at).toLocaleDateString(
                                'id-ID', {
                                    day: '2-digit',
                                    month: 'short',
                                    year: 'numeric',
                                    hour: '2-digit',
                                    minute: '2-digit'
                                }) : '-';
                            totalData += parseInt(p.jumlah_data) || 0;
                            totalNominal += parseFloat(p.total_nominal) || 0;
                            rows += `
                            <tr>
                                <td style="color:#B0BCCE; font-size:12px;">${idx + 1}</td>
                                <td style="font-size:13px; color:#3A4A6B;">${tgl}</td>
                                <td class="tc"><span class="verk-badge verk-badge-success">${p.jumlah_data} data</span></td>
                                <td class="tr" style="font-family:'DM Mono',monospace; font-weight:600; color:#0F6E56; font-size:13px;">${rupiah(p.total_nominal)}</td>
                            </tr>`;
                        });
                        body.innerHTML = `
                        <div class="table-responsive">
                            <table class="verk-riwayat-table">
                                <thead>
                                    <tr>
                                        <th style="width:44px">No</th>
                                        <th>Tanggal Bayar</th>
                                        <th class="tc">Jumlah Data</th>
                                        <th class="tr">Total Nominal</th>
                                    </tr>
                                </thead>
                                <tbody>${rows}</tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="2" style="font-weight:700;">Total Keseluruhan</td>
                                        <td class="tc">${totalData} data</td>
                                        <td class="tr" style="font-family:'DM Mono',monospace;">${rupiah(totalNominal)}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>`;
                    }
                    openModal('modalRiwayat');
                });
            });

            // â”€â”€ Modal Kalkulasi â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
            document.querySelectorAll('.btn-kalkulasi').forEach(btn => {
                btn.addEventListener('click', function() {
                    kalkState.url = this.dataset.url;
                    kalkState.filter = 'semua';
                    kalkState.pageLapangan = 1;
                    kalkState.pageProgress = 1;
                    kalkState.activeTab = 'lapangan';
                    document.getElementById('kalkulasiNama').textContent = this.dataset.nama;
                    setKalkFilter('semua');
                    switchKalkTab('lapangan');
                    loadKalkulasi();
                    openModal('kalkulasiModal');
                });
            });

            document.querySelectorAll('.verk-filter-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    kalkState.filter = this.dataset.filter;
                    kalkState.pageLapangan = 1;
                    kalkState.pageProgress = 1;
                    setKalkFilter(this.dataset.filter);
                    loadKalkulasi();
                });
            });

            function setKalkFilter(active) {
                document.querySelectorAll('.verk-filter-btn').forEach(b => {
                    b.classList.toggle('active', b.dataset.filter === active);
                });
            }

            function loadKalkulasi() {
                document.getElementById('kalkulasiLoading').style.display = 'block';
                document.getElementById('kalkulasiContent').style.display = 'none';

                const params = new URLSearchParams({
                    filter: kalkState.filter,
                    page_lapangan: kalkState.pageLapangan,
                    page_progress: kalkState.pageProgress,
                });

                fetch(`${kalkState.url}?${params}`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(r => r.json())
                    .then(data => {
                        renderKalkSummary(data.summary);
                        renderKalkRekap(data.rekap);
                        renderKalkData(data.dataLapangans);
                        renderKalkPagination(data.dataLapangans);
                        renderKalkRekapProgress(data.rekapProgress);
                        renderKalkProgress(data.dataProgress);
                        renderKalkProgressPagination(data.dataProgress);
                        document.getElementById('kalkulasiLoading').style.display = 'none';
                        document.getElementById('kalkulasiContent').style.display = 'block';
                    })
                    .catch(() => {
                        document.getElementById('kalkulasiLoading').innerHTML =
                            '<p style="color:#DC2626; text-align:center; margin-top:20px; font-size:13px;">Gagal memuat data.</p>';
                    });
            }

            function renderKalkSummary(s) {
                document.getElementById('kalk-rate').textContent = rupiah(s.rate_per_data);
                document.getElementById('kalk-total').textContent = s.total_data + ' data';
                document.getElementById('kalk-pending-lapangan').textContent = s.belum_dibayar_lapangan + ' data';
                document.getElementById('kalk-pending-progress').textContent = s.belum_dibayar_progress + ' data';
                document.getElementById('kalk-pending').textContent = s.belum_dibayar + ' data';
                document.getElementById('kalk-nominal').textContent = rupiah(s.total_nominal);
            }

            function renderKalkRekap(rekap) {
                const tbody = document.getElementById('kalk-rekap-body');
                if (!rekap.length) {
                    tbody.innerHTML = `<tr><td colspan="5" class="kalk-nodata">Belum ada data.</td></tr>`;
                    return;
                }
                tbody.innerHTML = rekap.map(r => `
                <tr>
                    <td style="font-weight:600; color:#0F1F40;">${r.bulan_label}</td>
                    <td class="tc">${r.total}</td>
                    <td class="tc"><span class="verk-badge verk-badge-success">${r.sudah_dibayar}</span></td>
                    <td class="tc"><span class="verk-badge verk-badge-warn">${r.belum_dibayar}</span></td>
                    <td class="tr" style="font-family:'DM Mono',monospace; font-weight:600; color:#0F6E56;">${rupiah(r.nominal_pending)}</td>
                </tr>`).join('');
            }

            function renderKalkRekapProgress(rekap) {
                const tbody = document.getElementById('kalk-rekap-progress-body');
                if (!rekap.length) {
                    tbody.innerHTML = `<tr><td colspan="5" class="kalk-nodata">Belum ada data.</td></tr>`;
                    return;
                }
                tbody.innerHTML = rekap.map(r => `
                <tr>
                    <td style="font-weight:600; color:#0F1F40;">${r.bulan_label ?? '-'}</td>
                    <td class="tc">${r.total}</td>
                    <td class="tc"><span class="verk-badge verk-badge-success">${r.sudah_dibayar}</span></td>
                    <td class="tc"><span class="verk-badge verk-badge-warn">${r.belum_dibayar}</span></td>
                    <td class="tr" style="font-family:'DM Mono',monospace; font-weight:600; color:#0F6E56;">${rupiah(r.nominal_pending)}</td>
                </tr>`).join('');
            }

            function renderKalkData(paginator) {
                const tbody = document.getElementById('kalk-data-body');
                const data = paginator.data;
                if (!data.length) {
                    tbody.innerHTML = `<tr><td colspan="4" class="kalk-nodata">Tidak ada data.</td></tr>`;
                    return;
                }
                tbody.innerHTML = data.map((d, i) => `
                <tr>
                    <td style="color:#B0BCCE; font-size:12px;">${(paginator.current_page - 1) * paginator.per_page + i + 1}</td>
                    <td>${d.nama_pu ?? '-'}</td>
                    <td style="color:#8A99B3; font-size:12px;">${formatTanggal(d.tanggal_verifikasi)}</td>
                    <td class="tc">${d.payment_id
                        ? '<span class="verk-badge verk-badge-success">âœ“ Lunas</span>'
                        : '<span class="verk-badge verk-badge-warn"><span class="dot"></span>Pending</span>'}</td>
                </tr>`).join('');
            }

            function renderKalkProgress(paginator) {
                const tbody = document.getElementById('kalk-progress-body');
                const data = paginator.data;
                if (!data.length) {
                    tbody.innerHTML = `<tr><td colspan="6" class="kalk-nodata">Tidak ada data.</td></tr>`;
                    return;
                }
                tbody.innerHTML = data.map((d, i) => {
                    const entryType = d.data_entry?.entry_type ?? '-';
                    const badgeType = entryType === 'OSS' ?
                        '<span class="verk-badge" style="background:#E0F4FF;color:#0369A1;">OSS</span>' :
                        entryType === 'SIHALAL' ?
                        '<span class="verk-badge" style="background:#EEF4FF;color:#1A5FC8;">SIHALAL</span>' :
                        '<span class="verk-badge" style="background:#F5F7FB;color:#8A99B3;">-</span>';
                    return `
                <tr>
                    <td style="color:#B0BCCE; font-size:12px;">${(paginator.current_page - 1) * paginator.per_page + i + 1}</td>
                    <td>${d.data_lapangan?.nama_pu ?? '-'}</td>
                    <td style="color:#6B7A99;">${d.data_entry?.user?.name ?? '-'}</td>
                    <td>${badgeType}</td>
                    <td style="color:#8A99B3; font-size:12px;">${formatTanggal(d.tanggal_verifikasi)}</td>
                    <td class="tc">${d.payment_id
                        ? '<span class="verk-badge verk-badge-success">âœ“ Lunas</span>'
                        : '<span class="verk-badge verk-badge-warn"><span class="dot"></span>Pending</span>'}</td>
                </tr>`;
                }).join('');
            }

            function renderKalkPagination(paginator) {
                document.getElementById('kalk-pagination-info').textContent =
                    `Menampilkan ${paginator.from ?? 0}â€“${paginator.to ?? 0} dari ${paginator.total} data`;
                buildPaginationButtons('kalk-pagination-buttons', paginator, pg => {
                    kalkState.pageLapangan = pg;
                    loadKalkulasi();
                });
            }

            function renderKalkProgressPagination(paginator) {
                document.getElementById('kalk-pagination-progress-info').textContent =
                    `Menampilkan ${paginator.from ?? 0}â€“${paginator.to ?? 0} dari ${paginator.total} data`;
                buildPaginationButtons('kalk-pagination-progress-buttons', paginator, pg => {
                    kalkState.pageProgress = pg;
                    loadKalkulasi();
                });
            }

            function buildPaginationButtons(containerId, paginator, onPageChange) {
                const btns = document.getElementById(containerId);
                btns.innerHTML = '';
                const mkBtn = (label, disabled, active, onClick) => {
                    const b = document.createElement('button');
                    b.className = 'verk-pagi-btn' + (active ? ' active' : '');
                    b.innerHTML = label;
                    b.disabled = disabled;
                    if (!disabled) b.onclick = onClick;
                    return b;
                };
                btns.appendChild(mkBtn('&laquo;', paginator.current_page <= 1, false,
                    () => onPageChange(paginator.current_page - 1)));
                const cur = paginator.current_page,
                    max = paginator.last_page;
                let start = Math.max(1, cur - 2),
                    end = Math.min(max, start + 4);
                if (end - start < 4) start = Math.max(1, end - 4);
                for (let p = start; p <= end; p++) {
                    btns.appendChild(mkBtn(p, false, p === cur, () => onPageChange(p)));
                }
                btns.appendChild(mkBtn('&raquo;', paginator.current_page >= max, false,
                    () => onPageChange(paginator.current_page + 1)));
            }

        }); // DOMContentLoaded
    </script>

@endsection

