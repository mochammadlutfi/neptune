<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;
use App\Http\Requests\Settings\GeneralRequest;
use App\Models\Currency;

class SystemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = collect([
            'app_name' => settings()->get('app_name'),
            'date_format' => settings()->get('date_format'),
            'time_format' => settings()->get('time_format'),
            'timezone' => settings()->get('timezone'),
            'logo_light' => settings()->get('logo_light'),
            'logo_dark' => settings()->get('logo_dark'),
            'currency_id' => settings()->get('currency_id'),
            'locale' => settings()->get('locale'),
            'stock_accounting_method' => settings()->get('stock_accounting_method'),
        ]);
        
        return response()->json($data, 200);
    }

    
    public function general()
    {
        $data = collect([
            'app_name' => settings()->get('app_name'),
            'company_name' => settings()->get('company_name'),
            'company_email' => settings()->get('company_email'),
            'company_phone' => settings()->get('company_phone'),
            'company_city' => settings()->get('company_city'),
            'company_country' => settings()->get('company_country'),
            'company_address' => settings()->get('company_address'),
            'date_format' => settings()->get('date_format'),
            'time_format' => settings()->get('time_format'),
            'timezone' => settings()->get('timezone'),
            'logo_light' => settings()->get('logo_light'),
            'logo_light_sm' => settings()->get('logo_light_sm'),
            'logo_dark' => settings()->get('logo_dark'),
            'logo_dark_sm' => settings()->get('logo_dark_sm'),
            'currency_id' => (int)settings()->get('currency_id'),
            'locale' => settings()->get('locale'),
        ]);

        return response()->json($data, 200);
    }
    
    public function generalUpdate(Request $request)
    {
        DB::beginTransaction();
        try{
            settings()->set('app_name', $request->app_name);
            settings()->set('company_name', $request->company_name);
            settings()->set('company_phone', $request->company_phone);
            settings()->set('company_email', $request->company_email);
            settings()->set('company_city', $request->company_city);
            settings()->set('company_country', $request->company_country);
            settings()->set('company_address', $request->company_address);
            settings()->set('date_format', $request->date_format);
            settings()->set('time_format', $request->time_format);
            settings()->set('timezone', $request->timezone);

            if($request->hasFile('logo_light')){
                deleteFile(settings()->get('logo_light'));
                settings()->set('logo_light', uploadFile($request->logo_light, 'logo', true));
            }

            if($request->hasFile('logo_dark')){
                deleteFile(settings()->get('logo_dark'));
                settings()->set('logo_dark', uploadFile($request->logo_dark, 'logo', true));
            }

            if($request->hasFile('logo_light_sm')){
                deleteFile(settings()->get('logo_light_sm'));
                settings()->set('logo_light_sm', uploadFile($request->logo_light_sm, 'logo', true));
            }

            if($request->hasFile('logo_dark_sm')){
                deleteFile(settings()->get('logo_dark_sm'));
                settings()->set('logo_dark_sm', uploadFile($request->logo_dark_sm, 'logo', true));
            }
            settings()->set('currency_id', $request->currency_id);
            settings()->set('locale', $request->locale);

        }catch(\QueryException $e){
            DB::rollback();
            return response()->json([
                'success' => false,
                'result' => $e,
            ], 422);
        }
        DB::commit();
        return response()->json([
            'success' => true,
        ], 200);
    }
    /**
     * Display a listing of the resource.
     */
    public function email()
    {
        $data = Collect([
            'email_provider' => settings()->get('mail_provider'),
            'mail_host' => settings()->get('mail_host'),
            'mail_port' => settings()->get('mail_port'),
            'mail_username' => settings()->get('mail_username'),
            'mail_password' => settings()->get('mail_password'),
            'mail_encryption' => settings()->get('mail_encryption'),
            'mail_from_address' => settings()->get('mail_from_address'),
            'mail_from_name' => settings()->get('mail_from_name'),
        ]);

        return response()->json($data, 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function emailUpdate(Request $request)
    {
        $rules = [
            'mail_provider' => 'required',
            'mail_host' => 'required',
            'mail_port' => 'required',
            'mail_username' => 'required',
            'mail_password' => 'required',
            'mail_encryption' => 'required',
            'mail_from_address' => 'required',
            'mail_from_name' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()){
            return response()->json([
                'success' => false,
                'result' => $validator->errors(),
            ], 422);
        }else{
            DB::beginTransaction();
            try{

                settings()->set('mail_provider', $request->mail_provider);
                settings()->set('mail_host', $request->mail_host);
                settings()->set('mail_port', $request->mail_port);
                settings()->set('mail_username', $request->mail_username);
                settings()->set('mail_password', $request->mail_password);
                settings()->set('mail_encryption', $request->mail_encryption);
                settings()->set('mail_from_address', $request->mail_from_address);
                settings()->set('mail_from_name', $request->mail_from_name);

            }catch(\QueryException $e){
                DB::rollback();
                return response()->json([
                    'success' => false,
                    'result' => $e,
                ], 422);
            }
            DB::commit();
            return response()->json([
                'success' => true,
            ], 200);
        }
    }

    /**
     * Test email configuration by sending a test email
     */
    public function emailTest(Request $request)
    {
        // Validasi input untuk test email
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'mail_host' => 'required|string',
            'mail_port' => 'required|integer',
            'mail_username' => 'required|string',
            'mail_password' => 'required|string',
            'mail_encryption' => 'required|string|in:ssl,tls,none',
            'mail_from_address' => 'required|email',
            'mail_from_name' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'result' => $validator->errors(),
            ], 422);
        }

        try {
            // Konfigurasi sementara untuk test email
            $config = [
                'transport' => 'smtp',
                'host' => $request->mail_host,
                'port' => $request->mail_port,
                'encryption' => $request->mail_encryption === 'none' ? null : $request->mail_encryption,
                'username' => $request->mail_username,
                'password' => $request->mail_password,
                'timeout' => null,
                'local_domain' => env('MAIL_EHLO_DOMAIN'),
            ];

            // Set konfigurasi mail sementara
            Config::set('mail.mailers.smtp', $config);
            Config::set('mail.from.address', $request->mail_from_address);
            Config::set('mail.from.name', $request->mail_from_name);

            // Kirim test email
            Mail::raw('This is a test email from your ERP system. If you receive this message, your email configuration is working correctly.', function ($message) use ($request) {
                $message->to($request->email)
                        ->subject('Test Email - ERP System')
                        ->from($request->mail_from_address, $request->mail_from_name);
            });

            return response()->json([
                'success' => true,
                'message' => 'Test email sent successfully to ' . $request->email,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send test email: ' . $e->getMessage(),
            ], 422);
        }
    }
    
    public function salesUpdate(Request $request)
    {
        DB::beginTransaction();
        try{
            settings()->set('invoicing_policy', $request->invoicing_policy);
            settings()->set('sale_location', $request->sale_location);
            settings()->set('sale_payment_term', $request->sale_payment_term);

        }catch(\QueryException $e){
            DB::rollback();
            return response()->json([
                'success' => false,
                'result' => $e,
            ], 422);
        }
        DB::commit();
        return response()->json([
            'success' => true,
        ], 200);
    }
    
    public function purchaseUpdate(Request $request)
    {
        DB::beginTransaction();
        try{
            settings()->set('bill_controll', $request->bill_controll);
            settings()->set('purchase_location', $request->purchase_location);
            settings()->set('purchase_payment_term', $request->purchase_payment_term);

        }catch(\QueryException $e){
            DB::rollback();
            return response()->json([
                'success' => false,
                'result' => $e,
            ], 422);
        }
        DB::commit();
        return response()->json([
            'success' => true,
        ], 200);
    }
    
    public function accounting()
    {
        $data = collect([
            'account_revenue' => settings()->get('account_revenue'),
            'account_receivable' => settings()->get('account_receivable'),
            'account_sales_discount' => settings()->get('account_sales_discount'),
            'account_tax_collected' => settings()->get('account_tax_collected'),
            'account_expense' => settings()->get('account_expense'),
            'account_payable' => settings()->get('account_payable'),
            'account_tax_paid' => settings()->get('account_tax_paid'),
            'account_purchase_discount' => settings()->get('account_purchase_discount'),
        ]);

        return response()->json($data, 200);
    }
    
    public function accountingUpdate(Request $request)
    {
        DB::beginTransaction();
        try{
            settings()->set('account_revenue', $request->account_revenue);
            settings()->set('account_receivable', $request->account_receivable);
            settings()->set('account_sales_discount', $request->account_sales_discount);
            settings()->set('account_tax_collected', $request->account_tax_collected);
            settings()->set('account_inventory_out', $request->account_inventory_out);
            settings()->set('account_cogs', $request->account_cogs);
            settings()->set('account_expense', $request->account_expense);
            settings()->set('account_payable', $request->account_payable);
            settings()->set('account_tax_paid', $request->account_tax_paid);
            settings()->set('account_purchase_discount', $request->account_purchase_discount);
            settings()->set('account_inventory_in', $request->account_inventory_in);
            settings()->set('account_cogs', $request->account_cogs);

        }catch(\QueryException $e){
            DB::rollback();
            return response()->json([
                'success' => false,
                'result' => $e,
            ], 422);
        }
        DB::commit();
        return response()->json([
            'success' => true,
        ], 200);
    }

    private function setEnvironmentValue($environmentName, $configKey, $newValue)
    {
        file_put_contents(App::environmentFilePath(), str_replace(
            $environmentName . '=' . Config::get($configKey),
            $environmentName . '=' . $newValue,
            file_get_contents(App::environmentFilePath())
        ));
    
        Config::set($configKey, $newValue);
    
        // Reload the cached config       
        if (file_exists(App::getCachedConfigPath())) {
            Artisan::call("config:cache");
        }
    }
}
