<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InsightController extends Controller
{
    /**
     * CASE 1: High-Variety Customers
     * Customers who purchased 5 or more DISTINCT products in a single day
     * Return top 5 customers
     */
    public function highVarietyCustomers()
    {
        return DB::table('invoices')
        ->join('invoice_items', 'invoice_items.invoice_id', '=', 'invoices.id')
        ->join('customers', 'customers.id', '=', 'invoices.customer_id')
        ->select(
            'customers.id', 
            'customers.email',
            DB::raw('DATE(invoices.created_at) as purchase_date'),
            DB::raw('COUNT(DISTINCT invoice_items.product_id) as distinct_products'),
            DB::raw('SUM(invoice_items.quantity) as total_items'),
            DB::raw('SUM(invoice_items.total_price) as total_amount'),
            DB::raw('SUM(invoice_items.total_tax) as total_tax')
        )
        ->groupBy(
            'customers.id', 
            'customers.email',
            DB::raw('DATE(invoices.created_at)')
        )
        ->having('distinct_products', '>=', 5)
            ->orderByDesc('total_amount')
            ->limit(5)
            ->get();
    }

    /**
     * CASE 2: Stock Forecast
     * Average daily sales (last 7 days) & estimated days until stock runs out
     */
    public function stockForecast()
    {
        $last7Days = Carbon::now()->subDays(7);

        return DB::table('products')
            ->leftJoin('invoice_items', 'invoice_items.product_id', '=', 'products.id')
            ->leftJoin('invoices', function ($join) use ($last7Days) {
                $join->on('invoices.id', '=', 'invoice_items.invoice_id')
                     ->where('invoices.created_at', '>=', $last7Days);
            })
            ->select(
                'products.id',
                'products.name',
                'products.stock',
                DB::raw('COALESCE(SUM(invoice_items.quantity) / 7, 0) as avg_daily_sales'),
                DB::raw('
                    CASE 
                        WHEN SUM(invoice_items.quantity) = 0 THEN NULL
                        ELSE ROUND(products.stock / (SUM(invoice_items.quantity) / 7))
                    END as estimated_days_left
                ')
            )
            ->groupBy(
                'products.id',
                'products.name',
                'products.stock',
            )
            ->get(); 
    }

    /**
     * CASE 3: Repeat Customer Insights
     * Customers who made  purchase within 7 days of first purchase
     * Return last 5 such customers
    */

    public function repeatCustomers()
    {
        return DB::table('invoices as i1')
            ->join('invoices as i2', function ($join) {
                $join->on('i1.customer_id', '=', 'i2.customer_id')
                     ->whereRaw('i2.created_at > i1.created_at')
                     ->whereRaw('DATEDIFF(i2.created_at, i1.created_at) <= 7');
            })
            ->join('customers', 'customers.id', '=', 'i1.customer_id')
            ->select(
                 'customers.email',
                DB::raw('MIN(i1.created_at) as first_purchase_date'),
                DB::raw('MIN(i2.created_at) as second_purchase_date'),
                DB::raw('SUM(i1.grand_total + i2.grand_total) as total_spent')
            )
            ->groupBy('customers.email')
            ->orderByDesc('second_purchase_date')
            ->limit(5)
            ->get();
    }

    /**
     * CASE 4: High-Demand Orders
     * Invoices that include top 5 most sold products in last 30 days
     */
    public function highDemandOrders()
    {
        $last30Days = Carbon::now()->subDays(30);

        //step 1 find top 5 products
        $topProducts = DB::table('invoice_items')
        ->join('invoices', 'invoices.id', '=', 'invoice_items.invoice_id')
        ->where('invoices.created_at', '>=', $last30Days)
        ->select(
            'invoice_items.product_id',
            DB::raw('SUM(invoice_items.quantity) as qty')
        )
        ->groupBy('invoice_items.product_id')
        ->orderByDesc('qty')
        ->limit(5)
        ->plunk('product_id');

        //stepm2 find invoices contain those product
        return DB::table('invoices')
        ->join('invoice_items', 'invoice_items.invoice_id', '=', 'invoices.id')
        ->join('products', 'products.id', '=', 'invoice_items.product_id')
        ->join('customers', 'customers.id', '=', 'invoices.customer_id')
          ->whereIn('invoice_items.product_id', $topProducts)
            ->select(
                'invoices.invoice_number',
                'customers.email',
                'products.name as product_name',
                'invoice_items.quantity',
                'invoice_items.total_price',
                'invoices.created_at'
            )
            ->orderByDesc('invoices.created_at')
            ->get();
    }
}
